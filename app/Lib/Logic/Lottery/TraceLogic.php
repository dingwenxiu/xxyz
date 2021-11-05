<?php
namespace App\Lib\Logic\Lottery;

use App\Lib\BaseCache;
use App\Lib\Clog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Models\Account\Account;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryTrace;
use App\Models\Game\LotteryTraceList;

/**
 * 追号逻辑　
 * 2019-10 整理
 * Class TraceLogic
 * @package App\Lib\Lottery\TraceLogic
 */
class TraceLogic
{
    use BaseCache;

    // 必须是数组
    static function traceMainCancel($traceMain, $status = LotteryTrace::STATUS_FINISHED_PLAYER_CANCEL, $detailStatus = LotteryTraceList::STATUS_TRACE_CANCEL) {

        // 所有未追的
        $items  = LotteryTraceList::where('trace_id', $traceMain->id)->where('status', LotteryTraceList::STATUS_TRACE_INIT)->get();

        $totalCancelCost = 0;
        if ($status == LotteryTrace::STATUS_FINISHED_PLAYER_CANCEL) {
            $issueArr = [];
            foreach ($items as $item) {
                $totalCancelCost += $item->total_cost;
                $issueArr[] = $item->issue;
            }

            // 获取所有issue
            $issues = LotteryIssue::where("lottery_sign", $traceMain->lottery_sign)->whereIn('issue', $issueArr)->get();
            foreach ($issues as $issue) {
                if ($issue->stats_trace == 1 || $issue->end_time <= time()) {
                    return "对不起, 存在已经追号的奖期($issue->issue)";
                }
            }
        } else {
            foreach ($items as $item) {
                $totalCancelCost += $item->total_cost;
            }
        }

        // 开始帐变逻辑
        $accountLocker = new AccountLocker($traceMain->user_id, "cancel_trace");
        if (!$accountLocker->getLock()) {
            return "对不起, 获取账户锁失败!";
        }

        $account = Account::findAccountByUserId($traceMain->user_id);
        if (!$account) {
            $accountLocker->release();
            return "对不起, 不存在的用户ID($traceMain->user_id)";
        }

        db()->beginTransaction();
        try {
            // 帐变
            $accountChange = new AccountChange();
            $params = [
                'user_id'       => $traceMain->user_id,
                'amount'        => $totalCancelCost,
                'lottery_sign'  => $traceMain->lottery_sign,
                'lottery_name'  => $traceMain->lottery_name,
                'method_sign'   => $traceMain->method_sign,
                'method_name'   => $traceMain->method_name,
                'project_id'    => $traceMain->id
            ];

            $res = $accountChange->doChange($account, "cancel_trace_order", $params);
            if ($res !== true) {
                db()->rollback();
                $accountLocker->release();
                return "对不起, 撤追号单帐变失败";
            }

            // 状态修改
            $traceMain->status          = $status;
            $traceMain->stop_issue      = $traceMain->now_issue;
            $traceMain->time_stop       = time();
            $traceMain->canceled_issues = count($items);
            $traceMain->canceled_amount = $totalCancelCost;
            $traceMain->save();

            $accountLocker->release();

            LotteryTraceList::where('trace_id', $traceMain->id)->where('status', LotteryTraceList::STATUS_TRACE_INIT)->update(
                [
                    'status'        => $detailStatus,
                    'time_cancel'   => time(),
                ]
            );

            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            $accountLocker->release();
            Clog::traceProcess("投注-异常:" . $e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine(),  ['a' => $e->getTraceAsString()]);
            return "对不起, " . $e->getMessage();
        }

        return true;
    }

