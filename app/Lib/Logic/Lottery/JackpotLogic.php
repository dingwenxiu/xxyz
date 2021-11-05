<?php
namespace App\Lib\Logic\Lottery;
use App\Lib\Clog;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Lock\JackpotLocker;
use App\Models\Game\LotteryIssueBet;
use App\Models\Game\LotteryJackpotPlan;
use App\Models\Game\LotteryProject;
use Illuminate\Support\Facades\DB;

/**
 * 控水逻辑　
 * 2019-10 整理
 * Class JackpotLogic
 * @package App\Lib\Lottery\JackpotLogic
 */
class JackpotLogic
{

    /**
     * @param $lottery
     * @param $issue
     * @return array|\Illuminate\Contracts\Foundation\Application|mixed|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function getCodeFrom2001($lottery, $issue) {

        try {
            $plan   = $lottery->partner_sign . "_" . $issue->lottery_sign . "_" . $issue->issue;

            // 开始
            $startTime  = microtime(TRUE);

            $nowData       = self::getJackCodeDataFromRedis($plan);
            if (!$nowData) {
                Clog::jackpot("jackpot-{$issue->lottery_sign}-{$issue->issue}-未找到plan数据-plan:{$plan}-未找到数据-随机", $lottery->lottery_sign);
                return LotteryLogic::getRandCode($lottery->series_id);
            }

            if ($lottery->series_id == 'ssc') {
                $data       = config("game.all_code_ssc", []);
            } else if ($lottery->series_id == 'k3') {
                $data = [];
                $tmp = [1,2,3,4,5,6];
                foreach ($tmp as $a) {
                    foreach ($tmp as $b) {
                        foreach ($tmp as $c) {
                            $data[$a.$b.$c] = 0;
                        }
                    }
                }
            }

            foreach ($nowData as $key => $value) {
                $data[$key] = $value;
            }

            if (!$data) {
                Clog::jackpot("jackpot-{$issue->lottery_sign}-{$issue->issue}-出号-plan:{$plan}-未找到数据-随机", $lottery->lottery_sign);
                return LotteryLogic::getRandCode($lottery->series_id);
            }

            $amount = self::getLotteryIssueNeedSendBonus($lottery, $issue);

            if ($amount === -1) {
                Clog::jackpot("jackpot-{$issue->lottery_sign}-{$issue->issue}-出号-plan:{$plan}-返回为-1-随机", $lottery->lottery_sign);
                return LotteryLogic::getRandCode($lottery->series_id);
            }

            // 不同的彩票系列　不同的处理
            if ("ssc" == $lottery->series_id) {
                $returnCodeArr = self::getSscCode($lottery, $issue, $plan, $data, $amount);
            } else if ("k3" == $lottery->series_id) {
                $returnCodeArr = self::getK3Code($lottery, $issue, $plan, $data, $amount);
                info($returnCodeArr);
            } else {
                return LotteryLogic::getRandCode($lottery->series_id);
            }

            $strCode    = implode('', $returnCodeArr);
            $endTime    = microtime(TRUE);
            $totalTime  = $endTime - $startTime;
            Clog::jackpot("jackpot-get-code-done-{$lottery->partner_sign}-彩种{$lottery->lottery_sign}-奖期:{$issue->issue}-号码:" . $strCode . "-派将:{$data[$strCode]}-耗时:{$totalTime}", $lottery->lottery_sign);

            return $returnCodeArr;

        } catch(\Exception $e)  {
            Clog::jackpot("jackpot-get-code-exception-" . $e->getMessage() . "-" . $e->getFile() . "-" . $e->getLine(), $lottery->lottery_sign);
            return [];
        }

        return [];
    }

    static function getSscCode($lottery, $issue, $plan, $data, $amount) {
        $canUseCodeArr      = [];
        $canUseCodeArr5     = [];
        $canUseCodeArr4     = [];

        $someAmountCodeArr  = [];
        $someAmountCodeArr5 = [];
        $someAmountCodeArr4 = [];

        $someCode50 = [];
        $someCode41 = [];
        $someCode3n = [];


        foreach ($data as $code => $prize) {
            // 排除豹子　4->１　3->2
            $_codeArr = str_split($code);
            if (count(array_unique($_codeArr)) == 1) {
                $prize = bcmul($prize, 10000, 0);
                if ($prize <= $amount) {
                    $someCode50[$code] = $prize;
                }
                continue;
            }

            if (count(array_unique($_codeArr)) == 2) {
                $prize = bcmul($prize, 10000, 0);
                if ($prize <= $amount) {
                    $someCode41[$code] = $prize;
                }
                continue;
            }

            // 三连号
            if (($_codeArr[0] == $_codeArr[1] && $_codeArr[1] == $_codeArr[2]) ||
                ($_codeArr[1] == $_codeArr[2] && $_codeArr[2] == $_codeArr[3]) ||
                ($_codeArr[2] == $_codeArr[3] && $_codeArr[3] == $_codeArr[4])
            ) {
                $prize = bcmul($prize, 10000, 0);
                if ($prize <= $amount) {
                    $someCode3n[$code] = $prize;
                }
                continue;
            }

            $prize = bcmul($prize, 10000, 0);
            if ($prize < $amount && $prize >= 0) {
                if (count(array_unique($_codeArr)) == 5) {
                    $canUseCodeArr5[$code] = $prize;
                } else if (count(array_unique($_codeArr)) == 4) {
                    $canUseCodeArr4[$code] = $prize;
                } else {
                    $canUseCodeArr[$code] = $prize;
                }
            }

            if ($prize == $amount) {
                if (count(array_unique($_codeArr)) == 5) {
                    $someAmountCodeArr5[$code] = $prize;
                } else if (count(array_unique($_codeArr)) == 4) {
                    $someAmountCodeArr4[$code] = $prize;
                } else {
                    $someAmountCodeArr[$code] = $prize;
                }
            }
        }

        if ($someAmountCodeArr5) {
            $returnCode = array_rand($someAmountCodeArr5);
        } else if ($someAmountCodeArr4) {
            $returnCode = array_rand($someAmountCodeArr4);
        } else if ($someAmountCodeArr) {
            $returnCode = array_rand($someAmountCodeArr);
        } else if ($canUseCodeArr5) {
            $returnCode = array_rand($canUseCodeArr5);
        } else if ($canUseCodeArr4) {
            $returnCode = array_rand($canUseCodeArr4);
        } else if ($canUseCodeArr) {

            arsort($canUseCodeArr);
            $canUseCodeArr  = array_slice($canUseCodeArr, 0, 300, true);
            $returnCode     = array_rand($canUseCodeArr);
        } else {
            if ($someCode3n) {
                $returnCode = array_rand($someCode3n);
            } elseif ($someCode41) {
                $returnCode = array_rand($someCode41);
            } elseif ($someCode50) {
                $returnCode = array_rand($someCode50);
            } else {
                Clog::jackpot("jackpot-{$issue->lottery_sign}-{$issue->issue}-出号-plan:{$plan}-@@@未找到合适的号码-@@@随机", $lottery->lottery_sign);
                return LotteryLogic::getRandCode($lottery->series_id);
            }
        }

        return str_split($returnCode);
    }

    /**
     * 获取快三的号码
     * @param $lottery
     * @param $issue
     * @param $plan
     * @param $data
     * @param $amount
     * @return array|\Illuminate\Contracts\Foundation\Application|mixed|string
     * @throws \Exception
     */
    static function getK3Code($lottery, $issue, $plan, $data, $amount) {
        $canUseCodeArr      = [];
        $canUseCodeArr3     = [];

        $someAmountCodeArr  = [];
        $someAmountCodeArr3 = [];

        $someCode30 = [];

        foreach ($data as $code => $prize) {
            // 排除豹子
            $_codeArr = str_split($code);
            if (count(array_unique($_codeArr)) == 1) {
                $prize = bcmul($prize, 10000, 0);
                if ($prize <= $amount) {
                    $someCode30[$code] = $prize;
                }
                continue;
            }

            $prize = bcmul($prize, 10000, 0);
            if ($prize < $amount && $prize >= 0) {
                if (count(array_unique($_codeArr)) == 3) {
                    $canUseCodeArr3[$code] = $prize;
                } else {
                    $canUseCodeArr[$code] = $prize;
                }
            }

            if ($prize == $amount) {
                if (count(array_unique($_codeArr)) == 3) {
                    $someAmountCodeArr3[$code] = $prize;
                } else {
                    $someAmountCodeArr[$code] = $prize;
                }
            }
        }

        if ($someAmountCodeArr3) {
            $returnCode = array_rand($someAmountCodeArr3);
        }  else if ($someAmountCodeArr) {
            $returnCode = array_rand($someAmountCodeArr);
        } else if ($canUseCodeArr3) {
            $returnCode = array_rand($canUseCodeArr3);
        } else if ($canUseCodeArr) {

            arsort($canUseCodeArr);
            $canUseCodeArr  = array_slice($canUseCodeArr, 0, 50, true);
            $returnCode     = array_rand($canUseCodeArr);
        } else {
            if ($someCode30) {
                $returnCode = array_rand($someCode30);
            } else {
                Clog::jackpot("jackpot-{$issue->lottery_sign}-{$issue->issue}-出号-plan:{$plan}-@@@未找到合适的号码-@@@随机", $lottery->lottery_sign);
                return LotteryLogic::getRandCode($lottery->series_id);
            }
        }

        return str_split($returnCode);
    }

