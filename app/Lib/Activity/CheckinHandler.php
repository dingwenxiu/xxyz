<?php

namespace App\Lib\Activity;

use App\Lib\Activity\Core\BaseActivity;
use App\Lib\Clog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityRule;

class CheckinHandler extends BaseActivity
{
    // 获取记录
    public function getActLog()
    {
        $c['type']          = $this->type;
        $c['user_id']       = $this->player->id;
        $partnerActivityLog = PartnerActivityLog::getList($c);
        return $partnerActivityLog;
    }

    // 签到
    public function joinAct()
    {
        /**
         *checkin_days : 连续签到的天数
         *prize: 奖品
         *prize_val:  奖品数量
         *obtain_type: 领取方式
         *check: 是否需要审核
         */
        $days = (int)$this->params['days'] ?? 0;
        if ($days === 0) {
            $this->errorMsg = '缺少参数';
            return false;
        }


        $activeM = $this->activeM;
        if ($days === 1) {
            return $this->checkEveryOne($activeM);
        } else {
            return $this->checkRow($activeM, $days);
        }
    }

    public function checkEveryOne($activeM, $days = 1)
    {
        $params       = json_decode($activeM->params, 1);
        $paramsNew    = [];
        $paramsNew['possible']
                      = $params['possible'];                                                            // 每天需要达到条件
        $paramsNew['possible_val']
            = $params['possible_val'];                                                        // 条件值

        $participants = $params['participants'] ?? [];
        $obtainType = 1; // 没有奖品
        $check      = $paramsNew['check'] ?? 2;       // 没有奖品
        $type_child = 'checkin';
        $desc       = $activeM->name . '|签到' . $days . '天奖励';

        $checkin_days = $params['prize'];

        if (empty($checkin_days)) {
            $this->errorMsg = '活动不存在~';
            return false;
        }

        // 参与人员
        foreach ($checkin_days as $key => $val) {
            if ($days === $val['checkin_days']) {
                $paramsNew['checkin_days'] = $val['checkin_days'] ?? 1;   // 连续签到天数
                $paramsNew['prize']        = $val['prize'] ?? 3;          //奖品
                $paramsNew['prize_value']  = $val['prize_value'] ?? 0;    // 奖品数量
                $paramsNew['obtain_type']  = $val['obtain_type'] ?? 1;    // 领取方式
                $paramsNew['check']        = $val['check'] ?? 2;          // 是否需要审核
                $obtainType                = $val['obtain_type'] ?? 1;
            }
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
                $possibleTotal = $this->getRechargeTotal($firstday);
                if ($paramsNew['possible_val'] > $possibleTotal) {
                    $this->errorMsg = '当天充值量数量不满足, 无法参与活动';

                    return false;
                }
                break;
        }

        // 参与人员
        if ( ! in_array($this->player->type, $participants)) {
            $this->errorMsg = '此会员, 无法参与活动1';
            return false;
        }

        if ($this->addLogAndRecharge($paramsNew, $type_child, $obtainType, $check, $activeM, $desc)) {
            $this->successData = ['obtain_type' => $obtainType, 'prize' => $paramsNew['prize'], 'prize_val' => $paramsNew['prize_value']];

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
                        $this->errorMsg    = '签到成功, 获取' . $paramsNew['prize_value'] . '礼金';
                        break;
                    case 2;
                        $this->errorMsg    = '签到成功, 获取' . $paramsNew['prize_value'] . '积分';
                        break;
                    case 3;
                        $this->errorMsg    = '签到成功, 获取' . $paramsNew['prize_value'] . '元';
                        break;
                }
            }

