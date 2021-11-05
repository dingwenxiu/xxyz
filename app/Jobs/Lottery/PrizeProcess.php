<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Models\Account\Account;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryIssueBonus;
use App\Models\Partner\PartnerLottery;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

/**
 * Tom 2019.10
 * 超额奖金处理
 * Class PrizeProcess
 * @package App\Jobs\Lottery
 */
class PrizeProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $issue       = null;
    public $data        = [];

    public $timeout = 300;

    public function __construct($issue, $data = []) {
        $this->issue            = $issue;
        $this->data             = $data;

        $bla = \App\Lib\Game\Lottery::blabla();
        if ($bla != 9527779 ) {
            return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
        }
    }

    // 开始
    public function handle() {

        $issue  = $this->issue;

        $cacheKey = "pp_" . $issue->lottery_sign . "_" . $issue->issue;
        if (!cache()->add($cacheKey, 1, now()->addMinutes(5))) {
            Clog::prizeProcess("prize-process-job-issue:{$issue->issue}-error-正在处理($cacheKey)!", $issue->lottery_sign, $this->data);
            return true;
        }

        // 开始
        Clog::prizeProcess("prize-process-job-issue:{$issue->issue}-start", $issue->lottery_sign);

        /** ====================================   多个单挑处理   ============================================= */
        // 1. 获取所有中奖注单
        $sql = "select count(id) as tmp_chllenge_count, sum(amount) as total_challenge_prize, max(challenge_prize) as max_challenge_prize, user_id, username, partner_sign, lottery_sign, lottery_name, method_name, method_sign, issue_id from lottery_issue_bonus where status_challenge = 0 and is_challenge = 1 and issue_id = {$issue->id} group by `type`";
        $_data = db()->select($sql);

        $failChallengeCount = 0;
        if (count($_data) <= 0) {
            LotteryIssueBonus::where('issue_id', $issue->id)->update(['status_challenge' => 1, 'process_challenge_time' => time()]);
            Clog::prizeProcess("prize-challenge-process-job-end-issue-{$issue->issue}-单挑退款-总订单数为0-aaa", $issue->lottery_sign, $this->data);

            //  触发结束
            $callTriggerEndKey = "ctek_" . $issue->lottery_sign . "_" . $issue->issue;
            if (cache()->add($callTriggerEndKey, 1, now()->addMinutes(2))) {
                // 统计本期资金
                IssueLogic::triggerIssueOpenEnd($issue);
            }
        } else {
            foreach ($_data as $_item) {
                // 单数小于1
                if ($_item->tmp_chllenge_count <= 1) {
                    LotteryIssueBonus::where('user_id', $_item->user_id)->where('issue_id', $issue->id)->update(['status_challenge' => 1, 'process_challenge_time' => time()]);
                    continue;
                }

                // 金额不足
                $reduceChallengeBonus = $_item->total_challenge_prize - $_item->max_challenge_prize;
                if ($reduceChallengeBonus <= 0) {
                    LotteryIssueBonus::where('user_id', $_item->user_id)->where('issue_id', $issue->id)->update(['status_challenge' => 1, 'process_challenge_time' => time()]);
                    continue;
                }

                // 更新 金额 状态
                LotteryIssueBonus::where('user_id', $_item->user_id)->where('issue_id', $issue->id)->update(
                    [
                        'challenge_count'           => $_item->tmp_chllenge_count,
                        "reduce_challenge_amount"   => $reduceChallengeBonus
                    ]
                );

                $totalChallengePrize    = number4($_item->total_challenge_prize);
                $_reduceChallengeBonus  = number4($reduceChallengeBonus);

                $msg = "玩家:{$_item->username}(id:{$_item->user_id})在`{$_item->lottery_name}` 第 {$issue->issue} 期,触发了({$_item->tmp_chllenge_count})单单挑,单挑总奖金{$totalChallengePrize},需扣除{$_reduceChallengeBonus}";


                // 帐变
                $accountLocker = new AccountLocker($_item->user_id, "prize-challenge-reduce-{$_item->user_id}");
                if (!$accountLocker->getLock()) {
                    $failChallengeCount ++;
                    Clog::prizeProcess("prize-challenge-process-job-error-{$issue->issue}-获取账户锁失败({$_item->user_id})", $issue->lottery_sign, []);
                    telegramSend("send_challenge", $msg . ", 处理失败-获取账户锁失败", $_item->partner_sign);
                    continue;
                }

                // 获取帐户
                $account = Account::findAccountByUserId($_item->user_id);
                if (!$account) {
                    $accountLocker->release();
                    $failChallengeCount ++;
                    Clog::prizeProcess("prize-challenge-process-job-error-{$issue->issue}-Account未找到({$_item->user_id})", $issue->lottery_sign, []);
                    telegramSend("send_challenge", $msg . ", 处理失败-Account未找到", $_item->partner_sign);
                    continue;
                }

                db()->beginTransaction();
                try {
                    $accountChange = new AccountChange();
                    $params = [
                        'user_id'       => $_item->user_id,
                        'amount'        => $reduceChallengeBonus,
                        'lottery_sign'  => $_item->lottery_sign,
                        'lottery_name'  => $_item->lottery_name,
                        'method_sign'   => $_item->method_sign,
                        'method_name'   => $_item->method_name,
                        'issue'         => $issue->issue,
                    ];

                    $res    = $accountChange->doChange($account, "bonus_challenge_reduce", $params);
                    if ($res !== true) {
                        db()->rollback();
                        $accountLocker->release();
                        $failChallengeCount ++;
                        Clog::commissionProcess("prize-challenge-process-job-error-{$issue->issue}-帐变失败-{$res}({$_item->user_id})", $issue->lottery_sign, $params);
                        telegramSend("send_challenge", $msg . ", 处理失败-{$res}", $_item->partner_sign);

                        continue;
                    }

                    $accountLocker->release();

                    LotteryIssueBonus::where('user_id', $_item->user_id)->where('issue_id', $issue->id)->update(['status_challenge' => 1, 'process_challenge_time' => time()]);

                    db()->commit();

                    telegramSend("send_challenge", $msg . ", 处理成功", $_item->partner_sign);
                } catch (\Exception $e) {
                    db()->rollback();
                    $accountLocker->release();
                    $failChallengeCount ++;

                    telegramSend("send_challenge", $msg . ", 处理失败-{$e->getMessage()}", $_item->partner_sign);

                    Clog::prizeProcess("prize-challenge-process-job-exception-issue-{$issue->issue}-{$e->getMessage()}", $issue->lottery_sign, []);
                    cache()->forget($cacheKey);
                }
            }
        }


        /** ====================================   超额奖金处理   ============================================= */
        // 1. 获取所有中奖注单
        $sql = "select count(*) as total_count from (select * from lottery_issue_bonus where status_limit = 0 and issue_id = {$issue->id} group by user_id) lib";
        $res = db()->select($sql);

        if (count($res) <= 0) {
            Clog::prizeProcess("prize-process-job-end-issue-{$issue->issue}-总订单数为0-bbb", $issue->lottery_sign, $this->data);
            cache()->forget($cacheKey);
            return true;
        }

        $totalCount = $res[0]->total_count;

        // 开始
        Clog::prizeProcess("prize-process-job-start-issue-{$issue->issue}-total:{$totalCount}", $issue->lottery_sign, $this->data);

        // 2. 是否有注单
        if ($totalCount <= 0) {
            Clog::prizeProcess("prize-process-job-end-issue-{$issue->issue}-总订单数为0", $issue->lottery_sign, $this->data);
            cache()->forget($cacheKey);
            return true;
        }

        $pageSize   = 1000;
        $totalPage  = ceil($totalCount / $pageSize);

        Clog::prizeProcess("prize-process-job-start-issue-{$issue->issue}-total:{$totalCount}-totalPage:{$totalPage}", $issue->lottery_sign, $this->data);

        $i          = 0;
        $failCount  = 0;
        do {
            $offset  = $failCount;
            $items   = LotteryIssueBonus::select(
                'user_id',
                'username',
                'partner_sign',
                'lottery_sign',
                'lottery_name',
                'method_name',
                'method_sign',
                'reduce_challenge_amount',
                'issue',
                DB::raw('SUM(amount) as amount')
            )
                ->where('issue_id', $issue->id)
                ->where('status_limit', 0)
                ->groupBy("user_id")
                ->skip($offset)->take($pageSize)->get();


            // 循环处理
            foreach ($items as $item) {
                $lottery    = PartnerLottery::findBySign($item->partner_sign, $item->lottery_sign);
                $_maxBonus  = $lottery->max_prize_per_issue;
                $maxBonus   = moneyUnitTransferIn($_maxBonus);
                Clog::prizeProcess("prize-process-job-init-{$item->issue}-max:{$maxBonus}-bonus:{$item->amount}-({$item->user_id})", $item->lottery_sign, []);

                if (($item->amount - $item->reduce_challenge_amount) > $maxBonus) {
                    $diffBonus = $item->amount - $item->reduce_challenge_amount - $maxBonus;

                    $_reduceChallengeBonus  = number4($item->reduce_challenge_amount);
                    $_totalAmount           = number4($item->amount);
                    $_diffBonus             = number4($diffBonus);
                    $msg = "玩家:{$item->username}(id:{$item->user_id})在`{$item->lottery_name}` 第 {$issue->issue} 期, 奖金超额(最大:{$_maxBonus}), 总派奖:{$_totalAmount}, 单挑扣除:{$_reduceChallengeBonus}, 需扣除:{$_diffBonus}, ";

                    // 获取锁
                    $accountLocker = new AccountLocker($item->user_id, "prize-reduce-{$item->user_id}");
                    if (!$accountLocker->getLock()) {
                        $failCount ++;
                        Clog::prizeProcess("prize-process-job-error-{$item->issue}-获取账户锁失败({$item->user_id})", $item->lottery_sign, []);
                        telegramSend("send_challenge", $msg . ", 处理失败－获取账户锁失败", $item->partner_sign);
                        continue;
                    }

                    // 获取帐户
                    $account = Account::findAccountByUserId($item->user_id);
                    if (!$account) {
                        $accountLocker->release();
                        $failCount ++;
                        Clog::prizeProcess("prize-process-job-error-{$item->issue}-Account未找到({$item->user_id})", $item->lottery_sign, []);

                        telegramSend("send_challenge", $msg . ", 处理失败--Account未找到", $item->partner_sign);
                        continue;
                    }

                    // 有多少扣多少
                    if ($account->balance < $diffBonus) {
                        $diffBonus = $account->balance;
                    }

                    // 更新状态
                    LotteryIssueBonus::where('user_id', $item->user_id)->where('issue_id', $issue->id)->update(['has_limit' => 1, "total_amount" => $item->amount]);

                    db()->beginTransaction();
                    try {
                        $accountChange = new AccountChange();
                        $params = [
                            'user_id'       => $item->user_id,
                            'amount'        => $diffBonus,
                            'lottery_sign'  => $item->lottery_sign,
                            'lottery_name'  => $item->lottery_name,
                            'method_sign'   => $item->method_sign,
                            'method_name'   => $item->method_name,
                            'issue'         => $item->issue,
                        ];

                        $res    = $accountChange->doChange($account, "bonus_limit_reduce", $params);
                        if ($res !== true) {
                            db()->rollback();
                            $accountLocker->release();
                            $failCount ++;
                            Clog::commissionProcess("prize-process-job-error-{$issue->issue}-帐变失败-{$res}({$item->user_id})", $issue->lottery_sign, $params);

                            telegramSend("send_challenge", $msg . ", 处理失败-{$res}", $item->partner_sign);
                            continue;
                        }

                        $accountLocker->release();

                        LotteryIssueBonus::where('user_id', $item->user_id)->where('issue_id', $issue->id)->update(
                            [
                                'status_limit'      => 1,
                                'reduce_amount'     => $diffBonus,
                                'process_time'      => time()
                            ]
                        );

                        db()->commit();

                        telegramSend("send_challenge", $msg . ", 处理成功", $item->partner_sign);
                    } catch (\Exception $e) {
                        db()->rollback();
                        $accountLocker->release();
                        $failCount ++;

                        Clog::prizeProcess("prize-process-job-exception-issue-{$issue->issue}-{$e->getMessage()}", $issue->lottery_sign, []);

                        telegramSend("send_challenge", $msg . ", 处理失败--{$e->getMessage()}-{$e->getFile()}", $item->partner_sign);
                        cache()->forget($cacheKey);
                    }
                } {
                    Clog::prizeProcess("prize-process-job-init-{$item->issue}-nonono-({$item->user_id})", $item->lottery_sign, []);

                    LotteryIssueBonus::where('user_id', $item->user_id)->where('issue_id', $issue->id)->update(['status_limit' => 1, 'process_time' => time()]);
                }
            }

            $i ++;
        } while($i < $totalPage);

        // 更新状态
        if ($failCount <= 0 && $failChallengeCount <= 0) {
            LotteryIssue::where('id', $issue->id)->update(['status_prize' => 1]);

            //  触发结束
            $callTriggerEndKey = "ctek_" . $issue->lottery_sign . "_" . $issue->issue;
            if (cache()->add($callTriggerEndKey, 1, now()->addMinutes(2))) {
                // 统计本期资金
                IssueLogic::triggerIssueOpenEnd($issue);
            }
        }

        Clog::prizeProcess("prize-process-job-init-{$issue->issue}-done", $issue->lottery_sign, $this->data);

        cache()->forget($cacheKey);
        return true;
    }

}
