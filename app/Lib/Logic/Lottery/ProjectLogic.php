<?php
namespace App\Lib\Logic\Lottery;

use App\Lib\BaseCache;
use App\Lib\Clog;
use App\Lib\Logic\Cache\LotteryCache;
use App\Models\Account\Account;
use App\Models\Game\LotteryCommission;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryIssueBonus;
use App\Models\Game\LotteryProject;
use App\Models\Game\LotteryTrace;
use App\Models\Game\LotteryTraceList;
use App\Models\Player\Player;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use App\Models\Report\ReportStatStack;
use Illuminate\Support\Facades\DB;

/**
 * 订单逻辑　
 * 2019-10 整理
 * Class ProjectLogic
 * @package App\Lib\Lottery\ProjectLogic
 */
class ProjectLogic
{
    use BaseCache;

    /**
     * @param $user
     * @param $lottery
     * @param $currentIssue
     * @param $data
     * @param $traceData
     * @param $openTrace
     * @param $traceWinStop
     * @param int $from
     * @return array
     */
    static function addProject($user, $lottery, $currentIssue, $data, $traceData, $openTrace, $traceWinStop, $from = 1)
    {
        // 返点
        $returnData             = [];
        $traceMainData          = [];
        $traceIssueKeyArr       = array_keys($traceData);
        $firstTraceDataIssue    = $traceIssueKeyArr[0];
        $endIssue               = $traceIssueKeyArr[count($traceIssueKeyArr) - 1];

        foreach ($data as $_item) {
            $commission     = self::getCommissionArr($user, moneyUnitTransferIn($_item['total_price']), $_item['bet_prize_group'], $lottery->series_id);

            $traceCost = 0;
            if ($openTrace) {
                $traceMainData = [
                    'partner_sign'      => $user->partner_sign,
                    'user_id'           => $user->id,
                    'username'          => $user->username,
                    'top_id'            => $user->top_id,
                    'parent_id'         => $user->parent_id,
                    'is_tester'         => $user->is_tester,
                    'series_id'         => $lottery->series_id,
                    'lottery_sign'      => $lottery->lottery_sign,
                    'lottery_name'      => $lottery->lottery_name,
                    'method_sign'       => $_item["method_sign"],
                    'method_name'       => $_item["method_name"],
                    'bet_number'        => base64_encode(self::rc4($_item['bet_number'])),
                    'bet_number_view'   => $_item['bet_number_view'],

                    'user_prize_group'  => $user->prize_group,
                    'bet_prize_group'   => $_item['bet_prize_group'],
                    'mode'              => $_item['mode'],
                    'count'             => $_item['count'],
                    'price'             => $_item['price'],
                    'trace_total_cost'  => moneyUnitTransferIn($_item['total_trace_cost']),
                    'prize_set'         => "",
                    'commission'        => serialize($commission),
                    'win_stop'          => $traceWinStop ? 1 : 0,

                    'total_issues'      => count($traceData),
                    'finished_issues'   => $currentIssue->issue == $firstTraceDataIssue ? 1 : 0,
                    'canceled_issues'   => 0,

                    'finished_amount'   => $currentIssue->issue == $firstTraceDataIssue ? moneyUnitTransferIn($_item['total_price']) : 0,
                    'canceled_amount'   => 0,

                    'start_issue'       => $firstTraceDataIssue,
                    'now_issue'         => $currentIssue->issue == $firstTraceDataIssue && count($traceData) == 1 ? $firstTraceDataIssue : '',
                    'end_issue'         => $endIssue,
                    'stop_issue'        => $currentIssue->issue == $firstTraceDataIssue && count($traceData) == 1 ? $firstTraceDataIssue : '',

                    'time_bought'       => time(),
                    'time_stop'         => 0,

                    'ip'                => real_ip(),
                    'proxy_ip'          => real_ip(),

                    'day'               => date("Ymd"),
                    'bet_from'          => $from,

                    'status'            => $currentIssue->issue == $firstTraceDataIssue && count($traceData) == 1 ? LotteryTrace::STATUS_FINISHED : 0,
                ];

                $traceCost = $traceMainData['trace_total_cost'];
            }

            // 追号处理
            if ($traceMainData && $traceData) {
                // 保存追号任务
                $traceId = DB::table('lottery_traces')->insertGetId($traceMainData);
                // 保存追号
                $traceListData = [];
                $traceSlotCount = config("game.main.logic_trace_slot", 5);

                $i = 0;
                foreach ($traceData as $issue =>   $times) {
                    $traceListData[] = [
                        'partner_sign'      => $user->partner_sign,
                        'user_id'           => $user->id,
                        'username'          => $user->username,
                        'top_id'            => $user->top_id,
                        'parent_id'         => $user->parent_id,
                        'is_tester'         => $user->is_tester,
                        'series_id'         => $lottery->series_id,

                        'trace_id'          => $traceId,
                        'lottery_sign'      => $lottery->lottery_sign,
                        'lottery_name'      => $lottery->lottery_name,
                        'method_sign'       => $_item["method_sign"],
                        'method_name'       => $_item["method_name"],
                        'issue'             => $issue,
                        'bet_number'        => base64_encode(self::rc4($_item['bet_number'])),
                        'bet_number_view'   => $_item['bet_number_view'],
                        'mode'              => $_item['mode'],
                        'count'             => $_item['count'],
                        'times'             => $times,
                        'price'             => $_item['price'],
                        'total_cost'        => moneyUnitTransferIn($_item['total_price'] * $times),

                        'is_challenge'      => $_item['is_challenge'],
                        'challenge_prize'   => $_item['challenge_prize'],

                        'prize_set'         => "",
                        'commission'        => serialize($commission),

                        'slot'              => $user->id % $traceSlotCount,

                        'user_prize_group'  => $user->prize_group,
                        'bet_prize_group'   => $_item['bet_prize_group'],
                        'ip'                => real_ip(),
                        'proxy_ip'          => real_ip(),
                        'bet_from'          => $from,
                        'status'            => $currentIssue->issue == $issue ? LotteryTraceList::STATUS_TRACE_FINISHED : 0,

                        'time_add'          => time(),
                        'time_bet'          => $currentIssue->issue == $issue ? time() : 0,

                        'sort_type'         => $i === 0 ? 1 : ($i === (count($traceData) - 1) ? 2 : 0),
                    ];

                    $i ++;
                }

                DB::table('lottery_trace_detail')->insert($traceListData);
            }


            // 1. 非追号　2. 追号　第一期为当前期
            if (!$openTrace || ($openTrace && $currentIssue->issue == $firstTraceDataIssue)) {

                $projectSlotCount = config("game.main.logic_project_slot", 5);

                $projectData    = [
                    'partner_sign'      => $user->partner_sign,
                    'user_id'           => $user->id,
                    'username'          => $user->username,
                    'top_id'            => $user->top_id,
                    'parent_id'         => $user->parent_id,
                    'is_tester'         => $user->is_tester,
                    'series_id'         => $lottery->series_id,
                    'lottery_sign'      => $lottery->lottery_sign,
                    'lottery_name'      => $lottery->lottery_name,
                    'method_sign'       => $_item["method_sign"],
                    'method_name'       => $_item["method_name"],
                    'user_prize_group'  => $user->prize_group,
                    'bet_prize_group'   => $_item['bet_prize_group'],
                    'is_challenge'      => $_item['is_challenge'],
                    'challenge_prize'   => $_item['challenge_prize'],

                    'mode'              => $_item['mode'],
                    'times'             => !$openTrace ? $_item['times'] : $traceData[$firstTraceDataIssue],
                    'count'             => $_item['count'],
                    'price'             => $_item['price'],
                    'total_cost'        => !$openTrace ? moneyUnitTransferIn($_item['total_price']) : moneyUnitTransferIn($_item['total_price'] * $traceData[$firstTraceDataIssue]),
                    'bet_number'        => base64_encode(self::rc4($_item['bet_number'])),
                    'bet_number_view'   => $_item['bet_number_view'],
                    'issue'             => $currentIssue->issue,

                    'prize_set'         => "",

                    'slot'              => $user->id % $projectSlotCount,

                    'ip'                => real_ip(),
                    'proxy_ip'          => real_ip(),
                    'day_m'             => date("YmdHi"),
                    'bet_from'          => $from,
                    'time_bought'       => time(),
                ];


                // 如果是追号 记录追号ID
                if ($openTrace && $currentIssue->issue == $firstTraceDataIssue && $traceId) {
                    $currentTraceList = LotteryTraceList::where("partner_sign", $user->partner_sign)->where("trace_id", $traceId)->where('issue', $currentIssue->issue)->first();
                    $projectData['trace_detail_id'] = $currentTraceList->id;
                    $projectData['trace_main_id']   = $traceId;
                }

                // 保存
                $id = DB::table('lottery_projects')->insertGetId($projectData);

                // 返点
                $commissionData = [];
                if ($commission && $projectData) {
                    $slotCount = config("game.main.logic_commission_slot", 5);
                    foreach ($commission as $playerId => $config) {
                        $commissionData[] = [
                            'partner_sign'          => $user->partner_sign,
                            'user_id'               => $playerId,
                            'username'              => $config['username'],
                            'top_id'                => $user->top_id,
                            'lottery_sign'          => $projectData['lottery_sign'],
                            'lottery_name'          => $projectData['lottery_name'],
                            'method_sign'           => $projectData['method_sign'],
                            'method_name'           => $projectData['method_name'],
                            'issue'                 => $projectData['issue'],
                            'project_id'            => $id,
                            'from_type'             => $config['type'],
                            'from_user_id'          => $config['from_user_id'],
                            'from_username'         => $config['from_username'],
                            'self_prize_group'      => $config['self_group'],
                            'child_prize_group'     => $config['child_group'],
                            'bet_prize_group'       => $config['bet_group'],
                            'amount'                => $config['amount'],
                            'slot'                  => $playerId % $slotCount,
                            'add_time'              => time(),
                        ];
                    }

                    DB::table('lottery_commissions')->insert($commissionData);
                }
            }

            if (!$openTrace) {
                $returnData[] = [
                    'id'            => $id,
                    'type'          => "project",
                    'cost'          => $projectData['total_cost'],
                    'lottery_sign'  => $lottery->lottery_sign,
                    'lottery_name'  => $lottery->lottery_name,
                    'method_sign'   => $_item['method_sign'],
                    'method_name'   => $_item['method_name'],
                    'mode'          => $_item['mode'],
                ];
            } else {
                $returnData[] = [
                    'id'            => $traceId,
                    'type'          => "trace",
                    'cost'          => $traceCost,
                    'lottery_sign'  => $lottery->lottery_sign,
                    'lottery_name'  => $lottery->lottery_name,
                    'method_sign'   => $_item['method_sign'],
                    'method_name'   => $_item['method_name'],
                    'mode'          => $_item['mode'],
                ];
            }
        }

        return $returnData;
    }