            return true;
        }

        return false;
    }

    public function checkRow($activeM, $days)
    {
        try{
            // 参与要求
            $params         = json_decode($activeM->params, 1);
            $paramsNew      = [];
            $paramsNew['possible']       = $params['possible'];      // 每天需要达到条件
            $paramsNew['possible_val']    = $params['possible_val'];  // 条件值
            $participants   = $params['participants'] ?? [];
            $checkin_days   = $params['prize'];          // 参与人员
            $obtainType = 1;  // 没有奖品
            $check      = $paramsNew['check'] ?? 2;        // 没有奖品
            $type_child = 'checkin_lx_' . $days;
            $clientIp   = ip2long(real_ip());
            $userId     = $this->player->id;
            $desc       = $activeM->name . '|连续签到' . $days . '天奖励';

            foreach ($checkin_days as $key => $val) {
                if ($days === $val['checkin_days']) {
                    $paramsNew['checkin_days'] = $val['checkin_days'] ?? $days;   // 连续签到天数
                    $paramsNew['prize']        = $val['prize'] ?? 3;          //奖品
                    $paramsNew['prize_value']  = $val['prize_value'] ?? 0;    // 奖品数量
                    $paramsNew['obtain_type']  = $val['obtain_type'] ?? 1;    // 领取方式
                    $paramsNew['check']        = $val['check'] ?? 2;          // 是否需要审核
                    $obtainType                = $val['obtain_type'] ?? 1;
                }
            }

            if (empty($obtainType) || empty($paramsNew)) {
                $this->errorMsg = '参数错误';
                return false;
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
                    $possibleTotal = $this->getRechargeTotal($firstday);
                    if ($paramsNew['possible_val'] > $possibleTotal) {
                        $this->errorMsg = '当天充值量数量不满足, 无法参与活动';
                        return false;
                    }
                    break;
            }

            $firstday = date('Y-m-01 00:00:00');  //本月第一天
            $partnerActivityLog = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'type' => $this->type, 'type_child' => $type_child])->where(function ($query) use($userId, $clientIp) {
                $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
            })->where(
                'created_at', '>=', $firstday
            )->count('id');

            if ($partnerActivityLog >= 1) {
                $this->errorMsg = '连续签到' . $days . '天的奖品, 已经领取';
                return false;
            }


            // 参与人员
            if (!in_array($this->player->type, $participants)) {
                $this->errorMsg = '此会员, 无法参与活动';
                return false;
            }

            // 符合要求 进行签到帐变
            //执行内部代码
            $partnerActivityLog = PartnerActivityLog::where('partner_sign', $this->partner_sign)->where('user_id', $userId)->where('created_at', '>=', $firstday)->where('type', $this->type)->get();

            $cqArr = []; // 去重备用数组
            foreach ($partnerActivityLog as $k => $v) {
                $v = date('d',strtotime($v['created_at']));
                if (empty($cqArr[$v])){
                    $cqArr[$v] = $v;
                }
            }
            $signData = [];  // 出重后的数组
            foreach ($cqArr as $k => $v){
                $signData[] = $v;
            }
            sort($signData);
            $continuousSign = $this->getConsecutive($signData);  // 最长签到次数
    //      $continuousSignLast = $this->getConsecutiveLast($signData); // 最后一次签到次数

            if ($continuousSign < $days) {
                $this->errorMsg = '签到天数不满足';
                return false;
            }

            if ($this->addLogAndRecharge($paramsNew, $type_child, $obtainType, $check, $activeM, $desc)) {
                $this->successData = ['obtain_type' => $obtainType, 'prize' => $paramsNew['prize'], 'prize_val' => $paramsNew['prize_value']];

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

        }catch (\Exception $e) {
            Clog::activeLog("签到-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
        }
    }

    public function getOne()
    {
        $prizeArr                     = config('active.prize');
        $this->params['partner_sign'] = $this->partner_sign;
        $partnerActivityRule          = PartnerActivityRule::getOne($this->params);

        if (is_null($partnerActivityRule)) {
            $this->errorMsg = '活动不存在';
            return false;
        }
        $prizeActiveNew              = [];
        $prizeActiveNew['pc_desc']      = $partnerActivityRule->pc_desc ?? '';;
        $prizeActiveNew['h5_desc']      = $partnerActivityRule->h5_desc ?? '';;
        $firstday                    = date('Y-m-01 00:00:00');  //本月第一天
        $partnerActivityRule         = json_decode($partnerActivityRule->params, 1);

        if (!$partnerActivityRule) {
            $this->errorMsg = '活动不存在';
            return false;
        }
        $prizeActive  = $partnerActivityRule['prize'];
        $possibleVal  = $partnerActivityRule['possible_val'];  // 条件值

        foreach ($prizeActive as $key => $item2) {
            $prizeActiveNew['prize'][] = [
                'prize'     => $prizeArr[$item2['prize']],
                'prize_val' => $item2['prize_value'],
                'days'      => $item2['checkin_days'],
            ];
        }
        // 签到记录
        if (empty($this->player)) {
            $partnerActivityLog = $partnerActivityLXLog = [];
        } else {
            $partnerActivityLog = PartnerActivityLog::where('partner_sign', $this->partner_sign)->where('user_id', $this->player->id)->where('possible', 1)->where('possible_val', $possibleVal)->where('type_child', 'checkin')->where('created_at', '>=', $firstday)->get(['id', 'partner_sign', 'type', 'type_name', 'created_at', 'type_child']);

            $partnerActivityLXLog = PartnerActivityLog::where('partner_sign', $this->partner_sign)->where('user_id', $this->player->id)->where('possible', 1)->where('possible_val', $possibleVal)->where('type_child', 'like', 'checkin_lx_%')->where('created_at', '>=', $firstday)->get(['id', 'partner_sign', 'type', 'type_name', 'created_at', 'type_child']);
        }




        $dayOne = date('d');

        $prizeActiveNew['signed'] = 0;
        $cqArr                    = []; // 去重备用数组

        foreach ($partnerActivityLog as $k => $v) {
            $v = date('d',strtotime($v['created_at']));
            if ($dayOne == $v && $prizeActiveNew['signed'] == 0) {
                $prizeActiveNew['signed'] = 1;
            }
            if (empty($cqArr[$v])){
                $cqArr[$v] = $v;
            }
        }
        $signData = [];  // 出重后的数组
        foreach ($cqArr as $k => $v){
            $signData[] = $v;
        }

        sort($signData);
        $continuousSign                   = $this->getConsecutive($signData);  // 最长签到次数
        $prizeActiveNew['lx_max']         = $continuousSign;
        $prizeActiveNew['avtive_log']     = $partnerActivityLog;
        $prizeActiveNew['avtive_lx_log']  = [];

        foreach ($partnerActivityLXLog as $key => $lxLogItem) {
            $days = explode('_', $lxLogItem->type_child)[2] ?? 0;
            $prizeActiveNew['avtive_lx_log'][] =  $days ? ['days' => $days] : ['days' => 0];
        }

        $this->errorMsg    = '获取成功';
        $this->successData = $prizeActiveNew;
        return true;
    }

    // 获取连续签到最大值
    function getConsecutive($arr)
    {
        $arrLength     = count($arr); // 数组长度
        $arrNew1       = [];  // 存放连续数据(第一次)
        $arrNew2       = [];  // 存放连续数据（最后一次）
        $arrNewLength1 = $arrLength > 0 ? 1 :0; // 存放第一次连续的长度
        $arrNewLength2 = $arrLength > 0 ? 1 :0; // 存放最后一次连续的长度

        foreach ($arr as $key => $value) {
            // $value + 1 == $arr[$key + 1] 第一个数字 + 1 == 第二个数字  说明是连续数字
            // $key + 1 < $arrLength 长度不能超多数组长度

            if ($key + 1 < $arrLength && $value + 1 == $arr[$key + 1]) {
                if (!in_array($value, $arrNew1)) {
                    $arrNew1[] = $value;
                }
                if (!in_array($arr[$key + 1], $arrNew1)) {
                    $arrNew1[] = $arr[$key + 1];
                }
                $arrNewLength1 = count($arrNew1);
            }else{
                // 保存连续签到的最长数值
                // 保存arrNew最大数组
                if ($arrNewLength2 < $arrNewLength1) {
                    $arrNewLength2 = $arrNewLength1;
                    $arrNew2       = $arrNew1;
                    $arrNewLength1 = 1;
                }
                // 数据连续中断，则清空数据
                $arrNew1 = [];
            }
        }
        return $arrNewLength2;
    }

    // 获取最后一次连续签到次数
    function getConsecutiveLast($arr)
    {
        $arrLength      = count($arr); // 数组长度
        $arrNew1        = [];  // 存放连续数据(第一次)
        $arrNew2        = [];  // 存放连续数据（最后一次）
        $arrNewLength1  = $arrLength > 0 ? 1 :0; // 存放第一次连续的长度
        $arrNewLength2  = $arrLength > 0 ? 1 :0; // 存放最后一次连续的长度
        foreach ($arr as $key => $value) {
            // $value + 1 == $arr[$key + 1] 第一个数字 + 1 == 第二个数字  说明是连续数字
            // $key + 1 < $arrLength 长度不能超多数组长度
            if ($key + 1 < $arrLength && $value + 1 == $arr[$key + 1]) {
                if (!in_array($value, $arrNew1)) {
                    $arrNew1[] = $value;
                }
                if (!in_array($arr[$key + 1], $arrNew1)) {
                    $arrNew1[] = $arr[$key + 1];
                }
                $arrNewLength1 = count($arrNew1);
            }else{
                $arrNewLength2 = $arrNewLength1;
                $arrNew2 = $arrNew1;
                $arrNewLength1 = 1;
                // 数据连续中断，则清空数据
                $arrNew1 = [];
            }
        }
        return $arrNewLength2;
    }

}