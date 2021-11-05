<?php
namespace App\Lib\Logic\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\BaseLogic;
use App\Lib\Logic\Stat\StatLogic;
use App\Models\Account\Account;
use App\Models\Game\LotteryCommission;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryProject;


/**
 * 返点逻辑　
 * 2019-10 整理
 * Class CommissionLogic
 * @package App\Lib\Lottery\CommissionLogic
 */
class CommissionLogic  extends BaseLogic
{

    /**
     * @param $issue
     * @param $slot
     * @return mixed
     * @throws \Exception
     */
    static function send($issue, $slot) {
        if (!$issue) {
            return "commission-process-logic-error-不存在的奖期";
        }

        // 判定
        if (random_int(1, 100) < 10) {
            if (!self::blaBla()) {
                //return "commission-process-logic-error-1-不存在的奖期";
            }
        }

        $cacheKey = "cp_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $slot;

        if (!cache()->add($cacheKey, 1, now()->addMinutes(10))) {
            cache()->forget($cacheKey);
            return "commission-process-logic-error-正在处理中";
        }

        // 1. 检测录号状态
        if ($issue->status_process != LotteryIssue::STATUS_PROCESS_SEND) {
            cache()->forget($cacheKey);
            return "commission-process-logic-error-奖期未录号";
        }

        // 2. 检测返点状态
        if ($issue->status_commission == 1) {
            cache()->forget($cacheKey);
            return "commission-process-logic-error-奖期返点已经处理";
        }

        // 3. 获取所有中奖注单
        $totalSlotCount = LotteryCommission::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where("slot", $slot)->where('status', 0)->count();

        // 开始
        Clog::commissionProcess("commission-process-logic-start-slot($slot)-issue-{$issue->issue}-total:{$totalSlotCount}", $issue->lottery_sign, []);

        $return = [
            'total_item'    => 0,
            'total_fail'    => 0,
            'total_user'    => 0,
            'status'        => true,
        ];

        // 4. 是否有注单
        if ($totalSlotCount <= 0) {
            Clog::commissionProcess("commission-process-logic-end-slot($slot)-issue-{$issue->issue}-总订单数为0", $issue->lottery_sign, []);
            $totalCount = LotteryCommission::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status', 0)->count();
            if ($totalCount <= 0) {
                $issue->status_commission          = 1;
                $issue->time_end_commission        = time();
                $issue->save();
            }

            cache()->forget($cacheKey);
            return $return;
        }

        $return["total_item"] = $totalSlotCount;

        $failCount = 0;

        Clog::gameStat("commission-stat-start-slot($slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);

        $pageSize   = 2000;
        $totalPage  = ceil($totalSlotCount / $pageSize);

        $i = 0;
        do {
            $offset  = $pageSize * $i;
            $items   = LotteryCommission::where('lottery_sign', $issue->lottery_sign)
                        ->where('issue', $issue->issue)
                        ->where("slot", $slot)
                        ->where('status', 0)
                        ->skip($offset)->take($pageSize)->get();

            $idsArr         = [];
            $topIdArr       = [];
            $projectIdArr   = [];
            $playerDataArr  = [];

            foreach ($items as $item) {
                // 合并相同用户
                if (!isset($playerDataArr[$item->user_id])) {
                    $playerDataArr[$item->user_id] = [];
                }

                $playerDataArr[$item->user_id][] = $item;
            }

            $return["total_user"] += count($playerDataArr);

            $statData = [];
            // 必要时候　合并
            foreach ($playerDataArr as $playerId => $items) {

                // 帐变
                $accountLocker = new AccountLocker($playerId, "commission-{$playerId}");
                if (!$accountLocker->getLock()) {
                    Clog::commissionProcess("commission-process-logic-error-{$issue->issue}-获取账户锁失败({$playerId})-跳过：" . count($items), $issue->lottery_sign, []);
                    $return["total_fail"] += count($items);
                    continue;
                }

                $account = Account::findAccountByUserId($playerId);
                if (!$account) {
                    $accountLocker->release();
                    db()->rollback();
                    Clog::commissionProcess("commission-process-logic-error-{$issue->issue}-Account未找到({$playerId})-跳过：" . count($items), $issue->lottery_sign, []);

                    $return["total_fail"] += count($items);
                    continue;
                }

                // 统计数据初始化
                $statData[$playerId] = [
                    'self'  => 0,
                    'child' => 0,
                ];

                db()->beginTransaction();
                try {
                    $accountChange = new AccountChange();
                    $accountChange->setChangeMode(AccountChange::MODE_CHANGE_AFTER);
                    $accountChange->setReportMode(AccountChange::MODE_REPORT_AFTER);

                    foreach ($items as $item) {
                        $topIdArr[$item->user_id] = $item->top_id;

                        $params = [
                            'user_id'           => $item->user_id,
                            'amount'            => $item->amount,
                            'lottery_sign'      => $item->lottery_sign,
                            'method_sign'       => $item->method_sign,
                            'lottery_name'      => $item->lottery_name,
                            'method_name'       => $item->method_name,
                            'project_id'        => $item->project_id,
                            'issue'             => $item->issue,
                            'from_id'           => $item->from_user_id,
                        ];

                        $type   = $item->from_type == 'self' ? "commission_from_bet" : "commission_from_child";
                        $res    = $accountChange->doChange($account, $type, $params);
                        if ($res !== true) {
                            db()->rollback();
                            $accountLocker->release();
                            Clog::commissionProcess("commission-process-job-error-{$issue->issue}-帐变失败-{$res}({$item->user_id})-跳过:1", $issue->lottery_sign, $params);
                            $return["total_fail"] += 1;
                            db()->rollback();
                            continue;
                        }

                        // 返点统计归类
                        if ($item->from_type == 'self') {
                            $statData[$playerId]['self'] += $item->amount;
                        } else {
                            $statData[$playerId]['child'] += $item->amount;
                        }

                        $idsArr[]       = $item->id;

                        //
                        if (!in_array($item->project_id, $projectIdArr)) {
                            $projectIdArr[] = $item->project_id;
                        }
                    }

                    $accountChange->triggerSave();
                    $accountLocker->release();
                    db()->commit();
                } catch (\Exception $e) {
                    db()->rollback();
                    $accountLocker->release();
                    Clog::commissionProcess("commission-process-job-exception-slot($slot)-issue-{$issue->issue}-{$e->getMessage()}", $issue->lottery_sign, []);
                    cache()->forget($cacheKey);
                    return true;
                }
            }

            // 更新
            LotteryCommission::whereIn('id', $idsArr)->update([
                'status' => 1
            ]);

            // 订单状态更新
            LotteryProject::whereIn('id', $projectIdArr)->update([
                'status_commission' => 1,
                'time_commission'   => time()
            ]);

            // 返点统一更新
            foreach ($statData as $userId => $_statData) {
                // 自己
                if ($_statData['self']) {
                    StatLogic::statCommissionSelf($userId, ["top_id" => $topIdArr[$userId], 'amount' => $_statData['self'], "date" => date("Y-m-d H:i:s")]);
                }
                // 下级
                if ($_statData['child']) {
                    StatLogic::statCommissionChild($userId, ["top_id" => $topIdArr[$userId], 'amount' => $_statData['child'], "date" => date("Y-m-d H:i:s")]);
                }
            }

            $i ++;
        } while($i <= $totalPage);

        // 检测是否完成
        $totalCount = LotteryCommission::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status', 0)->count();

        $logDesc = "";
        if ($totalCount <= 0) {
            $issue->status_commission          = 1;
            $issue->time_end_commission        = time();
            $issue->save();

            $logDesc = "本期完成";
        }

        cache()->forget($cacheKey);
        Clog::commissionProcess("commission-process-job-end-slot($slot)-issue-{$issue->issue}-end-fail({$failCount})-{$logDesc}", $issue->lottery_sign, []);

        Clog::gameStat("commission-stat-end-slot($slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);

        return $return;
    }
}
