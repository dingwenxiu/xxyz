<?php

namespace App\Http\Controllers\AdminApi\Lottery;

use App\Lib\Help;
use App\Models\Game\Lottery;
use App\Models\Partner\Partner;
use App\Models\Game\LotteryTrace;
use Illuminate\Http\JsonResponse;
use App\Models\Game\LotteryTraceList;
use App\Http\Controllers\AdminApi\ApiBaseController;

/**
 * version 1.0
 * 追号
 * Class ApiLotteryTraceController
 * @package App\Http\Controllers\AdminApi\Lottery
 */
class ApiLotteryTraceController extends ApiBaseController
{
    // 奖期规则列表
    public function traceList()
    {
        $c      = request()->all();
        $data   = LotteryTrace::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                        => $item->id,
                "lottery_sign"              => $item->lottery_sign,
                "lottery_name"              => $item->lottery_name,
                "username"                  => $item->username,
                "method_name"               => $item->method_name,
                "price"                     => $item->price,
                "bet_number"                => $item->bet_number,
                "count"                     => $item->count,
                "mode"                      => $item->mode,
                "win_stop"                  => $item->win_stop,

                "trace_total_cost"          => number4($item->trace_total_cost),
                "commission"                => $item->commission,
                "bet_prize_group"           => $item->bet_prize_group,
                "user_prize_group"          => $item->user_prize_group,
                "total_issues"              => $item->total_issues,

                "finished_issues"           => $item->finished_issues,
                "canceled_issues"           => $item->canceled_issues,
                "finished_amount"           => number4($item->finished_amount),
                "canceled_amount"           => number4($item->canceled_amount),

                "total_bonus"               => number4($item->total_bonus),
                "start_issue"               => $item->start_issue,
                "end_issue"                 => $item->end_issue,
                "now_issue"                 => $item->now_issue,
                "time_bought"               => date("Y-m-d H:i:s", $item->time_bought),
                "status"                    => $item->status,
            ];
        }

        $data['data'] = $_data;

        $data['lottery_options']    = Lottery::getOptions();
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 追号详情
     * @param $traceId
     * @return JsonResponse
     */
    public function traceDetail($traceId)
    {
        if (!$traceId) {
            return Help::returnApiJson('对不起, 无效的追号订单Id!', 0);
        }

        $ret             = hashId()->decode($traceId);

        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的追号订单Id!', 0);
        }

        $traceMain       = LotteryTrace::find($ret[0]);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        $c               = request()->all();
        $c['trace_id']   = $traceMain->id;
        $traceDetailData = LotteryTraceList::getList($c);
        return Help::returnApiJson("恭喜, 获取详情成功!", 1, $traceDetailData);

    }

    /**
     * 撤销追号
     * @param $traceId
     * @return JsonResponse
     * @throws \Exception
     */
    public function cancelTrace($traceId) {

        // 订单ID 数组
        if (!$traceId ) {
            return Help::returnApiJson('对不起, 无效的追号订单Id!', 0);
        }

        $ret        = hashId()->decode($traceId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        // 追号main
        $traceMain  = LotteryTrace::find($ret[0]);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        // 追好状态
        if ($traceMain->status != LotteryTrace::STATUS_INIT) {
            return Help::returnApiJson("对不起, 追号状态不正确!", 0);
        }

        $res        = TraceLogic::traceMainCancel($traceMain, LotteryTrace::STATUS_FINISHED_ADMIN_CANCEL, LotteryTraceList::STATUS_TRACE_SYSTEM_CANCEL);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        } else {
            return Help::returnApiJson("恭喜, 撤销追号成功!", 1);
        }
    }
}
