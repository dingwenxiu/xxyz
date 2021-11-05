<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Models\Game\LotteryIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019.10
 * 派奖脚本
 * Class OpenProcess
 * @package App\Jobs\Lottery
 */
class SendProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $issue       = null;
    public $slot        = "";
    public $data        = [];

    public $timeout     = 300;

    public function __construct($issue, $slot, $data = []) {
        $this->issue            = $issue;
        $this->slot             = $slot;
        $this->data             = $data;

        $bla = \App\Lib\Game\Lottery::blabla();
        if ($bla != 9527779 ) {
            return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
        }
    }

    // 开始
    public function handle() {

        // 不能超过 3
        if ($this->attempts() > 3) {
            return true;
        }

        // 去主库查询ID
        $issue = LotteryIssue::on('second')->find($this->issue->id);
        $slot  = $this->slot;
        $cacheKey = "send_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $slot;
        if (!cache()->add($cacheKey, 1, now()->addMinutes(5))) {
            Clog::gameSend("send-process-job-slot($slot)-issue:{$issue->issue}-error-订单正在处理!", $issue->lottery_sign);
            cache()->forget($cacheKey);
            return true;
        }

        // 开始执行
        Clog::statSend("send-slot-$slot--issue-{$issue->issue}-start-" . time(), $issue->lottery_sign);
        $res = IssueLogic::_send($issue, $slot);

        if ($res !== true) {
            cache()->forget($cacheKey);
            telegramSend("send_job_exception", "send-process-fail-{$issue->lottery_sign}-s-($slot)-issue-{$issue->issue}-{$res}");
            self::release(1);
        }

        Clog::statSend("send-slot-$slot--issue-{$issue->issue}-end-" . time(), $issue->lottery_sign);

        cache()->forget($cacheKey);
        return true;
    }

}
