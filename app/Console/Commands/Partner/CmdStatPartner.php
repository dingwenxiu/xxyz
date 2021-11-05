<?php namespace App\Console\Commands\Partner;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Models\Partner\Partner;
use App\Models\Report\ReportStatUserDay;
use Illuminate\Support\Facades\DB;


/**
 * 统计用户
 * Class CmdStatPartner
 * @package App\Console\Commands\Partner
 */
class CmdStatPartner extends Command {

    protected $signature    = 'partner:statDay {day} {focus}';
    protected $description  = "partner:statDay 每日统计!!";

    public function handle()
    {
        $day    = $this->argument('day');
        if ($day == "now") {
            $day = date("Ymd");
        }

        $focus  = $this->argument('focus', false);

        Clog::statPartner("stat-partner-start-day：{$day}-focus-{$focus}");

        $partners = Partner::where("status", 1)->get();
        foreach ($partners as $partner) {

            $R = ReportStatUserDay::select(
                db()->raw('SUM(first_register) as first_register'),

                db()->raw('SUM(recharge_amount) as recharge_amount'),
                db()->raw('SUM(recharge_count) as recharge_count'),
                db()->raw('SUM(first_recharge_count) as first_recharge_count'),

                db()->raw('SUM(withdraw_amount) as withdraw_amount'),
                db()->raw('SUM(withdraw_count) as withdraw_count'),

                db()->raw('SUM(bets) as bets'),
                db()->raw('SUM(cancel) as cancel'),
                db()->raw('SUM(bonus) as bonus'),

                db()->raw('SUM(commission_self) as commission_self'),
                db()->raw('SUM(commission_child) as commission_child'),

                db()->raw('SUM(transfer_to_child) as transfer_to_child'),
                db()->raw('SUM(transfer_from_parent) as transfer_from_parent'),

                db()->raw('SUM(score) as score'),

                db()->raw('SUM(gift) as gift'),
                db()->raw('SUM(salary) as salary'),
                db()->raw('SUM(dividend) as dividend'),

                db()->raw('SUM(system_transfer_add) as system_transfer_add'),
                db()->raw('SUM(system_transfer_reduce) as system_transfer_reduce'),
            );

            $R->where('partner_sign', $partner->sign);
            $R->where('day',  $day);

            $result =  $R->first();

            $exitItem = ReportStatUserDay::where("partner_sign", $partner->sign)->where("day", $day)->first();
            if ($exitItem || !$focus) {
                $data           = $result->toArray();
                array_filter($data);

                $data['partner_sign']    = $partner->sign;
                $data['partner_name']    = $partner->name;
                $data['day']    = $day;
                DB::table('report_stat_partner_day')->insert($data);
            } else {
                Clog::statPartner("stat-partner-exit-day：{$day}-partner:{$partner->sign}");
            }
        }

        Clog::statPartner("stat-partner-done-day：{$day}-focus-{$focus}");
    }

}
