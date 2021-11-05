<?php
namespace App\Lib\Logic\Lottery;

use App\Lib\BaseCache;
use App\Lib\Clog;
use App\Lib\Logic\Cache\IssueCache;
use App\Lib\Logic\Cache\LotteryCache;
use App\Models\Partner\PartnerLottery;
use App\Models\Player\Player;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use App\Models\Report\ReportStatStack;

/**
 * 投注逻辑　
 * 2019-10 整理
 * Class BetLogic
 * @package App\Lib\Lottery\BetLogic
 */
class BetLogic
{
    use BaseCache;

    /**
     * @param $player
     * @param $lottery
     * @param $data
     * @param int $from
     * @return array|bool|string
     * @throws \Exception
     */
    static function bet($player, $lottery, $data, $from = 1) {

        if ($player->frozen_type == Player::FROZEN_TYPE_DISABLE_BET) {
            return "对不起, 您当前状态为禁止投注状态, 请联系客服!";
        }

        // 彩种状态
        if (!$lottery || $lottery->status != 1) {
            return "对不起, 当前彩种禁止投注!";
        }

        // 投注内容
        if (!isset($data['balls']) || !$data['balls']) {
            return "对不起, 投注内容不正确(0x001)!";
        }

        $betDetail  = [];
        $_totalCost = 0;

        // 初次解析
        $data['balls'] = json_decode($data['balls'], true);

        // 再次判定
        if (!is_array($data['balls'])) {
            return "对不起, 投注内容不正确(0x002)!";
        }

        $openTrace      = isset($data["is_trace"]) && $data["is_trace"] ? 1 : 0;
        $traceWinStop   = isset($data["trace_win_stop"]) && $data["trace_win_stop"] ? 1 : 0;

        // 展开　加　过滤
        foreach ($data['balls'] as &$item) {
            // 参数是否合法
            $params = self::checkBetItemParams($player, $item, $lottery, $openTrace);
            if (!is_array($params)) {
                return $params;
            }

            $item = $params;

            $methodId = $item['method_sign'];

            // 是否开启 - 数据库
            $method = LotteryCache::getPartnerLotteryOneMethodConfig($lottery->lottery_sign, $methodId, $player->partner_sign);

            if (!$method || $method['status'] != 1) {
                return "对不起, 玩法 {$methodId} 未开启!";
            }
            $item['method_config'] = $method;

            // 玩法对象
            $oMethod = LotteryCache::getMethodObject($lottery->lottery_sign, $methodId, $player->partner_sign);

            if (!$oMethod) {
                return "对不起, 玩法 {$method['method_name']} 未定义!";
            }
            $item['method_object']  = $oMethod;

            $modeConfig = config("game.main.modes");
            $modeValue  = $modeConfig[$item['mode']]['val'];

            // 扩展玩法
            if ($oMethod->supportExpand) {
                $position = [];
                if (isset($item['position'])) {
                    $position = (array)$item['position'];
                }

                if (!$oMethod->checkPos($position)) {
                    return "对不起, 玩法{$method['name']}位置不正确!";
                }

                $expands = $oMethod->expand($item['codes'], $position);

                $totalCount     = 0;
                $originalCount  = $item['count'];
                foreach ($expands as $expand) {

                    $oMethod = LotteryCache::getMethodObject($lottery->lottery_sign, $expand['method_sign'], $player->partner_sign);
                    // 号码格式
                    if (!$oMethod->checkBetCode($expand['codes'])) {
                        return "对不起, 注单号码不合法!";
                    }

                    // 注数
                    $count = $oMethod->getCount($expand['codes']);
                    $totalCount += $count;


                    $item['method_sign']    = $expand['method_sign'];
                    $item['codes']          = $expand['codes'];
                    $item['count']          = $expand['count'];
                    $item['cost']           = $modeValue * $item['times'] * $item['price'] * $expand['count'];

                    // 转换
                    if ($oMethod->code_change) {
                        $item['bet_number_view']          = $oMethod->codeChange($expand['codes']);
                    }

                    // 检查单挑
                    $res = PartnerLottery::checkChallenge($method, $oMethod, $item);

                    $betDetail[] = [
                        'method_sign'           => $expand['method_sign'],
                        'method_name'           => $oMethod->name,
                        'mode'                  => $item['mode'],
                        'count'                 => $expand['count'],
                        'bet_prize_group'       => $item['prize_group'],
                        'user_prize_group'      => $player->prize_group,
                        'times'                 => $item['times'],
                        'is_challenge'          => $res ? 1 : 0,
                        'challenge_prize'       => $res,
                        'price'                 => $item['price'],
                        'total_price'           => $item['cost'] ,
                        'bet_number'            => $item['codes'],
                        'bet_number_view'       => isset($item['bet_number_view']) ? $item['bet_number_view'] : $item['codes'],
                    ];

                    $_totalCost += $item['cost'];
                }

                // 展开后的注数 和原始的注数
                if ($totalCount != $originalCount) {
                    return "对不起, 注数不合法!";
                }

            } else {
                // 号码格式
                if (!$oMethod->checkBetCode($item['codes'])) {
                    return "对不起, 注单号码不合法!";
                }

                // 注数
                $count = $oMethod->getCount($item['codes']);
                if ($count != $item['count']) {
                    return "对不起, 注数不合法!";
                }

                // 转换
                if ($oMethod->code_change) {
                    $item['bet_number_view']          = $oMethod->codeChange($item['codes']);
                }

                // 检查单挑
                $res = PartnerLottery::checkChallenge($method, $oMethod, $item);

                $betDetail[] = [
                    'method_sign'           => $methodId,
                    'method_name'           => $method['method_name'],
                    'mode'                  => $item['mode'],
                    'count'                 => $item['count'],
                    'bet_prize_group'       => $item['prize_group'],
                    'user_prize_group'      => $player->prize_group,
                    'times'                 => $item['times'],
                    'is_challenge'          => $res ? 1 : 0,
                    'challenge_prize'       => $res,
                    'price'                 => $item['price'],
                    'total_price'           => $item['cost'],
                    'bet_number'            => $item['codes'],
                    'bet_number_view'       => isset($item['bet_number_view']) ? $item['bet_number_view'] : $item['codes'],
                ];

                $_totalCost += $item['cost'];
            }
        }

        // 单倍总奖金
        $maxPrizeOneTimes = ProjectLogic::getMaxPrize($lottery, $betDetail, $player->partner_sign);

        // 追号期号
        $traceData = json_decode($data['trace_issues'], true);

        if (!$traceData)  {
            return "对不起, 奖期号不合法!";
        }

        // 检测追号奖期
        $traceData = $lottery->checkTraceData($traceData);
        if (!is_array($traceData)) {
            return $traceData . "(0x001)";
        }

        // 获取当前奖期
        $currentIssue = IssueCache::getCurrentIssue($lottery->lottery_sign);
        if (!$currentIssue) {
            return "对不起, 未找到当前奖期!";
        }

        // 如果非追号
        $firstTraceDataIssue = array_keys($traceData)[0];
        if (!$openTrace && $currentIssue->issue != $firstTraceDataIssue) {
            return "对不起, 您只能投注当前期!";
        }

        // 如果是追号
        if ($openTrace) {
            $totalTimes = 0;
            foreach ($traceData as $_issue => $times) {
                $totalTimes += $times;
            }

            $_totalCost = 0;
            foreach ($betDetail as $index => $_item) {
                $modeConfig = config("game.main.modes");
                $modeValue  = $modeConfig[$_item['mode']]['val'];
                $_totalCost += bcmul(bcmul(bcmul($modeValue, $_item['price'],4), $_item['count'], 4), $totalTimes, 4);

                $betDetail[$index]['total_trace_cost'] = $_totalCost;
            }
        }

        // 总价的计算
        if (!isset($data['total_cost']) || !$_totalCost || bccomp($data['total_cost'], $_totalCost, 4) !== 0) {
            return "对不起, 总价不正确!";
        }

        $accountLocker = null;
        db()->beginTransaction();
        try {

            $data       = ProjectLogic::addProject($player, $lottery, $currentIssue, $betDetail, $traceData, $openTrace, $traceWinStop, $from);

            // 获取锁
            $accountLocker = new AccountLocker($player->id, "bet");
            if (!$accountLocker->getLock()) {
                db()->rollback();
                return "对不起, 获取账户锁失败!";
            }

            $account = $player->account();
            if ($account->balance < $_totalCost) {
                db()->rollback();
                $accountLocker->release();
                return "对不起, 当前余额不足!";
            }

            // 帐变
            $accountChange = new AccountChange();
            $accountChange->setReportMode(AccountChange::MODE_REPORT_AFTER);
            $accountChange->setChangeMode(AccountChange::MODE_CHANGE_AFTER);

            foreach ($data as $item) {
                $params = [
                    'user_id'       => $player->id,
                    'amount'        => $item['cost'],
                    'lottery_sign'  => $item['lottery_sign'],
                    'lottery_name'  => $item['lottery_name'],
                    'method_sign'   => $item['method_sign'],
                    'method_name'   => $item['method_name'],
                    'mode'          => $item['mode'],
                    'project_id'    => $item['id'],
                ];

                if ($item['type'] == 'project') {
                    $params['issue']    = $currentIssue->issue;
                    $res = $accountChange->doChange($account, "bet_cost", $params);

                    // 首次投注
                    if ($res === true) {
                        ReportStatStack::doFirstBet($player, 1);
                    }
                } else {
                    $res = $accountChange->doChange($account, "trace_cost", $params);
                }

                if ($res !== true) {
                    db()->rollback();
                    $accountLocker->release();
                    return "对不起, " . $res;
                }
            }

            $accountChange->triggerSave();

            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            if($accountLocker) $accountLocker->release();
            Clog::gameError("投注-异常:" . $e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine(),  ['a' => $e->getTraceAsString()]);
            $text = "玩家:{$player->username}"  . "\r\n";
            $text .= "时间:" . date("Y-m-d H:i:s")  . "\r\n";
            $text .= "异常:" . "投注:" . $e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine()  . "\r\n";

            telegramSend("send_exception", $text);


            return "对不起, 服务器异常!!";
        }

        if($accountLocker) $accountLocker->release();

        // 投注 控水
        if ($player->is_tester != 1 && LotteryLogic::isJackpotLottery($lottery)) {
            foreach ($data as $item) {
                if ($item['type'] == 'project') {
                    JackpotLogic::doPlanBet($lottery, $item['id']);
                }
            }
        }

        return true;
    }

