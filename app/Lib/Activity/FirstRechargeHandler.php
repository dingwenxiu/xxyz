<?php

namespace App\Lib\Activity;

use App\Lib\Activity\Core\BaseActivity;
use App\Models\Activity\ActivityRecord;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityRule;

class FirstRechargeHandler extends BaseActivity
{
    // 获取记录
    public function getActLog()
    {
        $c['type']          = $this->type;
        $c['user_id']       = $this->player->id;
        $partnerActivityLog = PartnerActivityLog::getList($c);
        return $partnerActivityLog;
    }

    // 首冲赠送

    /**
     *
     * @return bool
     */
    public function joinAct()
    {
        $fistRecharge = $this->params['fist_recharge'] ?? 0;
        if (!isset($fistRecharge)) {
            $this->errorMsg = '充值参数不存在';
            return false;
        }


        $activeM = $this->activeM;

        // 参与要求
        $params      = json_decode($activeM->params, 1);
        $prizeActive = $params['prize'];
        $paramsNew   = $prizeActive[$fistRecharge] ?? 0;
        $obtainType  = $paramsNew['obtain_type'] ?? '';
        $check       = $paramsNew['check'] ?? '';
        $recharge    = $paramsNew['recharge'] ?? 0;
        $type_child  = 'fistRecharge' . $fistRecharge;
        $desc        = $activeM->name . '| 冲' . $paramsNew['recharge'] . '赠送' . $paramsNew['prize_value'];

        if (!$paramsNew || !$obtainType || !$check || !$recharge) {
            $this->errorMsg = '此活动参数错误';
            return false;
        }
        if (!is_array($paramsNew['participants'])) {
            $participants   = !empty($paramsNew['participants']) ? explode(',', $paramsNew['participants']) : [];
        } else {
            $participants   = $paramsNew['participants'];
        }

        $firstRechargeL = $this->getFirstRecharge($paramsNew['recharge']);// 已充值量

        if (is_null($firstRechargeL)) {
            $this->errorMsg = '充值量不足 或  暂无充值记录';
            return false;
        }

        // 参与人员
        if (!in_array($this->player->type, $participants)) {
            $this->errorMsg = '此会员, 无法参与活动';
            return false;
        }



        // 3. 帐变 添加log
        if ($this->addLogAndRecharge($paramsNew, $type_child, $obtainType, $check, $activeM, $desc)) {
            $prizeConfigArr = config('active.prize');
            $this->successData = ['obtain_type' => $obtainType, 'prize' => $prizeConfigArr[$paramsNew['prize']], 'prize_value' => $paramsNew['prize_value']];

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
                        $this->errorMsg    = '领取成功, 获取' . $paramsNew['prize_value'] . '礼金';
                        break;
                    case 2;
                        $this->errorMsg    = '领取成功, 获取' . $paramsNew['prize_value'] . '积分';
                        break;
                    case 3;
                        $this->errorMsg    = '领取成功, 获取' . $paramsNew['prize_value'] . '元';
                        break;
                }
            }

            return true;
        }
        return false;
    }

    public function getOne()
    {
        $prizeArr                     = config('active.prize');
        $possibleArr                  = config('active.possible');
        $this->params['partner_sign'] = $this->partner_sign;
        $partnerActivityRuleM         = PartnerActivityRule::getOne($this->params);


        if ($partnerActivityRuleM == null) {
            $this->errorMsg = '活动不存在';
            return false;
        }

        // 时间
        $start_time = $partnerActivityRuleM->start_time;
        $end_time   = $partnerActivityRuleM->end_time;
        $now_time   = date('Y-m-d H:i:s');
        if ($now_time < $start_time || $now_time > $end_time ) {
            $this->errorMsg = '此活动已经结束';
            return false;
        }
        $partnerActivityRule = json_decode($partnerActivityRuleM->params, 1);

        if (!$partnerActivityRule) {
            $this->errorMsg = '活动不存在';
            return false;
        }

        $firstRecharge                   = [];
        $firstday             = date('Y-m-d 00:00:00');  //本月第一天

        if (!empty($this->player)) {
            $partnerActivityCount = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'user_id' => $this->player->id])->whereIn('type',[$this->type, 'gift_recharge'])->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time)->count('id');
            $firstRechargeM                  = $this->getFirstRecharge();
            $firstRecharge['possible_total'] = $this->getPossibleTotal($firstday);// 投注量
        } else {
            $partnerActivityCount            = 0;
            $firstRechargeM                  = null;
            $firstRecharge['possible_total'] = 0;// 投注量
        }



        $firstRecharge['recharged']      = $firstRechargeM == null ? 0 : bcdiv($firstRechargeM->amount, $this->moneyUnit,2);// 已充值量
        $firstRecharge['expired']        = $partnerActivityCount >= 1 ? 1 : 0;// 是否已领取

        $prizeActive = $partnerActivityRule['prize'];
        foreach ($prizeActive as $key => $activeRuleItem) {
            $firstRecharge['prize'][] = [
                'prize_id'          => $key,
                'possible'          => $activeRuleItem['possible'] ? $possibleArr[$activeRuleItem['possible']] : '',
                'possible_id'       => $activeRuleItem['possible']  ?? '',
                'possible_val'      => $activeRuleItem['possible_val']  ?? '',
                'recharge'          => $activeRuleItem['recharge']  ?? '',                                    // 需要充值多少钱
                'prize'             => $activeRuleItem['prize'] ? $prizeArr[$activeRuleItem['prize']] : '',
                'prize_value'       => $activeRuleItem['prize_value'] ?? '',
                'give_type'         => $activeRuleItem['give_type']  ?? '',
                'give_val'          => $activeRuleItem['give_val'] ?? '',
                'achieve_recharge'  => $firstRecharge['recharged'] >= $activeRuleItem['recharge'] ? 1 : 0,     // 是否可领取
                'achieve_possible'  => 1,                                                                     // 是否可领取
                'achieve'           => $firstRecharge['recharged'] >= $activeRuleItem['recharge']? 1 : 0,      // 是否可领取
                'expired'           => $partnerActivityCount >= 1 ? 1 : 0,                                    // 是否已领取
            ];
        }
        $firstRecharge['pc_desc']      = $partnerActivityRuleM->pc_desc ?? '';;
        $firstRecharge['h5_desc']      = $partnerActivityRuleM->h5_desc ?? '';;
        $this->errorMsg             = '';
        $this->successData          = $firstRecharge;
        
        return true;
    }
}