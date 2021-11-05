<?php namespace App\Console\Commands\Lottery;

use App\Console\Commands\Command;
use App\Jobs\Lottery\SelfEncode;
use App\Lib\Clog;
use App\Models\Game\Lottery;

/**
 * 自开彩 - 随机录号脚本
 * Class CmdSelfEncode
 * @package App\Console\Commands\Lottery
 */
class CmdSelfEncode extends Command {

    protected $signature    = 'lottery:selfEncode {series_id}';
    protected $description  = "自开彩开奖!!";

    public function handle()
    {
        $seriesId   = $this->argument('series_id');
        $lotteryList    = Lottery::getLotteryBySeries($seriesId, true);

        foreach ($lotteryList as $lotterySign) {
            Clog::gameEncode("encode-job-start", $lotterySign, []);
            jtq(new SelfEncode($lotterySign), 'self_open');
        }

        return true;
    }

}