    /**
     * 检测所有参数
     * @param $player
     * @param $item
     * @param $lottery
     * @param $openTrace
     * @return array|string
     */
    static function checkBetItemParams($player, $item, $lottery, $openTrace) {
        $params = [];
        /** ======================= 玩法ID =========================== */
        if (!isset($item['method_sign']) || !$item['method_sign']) {
            return "对不起, 投注参数[玩法ID]不存在!";
        }

        $params['method_sign'] = trim($item['method_sign']);

        /** ======================= 模式 ============================= */
        if (!isset($item['mode']) || !$item['mode'] || $item['mode'] != intval($item['mode']) ) {
            return "对不起, 投注参数[模式]不存在!";
        }

        $mode       = intval($item['mode']);
        $allModes   = $lottery->valid_modes;

        if (!array_key_exists($mode, $allModes)) {
            return "对不起, 投注参数[模式]不合法!";
        }
        $params['mode'] = $mode;

        /** ======================= 奖金组 ========================== */
        if (!isset($item['prize_group']) || !$item['prize_group']) {
            return "对不起, 投注参数[奖金组]不存在!";
        }
        $prizeGroup = intval($item['prize_group']);

        // 奖金组不能大于用户
        if ($player->prize_group <  $prizeGroup) {
            return "对不起, 奖金组不在用户允许的范围!";
        }

        // 奖金组不能小于游戏最低奖金组
        if ($prizeGroup < $lottery->min_prize_group) {
            return "对不起, 当前彩种最低投注奖金组为{$lottery->min_prize_group}!";
        }

        // 奖金组不能大于游戏最低奖金组
        if ($prizeGroup > $lottery->max_prize_group) {
            return "对不起, 当前彩种最大投注奖金组为{$lottery->max_prize_group}!";
        }

        $params['prize_group'] = $prizeGroup;

        /** ======================= 号码 =========================== */
        if (!isset($item['codes']) || $item['codes'] === '') {
            return "对不起, 投注参数[号码]不存在!";
        }

        $params['codes'] = $item['codes'];

        /** ======================== 倍数 ========================== */
        if (!isset($item['times']) || !$item['times'] || $item['times'] != intval($item["times"])) {
            return "对不起, 投注参数[倍数]不存在!";
        }

        if ($openTrace && $item['times'] !== 1) {
            return "对不起, 投注参数[倍数]不正确(0x009)!";
        }

        $times  = intval($item["times"]);
        $params['times'] = $times;

        /** ======================== 单价 ========================= */
        if (!isset($item['price']) || !$item['price'] || !is_numeric($item['price'])) {
            return "对不起, 投注参数[单价]不合法!";
        }

        $price          = intval($item["price"]);
        $allPriceArr    = $lottery->valid_price;

        if (!$price || !array_key_exists($price, $allPriceArr)) {
            return "对不起, 单价{$price}, 不合法!";
        }

        $params['price'] = $price;

        /** ======================== 总价 ========================= */
        if (!isset($item['cost']) || !$item['cost']) {
            return "对不起, 投注参数[总价]不存在!";
        }
        $params['cost'] = $item['cost'];

        /** ======================== 总价 ========================== */
        if (!isset($item['count']) || !$item['count'] || $item['count'] != intval($item["count"])) {
            return "对不起, 投注参数[注数]不存在!";
        }
        $params['count'] = intval($item["count"]);

        // 求和比对
        $modeConfig = config("game.main.modes");
        $modeValue  = $modeConfig[$mode]['val'];
        $singleCost = $modeValue * $times * $price * $params['count'];

        if ($singleCost <= 0) {
            return "对不起, 总价计算结果为无效值!";
        }

        // 比较
        if (bccomp($singleCost,  $item['cost'], 5) !== 0) {
            return "对不起, 总价计算错误!";
        }

        $params['cost'] = $item['cost'];

        return $params;
    }
}
