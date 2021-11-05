<?php namespace App\Console\Commands\Lottery;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Lib\Logic\Lottery\CommissionFastLogic;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryCommission;
use App\Models\Game\LotteryIssue;

/**
 * 返点发放
 * Class CmdSendCommission
 * @package App\Console\Commands\Lottery
 */
class CmdSendCommission extends Command {

    protected $signature    = 'lottery:send_commission {slot}';
    protected $description  = "派发返点!!";

    public function handle()
    {
        $slot  = $this->argument('slot');

        $endTime = strtotime(date("Y-m-d H:i")) + 57;

        // 所有彩种
        $lotteryArr = Lottery::where("status", 1)->pluck("en_name")->toArray();

        do {
            // 获取所有未返点的奖期, 从老奖期开始到新奖期
            $issues = LotteryIssue::whereIn('lottery_sign', $lotteryArr)->where('status_process', 3)->where("status_commission", 0)->orderBy('begin_time', "ASC")->get();

            foreach ($issues as $issue) {

                // 1. 是否处理中
                $cacheKey = "cron_cc_" . $issue->lottery_sign . "_" . $issue->issue . "_" . $slot;
                if (!cache()->add($cacheKey, 1, now()->addMinutes(10))) {
                    Clog::commissionProcess("commission-process-logic-error-处理中-slot($slot)-issue-{$issue->issue}", $issue->lottery_sign, []);
                    continue;
                }

                // 2. 是否已经处理
                $total = LotteryCommission::where("lottery_sign", $issue->lottery_sign)->where("issue", $issue->issue)->whereIn('status', [0, 1])->count();
                if ($total <= 0) {
                    $issue->status_commission          = 1;
                    $issue->time_end_commission        = time();
                    $issue->save();

                    cache()->forget($cacheKey);
                    continue;
                }

                // 3. 派发返点
                $res = CommissionFastLogic::send($issue, $slot);
                if (!is_array($res)) {
                    Clog::commissionProcess("commission-process-logic-fail-slot($slot)-issue-{$issue->issue}-{$res}", $issue->lottery_sign, []);
                } else {
                    Clog::commissionProcess("commission-process-logic-done-slot($slot)-issue-{$issue->issue}-total:{$res['total_item']}-fail:{$res['total_fail']}-user:{$res['total_user']}", $issue->lottery_sign, []);
                }

                cache()->forget($cacheKey);
                continue;
            }

            $now = time();

            if ($now < $endTime - 2) {
                sleep(2);
            }

        } while($now < $endTime);

        return true;
    }

}
