<?php namespace App\Console\Commands\Stat;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Models\Partner\Partner;
use App\Models\Report\ReportStatPartnerDay;
use Illuminate\Support\Carbon;

/**
 * 平台日统计　+ 通知
 * Class CmdStat
 * @package App\Console\Commands\Stat
 */
class CmdStatPartnerDay extends Command {

    protected $signature    = 'stat:partner {action}';
    protected $description  = "统计!!";

    public function handle()
    {
        $day   = $this->argument('action', 'last');

        // 判定初始day
        if ($day == "last") {
            $day  = Carbon::yesterday()->format("Ymd");
        }

        $partnerList = Partner::getOptions();
        foreach ($partnerList as $sign => $name) {
            Clog::statUser("stat-user-start-{$name}-" . time(), []);

            ReportStatPartnerDay::doDayStat($day, $sign);

            Clog::statUser("stat-user-end-{$name}-" . time());
        }

        return true;
    }

}
