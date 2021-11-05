<?php

namespace App\Models\Game;

use App\Lib\Clog;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Models\Account\AccountChangeReport;
use Illuminate\Support\Facades\DB;

class LotteryIssueBet extends BaseGame
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table    = 'lottery_issue_bet';
    public $timestamps  = false;

	public static $fieldTransferNumber = array(
		'total_bet',
		'total_cancel',
		'total_bonus',
		'total_challenge_bonus',
		'total_real_bonus',
	);

    // 获取列表
    static function getList($c) {
        $query = self::select(
            'id',
        	'lottery_sign',
        	'lottery_name',
        	'day',
        	'partner_sign',
        	'rate',
        	'status',
        	DB::raw('SUM(total_bet) as total_bet'),
			DB::raw('SUM(total_cancel) as total_cancel'),
            DB::raw('SUM(total_bet_commission) as total_bet_commission'),
            DB::raw('SUM(total_child_commission) as total_child_commission'),
            DB::raw('SUM(total_bonus) as total_bonus'),
			DB::raw('SUM(total_challenge_bonus) as total_challenge_bonus'),
			DB::raw('SUM(total_real_bonus) as total_real_bonus'))
			->groupBy('lottery_sign','day','partner_sign');

		if (isset($c['lottery_sign']) && $c['lottery_sign']) {
            $query->where('lottery_sign', $c['lottery_sign']);
		}

		if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
		}

		if (isset($c['day']) && $c['day']) {
            $query->where('day', date("Ymd", strtotime($c['day'])));
		} else {
            $query->where('day', date("Ymd"));
        }


        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

		foreach ($data as $key => $item) {
		    // 水率
		    $resA = $item->total_bet - $item->total_cancel - $item->total_bet_commission;
		    $resB = $resA - $item->total_real_bonus;
            $data[$key]->bet_rate = bcdiv($resB, $resA,4);
            // 返点
            $data[$key]->total_commission = number4($item->total_bet_commission - $item->total_child_commission);
			foreach (self::$fieldTransferNumber as $field) {
                $data[$key]->{$field} = number4($item->{$field});
			}
		}


        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * @param $project
     * @return bool|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function addProjectUpdate($project) {
        $partnerSign    = $project->partner_sign;
        $lotterySign    = $project->lottery_sign;
        $issue          = $project->issue;
        $amount         = $project->total_cost;

        $key = "libb_" . $partnerSign . '_' .  $lotterySign . '_' . $issue;
        if (!ConfigureCache::has($key)) {
            $hasItem = LotteryIssueBet::where("partner_sign", $partnerSign)->where("lottery_sign", $lotterySign)->where("issue", $issue)->first();
        } else {
            $hasItem = true;
        }

        if ($hasItem) {
            $sql = "update `lottery_issue_bet` set `total_bet` = `total_bet` + {$amount} where  `partner_sign` = '{$partnerSign}' and `lottery_sign` = '{$lotterySign}' and `issue`='{$issue}'";

            $ret = db()->update($sql);
            if(!$ret) {
                return "add-partner-lottery-issue-bet-fail-" . $sql;
            }

        } else {
            $res = LotteryIssueBet::insertGetId([
                "lottery_name"  => $project->lottery_name,
                "partner_sign"  => $partnerSign,
                "lottery_sign"  => $lotterySign,
                "issue"         => $issue,
                "total_bet"     => $amount,
                "day"           => date("Ymd"),
            ]);

            if ($res) {
                ConfigureCache::put($key, 1, now()->addMinutes(20));
            }
        }

        return true;
    }

    // 更新返点数据
    static function updateLotteryIssueBet($partnerLottery, $issue) {
        $data           = AccountChangeReport::getJackpotBetCount($partnerLottery, $issue->issue);
        $commissionData = LotteryCommission::getIssueCommissionCount($partnerLottery->lottery_sign, $issue->issue);

        if ($data['bet_cost'] == 0 && $data['trace_cost'] == 0 && $data['cancel_order'] == 0 && $data['cancel_trace_order'] == 0
            && $commissionData['total_bet_commission'] == 0 && $commissionData['total_child_commission'] == 0
        ) {
            return true;
        }

        $totalBet           = $data['trace_cost'] + $data['bet_cost'];
        $totalCancel        = $data['cancel_order'] + $data['cancel_trace_order'];
        $totalCommission    = $commissionData['total_bet_commission'] + $commissionData['total_child_commission'];

        Clog::jackpot("jackpot-update-step-1(bet):{$partnerLottery->lottery_name}-奖期:{$issue->issue}-总投注:{$totalBet}-总撤单:{$totalCancel}-总返点:{$totalCommission}");

        $res = LotteryIssueBet::insertGetId([
            "day"                           => date('Ymd', $issue->begin_time),
            "partner_sign"                  => $partnerLottery->partner_sign,
            "lottery_sign"                  => $partnerLottery->lottery_sign,
            "lottery_name"                  => $partnerLottery->lottery_name,
            "issue"                         => $issue->issue,
            "total_bet"                     => $totalBet,
            "total_cancel"                  => $totalCancel,
            "total_bet_commission"          => $commissionData['total_bet_commission'],
            "total_child_commission"        => $commissionData['total_child_commission'],
        ]);

        return true;
    }

    /**
     * @param $partnerLottery
     * @param $issue
     * @return bool
     * @throws \Exception
     */
    static function updateLotteryIssueBonus($partnerLottery, $issue) {
        $data           = AccountChangeReport::getJackpotBonusCount($partnerLottery, $issue->issue);

        if ($data['bonus_challenge_reduce'] == 0 && $data['bonus_limit_reduce'] == 0 && $data['game_bonus'] == 0) {
            return true;
        }

        $totalBonus             = $data['game_bonus'];
        $totalChallengeBonus    = $data['bonus_challenge_reduce'] + $data['bonus_limit_reduce'];
        $realBonus              = $data['game_bonus'] - $data['bonus_challenge_reduce'] - $data['bonus_limit_reduce'];

        Clog::jackpot("jackpot-update-step-2(bonus):{$partnerLottery->lottery_name}-奖期:{$issue->issue}-总奖金:{$totalBonus}-单挑:{$totalChallengeBonus}-真实奖金:{$realBonus}");

        $res = LotteryIssueBet::where('partner_sign', $partnerLottery->partner_sign)
            ->where('lottery_sign', $partnerLottery->lottery_sign)
            ->where('issue', $issue->issue)->update([
                "total_bonus"                   => $totalBonus,
                "total_challenge_bonus"         => $totalChallengeBonus,
                "total_real_bonus"              => $realBonus,
            ]);

        return true;
    }

    /**
     * @param $project
     * @return bool|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function cancelProjectUpdate($project) {
        $partnerSign    = $project->partner_sign;
        $lotterySign    = $project->lottery_sign;
        $issue          = $project->issue;
        $amount         = $project->total_cost;

        $sql = "update `lottery_issue_bet` set `total_cancel` = `total_cancel` + {$amount} where  `partner_sign` = '{$partnerSign}' and `lottery_sign` = '{$lotterySign}' and `issue`='{$issue}'";

        $ret = db()->update($sql);
        if(!$ret) {
            return "cancel-partner-lottery-issue-bet-fail-" . $sql;
        }

        return true;
    }

    /**
     * @param $partnerSign
     * @param $lotterySign
     * @param $issue
     * @param $amount
     * @param $reduceAmount
     * @return bool|string
     */
    static function sendBonusUpdate($partnerSign, $lotterySign, $issue, $amount, $reduceAmount) {

        $realAmount = $amount - $reduceAmount;
        $sql = "update `lottery_issue_bet` set `total_bonus` = `total_bonus` + {$amount},  `total_challenge_bonus` = `total_challenge_bonus` + {$reduceAmount}, `total_real_bonus` = `total_real_bonus` + {$realAmount} where `partner_sign` = '{$partnerSign}' and `lottery_sign` = '{$lotterySign}' and `issue`='{$issue}'";

        info("inside-lottery:-" . $sql);

        $ret = db()->update($sql);
        if(!$ret) {
            return "bonus-partner-lottery-issue-bet-fail-" . $sql;
        }

        return true;
    }


    // 获取列表
    static function issueDetails($c) {
        $query = self::orderBy('id', 'DESC');

        if (isset($c['lottery_sign']) && $c['lottery_sign']) {
            $query->where('lottery_sign', $c['lottery_sign']);
        }

        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        if (isset($c['day']) && $c['day']) {

            $query->where('day', date("Ymd", strtotime($c['day'])));
        } else {
            $query->where('day', date("Ymd"));
        }


        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        foreach ($data as $key => $item) {
            // 水率
            $resA = $item->total_bet - $item->total_cancel - $item->total_bet_commission;
            $resB = $resA - $item->total_real_bonus;
            $data[$key]->bet_rate = bcdiv($resB, $resA,4);
            foreach (self::$fieldTransferNumber as $field) {
                $data[$key]->{$field} = number4($item->{$field});
            }
        }


        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

}
