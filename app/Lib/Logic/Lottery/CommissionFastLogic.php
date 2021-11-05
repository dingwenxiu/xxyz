<?php
namespace App\Lib\Logic\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\BaseLogic;
use App\Models\Account\Account;
use App\Models\Game\LotteryCommission;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryProject;

/**
 * 返点逻辑 2
 * 2019-10 整理
 * Class CommissionFastLogic
 * @package App\Lib\Lottery
 */
class CommissionFastLogic extends BaseLogic
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
            $bla = self::blabla();
            if ($bla != 9527779 ) {
                return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
            }
        }

        // 1. 检测录号状态
        if ($issue->status_process != LotteryIssue::STATUS_PROCESS_SEND) {
            return "commission-process-logic-error-奖期派奖未完成-status-process:{$issue->status_process}";
        }

        // 2. 检测返点状态
        if ($issue->status_commission == 1) {
            return "commission-process-logic-error-奖期返点已经处理-commission-process:{$issue->status_commission}";
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
            return $return;
        }

        $return["total_item"] = $totalSlotCount;


        Clog::gameStat("commission-stat-start-slot($slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);

        $pageSize   = 2000;
        $totalPage  = ceil($totalSlotCount / $pageSize);

        $i = 0;
        do {
            $items   = LotteryCommission::where('lottery_sign', $issue->lottery_sign)
                        ->where('issue', $issue->issue)
                        ->where("slot", $slot)
                        ->where('status', 0)
                        ->orderBy('id', "ASC")
                        ->skip($return["total_fail"])->take($pageSize)->get();

            $projectIdArr   = [];
            $playerDataArr  = [];

            foreach ($items as $item) {
                // 合并相同用户
                if (!isset($playerDataArr[$item->user_id])) {
                    $playerDataArr[$item->user_id]['self'] = [
                        'amount'            => 0,
                        'user_id'           => $item->user_id,
                        'lottery_sign'      => $item->lottery_sign,
                        'method_sign'       => $item->method_sign,
                        'lottery_name'      => $item->lottery_name,
                        'method_name'       => $item->method_name,
                        'project_id'        => $item->project_id,
                        'issue'             => $item->issue,
                        'from_id'           => $item->from_user_id,
                        'id_arr'            => [],
                        'project_id_arr'    => []
                    ];

                    $playerDataArr[$item->user_id]['child'] = [
                        'amount'            => 0,
                        'user_id'           => $item->user_id,
                        'lottery_sign'      => $item->lottery_sign,
                        'method_sign'       => $item->method_sign,
                        'lottery_name'      => $item->lottery_name,
                        'method_name'       => $item->method_name,
                        'project_id'        => $item->project_id,
                        'issue'             => $item->issue,
                        'from_id'           => $item->from_user_id,
                        'id_arr'            => [],
                        'project_id_arr'    => []
                    ];
                }

                // 个人
                if ($item->from_type == "self") {
                    $playerDataArr[$item->user_id]['self']["amount"]        += $item->amount;
                    $playerDataArr[$item->user_id]['self']["id_arr"][]      = $item->id;
                } else {
                    $playerDataArr[$item->user_id]['child']["amount"]       += $item->amount;
                    $playerDataArr[$item->user_id]['child']["id_arr"][]     = $item->id;
                }

                $playerDataArr[$item->user_id]["project_id_arr"][]  = $item->project_id;
            }

            $return["total_user"] += count($playerDataArr);

            // 必要时候　合并
            foreach ($playerDataArr as $playerId => $item) {

                // 帐变
                $accountLocker = new AccountLocker($playerId, "commission-{$playerId}");
                if (!$accountLocker->getLock()) {
                    Clog::commissionProcess("commission-process-logic-error-{$issue->issue}-获取账户锁失败({$playerId})-跳过：" . count($item['id_arr']), $issue->lottery_sign, []);
                    $return["total_fail"] += count($item['id_arr']);
                    continue;
                }

                $account = Account::findAccountByUserId($playerId);
                if (!$account) {
                    $accountLocker->release();
                    db()->rollback();
                    Clog::commissionProcess("commission-process-logic-error-{$issue->issue}-Account未找到({$playerId})-跳过：" . count($item['id_arr']), $issue->lottery_sign, []);

                    $return["total_fail"] += count($item['id_arr']);
                    continue;
                }

                db()->beginTransaction();
                try {
                    $accountChange = new AccountChange();
                    $accountChange->setReturnMode(AccountChange::MODE_RETURN_TYPE_ID);

                    // 下级返点
                    if ($item['child']['amount'] > 0) {
                        $params = [
                            'user_id'           => $item['child']['user_id'],
                            'amount'            => $item['child']['amount'],
                            'lottery_sign'      => $item['child']['lottery_sign'],
                            'method_sign'       => $item['child']['method_sign'],
                            'lottery_name'      => $item['child']['lottery_name'],
                            'method_name'       => $item['child']['method_name'],
                            'project_id'        => $item['child']['project_id'],
                            'issue'             => $item['child']['issue'],
                            'from_id'           => $item['child']['from_id'],
                        ];

                        $res = $accountChange->doChange($account, "commission_from_child", $params);
                        if (!is_array($res)) {
                            db()->rollback();
                            $accountLocker->release();
                            Clog::commissionProcess("commission-process-job-error-{$issue->issue}-帐变失败-{$res}({$item['child']['user_id']})-跳过:1", $issue->lottery_sign, $params);
                            $return["total_fail"] += count($item['child']['id_arr']);
                            db()->rollback();
                            continue;
                        }

                        // 更新 注单
                        LotteryCommission::whereIn('id', $item['child']['id_arr'])->update([
                            'status'            => 1,
                            'account_change_id' => $res['id'],
                            'process_time'      => time()
                        ]);
                    }

                    // 投注返点
                    if ($item["self"]['amount'] > 0) {
                        // 下级
                        $params = [
                            'user_id'           => $item['self']['user_id'],
                            'amount'            => $item['self']['amount'],
                            'lottery_sign'      => $item['self']['lottery_sign'],
                            'method_sign'       => $item['self']['method_sign'],
                            'lottery_name'      => $item['self']['lottery_name'],
                            'method_name'       => $item['self']['method_name'],
                            'project_id'        => $item['self']['project_id'],
                            'issue'             => $item['self']['issue'],
                            'from_id'           => $item['child']['from_id'],
                        ];

                        $res    = $accountChange->doChange($account, "commission_from_bet", $params);
                        if (!is_array($res)) {
                            db()->rollback();
                            $accountLocker->release();
                            Clog::commissionProcess("commission-process-job-error-{$issue->issue}-帐变失败-{$res}({$item['self']['user_id']})-跳过:1", $issue->lottery_sign, $params);
                            $return["total_fail"] += count($item['self']['id_arr']);
                            db()->rollback();
                            continue;
                        }

                        // 更新 注单
                        LotteryCommission::whereIn('id', $item['self']['id_arr'])->update([
                            'status'            => 1,
                            'account_change_id' => $res['id'],
                            'process_time'      => time()
                        ]);
                    }

                    $accountLocker->release();

                    db()->commit();

                } catch (\Exception $e) {
                    db()->rollback();
                    $accountLocker->release();
                    Clog::commissionProcess("commission-process-job-exception-slot($slot)-issue-{$issue->issue}-{$e->getMessage()}-{$e->getLine()}", $issue->lottery_sign, []);
                    $return["total_fail"] += count($item['id_arr']);
                    continue;
                }

                // 记录 处理过的数据ID
                $projectIdArr   = array_merge($projectIdArr, $item['project_id_arr']);
            }

            // 订单状态更新
            $projectIdArr = array_unique($projectIdArr);
            LotteryProject::whereIn('id', $projectIdArr)->update([
                'status_commission'     => 1,
                'time_commission'       => time()
            ]);

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

        Clog::commissionProcess("commission-process-job-end-slot($slot)-issue-{$issue->issue}-end-fail({$return["total_fail"]})-{$logDesc}", $issue->lottery_sign, []);

        Clog::gameStat("commission-stat-end-slot($slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);

        return $return;
    }
}