    /**
     * @param $traceData
     * @return bool|string
     * @throws \Exception
     */
    static function traceAddProjects($traceData) {

        $player = Player::findByCache($traceData->user_id);
        if (!$player) {
            return "对不起, 订单:($traceData->id) 用户($traceData->user_id)不存在";
        }

        $projectSlotCount = configure("game.main.logic_project_slot", 5);

        $projectData    = [
            'partner_sign'      => $player->partner_sign,
            'user_id'           => $player->id,
            'username'          => $player->username,
            'top_id'            => $player->top_id,
            'parent_id'         => $player->parent_id,
            'is_tester'         => $player->is_tester,
            'series_id'         => $traceData->series_id,
            'lottery_sign'      => $traceData->lottery_sign,
            'lottery_name'      => $traceData->lottery_name,
            'method_sign'       => $traceData->method_sign,
            'method_name'       => $traceData->method_name,
            'user_prize_group'  => $traceData->user_prize_group,
            'bet_prize_group'   => $traceData->bet_prize_group,
            'mode'              => $traceData->mode,
            'count'             => $traceData->count,
            'times'             => $traceData->times,
            'price'             => $traceData->price,
            'total_cost'        => $traceData->total_cost,
            'bet_number'        => $traceData->bet_number,
            'bet_number_view'   => $traceData->bet_number_view,

            'issue'             => $traceData->issue,

            'is_challenge'      => $traceData->is_challenge,
            'challenge_prize'   => $traceData->challenge_prize,

            'prize_set'         => "",

            'slot'              => $player->id % $projectSlotCount,

            'ip'                => $traceData->ip,
            'proxy_ip'          => $traceData->proxy_ip,

            'bet_from'              => $traceData->bet_from,
            'day_m'                 => date("YmdHi"),

            'time_bought'           => time(),
            'trace_detail_id'       => $traceData->id,
            'trace_main_id'         => $traceData->trace_id,
        ];


        $id = DB::table('lottery_projects')->insertGetId($projectData);

        // 返点
        $commission = $traceData->commission ? unserialize($traceData->commission) : [];
        $commissionData = [];
        if ($commission) {
            $slotCount = config("game.main.logic_commission_slot", 5);
            foreach ($commission as $playerId => $config) {
                $commissionData[] = [
                    'partner_sign'          => $traceData->partner_sign,
                    'user_id'               => $playerId,
                    'username'              => $config['username'],
                    'top_id'                => $traceData->top_id,
                    'lottery_sign'          => $traceData->lottery_sign,
                    'lottery_name'          => $traceData->lottery_name,
                    'method_sign'           => $traceData->method_sign,
                    'method_name'           => $traceData->method_name,
                    'issue'                 => $traceData->issue,
                    'project_id'            => $id,
                    'from_type'             => $config['type'],
                    'from_user_id'          => $config['from_user_id'],
                    'from_username'         => $config['from_username'],
                    'self_prize_group'      => $config['self_group'],
                    'child_prize_group'     => $config['child_group'],
                    'bet_prize_group'       => $config['bet_group'],
                    'amount'                => $config['amount'],
                    'slot'                  => $playerId % $slotCount,
                    'add_time'              => time(),
                ];
            }

            DB::table('lottery_commissions')->insert($commissionData);
        }

        // 投注 控水
        $lottery    = LotteryCache::getPartnerLottery($traceData->lottery_sign, $traceData->partner_sign);
        if (!$player->is_tester && LotteryLogic::isJackpotLottery($lottery)) {
            JackpotLogic::doPlanBet($lottery, $id);
        }

        ReportStatStack::doFirstBet($player, 1);
        return true;
    }