    /**
     * @param $lottery
     * @param $issue
     * @return float|int
     * @throws \Exception
     */
    static function getLotteryIssueNeedSendBonus($lottery, $issue) {
        $day = date("Ymd", $issue->begin_time);

        $query = LotteryIssueBet::select(
            'lottery_issue_bet.partner_sign',
            'lottery_issue_bet.lottery_sign',
            'lottery_issue_bet.issue',
            DB::raw('SUM(lottery_issue_bet.total_bet) as total_bet'),
            DB::raw('SUM(lottery_issue_bet.total_cancel) as total_cancel'),
            DB::raw('SUM(lottery_issue_bet.total_bet_commission) as total_bet_commission'),
            DB::raw('SUM(lottery_issue_bet.total_child_commission) as total_child_commission'),
            DB::raw('SUM(lottery_issue_bet.total_real_bonus) as total_bonus')
        );

        $query->where('lottery_issue_bet.lottery_sign', $lottery->lottery_sign);
        $query->where('lottery_issue_bet.day', $day);

        $item = $query->first();

        // 如果投注额小于某一个数　全随机
        $minJackpotBet = partnerConfigure($item->partner_sign,'lottery_jackpot_min_bet', 0);
        if ($item->total_bet < $minJackpotBet * 10000) {
            $maxDefaultRate = partnerConfigure($item->partner_sign,'lottery_jackpot_default_max_rate', 10);
            // 最大 30 %
            $maxShouldWin   = intval($minJackpotBet * $maxDefaultRate / 100);
            $shouldWin      = random_int(0, $maxShouldWin);
            Clog::jackpot("jackpot-count-bonus-(小于阀值($minJackpotBet)-最大{$maxDefaultRate}%={$shouldWin})-彩种:{$lottery->lottery_sign}-奖期:{$issue->issue}-总投注:{$item->total_bet}-总奖金:{$item->total_bonus}-总撤单:{$item->total_cancel}-小于阀值-{$minJackpotBet}", $lottery->lottery_sign);
            return $shouldWin;
        }

        $rate = $lottery->rate;

        $nowWin     = $item->total_bet - $item->total_bonus + $item->total_cancel - $item->total_bet_commission - $item->total_child_commission;
        $shouldWin  = $item->total_bet * $rate / 100;

        // 只能开不中奖的
        if ($nowWin <= 0 || $nowWin <= $shouldWin) {
            Clog::jackpot("jackpot-count-bonus-(必不中)-彩种:{$lottery->lottery_sign}-奖期:{$issue->issue}-总投注:{$item->total_bet}-总奖金:{$item->total_bonus}-总撤单:{$item->total_cancel}-水率:{$rate}-总赢:{$nowWin}-应该赢:{$shouldWin}-需派奖:0", $lottery->lottery_sign);
            return 0;
        }

        // 只能开小于amount的
        $amount = $nowWin - $shouldWin;
        Clog::jackpot("jackpot-count-bonus-(放水)-彩种:{$lottery->lottery_sign}-奖期:{$issue->issue}-总投注:{$item->total_bet}-总奖金:{$item->total_bonus}-总撤单:{$item->total_cancel}-水率:{$rate}-总赢:{$nowWin}-应该赢:{$shouldWin}-需派奖:{$amount}", $lottery->lottery_sign);

        return $amount;
    }