    /**
     * 撤销　指定　追好详情
     * @param $items
     * @param $traceMain
     * @param $status
     * @param $detailStatus
     * @return bool|string
     * @throws \Exception
     */
    static function traceDetailCancel($items, $traceMain, $status = LotteryTrace::STATUS_FINISHED_PLAYER_CANCEL, $detailStatus = LotteryTraceList::STATUS_TRACE_CANCEL) {

        $leftItemCount  = LotteryTraceList::where('trace_id', $traceMain->id)->where('status', LotteryTraceList::STATUS_TRACE_INIT)->count();

        $totalCancelCost = 0;

        $itemIds = [];
        if ($status == LotteryTrace::STATUS_FINISHED_PLAYER_CANCEL) {
            $issueArr = [];
            foreach ($items as $item) {
                $totalCancelCost += $item->total_cost;
                $issueArr[] = $item->issue;
                $itemIds[]  = $item->id;
            }

            // 获取所有issue
            $issues = LotteryIssue::where("lottery_sign", $traceMain->lottery_sign)->whereIn('issue', $issueArr)->get();
            foreach ($issues as $issue) {
                if ($issue->status_trace == 1 || $issue->end_time <= time()) {
                    return "对不起, 存在已经追号的奖期($issue->issue)";
                }
            }
        } else {
            foreach ($items as $item) {
                $totalCancelCost += $item->total_cost;
                $itemIds[]  = $item->id;
            }
        }

        $isFinished = false;
        if ($leftItemCount == count($items)) {
            $isFinished = true;
        }

        // 开始帐变逻辑
        $accountLocker = new AccountLocker($traceMain->user_id, "cancel_trace");
        if (!$accountLocker->getLock()) {
            return "对不起, 获取账户锁失败!";
        }

        $account = Account::findAccountByUserId($traceMain->user_id);
        if (!$account) {
            $accountLocker->release();
            return "对不起, 不存在的用户ID($traceMain->user_id)";
        }

        db()->beginTransaction();
        try {
            // 帐变
            $accountChange = new AccountChange();
            $params = [
                'user_id'       => $traceMain->user_id,
                'amount'        => $totalCancelCost,
                'lottery_sign'  => $traceMain->lottery_sign,
                'lottery_name'  => $traceMain->lottery_name,
                'method_sign'   => $traceMain->method_sign,
                'method_name'   => $traceMain->method_name,
                'project_id'    => $traceMain->id
            ];

            $res = $accountChange->doChange($account, "cancel_trace_order", $params);
            if ($res !== true) {
                db()->rollback();
                $accountLocker->release();
                return "对不起, 撤追号单帐变失败";
            }

            $traceMain->canceled_issues += count($items);
            $traceMain->canceled_amount += $totalCancelCost;

            // 如果已经完成
            if ($isFinished) {
                $traceMain->status          = $status;
                $traceMain->stop_issue      = $traceMain->now_issue;
                $traceMain->time_stop       = time();
            }
            $traceMain->save();

            $accountLocker->release();

            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            $accountLocker->release();
            Clog::traceProcess("投注-异常:" . $e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine(),  ['a' => $e->getTraceAsString()]);
            return "对不起, " . $e->getMessage();
        }

        // 变更状态
        LotteryTraceList::whereIn('id', $itemIds)->update(
            [
                'status'        => $detailStatus,
                'time_cancel'   => time(),
            ]
        );

        return true;
    }

    /**
     * 执行追号
     * @param $traceDetail
     * @return bool
     * @throws \Exception
     */
    static function trace($traceDetail) {
        $traceMain = LotteryTrace::find($traceDetail->trace_id);
        if (!$traceMain) {
            return "不存在的trace-main-{$traceDetail->trace_id}";
        }

        // 所有下面未追的订单
        $items  = LotteryTraceList::where('trace_id', $traceDetail->trace_id)->where('status', LotteryTraceList::STATUS_TRACE_INIT)->get();

        db()->beginTransaction();
        try {


            /**
             * 追号中奖停止检测
             * 1. 必须是第二期开始
             * 2. 必须正在处理状态
             */
            if ($traceDetail->sort_type != 1 && $traceMain->win_stop == 1 && $traceMain->status == LotteryTrace::STATUS_INIT) {
                // 获取上一期
                $lastTraceDetail = LotteryTraceList::where("trace_id", $traceMain->id)->where('is_win', 1)->first();
                // 如果上一期中奖
                if ($lastTraceDetail && $lastTraceDetail->id > 0) {

                    $res    = TraceLogic::traceDetailCancel($items, $traceMain, LotteryTrace::STATUS_WIN_STOP, LotteryTraceList::STATUS_TRACE_WIN_STOP);

                    if ($res === true) {
                        Clog::traceProcess("trace-process-logic-(win-stop-process)-issue:{$traceDetail->issue}-{$traceDetail->id}-success-追停成功!", $traceDetail->lottery_sign);
                    } else {
                        Clog::traceProcess("trace-process-logic-(win-stop-process)-issue:{$traceDetail->issue}-{$traceDetail->id}-error-{$res}!", $traceDetail->lottery_sign);
                        return $res;
                    }

                    db()->commit();
                    return true;
                }
            }

            ProjectLogic::traceAddProjects($traceDetail);
            $traceDetail->status    = LotteryTraceList::STATUS_TRACE_FINISHED;
            $traceDetail->time_bet  = time();
            $traceDetail->save();

            $traceMain->finished_amount += $traceDetail->total_cost;
            $traceMain->finished_issues += 1;
            $traceMain->now_issue       = $traceDetail->issue;

            if ($traceDetail->sort_type == 2 || count($items) <= 1) {
                $traceMain->status          = LotteryTrace::STATUS_FINISHED;
                $traceMain->time_stop       = time();
            }

            $traceMain->save();
            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            $msg = $e->getMessage() . "|" . $e->getLine() . "|" . $e->getFile();
            Clog::traceProcess("trace-process-logic-issue:{$traceDetail->issue}-exception-{$msg}-" . time(), $traceDetail->lottery_sign);
            return $msg;
        }

        return true;
    }
}
