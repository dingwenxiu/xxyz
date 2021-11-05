<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Lib\Telegram\TelegramTrait;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryProject;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019.10
 * 计奖脚本
 * Class OpenProcess
 * @package App\Jobs\Lottery
 */
class OpenProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use TelegramTrait;

    public $issueId     = null;
    public $slot        = "";
    public $data        = [];

    public $timeout     = 300;

    public function __construct($issue, $slot, $data = []) {
        $this->issueId          = $issue;
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

        $issue = LotteryIssue::on('second')->find($this->issueId);
        if (!$issue) {
            Clog::gameOpenProcess("open-process-logic-error-slot($this->slot)-issueId:{$this->issueId}-无效的issueId!");
            return true;
        }

        $slot  = $this->slot;

        $cacheKey = "open_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $slot;
        if (!cache()->add($cacheKey, 1, now()->addMinutes(10))) {
            Clog::gameOpenProcess("open-process-logic-error-slot($slot)-issue:{$issue->issue}-订单正在处理!", $issue->lottery_sign);
            cache()->forget($cacheKey);
            return true;
        }

        try {
            // 1. 当前槽订单为 未开　未派　未启
            $totalSlot = LotteryProject::where("lottery_sign", $issue->lottery_sign)->where("issue", $issue->issue)->where("slot", $slot)->whereIn("status_process", [LotteryProject::STATUS_PROCESS_INIT, LotteryProject::STATUS_PROCESS_OPEN, LotteryProject::STATUS_PROCESS_SEND])->count();
            if ($totalSlot <= 0) {
                Clog::gameOpenProcess("open-process-logic-end-slot($slot)-issue-{$issue->issue}-status:{$issue->status_process}-订单为0", $issue->lottery_sign, []);
                cache()->forget($cacheKey);
                return true;
            }

            // 2. 开奖
            $res = IssueLogic::_open($issue, $slot);
            if (!is_array($res)) {
                Clog::gameOpenProcess("open-process-logic-fail-slot($slot)-issue-{$issue->issue}-开奖不成功-status:{$issue->status_process}-{$res}", $issue->lottery_sign, []);

                cache()->forget($cacheKey);

                if ($res === 2) {
                    self::release(2);
                    return true;
                }
                return false;
            } else {
                Clog::gameOpenProcess("open-process-logic-done-slot($slot)-issue-{$issue->issue}-total:{$res['total_count']}-fail:{$res['fail_count']}-win:{$res['win_count']}-he:{$res['he_count']}-lose:{$res['lose_count']}", $issue->lottery_sign, []);
            }

            cache()->forget($cacheKey);
        } catch (\Exception $e) {
            cache()->forget($cacheKey);

            $msg = $e->getMessage() . "|" . $e->getLine() . "|" .  $e->getFile();
            telegramSend("send_job_exception", "open-process-fail-{$issue->lottery_sign}-s-($slot)-issue-{$issue->issue}-{$msg}");

            // 异常了重新开奖
            self::release(1);
        }

        return true;
    }

}
