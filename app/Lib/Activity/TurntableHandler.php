<?php

namespace App\Lib\Activity;


use App\Lib\Activity\Core\BaseActivity;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityRule;
use App\Lib\Clog;

class TurntableHandler extends BaseActivity
{

    // 获取记录
    public function getActLog()
    {
        $c['type'] = $this->type;
        $c['user_id'] = $this->player->id;
        $partnerActivityLog = PartnerActivityLog::getList($c);
        return $partnerActivityLog;
    }

    public function joinAct()
    {
        try{

            $activeM = $this->activeM;

            // 参与要求
            $possibleStatus = false;
            $params         = json_decode($activeM->params, 1);
            if (!$params) {
                $this->errorMsg = '此活动规则不存在';
                return false;
            }
            $paramsNew      = [];
            $cells        = $params['cells'] ?? 0;    // 槽位个数
            $turn_num     = $params['turn_num'] ?? [];  // 条件类型 条件值 转动次数
            $prize        = $params['prize'] ?? [];     // 奖品类型 奖品值 中奖概率
            $obtainType   = $params['obtain_type'] ?? 1;   // 领取方式
            $participants = $params['participants'] ?? [];
            $lotterySign  = $params['lottery_sign'] ?? [];
            $check        = $params['check'] ?? 1;   // 是否需要审核

            $type_child   = 'turntable';
            $clientIp     = ip2long(real_ip());
            $userId       = $this->player->id;
            $desc         = $activeM->name;



            $firstday = date('Y-m-d 00:00:00');  //本月第一天

            foreach ($turn_num as $item) {
                if (!$possibleStatus) {
                    switch ($item['possible']) {
                        // 投注量
                        case 1:
                            $possibleTotal     = $this->getPossibleTotal($firstday, $lotterySign);
                            if ($item['possible_val'] > $possibleTotal) {
                                $this->errorMsg = '当天投注数量不满足, 无法参与活动';
                                $possibleStatus = false;
                            } else {
                                $possibleStatus = true;
                            }
                            break;

                        // 充值
                        case 2:
                            $possibleTotal = $this->getRechargeTotal($firstday);
                            if ($item['possible_val'] > $possibleTotal) {
                                $this->errorMsg = '当天充值数量不满足, 无法参与活动';
                                $possibleStatus = false;
                            } else {
                                $possibleStatus = true;
                            }
                            break;

                    }
                }
            }

            // 参与人员
            if (!in_array($this->player->type, $participants)) {
                $this->errorMsg = '此会员, 无法参与活动';
                $possibleStatus = false;
            }

            if (!$possibleStatus) {
                return false;
            }
            $possibleStatus = false;


        // 符合要求 进行转盘帐变
        // 1. 今日转盘次数是否用完
        // 获取今日转盘总次数
        foreach ($turn_num as $item) {
            if (!$possibleStatus) {

                $partnerActivityCount = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'type' => $this->type, 'type_child' => $type_child, 'possible' => $item['possible'], 'possible_val' => $item['possible_val']])->where(function ($query) use($userId, $clientIp) {
                    $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
                })->where(
                    'created_at', '>=', $firstday
                )->count('id');

                if ($item['turn_num'] <= $partnerActivityCount) {
                    $this->errorMsg = '已参与过';
                    $possibleStatus = false;
                } else {
                    $paramsNew['turn_num']     = $item['turn_num'];      // 领取类型
                    $paramsNew['possible']     = $item['possible'];      // 领取类型
                    $paramsNew['possible_val'] = $item['possible_val'];  // 领取类型
                    $possibleStatus = true;
                }
            }
        }
        if ( ! $possibleStatus) {
            return false;
        }

        // 2. 转盘
        $winnPrize                    = $this->get_rand($prize);

        $paramsNew['prize']     = $prize[$winnPrize]['prize'];
        $paramsNew['prize_value'] = $prize[$winnPrize]['prize_value'];

