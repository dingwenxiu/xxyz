<?php namespace App\Console\Commands\Stat;

use App\Console\Commands\Command;

use App\Models\Report\ReportStatLotteryDay;


class InitLotteryStat extends Command {

    protected $signature    = 'stat:initLotteryDay {startDay}';

    protected $description  = "初始化彩种统计数据!!";

    public function handle()
    {
        $startDay   = $this->argument('startDay');

        $this->info("init-lottery-day-start" . date('Y-m-d H:i:s'));

        $lastItem = ReportStatLotteryDay::orderBy('id', 'DESC')->first();

        if (!$lastItem) {
            $startTime  = time();
        } else {
            if ($startDay == "next") {
                $startTime  = strtotime($lastItem->day) + 86400;
            } else {
                $startTime  = strtotime($startDay) + 86400;
            }
        }

        ReportStatLotteryDay::initStat($startTime);

        $this->info("init-lottery-day-end" );
        return true;
    }

}