    /**
     * 获取对应投注的返点阶梯
     * @param $user
     * @param $cost
     * @param $betGroup
     * @param $seriesId
     * @return array
     */
    static function getCommissionArr($user, $cost, $betGroup, $seriesId)
    {
        $ridArr = explode("|", trim($user->rid, "|"));

        if (count($ridArr) < 1) {
            return [];
        }

        $playerSet = Player::whereIn('id', $ridArr)->get();

        $_data = array_flip($ridArr);
        foreach ($playerSet as $player) {
            $_data[$player->id] = $player;
        }

        $_data = array_reverse($_data, true);
        $lastPrizeGroup = $betGroup;

        $data = [];
        foreach ($_data as $playerId => $player) {
            $diff = $player->prize_group - $lastPrizeGroup;
            if ($diff > 0) {
                $maxGroup = $seriesId == 'lotto' ? 1980 : 2000;
                $data[$playerId] = [
                    "type"              => $playerId == $user->id ? "self" : "parent",
                    'child_group'       => $lastPrizeGroup,
                    "username"          => $player->username,
                    'self_group'        => $player->prize_group,
                    'from_user_id'      => $user->id,
                    'from_username'     => $user->username,
                    'amount'            => $diff * $cost / $maxGroup,
                    'bet_group'         => $betGroup,
                ];
            }

            $lastPrizeGroup = $player->prize_group;
        }

        return $data;
    }

