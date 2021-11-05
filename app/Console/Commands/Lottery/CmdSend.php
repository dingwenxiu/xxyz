<?php namespace App\Console\Commands\Lottery;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Models\Game\LotteryIssue;

/**
 * 派奖 = 备用脚本 1
 * Class CmdSend
 * @package App\Console\Commands\Lottery
 */
class CmdSend extends Command {

    protected $signature    = 'lottery:send {slot}';
    protected $description  = "派奖!!";

    public function handle()
    {
        $slot  = $this->argument('slot');

        $endTime = strtotime(date("Y-m-d H:i")) + 58 + 60 * 30;

        do {
            // 获取所有未返点的奖期, 从老奖期开始到新奖期
            $issues = LotteryIssue::where('status_process', LotteryIssue::STATUS_PROCESS_OPEN)->orderBy('begin_time', "ASC")->get();

            foreach ($issues as $issue) {
                $cacheKey = "send_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $slot;
                if (!cache()->add($cacheKey, 1, now()->addMinutes(5))) {
                    Clog::gameSend("send-process-job-slot($slot)-issue:{$issue->issue}-error-订单正在处理!", $issue->lottery_sign);
                    cache()->forget($cacheKey);
                    return true;
                }

                // 开始执行
                Clog::statSend("send-slot-$slot--issue-{$issue->issue}-start-" . time(), $issue->lottery_sign);
                IssueLogic::_send($issue, $slot);
                Clog::statSend("send-slot-$slot--issue-{$issue->issue}-end-" . time(), $issue->lottery_sign);
            }

            $now = time();
            if ($now < $endTime - 2) {
                sleep(2);
            }

        } while($now < $endTime);

        return true;
    }

}
