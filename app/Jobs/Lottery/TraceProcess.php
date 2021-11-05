<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Lib\Logic\Lottery\TraceLogic;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryTraceList;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019.10
 * 追号
 * Class TraceProcess
 * @package App\Jobs\Lottery
 */
class TraceProcess implements ShouldQueue
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

        if ($this->attempts() > 3) {
            return true;
        }

        $currentIssue      = LotteryIssue::find($this->issue->id);
        if (!$currentIssue) {
            Clog::traceProcess("trace-process-job-error-不存在的奖期-{$this->issue->issue}!", $this->issue->lottery_sign);
            return true;
        }

        // 处理的是下一期
        $issue = LotteryIssue::where('lottery_sign', $currentIssue->lottery_sign)->where('begin_time', '>', $currentIssue->begin_time)->orderBy("begin_time", "ASC")->first();

        if (!$issue) {
            Clog::traceProcess("trace-process-job-error-不存在的下一期奖期-{$currentIssue->issue}!", $currentIssue->lottery_sign);
            return true;
        }

        $cacheKey = "tp_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $this->slot;
        if (!cache()->add($cacheKey, 1, now()->addMinutes(3))) {
            Clog::traceProcess("trace-process-job-slot($this->slot)-issue:{$issue->issue}-error-订单正在处理!", $issue->lottery_sign);
            cache()->forget($cacheKey);
            return true;
        }

        // 开始
        Clog::traceProcess("trace-process-job-slot($this->slot)-issue:{$issue->issue}-start", $issue->lottery_sign);

        if ($currentIssue->status_process != LotteryIssue::STATUS_PROCESS_SEND) {
            Clog::traceProcess("trace-process-job-slot($this->slot)-issue-{$issue->issue}-error-开奖未完成($issue->status_process)!", $issue->lottery_sign);
            cache()->forget($cacheKey);
            return true;
        }

        // 1. 获取所有中奖注单
        $totalSlotCount = LotteryTraceList::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where("slot", $this->slot)->where('status', LotteryTraceList::STATUS_TRACE_INIT)->count();

        // 开始
        Clog::traceProcess("trace-process-job-start-slot($this->slot)-issue-{$issue->issue}-total:{$totalSlotCount}", $issue->lottery_sign, $this->data);

        // 2. 是否有注单
        if ($totalSlotCount <= 0) {
            Clog::traceProcess("trace-process-job-end-slot($this->slot)-issue-{$issue->issue}-总订单数为0", $issue->lottery_sign, $this->data);
            $totalCount = LotteryTraceList::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status', LotteryTraceList::STATUS_TRACE_INIT)->count();
            if ($totalCount <= 0) {
                $issue->status_trace        = 1;
                $issue->time_end_trace      = time();
                $issue->save();
            }

            $this->checkNextIssue($currentIssue);

            //  触发结束
            $callTriggerEndKey = "ctek_" . $currentIssue->lottery_sign . "_" . $currentIssue->issue;
            if (cache()->add($callTriggerEndKey, 1, now()->addMinutes(2))) {
                // 统计本期资金
                IssueLogic::triggerIssueOpenEnd($currentIssue);
            }

            cache()->forget($cacheKey);
            return true;
        }

        $failCount = 0;

        Clog::gameStat("trace-stat-start-slot($this->slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);
        try {
            $pageSize   = 1000;
            $totalPage  = ceil($totalSlotCount / $pageSize);

            $i = 0;
            do {
                $offset     = $pageSize * $i;
                $items      = LotteryTraceList::where('lottery_sign', $issue->lottery_sign)
                                ->where('issue', $issue->issue)
                                ->where("slot", $this->slot)
                                ->where('status', LotteryTraceList::STATUS_TRACE_INIT)
                                ->skip($offset)->take($pageSize)->get();

                // 追号
                foreach ($items as $traceDetail) {
                    $res = TraceLogic::trace($traceDetail);
                    if ($res !== true) {
                        $failCount ++;
                    }
                }

                $i ++;
            } while($i <= $totalPage);

        } catch (\Exception $e) {
            Clog::traceProcess("trace-process-job-exception-slot($this->slot)-issue-{$issue->issue}-{$e->getMessage()}", $issue->lottery_sign, []);
            cache()->forget($cacheKey);
            telegramSend("send_job_exception", "trace-process-fail-{$issue->lottery_sign}-s-($this->slot)-issue-{$issue->issue}-{$e->getMessage()}");

            self::release(1);
            return true;
        }

        // 检测追号是否完成
        $totalCount = LotteryTraceList::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status', LotteryTraceList::STATUS_TRACE_INIT)->count();
        if ($totalCount <= 0) {
            $issue->status_trace        = 1;
            $issue->time_end_trace      = time();
            $issue->save();


            $this->checkNextIssue($issue);
        }

        cache()->forget($cacheKey);
        Clog::traceProcess("trace-process-job-end-slot($this->slot)-issue-{$issue->issue}-end-fail({$failCount})", $issue->lottery_sign, []);

        Clog::gameStat("trace-stat-end-slot($this->slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);
        return true;
    }

    /**
     * 检查是否需要开奖
     * @param $issue
     * @return bool
     * @throws \Exception
     */
    public function checkNextIssue($issue) {
        // 如果后面有录号未开的奖期　则开掉
        $needOpenIssue = LotteryIssue::where('lottery_sign', $issue->lottery_sign)->where("begin_time", ">", $issue->begin_time)->where("status_process",  LotteryIssue::STATUS_PROCESS_ENCODE)->orderBy("begin_time", "ASC")->first();
        if ($needOpenIssue) {
            IssueLogic::open($needOpenIssue);
        }

        return true;
    }
}