    /** ================================================  撤单  =================================================== */

    /**
     * @param $project
     * @param $isAdmin
     * @return bool|string
     * @throws \Exception
     */
    static function cancel($project, $isAdmin = false)
    {
        // 1 奖期
        $issue = LotteryIssue::findByIssueNo($project->lottery_sign, $project->issue);
        if (!$issue) {
            return '对不起, 无效的奖期!';
        }

        // 2. 过了截单时间
        if (!$isAdmin && $issue->end_time <= time()) {
            return '对不起, 已经过了撤单时间!';
        }

        // 3. 如果订单不是非处理状态
        if ($project->status_process != LotteryProject::STATUS_PROCESS_INIT) {
            return "对不起, 订单状态不正确!";
        }

        // 2. 检测时间
        if ($project->status_process != LotteryProject::STATUS_PROCESS_INIT) {
            return "对不起, 订单状态不正确!";
        }

        // 3. 获取账户锁
        $accountLocker = new AccountLocker($project->user_id, "projectLogic-" . $project->id);
        if (!$accountLocker->getLock()) {
            return "对不起, 获取账户锁失败!";
        }

        // 4. 获取帐户
        $account = Account::findAccountByUserId($project->user_id);
        if (!$account) {
            $accountLocker->release();
            return "对不起, 获取账户失败!";
        }

        // 事务开始
        db()->beginTransaction();
        try {

            $accountChange = new AccountChange();
            $params = [
                'user_id'           => $project->user_id,
                'amount'            => $project->total_cost,
                'lottery_sign'      => $project->lottery_sign,
                'lottery_name'      => $project->lottery_name,
                'method_sign'       => $project->method_sign,
                'method_name'       => $project->method_name,
                'project_id'        => $project->id,
                'issue'             => $project->issue,
            ];

            $res = $accountChange->doChange($account, "cancel_order", $params);
            if ($res !== true) {
                db()->rollback();
                $accountLocker->release();
                return "对不起, " . $res;
            }

            $project->status_process = LotteryProject::STATUS_PROCESS_CANCEL;
            $project->save();

            $accountLocker->release();

            // 清理
            LotteryCommission::where("project_id", $project->id)->update(['status' => -2]);
            ReportStatStack::doCancel($project);
            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            $accountLocker->release();
            Clog::userCancelProject("撤单-异常:" . $e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());

            return "对不起, 撤单失败, 请联系客服(0X0028)";
        }

        $lottery    = LotteryCache::getPartnerLottery($project->lottery_sign, $project->partner_sign);
        if (!$project->is_tester && LotteryLogic::isJackpotLottery($lottery)) {
            JackpotLogic::doPlanCancel($lottery, $project->id);
        }

        return true;
    }

