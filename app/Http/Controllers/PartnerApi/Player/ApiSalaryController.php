<?php namespace App\Http\Controllers\PartnerApi\Player;

use App\Lib\Help;
use Illuminate\Support\Facades\Hash;
use App\Lib\Logic\Player\SalaryLogic;
use App\Models\Report\ReportUserSalary;
use App\Http\Controllers\PartnerApi\ApiBaseController;


class ApiSalaryController extends ApiBaseController
{
    // 日工资报表
    public function reportSalaryList()
    {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;

        $data   = ReportUserSalary::getList($c);

        foreach ($data['data'] as $item) {
            $item->self_bets            = number4($item->self_bets);
            $item->self_cancel          = number4($item->self_cancel);
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

        $partnerAdminUser = auth() -> guard('partner_api') -> user();
        if (!$partnerAdminUser) {
            return Help ::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 资金密码
        $fundPassword = trim(request('fund_password', ''));
        if (!Hash ::check($fundPassword, $partnerAdminUser -> fund_password)) {
            return Help ::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        // 校验ID
        $ids    = request('ids', []);
        $items  = ReportUserSalary::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            if (!$item) {
                return Help::returnApiJson('对不起, 无效的数据!', 0);
            }

            if ($item->partner_sign != $partnerAdminUser->partner_sign) {
                return Help::returnApiJson("对不起, 存在一些用户您没有权限!", 0);
            }

            if ($item->status != ReportUserSalary::STATUS_COUNT) {
                return Help::returnApiJson("对不起, 用户{$item->username}已经发放!", 0);
            }
        }

        // 发放
        $res = SalaryLogic::sendSalary($items);

        if (!$res['status'] && $res['total_player'] != $res['fail_count']) {
            return Help::returnApiJson('对不起, 部分完成!', 0, $res);
        } else if (!$res['status'] ) {
            return Help::returnApiJson('对不起, 发放分行失败!', 0, $res);
        } else {
            return Help::returnApiJson('恭喜! 发放分红成功', 1, $res);
        }
    }
}
