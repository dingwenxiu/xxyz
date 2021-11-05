<?php
namespace App\Models\Report;

use App\Models\Base;
use App\Models\Casino\CasinoPlayerBet;
use App\Models\Player\Player;
use Illuminate\Support\Facades\DB;
use App\Lib\Logic\Stat\StatLogic;
use Illuminate\Support\Carbon;
use App\Lib\Clog;
use App\Models\Account\AccountChangeReport;

/**
 * 用户每日数据　
 * 2019-07 整理
 * Class ReportUserStatDay
 * @package App\Models\Report
 */
class ReportStatUserDay extends Base
{
    protected $table = 'report_stat_user_day';

    // 默认队列
    static $queueName = "stat_user";

    static $fieldTransferNumber = [
        "recharge_amount",
        "withdraw_amount",
        "bets",
        "cancel",
        "commission_from_bet",
        "commission_from_child",
        "bonus",
        "transfer_to_child",
        "transfer_from_parent",
        "salary",
        "dividend",
        "gift",
        "system_transfer_add",
        "system_transfer_reduce",

        "team_recharge_amount",
        "team_withdraw_amount",
        "team_bets",
        "team_cancel",
        "team_commission_from_bet",
        "team_commission_from_child",
        "team_bonus",

        "team_gift",
        "team_system_transfer_add",
        "team_system_transfer_reduce",
    ];

    /**
     * 获取列表
     * @param $c
     * @return array
     */
    static function getList($c)
    {
        //查找有帳變的記錄
        $rechargeAccountIds = AccountChangeReport::groupBy('user_id')->pluck('user_id')->toArray();

        $query = self::orderBy('id', 'desc')->whereIn('user_id',$rechargeAccountIds);

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户ID
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', $c['parent_id']);
        }

        // 日期 开始
        if (isset($c['start_day']) && $c['start_day']) {
            $query->where('day', ">=", $c['start_day']);
        } else {
            $query->where('day', ">=", date("Ymd"));
        }

