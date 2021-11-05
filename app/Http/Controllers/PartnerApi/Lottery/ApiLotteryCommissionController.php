<?php

namespace App\Http\Controllers\PartnerApi\Lottery;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Game\LotteryCommission;
use App\Models\Partner\PartnerLottery;


class ApiLotteryCommissionController extends ApiBaseController
{

    // 返点列表
    public function commissionList()
    {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;

        $data   = LotteryCommission::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
//                "hash_id"                   => hashId()->encode($item->id),
                "project_id"                => hashId()->encode($item->project_id),
                "lottery_sign"              => $item->lottery_sign,
                "lottery_name"              => $item->lottery_name,
                "method_name"               => $item->method_name,

                "username"                  => $item->username,
                "from_username"             => $item->from_username,

                "from_type"                 => $item->from_type,
                "self_prize_group"          => $item->self_prize_group,
                "child_prize_group"         => $item->child_prize_group,
                "bet_prize_group"           => $item->bet_prize_group,

                "issue"                     => $item->issue,
                "amount"                    => number4($item->amount),

                "process_time"              => $item->process_time ? date("Y-m-d H:i:s", $item->process_time) : "--",
                "account_change_id"         => hashId()->encode($item->account_change_id),
                "status"                    => $item->status,
            ];

            unset($item->id);
        }

        $data['data'] = $_data;

        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);
        
        return Help::returnApiJson('获取数据成功!', 1, $data);

    }
}
