<?php

namespace App\Lib\Activity;

use App\Lib\Activity\Core\BaseActivity;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityRule;

class GiftRechargeHandler extends BaseActivity
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
     *参与条件类型, 条件值, 充值量, 赠送金额/比例, 礼品类型, 礼品值 领取方式 参与人员 是否需要审核
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
        $params         = json_decode($activeM->params, 1);
        $prizeActive    = $params['prize'];
        $paramsNew      = $prizeActive[$fistRecharge] ?? 0;
        $obtainType     = $paramsNew['obtain_type'] ?? '';
        $check          = $paramsNew['check'] ?? '';
        $recharge       = $paramsNew['recharge'] ?? 0;

        $desc        = $activeM->name . '| 冲' . $recharge . '赠送' . $paramsNew['prize_value'];

        if (!$paramsNew || !$obtainType || !$check || !$recharge) {
            $this->errorMsg = '此活动参数错误';
            return false;
        }

        if (!is_array($paramsNew['participants'])) {
            $participants   = !empty($paramsNew['participants']) ? explode(',', $paramsNew['participants']) : [];
        } else {
            $participants   = $paramsNew['participants'];
        }

        $firstday = date('Y-m-d 00:00:00');  //当天

        switch ($paramsNew['possible']) {
            // 投注量
            case 1:
                $possibleTotal = $this->getPossibleTotal($firstday);
                if ($paramsNew['possible_val'] > $possibleTotal) {
                    $this->errorMsg = '当天投注数量不满足, 无法参与活动';
                    return false;
                }
                break;

            // 充值量
            case 2:
                $possibleTotal = $this->getNotFirstRecharge($firstday, $recharge);
                if ($paramsNew['possible_val'] > $possibleTotal['amountSum']) {
                    $this->errorMsg = '当天充值量数量不满足, 无法参与活动';
                    return false;
                }
                break;
        }


        $firstRechargeL     = $this->getNotFirstRecharge($firstday, $recharge);// 已充值量
        if ($firstRechargeL['amountCount'] <= 0) {
            $this->errorMsg = '充值量不足';
            return false;
        }

        // 参与人员
        if (!in_array($this->player->type, $participants)) {
            $this->errorMsg = '此会员, 无法参与活动';
            return false;
        }

        $type_child           = 'giftRecharge' . $fistRecharge;



        if ($this->addLogAndRecharge($paramsNew, $type_child, $obtainType, $check, $activeM, $desc)) {
            $prizeConfigArr = config('active.prize');

            $this->successData = ['obtain_type' => $obtainType, 'prize' => $prizeConfigArr[$paramsNew['prize']], 'prize_value' => $paramsNew['prize_value']];

            if ($check == 1) {
                $this->errorMsg    = '需要客服审核后, 在领取';
            } else if ($obtainType != 1) {
                switch ($obtainType) {
//                    case 2:
//                        $this->errorMsg    = '第二天赠送';
//                        break;
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
        $firstday = date('Y-m-d 00:00:00');  //当天


        if (!empty($this->player)) {
//            if ($this->player->register_time +  60 * 60 *24 > time()) {
//                $this->errorMsg = '新账户第二天可享受此活动';
//            }

            $partnerActivityCount = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'user_id' => $this->player->id])->whereIn('type', ['first_recharge', $this->type])->where('created_at', '>=', $firstday)->count('id');

            $firstRecharge['recharged']      = $this->getNotFirstRecharge($firstday)['amountSum'];// 已充值量
            $firstRecharge['possible_total'] = $this->getPossibleTotal($firstday);// 投注量

        } else {
            $partnerActivityCount            = 0;
            $firstRecharge['recharged']      = 0;// 已充值量
            $firstRecharge['possible_total'] = 0;// 投注量
        }



        $firstRecharge['expired']        = $partnerActivityCount >= 1 ? 1 : 0;// 是否已领取

        $prizeActive = $partnerActivityRule['prize'];

        foreach ($prizeActive as $key => $activeRuleItem) {

            $firstRecharge['prize'][] = [
                'prize_id'           => $key,
                'possible'           => $activeRuleItem['possible'] ? $possibleArr[$activeRuleItem['possible']] : '',
                'possible_id'        => $activeRuleItem['possible']  ?? '',
                'possible_val'       => $activeRuleItem['possible_val']  ?? '',
                'recharge'           => $activeRuleItem['recharge']  ?? '',
                'prize'              => $activeRuleItem['prize'] ? $prizeArr[$activeRuleItem['prize']] : '',
                'prize_value'        => $activeRuleItem['prize_value'] ?? '',
                'give_type'          => $activeRuleItem['give_type']  ?? '',
                'give_val'           => $activeRuleItem['give_val'] ?? '',
                'achieve_recharge'   => $firstRecharge['recharged'] >= $activeRuleItem['recharge'] ? 1 : 0,  // 是否可领取
                'achieve_possible'   => $firstRecharge['possible_total'] >= $activeRuleItem['possible_val'] ? 1 : 0,  // 是否可领取
                'achieve'            => $firstRecharge['possible_total'] >= $activeRuleItem['possible_val'] && $firstRecharge['recharged'] >= $activeRuleItem['recharge']? 1 : 0,  // 是否可领取
                'expired'            => $partnerActivityCount >= 1 ? 1 : 0,  // 是否已领取
            ];
        }

        $firstRecharge['pc_desc'] = $partnerActivityRuleM->pc_desc ?? '';
        $firstRecharge['h5_desc'] = $partnerActivityRuleM->h5_desc ?? '';
        $this->successData     = $firstRecharge;
        if ($this->errorMsg == '') {
            $this->errorMsg        = '';
            return true;
        }
        return false;
    }
}