    /** ================================================  开奖  =================================================== */

    /**
     * 计算奖金
     * @param $project
     * @param $openCode
     * @return array|bool
     * @throws \Exception
     */
    static function count($project, $openCode)
    {
        $lottery = LotteryCache::getPartnerLottery($project->lottery_sign, $project->partner_sign);
        $oMethod = LotteryCache::getMethodObject($project->lottery_sign, $project->method_sign, $project->partner_sign);

        $openCodeArr = LotteryLogic::formatOpenCode($lottery, $openCode);

        $result = $oMethod->assert(self::rc4(base64_decode($project->bet_number)), $openCodeArr);

        $modeConfig = $lottery->valid_modes;

        $totalBonus = 0;
        $totalCount = 0;

        $heMode = false;
        if ($result) {
            foreach ($result as $level => $count) {

                // 和局模式
                if ($count === 88888888) {
                    $heMode = true;
                    break;
                }

                $levelConfig = $oMethod->levels;
                if (isset($levelConfig[$level])) {
                    $prize = $levelConfig[$level]['prize'];
                    $betGroup = $project->bet_prize_group - $lottery->diff_prize_group;
                    $bonus = $betGroup * $prize / 1800;
                    $bonus = $bonus * $count * $project->times * $modeConfig[$project->mode]['val'];
                    if ($project->price == 1) {
                        $bonus = bcdiv($bonus,2, 4);
                    }
                    $totalBonus += $bonus;
                    $totalCount += $count;
                }
            }
        }

        if ($heMode) {
            return [
                'is_win'    => 3,
                'win_count' => 0,
                'bonus'     => 0,
            ];
        }

        // 中奖订单进行处理
        if ($totalBonus > 0) {
            // 单挑的控制
            if ($project->is_challenge && $project->challenge_prize < moneyUnitTransferIn($totalBonus) ) {
                $totalBonus = $project->challenge_prize;
            } else {
                $totalBonus = moneyUnitTransferIn($totalBonus);
            }

            return [
                'is_win'    => 1,
                'win_count' => $totalCount,
                'bonus'     => $totalBonus,
            ];
        }

        return false;
    }