        if ($this->addLogAndRecharge($paramsNew, $type_child, $obtainType, $check, $activeM, $desc)) {
            $prizeConfigArr    = config('active.prize');
            $this->successData = ['obtain_type' => $obtainType, 'prize' => $prizeConfigArr[$paramsNew['prize']], 'prize_val' => $prize[$winnPrize]['prize_value'], 'angle' => $prize[$winnPrize]['angle'], 'key' => $winnPrize];

            if ($check == 1) {
                $this->errorMsg    = '需要客服审核后, 在领取';
            } else if ($obtainType != 1) {
                switch ($obtainType) {
                    case 2:
                        $this->errorMsg    = '第二天赠送';
                        break;
                    case 3:
                        $this->errorMsg    = '客服领取';
                        break;
                }
            } else {
                switch ($paramsNew['prize']) {
                    case 1:
                        $this->errorMsg    = '领取成功, 获取' . $prize[$winnPrize]['prize_value'] . '礼金';
                        break;
                    case 2;
                        $this->errorMsg    = '领取成功, 获取' . $prize[$winnPrize]['prize_value'] . '积分';
                        break;
                    case 3;
                        $this->errorMsg    = '领取成功, 获取' . $prize[$winnPrize]['prize_value'] . '元';
                        break;
                }
            }

            return true;
        }
        return false;
        } catch (\Exception $e) {
            Clog::activeLog("转盘-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
        }
    }

    /**
     * 获取活动详情规则
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne()
    {
        try{
            $prizeArr                     = config('active.prize');
            $this->params['partner_sign'] = $this->partner_sign;
            $partnerActivityRuleM         = PartnerActivityRule::getOne($this->params);
            if (is_null($partnerActivityRuleM)) {
                $this->errorMsg = '活动不存在';
            }
            $prizeActiveNew               = [];
            $turntableNum                 = 0;
            $activeCount                  = 0;
            $firstday                     = date('Y-m-d 00:00:00');  //本月第一天
            $partnerActivityRule          = json_decode($partnerActivityRuleM->params, 1);

            if (!$partnerActivityRule) {
                $this->errorMsg = '活动不存在';
            }
            $prizeActive   = $partnerActivityRule['prize'];
            $turnNumActive = $partnerActivityRule['turn_num'];
            $lotterySign   = empty($partnerActivityRule['lottery_sign']) ? 0 : explode(',', $partnerActivityRule['lottery_sign']);

            foreach ($prizeActive as $key => $item2) {
                $prizeActiveNew['prize'][] = [
                    'prize'     => $prizeArr[$item2['prize']] ?? 0,
                    'prize_val' => $item2['prize_value'] ?? 0,
                    'id'        => $key,
                    'angle'     => $item2['angle'] ?? [],
                ];
            }

            if (!empty($this->player)) {
                // 获取去抽奖次数
                foreach ($turnNumActive as $item) {
                    if (!empty($item)) {
                        switch ($item['possible']) {
                            case 1:
                                $possibleTotal    = $this->getPossibleTotal($firstday, $lotterySign);
                                if ($possibleTotal >= $item['possible_val']) {
                                    $activeCount = $activeCount + PartnerActivityLog::where('partner_sign', $this->partner_sign)->where('user_id', $this->player->id)->where('possible', 1)->where('possible_val', $item['possible_val'])->where('type', $this->type)->where('created_at', '>=', date("Y-m-d"))->count('id');
                                    $turntableNum = $turntableNum + $item['turn_num'];
                                } else {
                                    $this->errorMsg     = '投注量还差' . bcsub($item['possible_val'], $possibleTotal, 2) . ' 元';
                                }
                                break;
                            case 2:
                                $possibleTotal   = $this->getRechargeTotal($firstday);
                                if ($possibleTotal >= $item['possible_val']) {
                                    $activeCount  = $activeCount + PartnerActivityLog::where('partner_sign', $this->partner_sign)->where('user_id', $this->player->id)->where('possible', 2)->where('possible_val', $item['possible_val'])->where('type', $this->type)->where('created_at', '>=', date("Y-m-d"))->count('id');
                                    $turntableNum = $turntableNum + $item['turn_num'];
                                } else {
                                    $this->errorMsg     = '充值量还差' . bcsub($item['possible_val'], $possibleTotal, 2) . ' 元';
                                }
                                break;
                        }
                    }
                }
            }

            $prizeActiveNew['turntableNum'] = $turntableNum - $activeCount;
            $prizeActiveNew['h5_desc']         = $partnerActivityRuleM->h5_desc ?? '';
            $prizeActiveNew['pc_desc']         = $partnerActivityRuleM->pc_desc ?? '';
            $prizeActiveNew['img']          =  $partnerActivityRule['img'] ?? '';
            $this->successData = $prizeActiveNew;
            if ($this->errorMsg == '') {
                $this->errorMsg = '获取成功';
                return true;
            }
            return false;
        }catch (\Exception $exception) {

        }
    }

    //计算中奖概率
    public function get_rand($proArr)
    {
        $randNum = random_int(1, 100); // 随机数
        $probability = [];
        foreach ($proArr as $key => $proCur) {
            if ($randNum <= $proCur['probability'] && $proCur['probability'] != 0) {
                $probability[$key] = $proCur['probability'];
            }
        }
        if (empty($probability)) {
            foreach ($proArr as $key => $proCur) {
                if ($proCur['probability'] != 0) {
                    $probability[$key] = $proCur['probability'];
                }
            }
        }
        if (empty($probability)) {
            return [];
        }
        $w = $this->nextNumberArray($randNum, $probability);
        return $w;
    }


    function nextNumberArray($Number, $NumberRangeArray)
    {
        $w       = 0;
        $c       = -1;
        $abstand = 0;

        foreach ($NumberRangeArray as  $pos => $item) {
            $n = $NumberRangeArray[$pos];
            $abstand = ($n < $Number) ? $Number - $n : $n - $Number;
            if ($c == -1) {
                $c = $abstand;
                $w = $pos;
                continue;
            } else if ($abstand < $c) {
                $c = $abstand;
                $w = $pos;
            }
        }
        return $w;
    }
}