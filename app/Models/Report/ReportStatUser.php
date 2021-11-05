<?php namespace App\Models\Report;

use App\Models\Base;
use App\Models\Player\Player;
use Illuminate\Support\Facades\DB;
use App\Models\Account\AccountChangeReport;

/**
 * 2019-07　整理
 * Class ReportStatUser
 * @package App\Models\Report
 */
class ReportStatUser extends Base
{
    protected $table = 'report_stat_user';

    public static $field = array(
        'have_bet',
        'first_register',

        'recharge_amount',
        'recharge_count',
        'first_recharge_count',

        'withdraw_amount',
        'withdraw_count',

        'bets',
        'cancel',
        'he_return',
        'commission_from_bet',
        'commission_from_child',
        'bonus',
        'score',

        'transfer_to_child',
        'transfer_from_parent',
        'system_transfer_add',
        'system_transfer_reduce',

        'salary',
        'dividend',
        'gift',

        'team_have_bet',
        'team_first_register',

        'team_recharge_amount',
        'team_recharge_count',
        'team_first_recharge_count',

        'team_withdraw_amount',
        'team_withdraw_count',

        'team_bets',
        'team_cancel',
        'team_he_return',
        'team_commission_from_bet',
        'team_commission_from_child',
        'team_bonus',
        'team_score',

        'team_system_transfer_add',
        'team_system_transfer_reduce',

        'team_salary',
        'team_gift',
    );

    /**
     * 获取列表
     * @param $c
     * @return array
     */
    static function getList($c)
    {
        //查找有帳變的記錄
        $rechargeAccountIds = AccountChangeReport::groupBy('user_id')->pluck('user_id')->toArray();

        $query = Player::select(
            'report_stat_user_day.user_id',
            'users.id',
            'users.username',
            'user_accounts.balance',
            'user_accounts.frozen',
            'users.child_count',
            'report_stat_user_day.score',
            DB::raw('SUM(report_stat_user_day.first_register) as first_register'),
            DB::raw('SUM(report_stat_user_day.have_bet) as have_bet'),

            DB::raw('SUM(report_stat_user_day.recharge_count) as recharge_count'),
            DB::raw('SUM(report_stat_user_day.first_recharge_count) as first_recharge_count'),

            DB::raw('SUM(report_stat_user_day.recharge_amount) as recharge_amount'),
            DB::raw('SUM(report_stat_user_day.withdraw_amount) as withdraw_amount'),
            DB::raw('SUM(report_stat_user_day.bets) as bets'),
            DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),
            DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),
            DB::raw('SUM(report_stat_user_day.bonus) as bonus'),
            DB::raw('SUM(report_stat_user_day.gift) as gift'),
            DB::raw('SUM(report_stat_user_day.salary) as salary'),
            DB::raw('SUM(report_stat_user_day.dividend) as dividend'),