    /**
     * 开奖　派奖
     * @param $dataList
     * @return bool|string
     * @throws \Exception
     */
    static function send($dataList) {

        $item = $dataList[0];

        // 获取锁
        $accountLocker = new AccountLocker($item['user_id'], "project-logic-open-" . $item['project_id']);
        if (!$accountLocker->getLock()) {
            Clog::gameSend("send-logic-{$item['issue']}-error-获取账户锁失败({$item['user_id']})", $item['lottery_sign'], []);
            return "send-logic-玩家({$item['user_id']})-没有获取到锁-ProjectId({$item['project_id']})-LotterySign({$item['lottery_sign']})";
        }

        // 是否存在
        $account = Account::findAccountByUserId($item['user_id']);
        if (!$account) {
            $accountLocker->release();
            Clog::gameSend("send-logic-{$item['issue']}-error-无效的账户({$item['user_id']})", $item['lottery_sign'], []);
            return "send-logic-玩家{$item['user_id']})无效的账户-ProjectId({$item['project_id']})-LotterySign({$item['lottery_sign']})";
        }

        // 获取帐户
        db()->beginTransaction();

        try {
            $accountChange = new AccountChange();
            $accountChange->setChangeMode(AccountChange::MODE_CHANGE_AFTER);
            $accountChange->setReportMode(AccountChange::MODE_REPORT_AFTER);

            $projectIds = [];
            $bonusData  = [];
            foreach ($dataList as $data) {
                $projectIds[] = $data['project_id'];

                // 所有的注单ID
                if ('game_bonus' == $data["change_type"]) {
                    $type = "default_";

                    $heMethodType = config("game.main.he_type_method");
                    if (in_array($data['method_sign'], $heMethodType)) {
                        $type = "he_";
                    }

                    $bonusData[] = [
                        'user_id'       => $data['user_id'],
                        'username'      => $data['username'],
                        'partner_sign'  => $data['partner_sign'],
                        'amount'        => $data['amount'],
                        'lottery_sign'  => $data['lottery_sign'],
                        'lottery_name'  => $data['lottery_name'],
                        'method_sign'   => $data['method_sign'],
                        'method_name'   => $data['method_name'],
                        'project_id'    => $data['project_id'],
                        'issue'         => $data['issue'],
                        'type'          => $type . $data['user_id'],
                        'issue_id'      => $data['issue_id'],
                        'is_challenge'      => $data['is_challenge'],
                        'challenge_prize'   => $data['challenge_prize'],
                    ];

                    $params = [
                        'user_id'       => $data['user_id'],
                        'amount'        => $data['amount'],
                        'lottery_sign'  => $data['lottery_sign'],
                        'lottery_name'  => $data['lottery_name'],
                        'method_sign'   => $data['method_sign'],
                        'method_name'   => $data['method_name'],
                        'project_id'    => $data['project_id'],
                        'issue'         => $data['issue'],
                    ];

                    $res = $accountChange->doChange($account,  $data["change_type"], $params);

                    if ($res !== true) {
                        $accountLocker->release();
                        db()->rollback();
                        Clog::gameSend("send-logic-{$data['issue']}-error-帐变失败-{$res}({$data['user_id']})", $data['lottery_sign'], []);
                        return "send-logic-帐变失败($res)-ProjectId({$data['project_id']})-LotterySign({$data['lottery_sign']})";
                    }
                }

                // 和局反款
                if ('he_return' == $data["change_type"]) {
                    // 真实扣款
                    $params = [
                        'user_id'       => $data['user_id'],
                        'amount'        => $data['amount'],
                        'lottery_sign'  => $data['lottery_sign'],
                        'lottery_name'  => $data['lottery_name'],
                        'method_sign'   => $data['method_sign'],
                        'method_name'   => $data['method_name'],
                        'project_id'    => $data['project_id'],
                        'issue'         => $data['issue'],
                    ];

                    $res = $accountChange->doChange($account, $data["change_type"], $params);
                    if ($res !== true) {
                        db()->rollback();
                        $accountLocker->release();
                        Clog::gameSend("send-logic-real-bet-job-error-{$data['issue']}-帐变失败-{$res}-project-{$data['project_id']}", $data['lottery_sign'], $params);
                        return "open-logic-real-bet-job-error-{$data['issue']}-帐变失败-{$res}-project-{$data['project_id']}";
                    }
                }

            }

            $accountChange->triggerSave();
            $accountLocker->release();

            LotteryProject::whereIn("id", $projectIds)->update([
                'time_send'         => time(),
                'status_process'    => LotteryProject::STATUS_PROCESS_SEND,
            ]);

            // 中奖保存
            LotteryIssueBonus::insert($bonusData);

            db()->commit();

        } catch (\Exception $e) {
            $accountLocker->release();
            db()->rollback();
            $msg = $e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine();
            Clog::gameSend("send-(issue)-{$item['issue']}-error-开奖异常-{$msg}", $item['lottery_sign'], ['project_id' => $item['project_id']]);
            return "send-logic(未中奖)-异常($msg)-ProjectId({$item['project_id']})-LotterySign({$item['lottery_sign']})";
        }

        return true;
    }

    /**
     * 计算奖金
     * @param $lottery
     * @param $oMethod
     * @param $project
     * @return array
     * @throws \Exception
     */
    static function countPrize($lottery, $oMethod, $project)
    {
        // 奖金计算
        $levelConfig = $oMethod->levels;

        $modeConfig = config("game.main.modes");

        $prizeArr = [];
        foreach ($levelConfig as $level => $item) {
            $prize      = $item['prize'];
            $betGroup   = $project->bet_prize_group - $lottery->diff_prize_group;
            $bonus = $betGroup * $prize / 1800;
            $bonus = $bonus *  $project->times * $modeConfig[$project->mode]['val'];
            if ($project->price == 1) {
                $bonus = bcdiv($bonus,2, 4);
            }

            $prizeArr[(string)$level] = (string)number4(moneyUnitTransferIn($bonus));
        }

        return $prizeArr;
    }

