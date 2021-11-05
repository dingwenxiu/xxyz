<?php
namespace App\Http\Controllers\AdminApi\Report;

use App\Lib\Help;
use App\Models\Game\Lottery;
use App\Models\Partner\Partner;
use App\Lib\Logic\Stat\StatLogic;
use App\Models\Report\ReportStatUser;
use App\Models\Report\ReportStatUserDay;
use App\Models\Report\ReportUserDividend;
use App\Models\Report\ReportStatPartnerDay;
use App\Models\Report\ReportStatLotteryDay;
use App\Http\Controllers\AdminApi\ApiBaseController;

class ApiReportController extends ApiBaseController
{
    // 获取 商户日结算 数据列表
    public function statPartnerDayList()
    {
        $c      = request()->all();
        $data   = ReportStatPartnerDay::getList($c);

        foreach ($data['data'] as $item) {
            $item->commission       = number4($item->commission_from_bet + $item->commission_from_child);
            $item->profit           = number4(
                // 充值 赚钱
                $item->recharge_amount+
                // 系统理赔 加钱 待明确
                $item->system_transfer_add+
                // 系统扣减 减钱 待明确
                $item->system_transfer_reduce+
                // 投注 赚钱
                $item->bets-
                // 分红 亏钱
                $item->dividend-
                // 返点 亏钱
                $item->commission-
                // 奖金 亏钱
                $item->gift-
                // 提现 亏钱
                $item->withdraw_amount-
                // 奖金 亏钱
                $item->bouns -
                // 撤单 亏钱
                $item->cancel -
                // 日工资
                $item->salary
            );
            foreach (StatLogic::$filters as $field) {
                $item->{$field}     = number4($item->{$field});
            }
        }

        $partnerOption = Partner::getOptions();
        $data['partner_option']     = $partnerOption;
        $data['default_partner']    = Partner::getDefaultPartnerSign();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取 用户日统计 数据列表
    public function statUserDayList()
    {
        $c      = request()->all();
        $data   = ReportStatUserDay::getList($c);
        $partnerOption = Partner::getOptions();
        $data['partner_option']     = $partnerOption;
        $data['default_partner']    = Partner::getDefaultPartnerSign();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    public function statUserDayCheck($id)
    {
        $id                = 3000;
        $id                = intval($id);
        $reportStatUserDay = ReportStatUserDay::find($id);
        $data              = $this->checkBill($reportStatUserDay);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /*
    * @model 1.ReportStatPartnerDay 2.ReportStatUserDay 3. ReportStatLotteryDay
    * 核对订单
    */
    private function checkBill($model)
    {
        if($model instanceof ReportStatPartnerDay)
        {

        }
        else if($model instanceof ReportStatUserDay)
        {

        }
        else if($model instanceof ReportStatLotteryDay)
        {

        }

    }

    // 获取 用户总统计 数据列表
    public function statUserList()
    {
        $c      = request()->all();
        $data   = ReportStatUser::getList($c);

        foreach ($data['data'] as $item) {
            $item->commission       = number4($item->commission_from_bet + $item->commission_from_child);
            $item->team_commission  = number4($item->team_commission_from_bet + $item->team_commission_from_child);
            foreach (StatLogic::$filters as $field) {
                $item->{$field}     = number4($item->{$field});
            }

            foreach (StatLogic::$team_filters as $field) {
                $field = "team_" . $field;
                $item->{$field} = number4($item->{$field});
            }
        }


        $partnerOption = Partner::getOptions();
        $data['partner_option']     = $partnerOption;
        $data['default_partner']    = Partner::getDefaultPartnerSign();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取 日工资数据 列表
    public function salaryList()
    {
        $c      = request()->all();
        $data   = ReportStatUser::getList($c);

        $partnerOption = Partner::getOptions();
        $data['partner_option']     = $partnerOption;
        $data['default_partner']    = Partner::getDefaultPartnerSign();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取 彩种 列表
    public function lotteryDayList()
    {
        $c      = request()->all();
        $data   = ReportStatLotteryDay::getList($c);

        $partnerOption = Partner::getOptions();
        $data['partner_option']     = $partnerOption;
        $data['default_partner']    = Partner::getDefaultPartnerSign();
        $data['lottery_option']     = Lottery::getSelectOptions(false);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    //代理分红列表
    public function dividendList() {
        $c                    = request()->all();
        $data                 = ReportUserDividend::getList($c);
        $_data                = [];
        foreach ($data['data'] as $item) {
            $_data[] = [
                'id'                          => $item->id,
                'parent_id'                   => $item->parent_id,
                'partner_sign'                => $item->partner_sign,
                'partner_name'                => isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'',
                'user_id'                     => $item->user_id,
                'user_level'                  => $item->user_level,
                'username'                    => $item->username,
                'from_user_id'                => $item->from_user_id,
                'from_username'               => $item->from_username,
                'month'                       => $item->month,
                'sort'                        => $item->sort,
                'send_day'                    => $item->send_day,
                'from_day'                    => $item->from_day,
                'end_day'                     => $item->end_day,
                'total_bets'                  => number4($item->total_bets),
                'total_bonus'                 => number4($item->total_bonus),
                'total_cancel'                => number4($item->total_cancel),
                'total_he_return'             => number4($item->total_he_return),
                'total_commission_from_bet'   => number4($item->total_commission_from_bet),
                'total_commission_from_child' => number4($item->total_commission_from_child),
                'total_gift'                  => number4($item->total_gift),
                'total_dividend'              => number4($item->total_dividend),
                'total_salary'                => number4($item->total_salary),
                'profit'                      => number4($item->profit),
                'amount'                      => number4($item->amount),
                'real_amount'                 => number4($item->real_amount),
                'rate'                        => $item->rate,
                'send_time'                   => date('Y-m-d H:i:s', $item->send_time),
                'status'                      => $item->status,
                'updated_at'                  => date('Y-m-d H:i:s', strtotime($item->updated_at)),
                'created_at'                  => date('Y-m-d H:i:s', strtotime($item->created_at)),
            ];
        }
        $data['data']                       = $_data;
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }
//商户报表
    public function statPartnerList(){
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }
        $c = request()->all();
        $data = ReportStatPartnerDay::getList($c);
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,$data);
    }
}
