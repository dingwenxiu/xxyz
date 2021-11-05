<?php namespace App\Console\Commands\Lottery;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryProject;

/**
 * 开奖
 * Class CmdOpen
 * @package App\Console\Commands\Lottery
 */
class CmdOpen extends Command {

    protected $signature    = 'lottery:open {slot}';
    protected $description  = "开奖!!";

    public function handle()
    {
        $slot  = $this->argument('slot');

        do {
            // 获取所有未返点的奖期, 从老奖期开始到新奖期
            $issues = LotteryIssue::where('status_process', LotteryIssue::STATUS_PROCESS_ENCODE)->orderBy('begin_time', "ASC")->get();

            foreach ($issues as $issue) {

                $cacheKey = "open_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $slot;
                if (!cache()->add($cacheKey, 1, now()->addMinutes(10))) {
                    Clog::gameOpenProcess("open-process-logic-error-slot($slot)-issue:{$issue->issue}-订单正在处理!", $issue->lottery_sign);
                    cache()->forget($cacheKey);
                    return true;
                }

                // 1. 如果没有订单
                $total = LotteryProject::where("lottery_sign", $issue->lottery_sign)->where("issue", $issue->issue)->count();
                if ($total <= 0) {
                    $issue->status_process          = LotteryIssue::STATUS_PROCESS_SEND;
                    $issue->status_commission       = 1;

                    $issue->time_open               = time();
                    $issue->time_send               = time();
                    $issue->time_commission         = time();

                    $issue->time_end_open           = time();
                    $issue->time_end_send           = time();
                    $issue->time_end_commission     = time();
                    $issue->save();

                    cache()->forget($cacheKey);

                    // 追号
                    IssueLogic::trace($issue);

                    continue;
                }

                // 2. 当前槽为0
                $totalSlot = LotteryProject::where("lottery_sign", $issue->lottery_sign)->where("issue", $issue->issue)->where("slot", $slot)->where("status_process", LotteryProject::STATUS_PROCESS_INIT)->count();
                if ($totalSlot <= 0) {
                    Clog::gameOpenProcess("open-process-logic-end-slot($slot)-issue-{$issue->issue}-订单为0", $issue->lottery_sign, []);
                    cache()->forget($cacheKey);
                    continue;
                }

                // 3. 开奖
                $res = IssueLogic::_open($issue, $slot);
                if (!is_array($res)) {
                    Clog::gameOpenProcess("open-process-logic-fail-slot($slot)-issue-{$issue->issue}-{$res}", $issue->lottery_sign, []);
                } else {
                    Clog::gameOpenProcess("open-process-logic-done-slot($slot)-issue-{$issue->issue}-total:{$res['total_count']}-fail:{$res['fail_count']}-win:{$res['win_count']}-he:{$res['he_count']}-lose:{$res['lose_count']}", $issue->lottery_sign, []);
                }


                cache()->forget($cacheKey);

                continue;
            }

            sleep(2);
        } while(true);

        return true;
    }

}