    /**
     * 奖级生成
     * @param $seriesId
     * @param $methodSign
     * @param $prize
     * @param string $sCode
     * @return array
     */
    static function prizeTransfer($seriesId, $methodSign, $prize, $sCode = "") {
        if($seriesId == "ssc") {
            // 龙虎和
            if (
                in_array($methodSign, [
                    "LHWQ", "LHWB", "LHWS", "LHWG",
                    "LHQB", "LHQS", "LHQG",
                    "LHBS", "LHBG",
                    "LHSG",
                ])
            ) {
                $_prize = [
                    "1" => (string)$prize[1],
                    "2" => (string)$prize[3],
                ];

                return $_prize;
            }

            // 组合5
            if ($methodSign == "ZH5") {
                $_prize     = [];
                $codeArr    = explode("|", $sCode);

                $countArr = [];
                foreach ($codeArr as $key => $_code) {
                    $countArr[$key + 1] = count(str_split($_code));
                }

                $_prize[1] = (string)$prize[1];
                $_prize[2] = (string) ($countArr[1] * $prize[2]);
                $_prize[3] = (string) ($countArr[1] * $countArr[2] * $prize[3]);
                $_prize[4] = (string) ($countArr[1] * $countArr[2] * $countArr[3] * $prize[4]);
                $_prize[5] = (string) ($countArr[1] * $countArr[2] * $countArr[3] * $countArr[4] * $prize[5]);
                return $_prize;
            }

            // 组合4
            if ($methodSign == "ZH4") {
                $_prize     = [];
                $codeArr    = explode("|", $sCode);

                $countArr = [];
                foreach ($codeArr as $key => $_code) {
                    $countArr[$key + 1] = count(str_split($_code));
                }

                $_prize[1] = (string)$prize[1];
                $_prize[2] = (string)($countArr[1] * $prize[2]);
                $_prize[3] = (string)($countArr[1] * $countArr[2] * $prize[3]);
                $_prize[4] = (string)($countArr[1] * $countArr[2] * $countArr[3] * $prize[4]);
                return $_prize;
            }

            // 组合3
            if ($methodSign == "QZH3" || $methodSign == "ZZH3" || $methodSign == "HZH3") {
                $_prize     = [];
                $codeArr    = explode("|", $sCode);

                $countArr = [];
                foreach ($codeArr as $key => $_code) {
                    $countArr[$key + 1] = count(str_split($_code));
                }

                $_prize[1] = (string)$prize[1];
                $_prize[2] = (string)($countArr[1] * $prize[2]);
                $_prize[3] = (string)($countArr[1] * $countArr[2] * $prize[3]);
                return $_prize;
            }
        }

        return $prize;
    }

    // 获取注单的最大可能奖金
    static function getMaxPrize($lottery, $projectArr, $partnerSign) {
        $maxPrize = 0;
        foreach ($projectArr as $project) {
            $oMethod    = LotteryCache::getMethodObject($lottery->lottery_sign, $project['method_sign'], $partnerSign);
            $_prize     = self::countPrize($lottery, $oMethod, (object)$project);
            $_prize     = self::prizeTransfer($lottery->series_id, $project['method_sign'], $_prize, $project['bet_number']);

            if($lottery->series_id == "ssc") {
                // 组合5
                if ($project['method_sign'] == "ZH5") {
                    $maxPrize += $_prize[1];
                    $maxPrize += $_prize[2];
                    $maxPrize += $_prize[3];
                    $maxPrize += $_prize[4];
                    $maxPrize += $_prize[5];
                }

                // 组合4
                if ($project['method_sign'] == "ZH4") {
                    $maxPrize += $_prize[1];
                    $maxPrize += $_prize[2];
                    $maxPrize += $_prize[3];
                    $maxPrize += $_prize[4];
                }

                // 组合3
                if ($project['method_sign'] == "QZH3" || $project['method_sign'] == "ZZH3" || $project['method_sign'] == "HZH3") {
                    $maxPrize += $_prize[1];
                    $maxPrize += $_prize[2];
                    $maxPrize += $_prize[3];
                }
            } else {
                $maxAmount = 0;
                foreach ($_prize as $level => $amount) {
                    $maxAmount = $amount > $maxAmount ? $amount : $maxAmount;
                }

                $maxPrize += $maxAmount;
            }


        }

        return $maxPrize;
    }


