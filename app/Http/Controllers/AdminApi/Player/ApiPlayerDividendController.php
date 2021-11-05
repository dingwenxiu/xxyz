<?php namespace App\Http\Controllers\AdminApi\Player;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Report\ReportUserDividend;


class ApiPlayerDividendController extends ApiBaseController
{
    // 日工资报表
    public function dividendList()
    {
        $c      = request()->all();
        $data   = ReportUserDividend::getList($c);

        $data["partner_option"] = Partner::getOptions();
        foreach ($data['data'] as $item) {
            $item->partner_name                     = $data["partner_option"][$item->partner_sign];

            $item->total_bets                       = number4($item->total_bets);
            $item->total_cancel                     = number4($item->total_cancel);
            $item->total_bonus                      = number4($item->total_bonus);

            $item->total_he_return                  = number4($item->total_he_return);
            $item->total_commission_from_bet        = number4($item->total_commission_from_bet);
            $item->total_commission_from_child      = number4($item->total_commission_from_child);
            $item->total_gift                       = number4($item->total_gift);
            $item->total_salary                     = number4($item->total_salary);
            $item->total_dividend                   = number4($item->total_dividend);

            $item->profit                           = number4($item->profit);
            $item->amount                           = number4($item->amount);
            $item->real_amount                      = number4($item->real_amount);

            $item->send_time                        = $item->send_time ? date("Y-m-d H:i:s", $item->send_time) : '';
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    public function reportSalarySend() {


        return Help::returnApiJson('获取数据成功!', 1, []);
    }
}