            DB::raw('SUM(report_stat_user_day.team_recharge_count) as team_recharge_count'),
            DB::raw('SUM(report_stat_user_day.team_withdraw_amount) as team_withdraw_amount'),
            DB::raw('SUM(report_stat_user_day.team_bets) as team_bets'),
            DB::raw('SUM(report_stat_user_day.team_commission_from_bet) as team_commission_from_bet'),
            DB::raw('SUM(report_stat_user_day.team_commission_from_child) as team_commission_from_child'),
            DB::raw('SUM(report_stat_user_day.team_bonus) as team_bonus'),
            DB::raw('SUM(report_stat_user_day.team_gift) as team_gift'),
            DB::raw('SUM(report_stat_user_day.team_salary) as team_salary'),
            DB::raw('SUM(report_stat_user_day.system_transfer_add) as system_transfer_add'),
            DB::raw('SUM(report_stat_user_day.system_transfer_reduce) as system_transfer_reduce'),
            DB::raw('SUM(report_stat_user_day.cancel) as cancel'),
            DB::raw('SUM(report_stat_user_day.transfer_from_parent) as transfer_from_parent'),
            DB::raw('SUM(report_stat_user_day.transfer_to_child) as transfer_to_child')

        )->leftJoin('user_accounts', 'user_accounts.user_id', '=', 'users.id')
            ->leftJoin('report_stat_user_day', 'report_stat_user_day.user_id', '=', 'users.id')
            ->groupBy("users.id")
            ->whereIn("users.id",$rechargeAccountIds);

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('users.partner_sign', $c['partner_sign']);
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

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total      = $query->count();
        $totalDatas = $query->get();
        $data       = $query->skip($offset)->take($pageSize)->get();

        //

        $page_recharge_amount        = 0;
        $page_withdraw_amount        = 0;
        $page_bets                   = 0;
        $page_commission_from_bet    = 0;
        $page_commission_from_child  = 0;
        $page_bonus                  = 0;
        $page_gift                   = 0;
        $page_salary                 = 0;
        $page_dividend               = 0;
        $page_system_transfer_add    = 0;
        $page_system_transfer_reduce = 0;
        $page_cancel                 = 0;
        $page_transfer_from_parent   = 0;
        $page_transfer_to_child      = 0;
        $page_profit                 = 0;
        $page_commission             = 0;

        $total_recharge_amount        = 0;
        $total_withdraw_amount        = 0;
        $total_bets                   = 0;
        $total_commission_from_bet    = 0;
        $total_commission_from_child  = 0;
        $total_bonus                  = 0;
        $total_gift                   = 0;
        $total_salary                 = 0;
        $total_dividend               = 0;
        $total_system_transfer_add    = 0;
        $total_system_transfer_reduce = 0;
        $total_cancel                 = 0;
        $total_transfer_from_parent   = 0;
        $total_transfer_to_child      = 0;
        $total_profit                 = 0;
        $total_commission             = 0;


        foreach ($data as $item) {
            //中奖+投返+下返+礼金+工资+分红-投注
            $item->profit       = number4($item->bonus + $item->commission_from_bet + $item->salary +$item->dividend +$item->gift +$item->commission_from_child + $item->cancel - $item->bets);
            $item->team_profit  = number4($item->team_bonus + $item->team_commission_from_bet + $item->team_commission_from_child+ $item->team_salary + $item->team_dividend + $item->team_gift + $item->team_cancel - $item->team_bets);
            $item->commission   = number4($item->commission_from_bet+$item->commission_from_child);


            $page_recharge_amount        = bcadd($page_recharge_amount       , $item->recharge_amount);
            $page_withdraw_amount        = bcadd($page_withdraw_amount       , $item->withdraw_amount);
            $page_bets                   = bcadd($page_bets                  , $item->bets);
            $page_commission_from_bet    = bcadd($page_commission_from_bet   , $item->commission_from_bet);
            $page_commission_from_child  = bcadd($page_commission_from_child , $item->commission_from_child);
            $page_bonus                  = bcadd($page_bonus                 , $item->bonus);
            $page_gift                   = bcadd($page_gift                  , $item->gift);
            $page_salary                 = bcadd($page_salary                , $item->salary);
            $page_dividend               = bcadd($page_dividend              , $item->dividend);
            $page_system_transfer_add    = bcadd($page_system_transfer_add   , $item->system_transfer_add);
            $page_system_transfer_reduce = bcadd($page_system_transfer_reduce, $item->system_transfer_reduce);
            $page_cancel                 = bcadd($page_cancel                , $item->cancel);
            $page_transfer_from_parent   = bcadd($page_transfer_from_parent  , $item->transfer_from_parent);
            $page_transfer_to_child      = bcadd($page_transfer_to_child     , $item->transfer_to_child);
            $page_profit                 = bcadd($page_profit                , $item->profit,4);
            $page_commission             = bcadd($page_commission            , $item->commission,4);

            foreach (ReportStatUserDay::$fieldTransferNumber as $field) {
                $item->{$field} = number4($item->{$field});
            }

        }

        foreach ($totalDatas as $item) {
            //中奖+投返+下返+礼金+工资+分红-投注
            $item->profit       = number4($item->bonus + $item->commission_from_bet + $item->salary +$item->dividend +$item->gift +$item->commission_from_child + $item->cancel - $item->bets);
            $item->team_profit  = number4($item->team_bonus + $item->team_commission_from_bet + $item->team_commission_from_child+ $item->team_salary + $item->team_dividend + $item->team_gift + $item->team_cancel - $item->team_bets);
            $item->commission   = number4($item->commission_from_bet+$item->commission_from_child);


            $total_recharge_amount        = bcadd($total_recharge_amount       , $item->recharge_amount);
            $total_withdraw_amount        = bcadd($total_withdraw_amount       , $item->withdraw_amount);
            $total_bets                   = bcadd($total_bets                  , $item->bets);
            $total_commission_from_bet    = bcadd($total_commission_from_bet   , $item->commission_from_bet);
            $total_commission_from_child  = bcadd($total_commission_from_child , $item->commission_from_child);
            $total_bonus                  = bcadd($total_bonus                 , $item->bonus);
            $total_gift                   = bcadd($total_gift                  , $item->gift);
            $total_salary                 = bcadd($total_salary                , $item->salary);
            $total_dividend               = bcadd($total_dividend              , $item->dividend);
            $total_system_transfer_add    = bcadd($total_system_transfer_add   , $item->system_transfer_add);
            $total_system_transfer_reduce = bcadd($total_system_transfer_reduce, $item->system_transfer_reduce);
            $total_cancel                 = bcadd($total_cancel                , $item->cancel);
            $total_transfer_from_parent   = bcadd($total_transfer_from_parent  , $item->transfer_from_parent);
            $total_transfer_to_child      = bcadd($total_transfer_to_child     , $item->transfer_to_child);
            $total_profit                 = bcadd($total_profit                , $item->profit,4);
            $total_commission             = bcadd($total_commission            , $item->commission,4);

            foreach (ReportStatUserDay::$fieldTransferNumber as $field) {
                $item->{$field} = number4($item->{$field});
            }

        }

        $d['page_recharge_amount']        = number4($page_recharge_amount);
        $d['page_withdraw_amount']        = number4($page_withdraw_amount);
        $d['page_bets']                   = number4($page_bets);
        $d['page_commission_from_bet']    = number4($page_commission_from_bet);
        $d['page_commission_from_child']  = number4($page_commission_from_child);
        $d['page_bonus']                  = number4($page_bonus);
        $d['page_gift']                   = number4($page_gift);
        $d['page_salary']                 = number4($page_salary);
        $d['page_dividend']               = number4($page_dividend);
        $d['page_system_transfer_add']    = number4($page_system_transfer_add);
        $d['page_system_transfer_reduce'] = number4($page_system_transfer_reduce);
        $d['page_cancel']                 = number4($page_cancel);
        $d['page_transfer_from_parent']   = number4($page_transfer_from_parent);
        $d['page_transfer_to_child']      = number4($page_transfer_to_child);
        $d['page_profit']                 = $page_profit;
        $d['page_commission']             = $page_commission;

        $d['total_recharge_amount']        = number4($total_recharge_amount);
        $d['total_withdraw_amount']        = number4($total_withdraw_amount);
        $d['total_bets']                   = number4($total_bets);
        $d['total_commission_from_bet']    = number4($total_commission_from_bet);
        $d['total_commission_from_child']  = number4($total_commission_from_child);
        $d['total_bonus']                  = number4($total_bonus);
        $d['total_gift']                   = number4($total_gift);
        $d['total_salary']                 = number4($total_salary);
        $d['total_dividend']               = number4($total_dividend);
        $d['total_system_transfer_add']    = number4($total_system_transfer_add);
        $d['total_system_transfer_reduce'] = number4($total_system_transfer_reduce);
        $d['total_cancel']                 = number4($total_cancel);
        $d['total_transfer_from_parent']   = number4($total_transfer_from_parent);
        $d['total_transfer_to_child']      = number4($total_transfer_to_child);
        $d['total_profit']                 = $total_profit;
        $d['total_commission']             = $total_commission;


        return ['data' => $data, 'total' => $total,'stat'=>$d, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

	/**
	 * 获取用户格式化的数据
	 * @param $c
	 * @param $userId
	 * @return bool
	 */
    static function findFormatDataByUserId($userId,$c = null) {
        $query = self::where("user_id", $userId);

        // 时间搜索 时间开始
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('updated_at', ">=", strtotime($c['start_time']));
        }

        // 时间搜索 时间结束
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('updated_at', "<=", strtotime($c['end_time']));
        }

        $stat = $query->first();
        if (!$stat) {
            return false;
        }

        $fields = [
            'recharge_amount',        // 充值金额
            'withdraw_amount',        // 取款数量
            'bets',                   // 下注
            'cancel',                 // 取消
            'points_self',            // 投注点数
            'points_child',           // 下级投注点数
            'bonus',                  // 奖金
            'score',                  // 得分
            'salary',                 // 个人日工资
            'dividend',               // 个人分红
            'gift',                   // 活动礼金
            'system_transfer_add',    // 系统理赔增加
            'system_transfer_reduce', // 系统理赔减少
            'bet_times'               // 下注次数
        ];

        // 投注和充值比率
        $stat->total_bet        = $stat->bets + $stat->cancel;
        $stat->total_recharge   = $stat->system_transfer_add + $stat->gift + $stat->recharge_amount;

        if ($stat->total_recharge > 0) {
            $stat->bet_times    = $stat->total_bet / $stat->total_recharge * 10000;
        } else {
            $stat->bet_times    = 0 ;
        }

        foreach ($fields as $field) {
            $stat->{$field} = number4($stat->{$field});
        }

        return $stat;
    }
}