    /**
     * 号码转换
     * @param $seriesId
     * @param $methodSign
     * @param $code
     * @return mixed|string
     */
    static function codeTransferToJackpot($seriesId, $methodSign, $code)
    {
        if($seriesId == "ssc") {
            // 跨度 组3 组6 和值尾数 组合3 直选3
            if (
                in_array($methodSign, [
                        "ZX5", "ZH5", "WXZU120", "WXZU60", "WXZU30", "WXZU20", "WXZU10", "WXZU5",
                        "ZX4", "ZH4", "SXZU24", "SXZU12", "SXZU6", "SXZU4",

                        "QZX3", "ZZX3", "HZX3",
                        "QZH3", "ZZH3", "HZH3",
                        "QZXKD", "QZXKD", "QZXKD",
                        "QZU3", "ZZU3", "HZU3",
                        "QZU6", "ZZU6", "HZU6",
                        "QHZWS", "ZHZWS", "HHZWS",

                        "QZX2", "HZX2",
                        "QZX2HZ", "HZX2HZ",
                        "QZX2KD", "HZX2KD",

                        "QZU2", "HZU2",
                        "QZU2HZ", "HZU2HZ",

                        "HBDW31", "HBDW32", "QBDW31", "QBDW32", "ZBDW31", "ZBDW32", "BDW41", "BDW42", "BDW52", "BDW53",

                        "YFFS",  "HSCS", "SXBX", "SJFC",
                    ]
                )
            ) {
                return str_replace("&", "", $code);
            }

            // 单式
            if (
                in_array($methodSign, [
                        "ZX5_S", "ZX4_S",
                        "QZX3_S", "QHHZX", "QZU3_S", "QZU6_S",
                        "ZZX3_S", "ZHHZX", "ZZU3_S", "ZZU6_S",
                        "HZX3_S", "HHHZX", "HZU3_S", "HZU6_S",
                        "QZX2_S", "QZU2_S", "HZX2_S", "HZU2_S",
                    ]
                )
            ) {
                return str_replace(",", "|", $code);
            }


            // 特殊3
            if (
                in_array($methodSign, [
                        "QTS3", "ZTS3", "HTS3"
                    ]
                )
            ) {
                return str_replace(['b', 's', "d"], ['0', '1', "2"], $code);
            }

            // 定位胆 万
            if ($methodSign == "DWD_W" || $methodSign == "CO_ZX_W") {
                $code =  str_replace("&", "", $code);
                $code = $code . "||||";
                return $code;
            }

            // 定位胆 千
            if ($methodSign == "DWD_Q" || $methodSign == "CO_ZX_Q") {
                $code =  str_replace("&", "", $code);
                $code = "|" . $code . "|||";
                return $code;
            }

            // 定位胆
            if ($methodSign == "DWD_B" || $methodSign == "CO_ZX_B") {
                $code =  str_replace("&", "", $code);
                $code = "||" . $code . "||";
                return $code;
            }

            // 定位胆
            if ($methodSign == "DWD_S" || $methodSign == "CO_ZX_S") {
                $code =  str_replace("&", "", $code);
                $code = "|||" . $code . "|";
                return $code;
            }

            // 定位胆
            if ($methodSign == "DWD_G" || $methodSign == "CO_ZX_G") {
                $code =  str_replace("&", "", $code);
                $code = "||||" . $code;
                return $code;
            }

            // 代码
            $codeArr = [
                'b' => "56789",
                's' => "12345",
                'o' => "13579",
                'e' => "02468",
            ];

            // 大小单双 万
            if ($methodSign == "CO_ZX_W_DXDS") {
                $code = $codeArr[$code] . "||||";
                return $code;
            }

            // 大小单双 千
            if ($methodSign == "CO_ZX_Q_DXDS") {
                $code = "|" . $codeArr[$code] . "|||";
                return $code;
            }

            // 大小单双 百
            if ($methodSign == "CO_ZX_B_DXDS") {
                $code = "||" . $codeArr[$code] . "||";
                return $code;
            }

            // 大小单双 十
            if ($methodSign == "CO_ZX_S_DXDS") {
                $code = "|||" . $codeArr[$code] . "|";
                return $code;
            }

            // 大小单双 个
            if ($methodSign == "CO_ZX_G_DXDS") {
                $code = "||||" . $codeArr[$code];
                return $code;
            }

            // 龙虎和
            if (
                in_array($methodSign, [
                    "LHWQ", "LHWB", "LHWS", "LHWG",
                    "LHQB", "LHQS", "LHQG",
                    "LHBS", "LHBG",
                    "LHSG",

                    "CO_LHWQ", "CO_LHWB", "CO_LHWS", "CO_LHWG",
                    "CO_LHQB", "CO_LHQS", "CO_LHQG",
                    "CO_LHBS", "CO_LHBG",
                    "CO_LHSG",
                ])
            ) {
                $code =  str_replace("&", "", $code);
                return str_replace(['1', '2', "3"], ['0', '1', "2"], $code);
            }

            // 大小单双
            if (
            in_array($methodSign, [
                "Q3DXDS", "Q2DXDS", "H3DXDS", "H2DXDS",
            ])
            ) {
                $code =  str_replace("&", "", $code);
                return str_replace(['b', 's', "o", "e"], ['0', '1', "2", "3"], $code);
            }
        }

        return $code;
    }


    /** ================================================  返点  =================================================== */

    /**
     * Rc4加密解密
     * @param $str
     * @return string
     */
    static function rc4($str)
    {
        $pwd = md5("ilovelottery3721");

        $key[]       = "";
        $box[]       = "";
        $pwd_length  = strlen($pwd);
        $data_length = strlen($str);
        $cipher      = '';

        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $key[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($str[$i]) ^ $k);
        }

        return $cipher;
    }
}
