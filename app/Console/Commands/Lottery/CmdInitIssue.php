<?php namespace App\Console\Commands\Lottery;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Models\Game\LotteryIssue;
use App\Models\Game\Lottery;

// 生成奖期
class CmdInitIssue extends Command {

    protected $signature    = 'lottery:initIssue';
    protected $description  = "lottery:initIssue 初始化奖期!!";

    public function handle()
    {

        $allLottery = [];
        $allData    = Lottery::where('status', 1)->get();
        foreach ($allData as $data) {
            $allLottery[] = $data->en_name;
        }


        foreach ($allLottery as $lotteryId) {
            $lottery = Lottery::findBySign($lotteryId);
            if (!$lottery) {
                Clog::issueGen("无效的彩种-" . $lotteryId);
                continue;
            }

            LotteryIssue::where('lottery_sign', $lotteryId)->where('end_time', '<=', time())->update([
                'status_process'    => LotteryIssue::STATUS_PROCESS_SEND,
                'status_trace'      => 1,
                'status_commission' => 1,
            ]);

            LotteryIssue::where('lottery_sign', $lotteryId)->where('begin_time', '<=', time())->update([
                'status_trace'      => 1,
            ]);
        }
        return true;
    }

}
