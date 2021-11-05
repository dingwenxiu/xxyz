<?php namespace App\Console\Commands\Stat;

use App\Console\Commands\Command;
use App\Models\Report\ReportStatUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


/**
 * 统计 玩家整体量
 * Class playerTotal
 * @package App\Console\Commands\Stat
 */
class CmdStatPlayerTotal extends Command {

    protected $signature    = 'stat:playerTotal {action}';
    protected $description  = "统计玩家整体量!!";

    public function handle()
    {
        $day   = $this->argument('action', 'last');

        // 判定初始day
        if ($day == "all") {
            $day  = '';
        } else {
            $day  = Carbon::yesterday()->format("Ymd");
        }

        $key = "stat_player_total_" . $day;

        if (!cache()->add($key, $day, now()->addDays(10))) {
            $this->info("对不起, {$day}-player all 统计中");
            return true;
        }

        $totalCount = ReportStatUser::count();

        $pageSize   = 2000;
        $totalPage  = ceil($totalCount / $pageSize);

        $i = 0;
        do {
            $offset = $i * $pageSize;
            if ($day) {
                $query = ReportStatUser::select(
                    'report_stat_user.user_id',
                    'report_stat_user.partner_sign',
                    'report_stat_user_day.first_register',
                    'report_stat_user_day.have_bet',

                    'report_stat_user_day.recharge_count',
                    'report_stat_user_day.first_recharge_count',
                    'report_stat_user_day.recharge_amount',

                    'report_stat_user_day.withdraw_amount',
                    'report_stat_user_day.withdraw_account',

                    'report_stat_user_day.bets',
                    'report_stat_user_day.cancel',
                    'report_stat_user_day.he_return',
                    'report_stat_user_day.commission_from_bet',
                    'report_stat_user_day.commission_from_child',
                    'report_stat_user_day.bonus',
                    'report_stat_user_day.score',

                    'report_stat_user_day.transfer_to_child',
                    'report_stat_user_day.transfer_from_parent',
                    'report_stat_user_day.system_transfer_add',
                    'report_stat_user_day.system_transfer_reduce',

                    'report_stat_user_day.gift',
                    'report_stat_user_day.salary',
                    'report_stat_user_day.dividend',


                    'report_stat_user_day.team_first_register',
                    'report_stat_user_day.team_have_bet',

                    'report_stat_user_day.team_recharge_count',
                    'report_stat_user_day.team_first_recharge_count',
                    'report_stat_user_day.team_recharge_amount',

                    'report_stat_user_day.team_withdraw_amount',
                    'report_stat_user_day.team_withdraw_count',

                    'report_stat_user_day.team_bets',
                    'report_stat_user_day.team_cancel',
                    'report_stat_user_day.team_he_return',
                    'report_stat_user_day.team_commission_from_bet',
                    'report_stat_user_day.team_commission_from_child',
                    'report_stat_user_day.team_bonus',
                    'report_stat_user_day.team_score',

                    'report_stat_user_day.team_gift',
                    'report_stat_user_day.team_salary',
                    'report_stat_user_day.team_system_transfer_add',
                    'report_stat_user_day.team_system_transfer_reduce'

                )
                    ->leftJoin('report_stat_user_day', 'report_stat_user_day.user_id', '=', 'report_stat_user.user_id')
                    ->where("report_stat_user_day.day", $day)
                    ->orderBy("report_stat_user.id", "ASC");
            } else {
                $query = ReportStatUser::select(
                    'report_stat_user.user_id',
                    'report_stat_user.partner_sign',
                    DB::raw('SUM(report_stat_user_day.first_register) as first_register'),
                    DB::raw('SUM(report_stat_user_day.have_bet) as have_bet'),

                    DB::raw('SUM(report_stat_user_day.recharge_count)   as recharge_count'),
                    DB::raw('SUM(report_stat_user_day.first_recharge_count)   as first_recharge_count'),
                    DB::raw('SUM(report_stat_user_day.recharge_amount)  as recharge_amount'),

                    DB::raw('SUM(report_stat_user_day.withdraw_amount)  as withdraw_amount'),
                    DB::raw('SUM(report_stat_user_day.withdraw_count)  as withdraw_count'),

                    DB::raw('SUM(report_stat_user_day.bets) as bets'),
                    DB::raw('SUM(report_stat_user_day.cancel) as cancel'),
                    DB::raw('SUM(report_stat_user_day.he_return) as he_return'),
                    DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),
                    DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),
                    DB::raw('SUM(report_stat_user_day.bonus) as bonus'),
                    DB::raw('SUM(report_stat_user_day.score) as score'),

                    DB::raw('SUM(report_stat_user_day.gift) as gift'),
                    DB::raw('SUM(report_stat_user_day.salary) as salary'),
                    DB::raw('SUM(report_stat_user_day.dividend) as dividend'),

                    DB::raw('SUM(report_stat_user_day.transfer_to_child)  as transfer_to_child'),
                    DB::raw('SUM(report_stat_user_day.transfer_from_parent)  as transfer_from_parent'),
                    DB::raw('SUM(report_stat_user_day.system_transfer_add)  as system_transfer_add'),
                    DB::raw('SUM(report_stat_user_day.system_transfer_reduce)  as system_transfer_reduce'),


                    DB::raw('SUM(report_stat_user_day.team_first_register) as team_first_register'),
                    DB::raw('SUM(report_stat_user_day.team_have_bet) as team_have_bet'),

                    DB::raw('SUM(report_stat_user_day.team_recharge_count)   as team_recharge_count'),
                    DB::raw('SUM(report_stat_user_day.team_first_recharge_count)   as team_first_recharge_count'),
                    DB::raw('SUM(report_stat_user_day.team_recharge_amount)  as team_recharge_amount'),

                    DB::raw('SUM(report_stat_user_day.team_withdraw_amount) as team_withdraw_amount'),
                    DB::raw('SUM(report_stat_user_day.team_withdraw_count) as team_withdraw_count'),

                    DB::raw('SUM(report_stat_user_day.team_bets) as team_bets'),
                    DB::raw('SUM(report_stat_user_day.team_cancel) as team_cancel'),
                    DB::raw('SUM(report_stat_user_day.team_he_return) as team_he_return'),

                    DB::raw('SUM(report_stat_user_day.team_commission_from_bet) as team_commission_from_bet'),
                    DB::raw('SUM(report_stat_user_day.team_commission_from_child) as team_commission_from_child'),
                    DB::raw('SUM(report_stat_user_day.team_bonus) as team_bonus'),
                    DB::raw('SUM(report_stat_user_day.team_score) as team_score'),

                    DB::raw('SUM(report_stat_user_day.team_gift) as team_gift'),
                    DB::raw('SUM(report_stat_user_day.team_salary) as team_salary'),
                    DB::raw('SUM(report_stat_user_day.team_system_transfer_add)  as team_system_transfer_add'),
                    DB::raw('SUM(report_stat_user_day.team_system_transfer_reduce)  as team_system_transfer_reduce')

                )
                    ->leftJoin('report_stat_user_day', 'report_stat_user_day.user_id', '=', 'report_stat_user.user_id')
                    ->groupBy("report_stat_user_day.user_id");
            }

            $data = $query->skip($offset)->take($pageSize)->get();

            foreach ($data as $item) {
                $updateSql = '';
                $addStr = '';
                foreach (ReportStatUser::$field as $field) {
                    $updateSql .= $addStr . "`{$field}` = `{$field}` + {$item->{$field}}";
                    $addStr = ',';
                }

                $sql = "update `report_stat_user` set {$updateSql} where `partner_sign` = '{$item->partner_sign}' and `user_id` ='{$item->user_id}'";
                db()->update($sql);
            }

            $i ++;
        } while ($i < $totalPage);

        cache()->forget($key);
        return true;
    }

}
