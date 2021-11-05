<?php namespace App\Http\Controllers\AdminApi\Player;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Report\ReportUserSalary;


class ApiSalaryController extends ApiBaseController
{
    // 日工资报表
    public function salaryReportList()
    {
        $c      = request()->all();
        $data   = ReportUserSalary::getList($c);

        $data["partner_option"] = Partner::getOptions();

        foreach ($data['data'] as $item) {
            $item->partner_name         =  $data["partner_option"][$item->partner_sign];

            $item->self_bets            = number4($item->self_bets);
            $item->self_cancle          = number4($item->self_cancle);
            $item->self_real_bets       = number4($item->self_real_bet);

            $item->team_bets            = number4($item->team_bets);
            $item->team_cancle          = number4($item->team_cancle);
            $item->team_real_bets       = number4($item->team_real_bet);

            $item->total_salary         = number4($item->total_salary);
            $item->child_salary         = number4($item->child_salary);
            $item->self_salary          = number4($item->self_salary);

            $item->real_salary          = number4($item->real_salary);

            $item->total_bets           = bcadd($item->team_real_bets, $item->self_real_bets, 4);

            $item->send_time            = $item->send_time ? date("Y-m-d H:i:s", $item->send_time) : '';
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    public function reportSalarySend() {

        return Help::returnApiJson('获取数据成功!', 1, []);
    }
}
