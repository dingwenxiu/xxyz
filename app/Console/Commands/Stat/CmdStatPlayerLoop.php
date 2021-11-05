<?php namespace App\Console\Commands\Stat;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Lib\Logic\Stat\StatLogic;

/**
 * 统计 玩家　loop
 * Class CmdStatPlayerLoop
 * @package App\Console\Commands\Stat
 */
class CmdStatPlayerLoop extends Command {

    protected $signature    = 'stat:player {startTime}';
    protected $description  = "统计!!";

    public function handle()
    {
        $dayM = date("YmdHi");
        Clog::statUser("stat-user-start-{$dayM}-" . time(), []);
        $stat = StatLogic::doPlayerStat();
        Clog::statUser("stat-user-end-{$dayM}-" . time(), ['res' => $stat]);
        return true;
    }

}
