<?php namespace App\Console\Commands\Lottery;

use App\Console\Commands\Command;
use App\Lib\Logic\Lottery\LotteryLogic;
use App\Models\Game\Lottery;

// 生成玩法
class CmdGenMethods extends Command {

    protected $signature    = 'lottery:genMethods';
    protected $description  = "lottery:genMethods 生成玩法到数据库!!";

    public function handle()
    {
        $totalCount = 0;
        $lotteries  = Lottery::where('status', 1)->get();
        $bar        = $this->output->createProgressBar(count($lotteries));
        foreach ($lotteries as $lottery) {
            $res = LotteryLogic::genMethodByLottery($lottery);
            $bar->advance();
            $this->info("--彩种{$lottery->cn_name}插入条数:" . $res);
            $totalCount += $res;
        }

        $bar->finish();
        $this->info("总计插入条数:" . $totalCount);
    }

}