    /**
     * 投注
     * @param $lottery
     * @param $projectId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function doPlanBet($lottery, $projectId) {
        $startTime = microtime(true);
        Clog::jackpot("jackpot-bet-update-$lottery->lottery_name-" . $projectId  . "-" . $startTime, $lottery->lottery_sign);

        $project = LotteryProject::find($projectId);
        if (!$project) {
            telegramSend("send_exception", "jackpot-fail-无效的订单-{$projectId}");
            return true;
        }

        try {
            $oMethod    = LotteryCache::getMethodObject($lottery->lottery_sign, $project->method_sign, $project->partner_sign);
            $prizes     = ProjectLogic::countPrize($lottery, $oMethod, $project);

            $_prizes = [];
            foreach ($prizes as $level => $bonus) {
                if ($project->is_challenge) {
                    $_prizes[$level] = $project->challenge_prize > $bonus ? $bonus : $project->challenge_prize;
                } else {
                    $_prizes[$level] = $bonus;
                }
            }

            $sCodes     = ProjectLogic::rc4(base64_decode($project->bet_number));
            $initData   = [];
            $newData    = $oMethod->doControl($initData, $sCodes, $prizes);
            $plan       = $project->partner_sign . "_" . $lottery->lottery_sign . "_" . $project->issue;

            $locker = new JackpotLocker($plan, "bet");
            if (!$locker -> getLock()) {
                Clog::jackpot("jackpot-do-plan-获取锁失败-" . $projectId  . "-" . $startTime, $lottery->lottery_sign);
                return true;
            }

            self::updateJackCodeDataFromRedis($plan, $newData, 'add');

            $locker->release();

            $endTime = microtime(true);
            Clog::jackpot("jackpot-bet-update-$lottery->lottery_name-end-" . $projectId  . "-" .$endTime . "-共花费" . ($endTime - $startTime), $lottery->lottery_sign);

        } catch (\Exception $e) {

            if (isset($locker)) {
                $locker->release();
            }

            $msg = "jackpot-exception-{$e->getMessage()}-{$e->getFile()}-{$e->getLine()}-玩法:{$project->method_sign}";
            Clog::jackpot($msg, $lottery->lottery_sign);
            telegramSend("send_exception", $msg);
        }

        return true;
    }

    /**
     * 撤单
     * @param $lottery
     * @param $projectId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function doPlanCancel($lottery, $projectId) {

        Clog::jackpot("jackpot-do-plan-cancel-" . $projectId, $lottery->lottery_sign);

        $project = LotteryProject::find($projectId);
        if (!$project) {
            telegramSend("send_exception", "jackpot-do-plan-cancel-无效的订单-{$projectId}");
            return true;
        }

        try {
            $oMethod    = LotteryCache::getMethodObject($lottery->lottery_sign, $project->method_sign, $project->partner_sign);
            $prizes     = ProjectLogic::countPrize($lottery, $oMethod, $project);

            $_prizes = [];
            foreach ($prizes as $level => $bonus) {
                if ($project->is_challenge) {
                    $_prizes[$level] = $project->challenge_prize > $bonus ? $bonus : $project->challenge_prize;
                } else {
                    $_prizes[$level] = $bonus;
                }
            }

            $sCodes     = ProjectLogic::rc4(base64_decode($project->bet_number));
            $initData   = [];
            $newData    = $oMethod->doControl($initData, $sCodes, $prizes);

            $plan       = $project->partner_sign . "_" . $lottery->lottery_sign . "_" . $project->issue;

            $locker = new JackpotLocker($plan, "cancel");
            if (!$locker -> getLock()) {
                Clog::jackpot("jackpot-do-plan-cancel-获取锁失败-" . $projectId, $lottery->lottery_sign);
                return true;
            }

            self::updateJackCodeDataFromRedis($plan, $newData, 'del');

            $locker->release();

            $endTime = microtime(true);
            Clog::jackpot("jackpot-do-plan-cancel-end-" . $projectId  . "-" . $endTime, $lottery->lottery_sign);

        } catch (\Exception $e) {
            if (isset($locker)) {
                $locker->release();
            }

            $msg = "jackpot-do-plan-cancel-exception-{$e->getMessage()}-{$e->getFile()}-{$e->getLine()}-玩法:{$project->method_sign}";
            Clog::jackpot($msg, $lottery->lottery_sign);
            telegramSend("send_exception", $msg);
        }

        return true;
    }

    /**
     * @param $plan
     * @param $newData
     * @param $type
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function updateJackCodeDataFromRedis($plan, $newData, $type = 'add') {
        if (cache()->store("jackpot")->has($plan)) {
            $oldData = cache()->store("jackpot")->get($plan);
        } else {
            $oldData = [];
        }

        foreach ($newData as $key => $value) {
            if ($type == 'add') {
                if (isset($oldData[$key])) {
                    $oldData[$key] = bcadd($oldData[$key], $value, 4);
                } else {
                    $oldData[$key] = $value;
                }
            } else {
                $oldData[$key] = bcsub($oldData[$key], $value, 4);
            }
        }

        return cache()->store("jackpot")->set($plan, $oldData, now()->addMinutes(10));
    }

    /**
     * 获取当前期数据
     * @param $plan
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function getJackCodeDataFromRedis($plan) {
        return cache()->store("jackpot")->get($plan, []);
    }

    /**
     * @param $plan
     * @return bool
     * @throws \Exception
     */
    static function delJackCodeDataFromRedis($plan)
    {
        return cache()->store("jackpot")->forget($plan);
    }

