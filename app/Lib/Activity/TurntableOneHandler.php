<?php

namespace App\Lib\Activity;


use App\Lib\Activity\Core\BaseActivity;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityRule;
use App\Lib\Clog;
use App\Models\Partner\PartnerLottery;

class TurntableOneHandler extends BaseActivity
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

            $step = $this->params['step'] ?? 0;

            $activeM = $this->activeM;

            // 参与要求
            $params         = json_decode($activeM->params, 1);
            $paramsNew      = [];
            $prize        = $params['prize'] ?? [];     // 奖品类型 奖品值 中奖概率

            $obtainType   = $params['obtain_type'] ?? 1;   // 领取方式
            $participants = $params['participants'] ?? [];
            $check        = $params['check'] ?? 1;   // 是否需要审核

            $type_child   = $this->type;
            $clientIp     = ip2long(real_ip());
            $userId       = $this->player->id;
            $desc         = $activeM->name;

            $firstday = date('Y-m-d 00:00:00');  //本月第一天

            if (!$params || !$step) {
                $this->errorMsg = '参数有误';
                return false;
            }

            // 参与人员
            if (!in_array($this->player->type, $participants)) {
                $this->errorMsg = '此会员, 无法参与活动';
                return false;
            }

            if ($this->params['step'] == 'turntable') {

                $winnPrize                    = $this->get_rand($prize);
                $paramsNew['prize']        = $prize[$winnPrize]['prize'];
                $paramsNew['prize_value']  = $prize[$winnPrize]['prize_value'];
                $paramsNew['possible']     = $prize[$winnPrize]['possible'];
                $paramsNew['possible_val'] = $prize[$winnPrize]['possible_val'];
                $paramsNew['lottery_sign'] = $prize[$winnPrize]['lottery_sign'];
                $paramsNew['turn_num']     = 1;



                if ($this->addLogAndRecharge($paramsNew, $type_child, $obtainType, $check, $activeM, $desc)) {
                    $prizeConfigArr    = config('active.prize');
                    $this->successData = ['obtain_type' => $obtainType, 'prize' => $prizeConfigArr[$paramsNew['prize']], 'prize_val' => $prize[$winnPrize]['prize_value'], 'angle' => $prize[$winnPrize]['angle'], 'key' => $winnPrize];
                    $this->errorMsg = '恭喜获取' . $prize[$winnPrize]['prize_value'] . $prizeConfigArr[$paramsNew['prize']];
                    return true;
                }
                return false;
            } else {
                $partnerActivityCount = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'type' => $this->type, 'type_child' => $type_child, 'status' => 2])->where(function ($query) use($userId, $clientIp) {
                    $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
                })->where(
                    'created_at', '>=', $firstday
                )->first();
                if (is_null($partnerActivityCount)) {
                    $this->errorMsg = '暂无奖品';
                    return false;
                }
                $lotterySign = $partnerActivityCount->lottery_sign;
                $partnerLottery = PartnerLottery::where(['partner_sign' => $this->partner_sign, 'lottery_sign' => $lotterySign])->first();
                if (is_null($partnerLottery)) {
                    $this->errorMsg = '彩种不存在';
                    return false;
                }
                $partnerLotteryName = $partnerLottery->lottery_name;
                switch ($partnerActivityCount->possible) {
                    // 投注量
                    case 1:
                        $possibleTotal     = $this->getPossibleTotal($firstday, $lotterySign);
                        if ($partnerActivityCount->possible_val > $possibleTotal) {
                            $this->errorMsg = '当天' . $partnerLotteryName . '投注数量需要达到' . $partnerActivityCount->possible_val . '元,已经投注' . $possibleTotal . '才能领取';
                            $this->successData = $lotterySign;
                            return true;
                        }
                        break;

                    // 充值
                    case 2:
                        $possibleTotal = $this->getRechargeTotal($firstday);
                        if ($partnerActivityCount->possible_val > $possibleTotal) {
                            $this->errorMsg = '当天' . $partnerLotteryName . '充值数量需要达到' . $partnerActivityCount->possible_val . '元,已经投注' . $possibleTotal . '才能领取';
                            $this->successData = $lotterySign;
                            return true;
                        }
                        break;
                }

                $c['check_status'] = 1;
                $c['partner_admin_username'] = '';
                $c['partner_admin_user_id'] = '';
                $c['reason'] = '';

                $desc = '领取成功';

                if ($this->getReward($c, $partnerActivityCount->id, $desc)) {
                    $this->successData = ['obtain_type' => $obtainType, 'prize' => $partnerActivityCount->prize, 'prize_val' => $partnerActivityCount->prize_value];
                    if ($partnerActivityCount->check == 1) {
                        $this->errorMsg    = '需要客服审核后, 在领取1';
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
                        switch ($partnerActivityCount->prize) {
                            case 1:
                                $this->errorMsg    = '领取成功, 获取' . $partnerActivityCount->prize_val . '礼金';
                                break;
                            case 2;
                                $this->errorMsg    = '领取成功, 获取' . $partnerActivityCount->prize_val . '积分';
                                break;
                            case 3;
                                $this->errorMsg    = '领取成功, 获取' . $partnerActivityCount->prize_val . '元';
                                break;
                        }
                    }
                    return true;
                }
                return false;
            }
        } catch (\Exception $e) {
            var_dump("转盘-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
            Clog::activeLog("转盘-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
        }
    }

    /**
     * 获取活动详情规则
     *
     * @return bool
     */
    public function getOne()
    {
        try {
            $prizeArr                     = config('active.prize');
            $this->params['partner_sign'] = $this->partner_sign;
            $partnerActivityRuleM         = PartnerActivityRule::getOne($this->params);
            if (is_null($partnerActivityRuleM)) {
                $this->errorMsg = '活动不存在';
            }
            $prizeActiveNew      = [];
            $clientIp            = ip2long(real_ip());
            $userId              = $this->player->id ?? 0;
            $firstday            = date('Y-m-d 00:00:00');  //本月第一天
            $partnerActivityRule = json_decode($partnerActivityRuleM->params, 1);

            if (!$partnerActivityRule) {
                $this->errorMsg = '活动不存在';
            }
            $prizeActive   = $partnerActivityRule['prize'];

            foreach ($prizeActive as $key => $item2) {
                $prizeActiveNew['prize'][] = [
                    'prize'     => $prizeArr[$item2['prize']] ?? 0,
                    'prize_val' => $item2['prize_value'] ?? 0,
                    'id'        => $key,
                    'angle'     => $item2['angle'] ?? [],
                ];
            }
            $turntableNum= 1;

            if ($userId) {
                $partnerActivityCount = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'type' => $this->type ])->where(function ($query) use($userId, $clientIp) {
                    $query->where('user_id', $userId);
                })->where(
                    'created_at', '>=', $firstday
                )->first();
                // 抽过奖 次数 为0
                if (!is_null($partnerActivityCount)) {
                    $prizeConfigArr    = config('active.prize');
                    $this->errorMsg = '恭喜您抽中'. $partnerActivityCount->prize_val . $prizeConfigArr[$partnerActivityCount->prize] . '~';
                    $turntableNum = 0;
                } else {
                    // 没抽过奖 次数为1
                    $turntableNum = 1;
                }
            }

            $prizeActiveNew['turntableNum']     = $turntableNum;
            $prizeActiveNew['h5_desc']          = $partnerActivityRuleM->h5_desc ?? '';
            $prizeActiveNew['pc_desc']          = $partnerActivityRuleM->pc_desc ?? '';
            $prizeActiveNew['img']              =  $partnerActivityRule['img'] ?? '';
            $this->successData = $prizeActiveNew;

            return true;
        }catch (\Exception $exception) {
            var_dump($exception->getLine() . '-' . $exception->getMessage());
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