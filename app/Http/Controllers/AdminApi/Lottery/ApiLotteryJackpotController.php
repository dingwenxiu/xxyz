<?php

namespace App\Http\Controllers\AdminApi\Lottery;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Lib\Logic\Lottery\JackpotLogic;
use App\Models\Game\LotteryIssueBet;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerLottery;
use Illuminate\Http\JsonResponse;

/**
 * version 1.0
 * 控水
 * Class ApiLotteryJackpotController
 * @package App\Http\Controllers\AdminApi\Lottery
 */
class ApiLotteryJackpotController extends ApiBaseController
{

    public function jackpotIssueList() {
        $c      = request()->all();
        $data   = LotteryIssueBet::getList($c);

        $_data = [];
        foreach ($data['data'] as $item) {
            $partner = Partner::where('sign', $item->partner_sign)->first();
            $_data[] = [
                'id'                    => $item->id,
                'partner_name'          => $partner->name,
                'partner_sign'          => $item->partner_sign,
                'lottery_name'          => $item->lottery_name,
                'lottery_sign'          => $item->lottery_sign,
                'issue'                 => $item->issue,
                'total_bet'             => $item->total_bet,
                'total_cancel'          => $item->total_cancel,
                'total_commission'      => $item->total_commission,
                'total_bonus'           => $item->total_bonus,
                'total_challenge_bonus' => $item->total_challenge_bonus,
                'total_real_bonus'      => $item->total_real_bonus,
                'day'					=> $item->day,
                'rate'                  => $item->rate,
                'bet_rate'              => $item->bet_rate,
                'loss'                  => $item->loss,
                'status'                => $item->status,
            ];
        }
        $data['data'] = $_data;

        $data['partner_options']    = Partner::getOptions();

        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $partnerSign = $c['partner_sign'];
        } else {
            $partner = Partner::where('status', 1)->first();
            $partnerSign = $partner->sign;
        }

        $data['lottery_options']    = PartnerLottery::getSelectSelfOpenOptions($partnerSign);
        $data['series_options']     = config('game.main.series');
        // tab 选项
        $data['series_tab_options']     = PartnerLottery::getSeriesSelfOpenLotteryOptions($partnerSign);

        // 几个指标
        $data['jackpot_is_open']    = configure("lottery_jackpot_open", 0); // 是否开启
//        $data['jackpot_min_bet']    = partnerConfigure($this->adminUser->partner_sign, "lottery_jackpot_min_bet", 1000); // 开启最低投注量


        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 每期的数据
     * @return JsonResponse
     * @throws \Exception
     */
    public function jackpotIssueDetail() {
        $c      = request()->all();

        $partnerSign    = $c['partner_sign'];
        $lotterySign    = $c['lottery_sign'];
        $issue          = $c['issue'];

        $data = JackpotLogic::getIssuePlanDetail($partnerSign, $lotterySign, $issue);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 奖期详情
     */
    public function issueDetail () {
        $c = request()->all();

        if (!isset($c['partner_sign'],$c['lottery_sign'],$c['day'] )){
            return Help::returnApiJson('对不起,请填写完整条件',0);
        }

        $data = LotteryIssueBet::issueDetails($c);

        return Help::returnApiJson('获取数据成功', 1, $data);
    }
}