    /**
     * 执行数据更新　＝＝　数据库
     * @param $lottery
     * @param $project
     * @param $newData
     * @param string $type
     * @return bool
     */
    static function updateJackCodeDataFromDb($lottery, $project, $newData, $type = 'add') {
        $item = LotteryJackpotPlan::where("lottery_sign", $lottery->lottery_sign)->where("issue", $project->issue)->first();

        if ('add' == $type) {
            if ($item) {
                $oldData = json_decode($item->detail, true);

                foreach ($newData as $key => $value) {
                    if (isset($oldData[$key])) {
                        $oldData[$key] = bcadd($oldData[$key], $value, 4);
                    } else {
                        $oldData[$key] = $value;
                    }
                }

                $item->detail = json_encode($oldData);
                $item->save();

            } else {
                LotteryJackpotPlan::insert([
                    "lottery_sign"      => $lottery->lottery_sign,
                    "lottery_name"      => $lottery->lottery_name,
                    "issue"             => $project->issue,
                    "detail"            => json_encode($newData),
                    "day_m"             => date("YmdHi"),
                ]);
            }
        } else {
            if ($item) {
                $oldData = json_decode($item->detail, true);

                foreach ($newData as $key => $value) {
                    if (isset($oldData[$key])) {
                        $oldData[$key] = bcsub($oldData[$key], $value, 4);
                    }
                }

                $item->detail = json_encode($oldData);
                $item->save();
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    static function delJackCodeDataFromDb()
    {
        return true;
    }
}
