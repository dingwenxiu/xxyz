<?php

namespace App\Http\Controllers\PartnerApi\Lottery;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Lib\Logic\Lottery\TraceLogic;
use App\Lib\Logic\Lottery\ProjectLogic;
use App\Models\Game\LotteryTrace;
use App\Models\Game\LotteryTraceList;
use App\Models\Partner\PartnerLottery;
use Illuminate\Http\JsonResponse;

class ApiLotteryTraceController extends ApiBaseController
{

    // 追号列表
    public function traceList()
    {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $data   = LotteryTrace::getList($c);

        $_data = [];
        $modeArr = config("game.main.modes");
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"           => hashId()->encode($item->id),
                "user_id"      => $item->user_id,
                "lottery_sign" => $item->lottery_sign,
                "lottery_name" => $item->lottery_name,
                "username"     => $item->username,
                "method_name"  => $item->method_name,
                "price"        => $item->price,
                "bet_number"   => $item->bet_number,
                "count"        => $item->count,
                "ip"           => $item->ip,
                "is_tester"    => $item->is_tester,
                "mode"         => $modeArr[$item->mode]['title'],
                "win_stop"     => $item->win_stop,

                "trace_total_cost" => number4($item->trace_total_cost),
                "bet_prize_group"  => $item->bet_prize_group,
                "user_prize_group" => $item->user_prize_group,
                "total_issues"     => $item->total_issues,

                "finished_issues" => $item->finished_issues,
                "canceled_issues" => $item->canceled_issues,
                "finished_amount" => number4($item->finished_amount),
                "canceled_amount" => number4($item->canceled_amount),

                "total_bonus" => number4($item->total_bonus),
                "start_issue" => $item->start_issue,
                "end_issue"   => $item->end_issue,
                "now_issue"   => $item->now_issue,
                "time_bought" => date("Y-m-d H:i:s", $item->time_bought),
                "status"      => $item->status,
            ];
        }

        $data['data'] = $_data;

        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);
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

        $ret        = hashId()->decode($traceId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的追号订单Id!', 0);
        }

        $traceMain = LotteryTrace::find($ret[0]);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        // 追号权限
        if ($traceMain->partner_sign != $this->partnerSign) {
            return Help::returnApiJson("对不起, 您没有权限!", 0);
        }

        $c = request()->all();
        $c['trace_id'] = $traceMain->id;
        
        $traceDetailData = LotteryTraceList::getList($c);

        return Help::returnApiJson("恭喜, 获取详情成功!", 1, $traceDetailData);

    }

    /**
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
        $traceMain = LotteryTrace::find($ret[0]);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        // 是否属于自己
        if ($traceMain->partner_sign != $this->partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有权限!", 0);
        }

        // 追好状态
        if ($traceMain->status != LotteryTrace::STATUS_INIT) {
            return Help::returnApiJson("对不起, 追号状态不正确!", 0);
        }

        $res = TraceLogic::traceMainCancel($traceMain, LotteryTrace::STATUS_FINISHED_ADMIN_CANCEL, LotteryTraceList::STATUS_TRACE_SYSTEM_CANCEL);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        } else {
            return Help::returnApiJson("恭喜, 撤销追号成功!", 1);
        }
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function cancelTraceDetail($traceDetailId) {

        $user   = $this->partnerAdminUser;

        $ret        = hashId()->decode($traceDetailId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        // 追号订单详情
        $traceDetail    = LotteryTraceList::find($ret[0]);
        if (!$traceDetail) {
            return Help::returnApiJson("对不起, 包含无效的追号订单ID!", 0);
        }

        // 是否属于自己
        if ($traceDetail->partner_sign != $user->partner_sign) {
            return Help::returnApiJson("对不起, 您没有权限!", 0);
        }

        // 订单状态
        if ($traceDetail->status != LotteryTraceList::STATUS_TRACE_INIT) {
            return Help::returnApiJson("对不起, 追号订单状态不正确!", 0);
        }

        // 追号main
        $traceMain = LotteryTrace::find($traceDetail->trace_id);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        // 追好状态
        if ($traceMain->status != LotteryTrace::STATUS_INIT) {
            return Help::returnApiJson("对不起, 追号状态不正确(0x002)!", 0);
        }

        $res = TraceLogic::traceDetailCancel([$traceDetail], $traceMain, LotteryTrace::STATUS_FINISHED_ADMIN_CANCEL, LotteryTraceList::STATUS_TRACE_SYSTEM_CANCEL);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        } else {
            return Help::returnApiJson("恭喜, 撤销追号成功!", 1);
        }
    }
}
