<?php
namespace App\Http\Controllers\PartnerApi\Report;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Account\AccountChangeReport;
use App\Models\Finance\Recharge;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryCommission;
use App\Models\Game\LotteryProject;
use App\Models\Report\ReportStatLotteryDay;
use App\Models\Report\ReportStatPartnerDay;
use App\Models\Report\ReportStatUser;
use App\Models\Report\ReportStatUserDay;
use App\Models\Report\ReportUserSalary;
use App\Models\Report\ReportUserDividend;

class ApiReportController extends ApiBaseController
{

    // 获取 用户日统计 数据列表
    public function statUserDayList()
    {
        $c      = request()->all();
        $c['partner_sign']  = $this->partnerSign;
        $data   = ReportStatUserDay::getList($c);


        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 用户每日数据审核
    public function statUserDayCheck($id)
    {
        $stat = ReportStatUserDay::find($id);

        $field = [
            "recharge"              => "充值",
            "withdraw"              => "提现",
            "he_return"             => "和局退款",
            "bet_cost"              => "投注",
            "cancel_order"          => "撤单",
            "game_bonus"            => "奖金",
            "commission_from_bet"   => "投注返点",
            "commission_from_child" => "下级返点",
            "day_salary"            => "日工资",
            "dividend_from_parent"  => "分红",
            "active_amount"         => "礼金",
            "score"                 => "积分",
        ];

        // 统计数据
        $statData  = [
            "recharge"                  => number4($stat->recharge_amount),
            "withdraw"                  => number4($stat->withdraw_amount),
            "bet_cost"                  => number4($stat->bets),
            "he_return"                 => number4($stat->he_return),
            "cancel_order"              => number4($stat->cancel),
            "game_bonus"                => number4($stat->bonus),
            "commission_from_bet"       => number4($stat->commission_from_bet),
            "commission_from_child"     => number4($stat->commission_from_child),
            "day_salary"                => number4($stat->salary),
            "dividend_from_parent"      => number4($stat->dividend),
            "active_amount"             => number4($stat->gift),
            "score"                     => number4($stat->score),
        ];

        $changeData  = [
            "recharge"              => 0,
            "withdraw"              => 0,
            "he_return"             => 0,
            "bonus_challenge_reduce"    => 0,
            "bonus_limit_reduce"        => 0,
            "bet_cost"              => 0,
            "cancel_order"          => 0,
            "game_bonus"            => 0,
            "commission_from_bet"   => 0,
            "commission_from_child" => 0,
            "day_salary"            => 0,
            "dividend_from_parent"  => 0,
            "active_amount"         => 0,
            "score"                 => 0,
        ];

        $day    = date("Y-m-d", strtotime($stat->day) + 200);

        // 帐变数据
        $changeRes   = AccountChangeReport::getProjectSumBySign($stat, $day);
        foreach ($changeRes as $_data) {
            if (isset($changeData[$_data->type_sign])) {
                if ($_data->type_sign == 'bet_cost' || $_data->type_sign == 'trace_cost') {
                    $changeData['bet_cost'] += number4($_data->amount);
                } else if ($_data->type_sign == 'cancel_order' || $_data->type_sign == 'cancel_trace_order') {
                    $changeData['cancel_order'] += number4($_data->amount);
                } else {
                    $changeData[$_data->type_sign] = number4($_data->amount);
                }
            }
        }

        $changeData['game_bonus'] = $changeData['game_bonus'] - $changeData['bonus_challenge_reduce'] - $changeData['bonus_limit_reduce'];

        // 销量数据
        $saleData  = [
            "recharge"              => 0,
            "withdraw"              => 0,
            "he_return"             => 0,
            "bet_cost"              => 0,
            "cancel_order"          => 0,
            "game_bonus"            => 0,
            "commission_from_bet"   => 0,
            "commission_from_child" => 0,
            "day_salary"            => 0,
            "dividend_from_parent"  => 0,
            "active_amount"         => 0,
            "score"                 => 0,
        ];

        $projectStat    = LotteryProject::getPlayerDaySum($stat->user_id, date("Ymd", strtotime($day)));
        $saleData["bet_cost"]           = number4($projectStat['bets']);
        $saleData["cancel_order"]       = number4($projectStat['cancel']);
        $saleData["game_bonus"]         = number4($projectStat['bonus']);
        $saleData["he_return"]          = number4($projectStat['he_return']);

        // 返点数据
        $commissionStat    = LotteryCommission::getPlayerDaySum($stat->user_id, date("Ymd", strtotime($day)));
        $saleData["commission_from_bet"]    = number4($commissionStat['commission_from_bet']);
        $saleData["commission_from_child"]  = number4($commissionStat['commission_from_child']);

        // 充值数据
        $saleData['recharge']   = Recharge::getPlayerTotalRechargeByDay($stat->user_id, $day);
        $saleData['day_salary'] = ReportUserSalary::getPlayerTotalSalaryByDay($stat->user_id, $day);

        $data = [];
        foreach ($field as $key => $title) {
            $data[] = [
                'title'             => $title,
                'stat_amount'       => $statData[$key],
                'change_amount'     => $changeData[$key],
                'sale_amount'       => $saleData[$key],
                'day'               => $day
            ];
        }

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取 用户总统计 数据列表
    public function statUserList()
    {
        $c      = request()->all();
        $c['partner_sign']  = $this->partnerSign;

        $data   = ReportStatUser::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取 日工资数据 列表
    public function salaryList()
    {
        $c      = request()->all();
        $c['partner_sign']  = $this->partnerSign;
        $data   = ReportUserSalary::getList($c);
        $_data = [];
        foreach ($data['data'] as $item) {
            $_data[] = [
                'id'             => $item->id,
                'parent_id'      => $item->parent_id,
                'user_id'        => $item->user_id,
                'user_level'     => $item->user_level,
                'username'       => $item->username,
                'self_bets'      => number4($item->self_bets),//自己投注
                'self_cancel'    => number4($item->self_cancel),//自己撤单
                'self_he_return' => number4($item->self_he_return),//自己和值返款
                'self_real_bet'  => number4($item->self_real_bet),//自己真实投注
                'team_bets'      => number4($item->team_bets),//团队投注
                'team_cancel'    => number4($item->team_cancel),//团队撤销
                'team_he_return' => number4($item->team_he_return),//团队和值返款
                'team_real_bet'  => number4($item->team_real_bet),//团队真实投注
                'total_salary'   => number4($item->total_salary),//总工资
                'child_salary'   => number4($item->child_salary),//下级工资
                'self_salary'    => number4($item->self_salary),//自己的工资
                'real_salary'    => number4($item->real_salary),//真实工资
                'rate'           => $item->rate,
                'day'            => $item->day,
                'send_time'      => date('Y-m-d H:i:s', $item->send_time),
                'status'         => $item->status,
                'updated_at'     => date('Y-m-d H:i:s', strtotime($item->updated_at)),
                'created_at'     => date('Y-m-d H:i:s', strtotime($item->created_at)),

            ];
        }
        $data = $_data;

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取 彩种 列表
    public function lotteryDayList()
    {
        $c      = request()->all();
        $c['partner_sign']  = $this->partnerSign;
        $data   = ReportStatLotteryDay::getList($c);

        $totalBet = $totalBonus = $totalCancel = $totalLimitReduce = $totalChallengeReduce = $totalHeReturn = 0;
        $totalCommissionFromChild = $totalCommissionFromBet = $totalProfit = 0;

        $pageBet = $pageBonus = $pageCancel = $pageLimitReduce = $pageChallengeReduce = $pageHeReturn = 0;
        $pageCommissionFromChild = $pageCommissionFromBet = $pageProfit = 0;

        foreach ($data['data'] as $item) {

            $pageBet                   += $item->bets;
            $pageBonus                 += $item->bonus;
            $pageCancel                += $item->cancel;
            $pageLimitReduce           += $item->limit_reduce;
            $pageChallengeReduce       += $item->challenge_reduce;
            $pageHeReturn              += $item->he_return;
            $pageCommissionFromChild   += $item->commission_from_child;
            $pageCommissionFromBet     += $item->commission_from_bet;


            $profit = $item->bonus + $item->cancel + $item->he_return + $item->commission_from_child + $item->commission_from_bet - ($item->bets + $item->limit_reduce + $item->challenge_reduce);

            $pageProfit += $profit;

            $item->bonus                    = number4($item->bonus);
            $item->cancel                   = number4($item->cancel);
            $item->he_return                = number4($item->he_return);
            $item->commission_from_child    = number4($item->commission_from_child);
            $item->commission_from_bet      = number4($item->commission_from_bet);
            $item->bets                     = number4($item->bets);
            $item->limit_reduce             = number4($item->limit_reduce);
            $item->challenge_reduce         = number4($item->challenge_reduce);

            $item->profit                   = number4($profit);
        }

        foreach ($data['statDatas'] as $item) {

            $totalBet                   += $item->bets;
            $totalBonus                 += $item->bonus;
            $totalCancel                += $item->cancel;
            $totalLimitReduce           += $item->limit_reduce;
            $totalChallengeReduce       += $item->challenge_reduce;
            $totalHeReturn              += $item->he_return;
            $totalCommissionFromChild   += $item->commission_from_child;
            $totalCommissionFromBet     += $item->commission_from_bet;


            $profit = $item->bonus + $item->cancel + $item->he_return + $item->commission_from_child + $item->commission_from_bet - ($item->bets + $item->limit_reduce + $item->challenge_reduce);

            $totalProfit += $profit;

            $item->bonus                    = number4($item->bonus);
            $item->cancel                   = number4($item->cancel);
            $item->he_return                = number4($item->he_return);
            $item->commission_from_child    = number4($item->commission_from_child);
            $item->commission_from_bet      = number4($item->commission_from_bet);
            $item->bets                     = number4($item->bets);
            $item->limit_reduce             = number4($item->limit_reduce);
            $item->challenge_reduce         = number4($item->challenge_reduce);

            $item->profit                   = number4($profit);
        }


        // 合计
        $data['page_stat'] = [
            "page_bets"                  => number4($pageBet),
            "page_limit_reduce"          => number4($pageLimitReduce),
            "page_challenge_reduce"      => number4($pageChallengeReduce),

            "page_bonus"                 => number4($pageBonus),
            "page_cancel"                => number4($pageCancel),
            "page_he_return"             => number4($pageHeReturn),
            "page_commission_from_child" => number4($pageCommissionFromChild),
            "page_commission_from_bet"   => number4($pageCommissionFromBet),
            "page_profit"                => number4($pageProfit),
        ];

        $data['total_stat'] = [
            "total_bets"                  => number4($totalBet),
            "total_limit_reduce"          => number4($totalLimitReduce),
            "total_challenge_reduce"      => number4($totalChallengeReduce),

            "total_bonus"                 => number4($totalBonus),
            "total_cancel"                => number4($totalCancel),
            "total_he_return"             => number4($totalHeReturn),
            "total_commission_from_child" => number4($totalCommissionFromChild),
            "total_commission_from_bet"   => number4($totalCommissionFromBet),
            "total_profit"                => number4($totalProfit),
        ];

        $data['lottery_option']     = Lottery::getSelectOptions(false);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    //代理分红列表
    public function dividendList() {
        $user   = auth()->guard('partner_api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        $data   = ReportUserDividend::getList($c);

        $_data = [];
        foreach ($data['data'] as $item) {
            $_data[] = [
                'id'                          => $item->id,
                'parent_id'                   => $item->parent_id,
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
        $data = $_data;

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }



    public function getDailyStatistical()
    {
        $c                  = request()->all();
        $c['partner_sign'] = $this->partnerSign;

        $data   = ReportStatUserDay::getStatDayDataForFrontend($c);
        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }
}