        // 日期 结束
        if (isset($c['end_day']) && $c['end_day']) {
            $query->where('day', "<=", $c['end_day']);
        } else {
            $query->where('day', "<=", date("Ymd"));
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total       = $query->count();
        $totalDatas  = $query->get();
        $data        = $query->skip($offset)->take($pageSize)->get();

        //总统计

        $total_recharge_amount             = 0;
        $total_team_recharge_amount        = 0;
        $total_withdraw_amount             = 0;
        $total_team_withdraw_amount        = 0;
        $total_system_transfer_add         = 0;
        $total_team_system_transfer_add    = 0;
        $total_system_transfer_reduce      = 0;
        $total_team_system_transfer_reduce = 0;
        $total_bets                        = 0;
        $total_team_bets                   = 0;
        $total_cancel                      = 0;
        $total_team_cancel                 = 0;
        $total_bonus                       = 0;
        $total_team_bonus                  = 0;
        $total_commission_from_bet         = 0;
        $total_team_commission_from_bet    = 0;
        $total_commission_from_child       = 0;
        $total_team_commission_from_child  = 0;
        $total_transfer_from_parent        = 0;
        $total_team_transfer_from_parent   = 0;
        $total_transfer_to_child           = 0;
        $total_team_transfer_to_child      = 0;
        $total_gift                        = 0;
        $total_team_gift                   = 0;
        $total_salary                      = 0;
        $total_team_salary                 = 0;
        $total_dividend                    = 0;
        $total_team_dividend               = 0;
        $total_profit                      = 0;
        $total_team_profit                 = 0;

        //每页统计

        $page_recharge_amount             = 0;
        $page_team_recharge_amount        = 0;
        $page_withdraw_amount             = 0;
        $page_team_withdraw_amount        = 0;
        $page_system_transfer_add         = 0;
        $page_team_system_transfer_add    = 0;
        $page_system_transfer_reduce      = 0;
        $page_team_system_transfer_reduce = 0;
        $page_bets                        = 0;
        $page_team_bets                   = 0;
        $page_cancel                      = 0;
        $page_team_cancel                 = 0;
        $page_bonus                       = 0;
        $page_team_bonus                  = 0;
        $page_commission_from_bet         = 0;
        $page_team_commission_from_bet    = 0;
        $page_commission_from_child       = 0;
        $page_team_commission_from_child  = 0;
        $page_transfer_from_parent        = 0;
        $page_team_transfer_from_parent   = 0;
        $page_transfer_to_child           = 0;
        $page_team_transfer_to_child      = 0;
        $page_gift                        = 0;
        $page_team_gift                   = 0;
        $page_salary                      = 0;
        $page_team_salary                 = 0;
        $page_dividend                    = 0;
        $page_team_dividend               = 0;
        $page_profit                      = 0;
        $page_team_profit                 = 0;
       
        foreach ($data as $item) {

            //中奖+投返+下返+礼金+工资+分红-投注
            $item->profit       = number4($item->bonus + $item->commission_from_bet + $item->salary +$item->dividend +$item->gift +$item->commission_from_child + $item->cancel - $item->bets);


            //團隊充值
            $item->team_recharge_amount        = $item->recharge_amount +$item->team_recharge_amount;

            //團隊提現
            $item->team_withdraw_amount        = $item->withdraw_amount +$item->team_withdraw_amount;

            //團隊理賠
            $item->team_system_transfer_add    = $item->system_transfer_add +$item->team_system_transfer_add;

            //團隊扣減
            $item->team_system_transfer_reduce = $item->system_transfer_reduce +$item->team_system_transfer_reduce;

            //團隊投注
            $item->team_bets                   = $item->team_bets +$item->bets;

            //團隊撤單
            $item->team_cancel                 = $item->team_cancel+$item->cancel;

            //團隊中獎
            $item->team_bonus                 = $item->team_bonus+$item->bonus;

            //團隊投注返點
            $item->team_commission_from_bet   = $item->commission_from_bet+$item->team_commission_from_bet;

            //團隊下返
            $item->team_commission_from_child = $item->team_commission_from_child+$item->commission_from_child;

            //團隊轉入
            $item->team_transfer_from_parent  = $item->transfer_from_parent+$item->team_transfer_from_parent;

            //團隊轉出
            $item->team_transfer_to_child  = $item->team_transfer_to_child+$item->transfer_to_child;

            //團隊禮金
            $item->team_gift               = $item->team_gift  + $item->gift;

            //團隊工資
            $item->team_salary             = $item->team_salary  + $item->salary; 

            //團隊分紅
            $item->team_dividend           = $item->team_dividend  + $item->dividend;   


            $item->team_profit  = number4($item->team_bonus  + $item->team_commission_from_bet + $item->team_commission_from_child + $item->team_salary + $item->team_dividend + $item->team_gift + $item->team_cancel - $item->team_bets);

                    $page_recharge_amount             = bcadd($page_recharge_amount             , $item->recharge_amount);
                    $page_team_recharge_amount        = bcadd($page_team_recharge_amount        , $item->team_recharge_amount);
                    $page_withdraw_amount             = bcadd($page_withdraw_amount             , $item->withdraw_amount);
                    $page_team_withdraw_amount        = bcadd($page_team_withdraw_amount        , $item->team_withdraw_amount);
                    $page_system_transfer_add         = bcadd($page_system_transfer_add         , $item->system_transfer_add);
                    $page_team_system_transfer_add    = bcadd($page_team_system_transfer_add    , $item->team_system_transfer_add);
                    $page_system_transfer_reduce      = bcadd($page_system_transfer_reduce      , $item->system_transfer_reduce);
                    $page_team_system_transfer_reduce = bcadd($page_team_system_transfer_reduce ,$item->team_system_transfer_reduce);
                    $page_bets                        = bcadd($page_bets                        , $item->bets);
                    $page_team_bets                   = bcadd($page_team_bets                   , $item->team_bets);
                    $page_cancel                      = bcadd($page_cancel                      , $item->cancel);
                    $page_team_cancel                 = bcadd($page_team_cancel                 , $item->team_cancel);
                    $page_bonus                       = bcadd($page_bonus                       , $item->bonus);
                    $page_team_bonus                  = bcadd($page_team_bonus                  , $item->team_bonus);
                    $page_commission_from_bet         = bcadd($page_commission_from_bet         , $item->commission_from_bet);
                    $page_team_commission_from_bet    = bcadd($page_team_commission_from_bet    , $item->team_commission_from_bet);
                    $page_commission_from_child       = bcadd($page_commission_from_child       , $item->commission_from_child);
                    $page_team_commission_from_child  = bcadd($page_team_commission_from_child  , $item->team_commission_from_child);
                    $page_transfer_from_parent        = bcadd($page_transfer_from_parent        , $item->transfer_from_parent);
                    $page_team_transfer_from_parent   = bcadd($page_team_transfer_from_parent   , $item->team_transfer_from_parent);
                    $page_transfer_to_child           = bcadd($page_transfer_to_child           , $item->transfer_to_child);
                    $page_team_transfer_to_child      = bcadd($page_team_transfer_to_child      , $item->team_transfer_to_child);
                    $page_gift                        = bcadd($page_gift                        , $item->gift); 
                    $page_team_gift                   = bcadd($page_team_gift                   , $item->team_gift);
                    $page_salary                      = bcadd($page_salary                      , $item->salary);
                    $page_team_salary                 = bcadd($page_team_salary                 , $item->team_salary);
                    $page_dividend                    = bcadd($page_dividend                    , $item->dividend);
                    $page_team_dividend               = bcadd($page_team_dividend               , $item->team_dividend);
                    $page_profit                      = bcadd($page_profit                      , $item->profit,4);
                    $page_team_profit                 = bcadd($page_team_profit                 , $item->team_profit,4);

            foreach (self::$fieldTransferNumber as $field) {
                $item->{$field} = number4($item->{$field});
            }
        }

        foreach ($totalDatas as $item) {

            //中奖+投返+下返+礼金+工资+分红-投注
            $item->profit       = number4($item->bonus + $item->commission_from_bet + $item->salary +$item->dividend +$item->gift +$item->commission_from_child + $item->cancel - $item->bets);


            //團隊充值
            $item->team_recharge_amount        = $item->recharge_amount +$item->team_recharge_amount;

            //團隊提現
            $item->team_withdraw_amount        = $item->withdraw_amount +$item->team_withdraw_amount;

            //團隊理賠
            $item->team_system_transfer_add    = $item->system_transfer_add +$item->team_system_transfer_add;

            //團隊扣減
            $item->team_system_transfer_reduce = $item->system_transfer_reduce +$item->team_system_transfer_reduce;

            //團隊投注
            $item->team_bets                   = $item->team_bets +$item->bets;

            //團隊撤單
            $item->team_cancel                 = $item->team_cancel+$item->cancel;

            //團隊中獎
            $item->team_bonus                 = $item->team_bonus+$item->bonus;

            //團隊投注返點
            $item->team_commission_from_bet   = $item->commission_from_bet+$item->team_commission_from_bet;

            //團隊下返
            $item->team_commission_from_child = $item->team_commission_from_child+$item->commission_from_child;

            //團隊轉入
            $item->team_transfer_from_parent  = $item->transfer_from_parent+$item->team_transfer_from_parent;

            //團隊轉出
            $item->team_transfer_to_child  = $item->team_transfer_to_child+$item->transfer_to_child;

            //團隊禮金
            $item->team_gift               = $item->team_gift  + $item->gift;

            //團隊工資
            $item->team_salary             = $item->team_salary  + $item->salary; 

            //團隊分紅
            $item->team_dividend           = $item->team_dividend  + $item->dividend;   


            $item->team_profit  = number4($item->team_bonus  + $item->team_commission_from_bet + $item->team_commission_from_child + $item->team_salary + $item->team_dividend + $item->team_gift + $item->team_cancel - $item->team_bets);

                    $total_recharge_amount             = bcadd($total_recharge_amount             , $item->recharge_amount);
                    $total_team_recharge_amount        = bcadd($total_team_recharge_amount        , $item->team_recharge_amount);
                    $total_withdraw_amount             = bcadd($total_withdraw_amount             , $item->withdraw_amount);
                    $total_team_withdraw_amount        = bcadd($total_team_withdraw_amount        , $item->team_withdraw_amount);
                    $total_system_transfer_add         = bcadd($total_system_transfer_add         , $item->system_transfer_add);
                    $total_team_system_transfer_add    = bcadd($total_team_system_transfer_add    , $item->team_system_transfer_add);
                    $total_system_transfer_reduce      = bcadd($total_system_transfer_reduce      , $item->system_transfer_reduce);
                    $total_team_system_transfer_reduce = bcadd($total_team_system_transfer_reduce ,$item->team_system_transfer_reduce);
                    $total_bets                        = bcadd($total_bets                        , $item->bets);
                    $total_team_bets                   = bcadd($total_team_bets                   , $item->team_bets);
                    $total_cancel                      = bcadd($total_cancel                      , $item->cancel);
                    $total_team_cancel                 = bcadd($total_team_cancel                 , $item->team_cancel);
                    $total_bonus                       = bcadd($total_bonus                       , $item->bonus);
                    $total_team_bonus                  = bcadd($total_team_bonus                  , $item->team_bonus);
                    $total_commission_from_bet         = bcadd($total_commission_from_bet         , $item->commission_from_bet);
                    $total_team_commission_from_bet    = bcadd($total_team_commission_from_bet    , $item->team_commission_from_bet);
                    $total_commission_from_child       = bcadd($total_commission_from_child       , $item->commission_from_child);
                    $total_team_commission_from_child  = bcadd($total_team_commission_from_child  , $item->team_commission_from_child);
                    $total_transfer_from_parent        = bcadd($total_transfer_from_parent        , $item->transfer_from_parent);
                    $total_team_transfer_from_parent   = bcadd($total_team_transfer_from_parent   , $item->team_transfer_from_parent);
                    $total_transfer_to_child           = bcadd($total_transfer_to_child           , $item->transfer_to_child);
                    $total_team_transfer_to_child      = bcadd($total_team_transfer_to_child      , $item->team_transfer_to_child);
                    $total_gift                        = bcadd($total_gift                        , $item->gift); 
                    $total_team_gift                   = bcadd($total_team_gift                   , $item->team_gift);
                    $total_salary                      = bcadd($total_salary                      , $item->salary);
                    $total_team_salary                 = bcadd($total_team_salary                 , $item->team_salary);
                    $total_dividend                    = bcadd($total_dividend                    , $item->dividend);
                    $total_team_dividend               = bcadd($total_team_dividend               , $item->team_dividend);
                    $total_profit                      = bcadd($total_profit                      , $item->profit,4);
                    $total_team_profit                 = bcadd($total_team_profit                 , $item->team_profit,4);

            foreach (self::$fieldTransferNumber as $field) {
                $item->{$field} = number4($item->{$field});
            }
        }

        $data = $data->toArray();

        $d['total_recharge_amount']             = number4($total_recharge_amount);
        $d['total_team_recharge_amount']        = number4($total_team_recharge_amount);
        $d['total_withdraw_amount']             = number4($total_withdraw_amount);
        $d['total_team_withdraw_amount']        = number4($total_team_withdraw_amount);
        $d['total_system_transfer_add']         = number4($total_system_transfer_add);
        $d['total_team_system_transfer_add']    = number4($total_team_system_transfer_add);
        $d['total_system_transfer_reduce']      = number4($total_system_transfer_reduce);
        $d['total_team_system_transfer_reduce'] = number4($total_team_system_transfer_reduce);
        $d['total_bets']                        = number4($total_bets);
        $d['total_team_bets']                   = number4($total_team_bets);
        $d['total_cancel']                      = number4($total_cancel);
        $d['total_team_cancel']                 = number4($total_team_cancel);
        $d['total_bonus']                       = number4($total_bonus);
        $d['total_team_bonus']                  = number4($total_team_bonus);
        $d['total_commission_from_bet']         = number4($total_commission_from_bet);
        $d['total_team_commission_from_bet']    = number4($total_team_commission_from_bet);
        $d['total_commission_from_child']       = number4($total_commission_from_child);
        $d['total_team_commission_from_child']  = number4($total_team_commission_from_child);
        $d['total_transfer_from_parent']        = number4($total_transfer_from_parent);
        $d['total_team_transfer_from_parent']   = number4($total_team_transfer_from_parent);
        $d['total_transfer_to_child']           = number4($total_transfer_to_child);
        $d['total_team_transfer_to_child']      = number4($total_team_transfer_to_child);
        $d['total_gift']                        = number4($total_gift);
        $d['total_team_gift']                   = number4($total_team_gift);
        $d['total_salary']                      = number4($total_salary);
        $d['total_team_salary']                 = number4($total_team_salary);
        $d['total_dividend']                    = number4($total_dividend);
        $d['total_team_dividend']               = number4($total_team_dividend);
        $d['total_profit']                      = $total_profit;
        $d['total_team_profit']                 = $total_team_profit;

        //每页统计

        $d['page_recharge_amount']             = number4($page_recharge_amount);
        $d['page_team_recharge_amount']        = number4($page_team_recharge_amount);
        $d['page_withdraw_amount']             = number4($page_withdraw_amount);
        $d['page_team_withdraw_amount']        = number4($page_team_withdraw_amount);
        $d['page_system_transfer_add']         = number4($page_system_transfer_add);
        $d['page_team_system_transfer_add']    = number4($page_team_system_transfer_add);
        $d['page_system_transfer_reduce']      = number4($page_system_transfer_reduce);
        $d['page_team_system_transfer_reduce'] = number4($page_team_system_transfer_reduce);
        $d['page_bets']                        = number4($page_bets);
        $d['page_team_bets']                   = number4($page_team_bets);
        $d['page_cancel']                      = number4($page_cancel);
        $d['page_team_cancel']                 = number4($page_team_cancel);
        $d['page_bonus']                       = number4($page_bonus);
        $d['page_team_bonus']                  = number4($page_team_bonus);
        $d['page_commission_from_bet']         = number4($page_commission_from_bet);
        $d['page_team_commission_from_bet']    = number4($page_team_commission_from_bet);
        $d['page_commission_from_child']       = number4($page_commission_from_child);
        $d['page_team_commission_from_child']  = number4($page_team_commission_from_child);
        $d['page_transfer_from_parent']        = number4($page_transfer_from_parent);
        $d['page_team_transfer_from_parent']   = number4($page_team_transfer_from_parent);
        $d['page_transfer_to_child']           = number4($page_transfer_to_child);
        $d['page_team_transfer_to_child']      = number4($page_team_transfer_to_child);
        $d['page_gift']                        = number4($page_gift);
        $d['page_team_gift']                   = number4($page_team_gift);
        $d['page_salary']                      = number4($page_salary);
        $d['page_team_salary']                 = number4($page_team_salary);
        $d['page_dividend']                    = number4($page_dividend);
        $d['page_team_dividend']               = number4($page_team_dividend);
        $d['page_profit']                      = $page_profit;
        $d['page_team_profit']                 = $page_team_profit;


        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize)),'dataToal'=>$d];
    }

    /**
     * 获取数据 针对前端
     * @param $c
     * @return mixed
     */
    static function getStatDataForFrontend($c)
    {
        $yesterday  = Carbon::yesterday()->format('Ymd');
        $today      = Carbon::today()->format('Ymd');

        $query = Player::select(
            'report_stat_user_day.user_id',                                                                // 用户ID
            'report_stat_user_day.username',                                                               // 用户名
            'user_accounts.balance',                                                                       // 用户余额
            'user_accounts.frozen',                                                                        // 用户冻结额
            'users.child_count',                                                                           // 下级用户数量
            DB::raw('SUM(report_stat_user_day.first_register) as first_register'),                         //新注册
            DB::raw('SUM(report_stat_user_day.have_bet) as have_bet'),                                     //投注人数

            DB::raw('SUM(report_stat_user_day.recharge_count)   as recharge_count'),                       //充值人数
            DB::raw('SUM(report_stat_user_day.first_recharge_count)   as first_recharge_count'),           // 首次充值
            DB::raw('SUM(report_stat_user_day.repeat_recharge_count)   as repeat_recharge_count'),         // 复充人数


            DB::raw('SUM(report_stat_user_day.recharge_amount)  as recharge_amount'),                      // 充值总额
            DB::raw('SUM(report_stat_user_day.withdraw_amount)  as withdraw_amount'),                      // 提现总额
            DB::raw('SUM(report_stat_user_day.bets) as bets'),                                             // 投注总额
            DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),               // 投注返点
            DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),           // 下级投注返点
            DB::raw('SUM(report_stat_user_day.bonus) as bonus'),                                           // 奖金
            DB::raw('SUM(report_stat_user_day.gift) as gift'),                                             //活动礼金
            DB::raw('SUM(report_stat_user_day.salary) as salary'),                                         //日工资
            DB::raw('SUM(report_stat_user_day.dividend) as dividend'),                                     //分红

            DB::raw('SUM(report_stat_user_day.team_first_register)  as team_first_register'),              //团队新注册
            DB::raw('SUM(report_stat_user_day.team_have_bet)  as team_have_bet'),                          //团队投注人数

            DB::raw('SUM(report_stat_user_day.team_recharge_count)  as team_recharge_count'),              //团队充值
            DB::raw('SUM(report_stat_user_day.team_first_recharge_count)  as team_first_recharge_count'),  // 团队首冲金额
            DB::raw('SUM(report_stat_user_day.team_repeat_recharge_count)  as team_repeat_recharge_count'),//团队复充金额

            DB::raw('SUM(report_stat_user_day.team_withdraw_amount) as team_withdraw_amount'),             //团队提现金额
            DB::raw('SUM(report_stat_user_day.team_bets) as team_bets'),                                   //团队投注金额
            DB::raw('SUM(report_stat_user_day.team_commission_from_bet) as team_commission_from_bet'),     //团队投注返点
            DB::raw('SUM(report_stat_user_day.team_commission_from_child) as team_commission_from_child'), //团队下级返点
            DB::raw('SUM(report_stat_user_day.team_bonus) as team_bonus'),                                 //团队奖金
            DB::raw('SUM(report_stat_user_day.team_gift) as team_gift'),                                   //团队礼金
            DB::raw('SUM(report_stat_user_day.team_salary) as team_salary')//团队日工资

        )->leftJoin('user_accounts', 'user_accounts.user_id', '=', 'users.id')
            ->leftJoin('report_stat_user_day', 'report_stat_user_day.user_id', '=', 'users.id');

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('users.partner_sign', $c['partner_sign'])->where("report_stat_user_day.partner_sign", $c['partner_sign']);
        }

        // 用户ID
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('users.id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('users.username', $c['username']);
        }

        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('users.parent_id', $c['parent_id']);
        }

        // 日期 开始
        if (isset($c['start_day']) && $c['start_day']) {
            $query->where('report_stat_user_day.day', ">=", date("Ymd", strtotime($c['start_day'])));
        } else {
            $query->where('report_stat_user_day.day', ">=", date("Ymd"));
        }

        // 日期 结束
        if (isset($c['end_day']) && $c['end_day']) {
            $query->where('report_stat_user_day.day', "<=", date("Ymd", strtotime($c['end_day'])));
        } else {
            $query->where('report_stat_user_day.day', "<=", date("Ymd"));
        }

        // 默认显示当天数据
        if (isset($c['start_day'] ,$c['end_day']) && $c['start_day'] && $c['end_day']) {
            $query->whereBetween('report_stat_user_day.day', [date("Ymd", strtotime($c['start_day'])), date("Ymd", strtotime($c['end_day']))]);
        } else {
            $query->whereBetween('report_stat_user_day.day', [$yesterday, $today]);
        }

        $query->groupBy("users.id");
        $query->orderBy("users.id", "DESC");

        $total  = $query->count();

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;


        $data   = $query->skip($offset)->take($pageSize)->get();

        foreach ($data as $item) {
            $item->balance      = 0;
            foreach (StatLogic::$filters as $field) {
                if (in_array($field, ['have_bet', 'first_recharge_count', 'repeat_recharge_count', 'first_register'])) {
                    continue;
                }
                $item->{$field} = number4($item->{$field});
            }

            foreach (StatLogic::$team_filters as $field) {
                $field = "team_" . $field;
                if (in_array($field, ['team_have_bet', 'team_first_recharge_count', 'team_repeat_recharge_count', 'team_first_register'])) {
                    continue;
                }
                $item->{$field} = number4($item->{$field});
            }

            $item->profit = $item->commission_from_child + $item->commission_from_bet + $item->bonus + $item->salary + $item->dividend - $item->bets;//净盈亏
        }

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取数据 针对前端
     * @param $c
     * @return mixed
     */
    static function getStatDayDataForFrontend($c)
    {
        $query = self::orderBy('day', 'desc');

        // 平台
        if (!empty($c['partner_sign'])) {
            $query->where('partner_sign', $c['partner_sign'])->where("partner_sign", $c['partner_sign']);
        }

        // 日期 开始
        if (!empty($c['start_time'])) {
            $query->where('day', ">=", date("Ymd", strtotime($c['start_time'])));
        } else {
            $query->where('day', ">=", date("Ymd",strtotime("first day of")));
        }

        // 日期结束
        if (!empty($c['end_time'])) {
            $query->where('day', "<=", date("Ymd", strtotime($c['end_time'])));
        } else {
            $query->where('day', "<=", date("Ymd",strtotime("last day of")));
        }

        // 默认显示当天数据
       // if (empty($c['start_time']) && empty($c['end_time'])) {
       //     $query->where('day', date('Ymd'));
       // }

        $data['data']   = $query->select(
            DB::raw('SUM(team_first_register) as team_first_register'),         // 总代注册人数
            DB::raw('SUM(first_register) as first_register'),                   // 注册人数
            DB::raw('SUM(first_recharge_count) as first_recharge_count'),       // 首值人数
            //DB::raw('SUM(recharge_count)   as recharge_count'),                 //充值人数
            DB::raw('SUM(CASE WHEN (recharge_count) THEN 1 ELSE 0 END) as recharge_count'),  //充值人数
            
            //DB::raw('SUM(withdraw_count) as withdraw_count'),                 // 提现人数
            DB::raw('SUM(CASE WHEN (withdraw_count) THEN 1 ELSE 0 END) as withdraw_count'), 

            DB::raw('SUM(have_bet) as have_bet'),                               // 投注人数

            DB::raw('SUM(repeat_recharge_count) as repeat_recharge_count'),     // 复值人数

            DB::raw('SUM(recharge_amount)  as recharge_amount'),                      // 充值总额
            DB::raw('SUM(withdraw_amount)  as withdraw_amount'),                      // 提现总额
            DB::raw('SUM(system_transfer_add)  as system_transfer_add'),           // 总理赔
            DB::raw('SUM(system_transfer_reduce)  as system_transfer_reduce'),      // 扣减
            DB::raw('SUM(bets) as bets'),                                             // 投注总额
            DB::raw('SUM(cancel)  as cancel'),                                          // 总撤单
            DB::raw('SUM(bonus) as bonus'),                                           // 中奖
            DB::raw('SUM(commission_from_bet) as commission_from_bet'),               // 投注返点
            DB::raw('SUM(commission_from_child) as commission_from_child'),           // 下级投注返点
            DB::raw('SUM(transfer_to_child) as transfer_to_child'),           // 为下级充值
            DB::raw('SUM(transfer_from_parent) as transfer_from_parent'),           // 上级转账
            DB::raw('SUM(gift) as gift'),                                             //活动礼金
            DB::raw('SUM(salary) as salary'),                                         //日工资
            DB::raw('SUM(dividend) as dividend'),                                     //分红
            DB::raw('SUM(score) as score'),                                         // 积分
            DB::raw('SUM(balance) as balance'),                                     // 余额
            'day'
        )->groupBy('day')->orderBy('day','desc')->get();

        $total_team_first_register       = 0;
        $total_first_register            = 0;
        $total_first_recharge_count      = 0;
        $total_recharge_count            = 0;
        $total_withdraw_count            = 0;
        $total_have_bet                  = 0;
        $total_repeat_recharge_count     = 0;
        $total_recharge_amount           = 0;
        $total_withdraw_amount           = 0;
        $total_system_transfer_add       = 0;
        $total_system_transfer_reduce    = 0;
        $total_bets                      = 0;
        $total_cancel                    = 0;
        $total_bonus                     = 0;
        $total_commission_from_bet       = 0;
        $total_commission_from_child     = 0;
        $total_transfer_to_child         = 0;
        $total_transfer_from_parent      = 0;
        $total_gift                      = 0;
        $total_salary                    = 0;
        $total_dividend                  = 0;
        $total_score                     = 0;
        $total_profit                    = 0;
        $total_balance                   = 0;

        foreach ($data['data'] as $item) {
            //中奖+投返+下返+礼金+工资+分红-投注
            $item->profit       = number4($item->bonus + $item->commission_from_bet + $item->salary +$item->dividend +$item->gift +$item->commission_from_child + $item->cancel - $item->bets);
            $item->team_profit  = number4($item->team_bonus  + $item->team_commission_from_bet + $item->team_commission_from_child + $item->team_salary + $item->team_dividend + $item->team_gift + $item->team_cancel - $item->team_bets);

            $total_team_first_register       = bcadd($total_team_first_register,$item->team_first_register);
            $total_first_register            = bcadd($total_first_register,$item->first_register);
            $total_first_recharge_count      = bcadd($total_first_recharge_count,$item->first_recharge_count);
            $total_recharge_count            = bcadd($total_recharge_count,$item->recharge_count);
            $total_withdraw_count            = bcadd($total_withdraw_count,$item->withdraw_count);
            $total_have_bet                  = bcadd($total_have_bet,$item->have_bet);
            $total_repeat_recharge_count     = bcadd($total_repeat_recharge_count,$item->repeat_recharge_count);
            $total_recharge_amount           = bcadd($total_recharge_amount,$item->recharge_amount);
            $total_withdraw_amount           = bcadd($total_withdraw_amount,$item->withdraw_amount);
            $total_system_transfer_add       = bcadd($total_system_transfer_add,$item->system_transfer_add);
            $total_system_transfer_reduce    = bcadd($total_system_transfer_reduce,$item->system_transfer_reduce);
            $total_bets                      = bcadd($total_bets,$item->bets);
            $total_cancel                    = bcadd($total_cancel,$item->cancel);
            $total_bonus                     = bcadd($total_bonus,$item->bonus);
            $total_commission_from_bet       = bcadd($total_commission_from_bet,$item->commission_from_bet);
            $total_commission_from_child     = bcadd($total_commission_from_child,$item->commission_from_child);
            $total_transfer_to_child         = bcadd($total_transfer_to_child,$item->transfer_to_child);
            $total_transfer_from_parent      = bcadd($total_transfer_from_parent,$item->transfer_from_parent);
            $total_gift                      = bcadd($total_gift,$item->gift);
            $total_salary                    = bcadd($total_salary,$item->salary);
            $total_dividend                  = bcadd($total_dividend,$item->dividend);
            $total_score                     = bcadd($total_score,$item->score);
            $total_balance                   = bcadd($total_balance,$item->balance);
            $total_profit                    = bcadd($total_profit,$item->profit,4);

            foreach (self::$fieldTransferNumber as $field) {
                $item->{$field} = number4($item->{$field});
            }
        }

        $data['total_team_first_register']       = $total_team_first_register;
        $data['total_first_register']            = $total_first_register;
        $data['total_first_recharge_count']      = $total_first_recharge_count;
        $data['total_recharge_count']            = $total_recharge_count;
        $data['total_withdraw_count']            = $total_withdraw_count;
        $data['total_have_bet']                  = $total_have_bet;
        $data['total_repeat_recharge_count']     = $total_repeat_recharge_count;
        $data['total_recharge_amount']           = number4($total_recharge_amount);
        $data['total_withdraw_amount']           = number4($total_withdraw_amount);
        $data['total_system_transfer_add']       = number4($total_system_transfer_add);
        $data['total_system_transfer_reduce']    = number4($total_system_transfer_reduce);
        $data['total_bets']                      = number4($total_bets);
        $data['total_cancel']                    = number4($total_cancel);
        $data['total_bonus']                     = number4($total_bonus);
        $data['total_commission_from_bet']       = number4($total_commission_from_bet);
        $data['total_commission_from_child']     = number4($total_commission_from_child);
        $data['total_transfer_to_child']         = number4($total_transfer_to_child);
        $data['total_transfer_from_parent']      = number4($total_transfer_from_parent);
        $data['total_gift']                      = number4($total_gift);
        $data['total_salary']                    = number4($total_salary);
        $data['total_dividend']                  = number4($total_dividend);
        $data['total_score']                     = number4($total_score);
        $data['total_balance']                   = number4($total_balance);
        $data['total_profit']                    = $total_profit;

        return $data;
    }


    /**
     * @param $player
     * @param $startDay
     * @param $endDay
     * @return object
     */
    static function getPlayerProxyData($player, $startDay, $endDay) {
        $query = Player::select(
            'report_stat_user_day.user_id',
            'report_stat_user_day.username',
            'users.child_count',
            DB::raw('SUM(report_stat_user_day.first_register) as first_register'),
            DB::raw('SUM(report_stat_user_day.have_bet) as have_bet'),

            DB::raw('SUM(report_stat_user_day.recharge_count)   as recharge_count'),
            DB::raw('SUM(report_stat_user_day.first_recharge_count)   as first_recharge_count'),
            DB::raw('SUM(report_stat_user_day.repeat_recharge_count)   as repeat_recharge_count'),
            DB::raw('SUM(report_stat_user_day.recharge_amount)  as recharge_amount'),
            DB::raw('SUM(report_stat_user_day.withdraw_amount)  as withdraw_amount'),
            DB::raw('SUM(report_stat_user_day.bets) as bets'),
            DB::raw('SUM(report_stat_user_day.cancel) as cancel'),
            DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),
            DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),
            DB::raw('SUM(report_stat_user_day.bonus) as bonus'),
            DB::raw('SUM(report_stat_user_day.gift) as gift'),
            DB::raw('SUM(report_stat_user_day.salary) as salary'),
            DB::raw('SUM(report_stat_user_day.dividend) as dividend'),

            DB::raw('SUM(report_stat_user_day.team_first_register)  as team_first_register'),
            DB::raw('SUM(report_stat_user_day.team_have_bet)  as team_have_bet'),

            DB::raw('SUM(report_stat_user_day.team_recharge_count)  as team_recharge_count'),
            DB::raw('SUM(report_stat_user_day.team_recharge_amount)  as team_recharge_amount'),
            DB::raw('SUM(report_stat_user_day.team_first_recharge_count)  as team_first_recharge_count'),
            DB::raw('SUM(report_stat_user_day.team_repeat_recharge_count)  as team_repeat_recharge_count'),

            DB::raw('SUM(report_stat_user_day.team_withdraw_amount) as team_withdraw_amount'),
            DB::raw('SUM(report_stat_user_day.team_bets) as team_bets'),
            DB::raw('SUM(report_stat_user_day.team_cancel) as team_cancel'),
            DB::raw('SUM(report_stat_user_day.team_commission_from_bet) as team_commission_from_bet'),
            DB::raw('SUM(report_stat_user_day.team_commission_from_child) as team_commission_from_child'),
            DB::raw('SUM(report_stat_user_day.team_bonus) as team_bonus'),
            DB::raw('SUM(report_stat_user_day.team_gift) as team_gift'),
            
            DB::raw('SUM(report_stat_user_day.team_salary) as team_salary')

        )->leftJoin('report_stat_user_day', 'report_stat_user_day.user_id', '=', 'users.id');


        $query->where('users.partner_sign', $player->partner_sign);
        $query->where('report_stat_user_day.partner_sign', $player->partner_sign);
        $query->where('users.id', $player->id);

        $ids = Player::where('rid','like',$player->rid.'%')->pluck('id')->toArray();
        $query1 =self::whereIn('user_id',$ids)->where('recharge_count','>',0);

        // 日期 开始
        if ($startDay) {
            $query->where('report_stat_user_day.day', ">=", $startDay);
            $query1->where('report_stat_user_day.day', ">=", $startDay);
        } else {
            $query->where('report_stat_user_day.day', ">=", date("Ymd"));
            $query1->where('report_stat_user_day.day', ">=", date("Ymd"));
        }

        // 日期 结束
        if ($endDay) {
            $query->where('report_stat_user_day.day', "<=", $endDay);
            $query1->where('report_stat_user_day.day', "<=", $endDay);
        } else {
            $query->where('report_stat_user_day.day', "<=", date("Ymd"));
            $query1->where('report_stat_user_day.day', "<=", date("Ymd"));
        }

        $item   = $query->first();

        \DB::enableQueryLog();
        $item->recharge_preson=$query1->count();
        
        Clog::test('代理首页查询',[\DB::getQueryLog()]);

        $item->team_balance = 0;

        foreach (StatLogic::$filters as $field) {
            if (in_array($field, ['have_bet', 'first_recharge_count', 'repeat_recharge_count', 'first_register', 'recharge_count'])) {
                continue;
            }
            $item->{$field} = number4($item->{$field});
        }

        foreach (StatLogic::$team_filters as $field) {
            $field = "team_" . $field;
            if (in_array($field, ['team_have_bet', 'team_first_recharge_count', 'team_repeat_recharge_count', 'team_first_register', 'team_recharge_count'])) {
                continue;
            }
            $item->{$field} = number4($item->{$field});
        }

        // 前台净盈亏            投注返点3       +              派奖总额  2                     + 代理返点 4                   + 活动礼金5           + 日工资6         -   投注 1
        $item->profit = $item->team_commission_from_bet + $item->team_bonus   + $item->team_commission_from_child +  $item->team_gift + $item->team_salary  - $item->team_bets + $item->team_cancel + $item->commission_from_bet + $item->bonus + $item->commission_from_child+  $item->gift+ $item->salary - $item->bets + $item->cancel ;

        $item->profit = number4(moneyUnitTransferIn($item->profit));

        return $item;
    }


    /**
     * 娱乐城区间盈亏
     * @param $username
     * @param $c
     *
     * @return string
     */
    static function getCasinoPartProfitList($username, $c)
    {
        $query = CasinoPlayerBet::where(['partner_sign' => $c['partner_sign']]);
        $timeNow = date('Y-m-d 00:00:00');
        $timeFuture = date('Y-m-d 23:59:59');

        // 开始时间
        // 结束时间
        if (isset($c['start_day'],$c['end_day'] ) && $c['start_day'] && $c['end_day']) {
            if(strtotime($c['end_day']) - strtotime($c['start_day']) > 60 * 60 * 24 *30 ) {
                return '最多只能获取30天的数据';
            }

            $query->whereBetween('bet_time',[$c['start_day'], $c['end_day']]);
        }else{
            $query->whereBetween('bet_time',[$timeNow, $timeFuture]);
        }

        if (isset($c['plat_type'] ) && $c['plat_type']) {
            $query->where('plat_type', $c['plat_type']);
        }
        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('account_username', $c['username'])->orWhere('account_username', $username);
        }
        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', $c['parent_id'])->orWhere('account_username', $username);
        }

        return $query->first([
            DB::raw('company_win_neat_amount'),
            DB::raw('account_username as username'),
            DB::raw('SUM(company_payout_amount) as company_payout_amount'),
            DB::raw('SUM(company_win_amount) as company_win_amount'),
            DB::raw('SUM(bet_amount) as bet_amount')
        ]);
    }


    /**
     * 娱乐城团队盈亏
     * @param $username
     * @param $c
     *
     * @return string
     */
    static function getCasinoTeamProfitList($c)
    {

        $query = CasinoPlayerBet::where(['partner_sign' => $c['partner_sign']]);
        $timeNow = date('Y-m-d 00:00:00');
        $timeFuture = date('Y-m-d 23:59:59');

        // 开始时间
        // 结束时间
        if (isset($c['start_day'],$c['end_day'] ) && $c['start_day'] && $c['end_day']) {
            if(strtotime($c['end_day']) - strtotime($c['start_day']) > 60 * 60 * 24 *30 ) {
                return '最多只能获取30天的数据';
            }

            $query->whereBetween('bet_time',[$c['start_day'], $c['end_day']]);
        }else{
            $query->whereBetween('bet_time',[$timeNow, $timeFuture]);
        }

        if (isset($c['plat_type'] ) && $c['plat_type']) {
            $query->where('plat_type', $c['plat_type']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('account_username', $c['username']);
        }

        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', $c['parent_id']);
        }

        $query->groupBy("user_id");
        $query->orderBy("user_id", "DESC");


        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;


        $total  = count($query->get()->toArray());

        $data   = $query->skip($offset)->take($pageSize)->get([
            DB::raw('company_win_neat_amount'),
            DB::raw('account_username as username'),
            DB::raw('SUM(company_payout_amount) as company_payout_amount'),
            DB::raw('SUM(company_win_amount) as company_win_amount'),
            DB::raw('SUM(bet_amount) as bet_amount')
        ]);

        return ['data' => $data, 'total' => $total,'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 娱乐城本身盈亏
     * @param $username
     * @param $c
     *
     * @return string
     */
    static function getSelfCasinoProfitList($username, $c)
    {
        $query = CasinoPlayerBet::where(['partner_sign' => $c['partner_sign'], 'account_username' => $username]);
        $timeNow = date('Y-m-d 00:00:00');
        $timeFuture = date('Y-m-d 23:59:59');

        // 开始时间
        // 结束时间
        if (isset($c['start_day'],$c['end_day'] ) && $c['start_day'] && $c['end_day']) {
            if(strtotime($c['end_day']) - strtotime($c['start_day']) > 60 * 60 * 24 *30 ) {
                return '最多只能获取30天的数据';
            }

            $query->whereBetween('bet_time',[$c['start_day'], $c['end_day']]);
        }else{
            $query->whereBetween('bet_time',[$timeNow,$timeFuture]);
        }

        if (isset($c['plat_type'] ) && $c['plat_type']) {
            $query->where('plat_type', $c['plat_type']);
        }

         return $query->first([
             DB::raw('company_win_neat_amount'),
             DB::raw('account_username as username'),
             DB::raw('SUM(company_payout_amount) as company_payout_amount'),
            DB::raw('SUM(company_win_amount) as company_win_amount'),
            DB::raw('SUM(bet_amount) as bet_amount')
        ]);
    }


    // 团队盈亏 代理自己本人盈亏
    static function getSelfProfitList($playerId, $c) {
        $query = self::select(
            'report_stat_user_day.user_id',
            'report_stat_user_day.username',
            DB::raw('SUM(report_stat_user_day.recharge_amount) as recharge_amount'),            // 充值
            DB::raw('SUM(report_stat_user_day.withdraw_amount) as withdraw_amount'),            // 提现
            DB::raw('SUM(report_stat_user_day.bets) as bets'),                                  // 投注
            DB::raw('SUM(report_stat_user_day.cancel) as cancel'),                              // 撤销返款
            DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),    // 投注返点
            DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),// 下级返点
            DB::raw('SUM(report_stat_user_day.bonus) as bonus'),                                // 投注奖金
            DB::raw('SUM(report_stat_user_day.gift) as gift'),                                  // 礼金
            DB::raw('SUM(report_stat_user_day.salary) as salary')                               // 日工资
        );

        $query->where('report_stat_user_day.user_id', $playerId);

        $today = Carbon::today('Asia/shanghai');
        $tomorrow = Carbon::tomorrow('Asia/shanghai');
        $timeNow = date('Ymd', strtotime($today));
        $timeFuture = date('Ymd', strtotime($tomorrow));

        // 开始时间
        // 结束时间
        if (isset($c['start_day'],$c['end_day'] ) && $c['start_day'] && $c['end_day']) {
            $query->whereBetween('report_stat_user_day.day',[date("Ymd", strtotime($c['start_day'])), date("Ymd", strtotime($c['end_day']))]);
        }else{
            $query->whereBetween('report_stat_user_day.day',[$timeNow,$timeFuture]);
        }

        $item   = $query->first();

        if ($item) {
            //前台净盈亏             派奖        + 投注返点               +下级返点                        +活动礼金      +日工资         - 投注 +  撤單
            $item->profit   = $item->bonus + $item->commission_from_bet + $item->commission_from_child + $item->gift + $item->salary - $item->bets + $item->cancel ;

            //前台游戏盈亏            派奖          +  投注返点                  - 投注  + 撤單
            $item->game_profit = $item->bonus  + $item->commission_from_bet - $item->bets + $item->cancel;

            //投注总额         投注     -   撤单
            $item->bets = $item->bets - $item->cancel;
            foreach (StatLogic::$filters as $field) {
                $item->{$field} = number4($item->{$field});
            }

            $item->game_profit = number4($item->game_profit);//游戏盈亏
            $item->profit = number4($item->profit);

            //查不到数据时特殊处理
            if(is_null($item->user_id)) {
                $currPlayer     = Player::where('id',$playerId)->first();
                $item->user_id  = $playerId;
                $item->username = $currPlayer->username;
            }
        }

        return $item;
    }


    // 团队盈亏 团队部分
    static function getTeamProfitList($c) {
        $query = Player::select(
            'report_stat_user_day.user_id',
            'report_stat_user_day.username',
            'report_stat_user_day.bets',
            'report_stat_user_day.cancel',
            DB::raw('SUM(report_stat_user_day.team_recharge_amount) as team_recharge_amount'),             // 团队充值
            DB::raw('SUM(report_stat_user_day.recharge_amount) as recharge_amount'),                       // 自已充值
            DB::raw('SUM(report_stat_user_day.team_withdraw_amount) as team_withdraw_amount'),             // 团队提现
            DB::raw('SUM(report_stat_user_day.withdraw_amount) as withdraw_amount'),                       // 自已提现
            DB::raw('SUM(report_stat_user_day.team_bets) as team_bets'),                                   // 总投注
            DB::raw('SUM(report_stat_user_day.team_cancel) as team_cancel'),                               //总撤单
            DB::raw('SUM(report_stat_user_day.team_commission_from_bet) as team_commission_from_bet'),     // 总投注返点
            DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),               // 自已投注返点
            DB::raw('SUM(report_stat_user_day.team_commission_from_child) as team_commission_from_child'), // 團隊代理返点
            DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),           // 自已代理返点
            DB::raw('SUM(report_stat_user_day.team_bonus) as team_bonus'),                                 // 團隊总派彩
            DB::raw('SUM(report_stat_user_day.bonus) as bonus'),                                           // 自已派彩
            DB::raw('SUM(report_stat_user_day.team_gift) as team_gift'),                                   // 总礼金
            DB::raw('SUM(report_stat_user_day.gift) as gift'),                                              // 总礼金
            DB::raw('SUM(report_stat_user_day.team_salary) as team_salary'),                                // 團隊日工资
            DB::raw('SUM(report_stat_user_day.salary) as salary')                                          // 自已日工资
        )->leftJoin('report_stat_user_day', 'report_stat_user_day.user_id', '=', 'users.id');

        $today = Carbon::today('Asia/shanghai');
        $tomorrow = Carbon::tomorrow('Asia/shanghai');
        $timeNow = date('Ymd', strtotime($today));
        $timeFuture = date('Ymd', strtotime($tomorrow));

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('users.partner_sign', $c['partner_sign']);
        }

        // 用户ID
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('report_stat_user_day.user_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('report_stat_user_day.username', $c['username']);
        }

        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('users.parent_id', $c['parent_id']);
        }

        // 开始时间
        // 结束时间
        if (isset($c['start_day'],$c['end_day'] ) && $c['start_day'] && $c['end_day']) {
            $query->whereBetween('report_stat_user_day.day',[date("Ymd", strtotime($c['start_day'])), date("Ymd", strtotime($c['end_day']))]);
        }else{
            $query->whereBetween('report_stat_user_day.day',[$timeNow,$timeFuture]);
        }


        //仅充值用户
        if(isset($c['is_recharge']) && $c['is_recharge'] == 1) {
            $userIds = AccountChangeReport::where('partner_sign',$c['partner_sign'])->pluck('user_id')->toArray();
            $query->whereIn('report_stat_user_day.user_id',$userIds);
        }

        $query->groupBy("users.id");
        $query->orderBy("users.id", "DESC");

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = count($query->get()->toArray());

        $data   = $query->skip($offset)->take($pageSize)->get();
        foreach ($data as $item) {

            // 投注总额             投注额            撤销总额 
            $item->team_bets = $item->team_bets - $item->team_cancel;

            //派彩總額
            $item->team_bonus = $item->team_bonus + $item->bonus;

            //投注返點總額
            $item->team_commission_from_child = $item->team_commission_from_child + $item->commission_from_child;

            //下級返點總額
            $item->team_commission_from_bet = $item->team_commission_from_bet + $item->commission_from_bet;

            //日工資
            $item->salary = $item->salary  + $item->team_salary;

            //充值总额
            $item->recharge_amount = $item->recharge_amount + $item->team_recharge_amount;

            //提现总额
            $item->withdraw_amount = $item->withdraw_amount + $item->team_withdraw_amount;


            foreach (StatLogic::$filters as $field) {
                $item->{$field} = number4($item->{$field});
            }

            $item->team_bets   = number4($item->team_bets)+$item->bets-$item->cancel;
            $item->team_bets   = bcmul($item->team_bets,1,4);

            $item->game_profit = number4($item->game_profit);//游戏盈亏
            $item->profit      = number4($item->profit);
            $item->team_bonus  = number4($item->team_bonus);
            $item->team_commission_from_child = number4($item->team_commission_from_child);
            $item->team_commission_from_bet   = number4($item->team_commission_from_bet);

             //前台游戏盈亏            派奖          +  投注返点                  - 投注  +撤單
            $item->game_profit = $item->team_bonus  + $item->commission_from_bet - $item->team_bets;

             //前台净盈亏             派奖        + 投注返点     +下级返点     +活动礼金      +日工资         - 投注  +撤單
            $item->profit   = $item->team_bonus + $item->salary + $item->team_gift + $item->gift + $item->team_commission_from_bet + $item->team_commission_from_child - $item->team_bets;
        }

        return ['data' => $data, 'total' => $total,'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }


    // 团队盈亏 区间总额
    static function getPartProfitList ($playerId, $c) {
        $query = self::select(
            DB::raw('SUM(report_stat_user_day.recharge_amount) as recharge_amount'),            // 充值
            DB::raw('SUM(report_stat_user_day.withdraw_amount) as withdraw_amount'),            // 提现
            DB::raw('SUM(report_stat_user_day.bets) as bets'),                                  // 投注
            DB::raw('SUM(report_stat_user_day.cancel) as cancel'),                              // 撤销返款
            DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),    // 投注返点
            DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),// 代理返点
            DB::raw('SUM(report_stat_user_day.bonus) as bonus'),                                // 投注奖金
            DB::raw('SUM(report_stat_user_day.gift) as gift'),                                  // 礼金
            DB::raw('SUM(report_stat_user_day.salary) as salary')                               // 日工资
        );

        $ids = Player::where('rid', 'like', '%'.$playerId.'%')->pluck('id')->toArray();
        $query->whereIn('report_stat_user_day.user_id', $ids);

        $today = now('Asia/shanghai')->firstOfMonth();
        $tomorrow = Carbon::now('Asia/shanghai');
        $timeNow = date('Ymd', strtotime($today));
        $timeFuture = date('Ymd', strtotime($tomorrow));

        // 开始时间
        // 结束时间
        if (isset($c['start_day'],$c['end_day'] ) && $c['start_day'] && $c['end_day']) {
            $query->whereBetween('report_stat_user_day.day',[date("Ymd", strtotime($c['start_day'])), date("Ymd", strtotime($c['end_day']))]);
        }else{
            $query->whereBetween('report_stat_user_day.day',[$timeNow,$timeFuture]);
        }
    
        $item  = $query->first();
        
        if ($item) {
           
            // 前台净盈亏          派奖 +       +  投注返点                +   下级返点                    +  活动礼金      + 日工资       -  投注 + 撤單
            $item->profit   = $item->bonus + $item->commission_from_bet  + $item->commission_from_child + $item->gift + $item->salary - $item->bets + $item->cancel;
            // 游戏盈亏            派奖          +  投注返点                  - 投注  +撤單
            $item->game_profit = $item->bonus  + $item->commission_from_bet - $item->bets + $item->cancel;
            // 投注总额  - 撤单总额
			$item->bets = $item->bets - $item->cancel;
            
            foreach (StatLogic::$filters as $field) {
                $item->{$field} = number4($item->{$field});
            }

            $item->game_profit = number4($item->game_profit);//游戏盈亏
            $item->profit = number4($item->profit);
        }

        return $item;
    }

    /**
     * 初始化2天数据
     * @param $user
     * @param $newRegister
     * @return mixed
     */
    static function initUserStatData($user, $newRegister = false) {

        $startTime  = time();
        $endTime    = time() + 86400 * 3;
        $daySet     = getDaySet($startTime, $endTime);

        $statUserData   = [];
        $statData       = [];
        foreach ($daySet as $day) {
            $_tmp = [
                'partner_sign'      => $user->partner_sign,
                'user_id'           => $user->id,
                'top_id'            => $user->top_id,
                'parent_id'         => $user->parent_id,
                'username'          => $user->username,
                'is_tester'         => $user->is_tester,
                'first_register'    => 0,
                'day'               => $day,
            ];

            $statUserData[] = $_tmp;
            unset($_tmp['day']);
            unset($_tmp['first_register']);
            $statData     = $_tmp;
        }

        self::insert($statUserData);
        ReportStatUser::insert($statData);
        return true;
    }

    // 获取玩家所有的倍数
    static function getTotalBets($userName)
    {
        $bets = self ::where("username", $userName) -> sum("bets");
        return $bets;
    }

    // 获取玩家所有的倍数
    static function getTotalCancel($userName)
    {
        $cancel = self ::where("username", $userName) -> sum("cancel");
        return $cancel;
    }

    static function getProfitList($playerId, $c) {
        $query = self::select(
            'report_stat_user_day.user_id',
            'report_stat_user_day.username',
            DB::raw('SUM(report_stat_user_day.recharge_amount) as recharge_amount'),                // 充值合计
            DB::raw('SUM(report_stat_user_day.transfer_from_parent) as transfer_from_parent'),      // 转账转入
            DB::raw('SUM(report_stat_user_day.withdraw_amount) as withdraw_amount'),                // 提现
            DB::raw('SUM(report_stat_user_day.bets) as bets'),                                      // 投注额
            DB::raw('SUM(report_stat_user_day.bonus) as bonus'),                                    // 奖金派送
            DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),        // 投注返点
            DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),    // 下级返点
            DB::raw('SUM(report_stat_user_day.salary) as salary'),                                  // 日工资
            DB::raw('SUM(report_stat_user_day.dividend) as dividend'),                              // 分红
            DB::raw('SUM(report_stat_user_day.system_transfer_add) as system_transfer_add'),        // 理赔充值
            DB::raw('SUM(report_stat_user_day.gift) as gift'),                                      // 活动礼金
            DB::raw('SUM(report_stat_user_day.cancel) as cancel'),                                  // 撤单亏钱
            DB::raw('SUM(report_stat_user_day.day) as day')                                         // day
        );
        $query->where('report_stat_user_day.user_id', $playerId);

        // 默认显示第一次充值和最后一个了充值时间
        $recharge_first     = date('Ymd',strtotime(Carbon ::today('PRC')));
        $recharge_last      = date('Ymd',strtotime(Carbon::tomorrow('PRC')));
        if (isset($recharge_first,$recharge_last) && $recharge_first && $recharge_last) {
            $query->whereBetween('report_stat_user_day.day', [$recharge_first,$recharge_last]);
        }

        // 日期 开始
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('report_stat_user_day.day', ">=", date('Ymd',strtotime($c['start_time'])));
        }

        // 日期 结束
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('report_stat_user_day.day', "<=", date('Ymd',strtotime($c['end_time'])));
        }

        $item   = $query->first();
        if ($item) {
            // 投注和充值比率
            $item->total_bet      = $item->bets + $item->cancel;
            $item->total_recharge = $item->system_transfer_add + $item->gift + $item->recharge_amount;
            //***********************************************************
            $item->recharge_amount = moneyUnitTransferOut($item->recharge_amount);
            $item->withdraw_amount = moneyUnitTransferOut($item->withdraw_amount);
            if (!$item->recharge_amount && $item->withdraw_amount){
                $recharge_withdraw_ratio = '1:'.round($item->withdraw_amount,1);
            }elseif(!$item->recharge_amount && !$item->withdraw_amount){
                $recharge_withdraw_ratio = '1:1';
            }elseif($item->recharge_amount && !$item->withdraw_amount){
                $recharge_withdraw_ratio = round($item->recharge_amount,1).':1';
            }else{
                $recharge_withdraw_ratio = round($item->recharge_amount/$item->withdraw_amount,1).':1';
            }
            $item->recharge_withdraw_ratio = $recharge_withdraw_ratio;
            //************************************************************
            $item->profit         = $item->commission_from_bet + $item->commission_from_child + $item->bonus + $item->cancel + $item->gift - $item->bets;
            $item->game_profit    = $item->bonus - $item->bets + $item->commission_from_bet;//游戏盈亏
            foreach (StatLogic::$filters as $field) {
                $item->{$field}   = number4($item->{$field});
            }

            $item->game_profit    = number4($item->game_profit);  // 游戏盈亏
            $item->profit         = number4($item->profit);
            $item->recharge_first = empty($recharge_first)?'':date("Y-m-d H:i:s", strtotime($recharge_first));
            $item->recharge_last  = empty($recharge_last)?'':date("Y-m-d H:i:s", strtotime($recharge_last));
        }

        return $item;
    }

    /**
     * @param $uid
     * @return string
     */
    static function getTotalTodayCost($uid)
    {
        $today                = Carbon::today()-> format('Ymd');
        $total_today_bets     = self ::where("user_id", $uid) -> where('day',$today) -> sum('bets');
        $total_today_bets     = number4(moneyUnitTransferIn($total_today_bets));
        return $total_today_bets;
    }

    /**
     * @param $uid
     * @return string
     */
    static function getTotalCost($uid)
    {
        $total_bets     = self ::where("user_id", $uid) -> sum('bets');
        $total_bets     = number4(moneyUnitTransferIn($total_bets));
        return $total_bets;
    }
}
