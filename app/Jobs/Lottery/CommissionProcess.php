<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\Lottery\CommissionFastLogic;
use App\Models\Game\LotteryIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019.10
 * 返点
 * Class CommissionProcess
 * @package App\Jobs\Lottery
 */
class CommissionProcess implements ShouldQueue
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

        // hash
        $bla = \App\Lib\Game\Lottery::blabla();
        if ($bla != 9527779 ) {
            return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
        }
    }

    // 开始
    public function handle() {

        // 不能超过 3次
        if ($this->attempts() > 3) {
            return true;
        }

        $slot   = $this->slot;

        $issue = LotteryIssue::on('second')->find($this->issue->id);
        if (!$issue) {
            Clog::commissionProcess("commission-process-job-error-不存在的奖期-{$issue->issue->issue}!");
            return true;
        }

        $cacheKey = "cp_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $slot;
        if (!cache()->add($cacheKey, 1, now()->addMinutes(3))) {
            Clog::commissionProcess("commission-process-job-slot($slot)-issue:{$issue->issue}-error-正在处理!", $issue->lottery_sign);
            cache()->forget($cacheKey);
            return true;
        }

        // 开始
        Clog::commissionProcess("commission-process-job-slot($slot)-issue:{$issue->issue}-status:{$issue->status_process}-start", $issue->lottery_sign);

        try {
            // 1. 奖期未开奖
            if ($issue->status_process <  LotteryIssue::STATUS_PROCESS_OPEN) {
                Clog::commissionProcess("commission-process-job-slot($slot)-issue-{$issue->issue}-error-奖期开奖未完成($issue->status_process)!", $issue->lottery_sign);
                cache()->forget($cacheKey);
                return true;
            }

            // 2. 奖期返点以及给你发放
            if ($issue->status_commission == 1) {
                Clog::commissionProcess("commission-process-job-slot($slot)-issue-{$issue->issue}-error-奖期已经追号($issue->status_commission)!", $issue->lottery_sign);
                cache()->forget($cacheKey);
                return true;
            }

            // 3. 派发返点
            $res = CommissionFastLogic::send($issue, $slot);
            if (!is_array($res)) {
                Clog::commissionProcess("commission-process-logic-fail-slot($slot)-issue-{$issue->issue}-{$res}", $issue->lottery_sign, []);
                telegramSend("send_exception", "commission-process-fail-{$issue->lottery_sign}-slot-($slot)-issue-{$issue->issue}-{$res}");

                self::release(2);
            } else {

                if ($res['total_fail'] > 0) {
                    telegramSend("send_exception", "commission-process-fail-{$issue->lottery_sign}-slot-($slot)-issue-{$issue->issue}-失败-{$res['total_fail']}");

                    cache()->forget($cacheKey);
                    self::release(2);
                }

                Clog::commissionProcess("commission-process-logic-done-slot($slot)-issue-{$issue->issue}-total:{$res['total_item']}-fail:{$res['total_fail']}-user:{$res['total_user']}", $issue->lottery_sign, $res);
            }

            cache()->forget($cacheKey);

        } catch (\Exception $e) {
            telegramSend("send_job_exception", "commission-process-fail-{$issue->lottery_sign}-slot-($slot)-issue-{$issue->issue}-{$e->getMessage()}");
            Clog::commissionProcess("commission-process-logic-exception-slot($slot)-issue-{$issue->issue}-status:{$issue->status_process}-error:{$e->getMessage()}", $issue->lottery_sign);

            self::release(2);
        }

        return true;
    }

}
