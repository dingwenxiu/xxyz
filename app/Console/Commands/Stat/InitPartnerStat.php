<?php namespace App\Console\Commands\Stat;

use App\Console\Commands\Command;
use App\Models\Report\ReportStatPartnerDay;


/**
 * 初始化商户 统计数据
 * Class InitPartnerStat
 * @package App\Console\Commands\Stat
 */
class InitPartnerStat extends Command {

    protected $signature    = 'stat:initPartnerDay {startDay}';

    protected $description  = "初始化商户统计数据!!";

    public function handle()
    {
        $startDay   = $this->argument('startDay');

        $this->info("init-lottery-day-start-" . date('Y-m-d H:i:s'));

        $lastItem = ReportStatPartnerDay::orderBy('id', 'DESC')->first();

        if (!$lastItem) {
            $startTime  = time();
        } else {
            if ($startDay == "next") {
                $startTime  = strtotime($lastItem->day) + 86400;
            } else {
                $startTime  = strtotime($startDay) + 86400;
            }
        }

        $res = ReportStatPartnerDay::initStat($startTime);

        $this->info("init-lottery-day-end-");
        return true;
    }

}
