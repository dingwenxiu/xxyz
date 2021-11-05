<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Lib\Logic\Lottery\LotteryLogic;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryIssueBet;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019.12
 * 自录号
 * Class SelfEncode
 * @package App\Jobs\Lottery
 */
class SelfEncode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lotterySign     = null;
    public $timeout         = 300;

    public function __construct($lotterySign) {
        $this->lotterySign          = $lotterySign;
        Clog::gameEncode("encode-job-contruct", $lotterySign, []);
        // hash
        $bla = \App\Lib\Game\Lottery::blabla();
        if ($bla != 9527779 ) {
            Clog::gameEncode("encode-auth-error", $lotterySign, []);
            return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
        }

    }

    // 开始
    public function handle() {

        // 不能超过 3
        $totalAttempts = $this->attempts();
        if ($totalAttempts > 2) {
            Clog::gameEncode("encode-error-attempts-{$totalAttempts}", $this->lotterySign, []);
            return true;
        }

        $lotterySign    = $this->lotterySign;
        $lottery        = Lottery::findBySign($lotterySign);

        Clog::gameEncode("encode-001-获取到彩种-start", $lotterySign, []);

        // 获取所有待开奖奖期
        $issues = LotteryIssue::getNeedOpenIssue($lotterySign);

        // 上一期未开
        $lastNotOpenIssue = LotteryIssue::getEncodeAndNotOpenIssue($lotterySign);
        if ($lastNotOpenIssue) {
            IssueLogic::open($lastNotOpenIssue);
        }

        Clog::gameEncode("encode-002-获取到所有待开奖期-start", $lotterySign, []);

        // 开所有奖期
        foreach ($issues as $issue) {
            $key = "self_encode_" . $lotterySign . "_" . $issue->issue;
            if (!cache()->add($key, 1, now()->addMinute(5))) {
                Clog::gameEncode("encode-{$issue->issue}-error-录号中", $issue->lottery_sign, []);
                continue;
            }

            Clog::gameEncode("encode-{$issue->issue}-003-获取到奖期-start", $issue->lottery_sign, []);

            // 统计投注数据
            $partnerLottery = LotteryCache::getPartnerLottery($lottery->en_name, $lottery->partner_sign);
            if (LotteryLogic::isJackpotLottery($partnerLottery)) {
                LotteryIssueBet::updateLotteryIssueBet($partnerLottery, $issue);
            }

            // 获取开奖号码
            Clog::gameEncode("encode-{$issue->issue}-004-获取开奖号码-start", $issue->lottery_sign, []);
            $code = $lottery->getRandCode($partnerLottery, $issue);
            $code = implode(",", $code);
            Clog::gameEncode("encode-{$issue->issue}-005-获取开奖号码-结束-start", $issue->lottery_sign, []);

            $res = IssueLogic::encode($issue, $code);

            Clog::gameEncode("encode-result-Lottery:{$lottery->en_name}-Issue:{$issue->issue}-code:{$code}-{$res}", $issue->lottery_sign);

            cache()->forget($key);
        }

        return true;
    }
}
