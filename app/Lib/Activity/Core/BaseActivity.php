<?php

namespace App\Lib\Activity\Core;

use App\Lib\Clog;
use App\Lib\Logic\Player\AccountLogic;
use App\Models\Account\Account;
use App\Models\Account\AccountChangeReport;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityRule;
use App\Models\Report\ReportStatStack;

class BaseActivity
{
    public $player = '';
    public $type = '';
    public $partner_sign = '';
    public $params = '';
    public $errorMsg = '';
    public $moneyUnit = '';
    public $successData = '';
    public $activeM = '';

    public function __construct($player, $type, $partner_sign, $params = '')
    {
        $this->player       = $player ?? [];
        $this->type         = $type;
        $this->partner_sign = $partner_sign;
        $this->params       = $params;
        $this->moneyUnit    = config('game.main.money_unit');

        // 1. 获取签到规则
        $this->activeM = PartnerActivityRule::where('partner_sign', $partner_sign)->where('type', $type)->first();

        // 2. 活动有效性
        if (is_null($this->activeM)) {
            $this->errorMsg = '此活动不存在';
            return false;
        }

        // 3. 时间
        $start_time = $this->activeM->start_time;
        $end_time   = $this->activeM->end_time;
        $now_time   = date('Y-m-d H:i:s');
        if ($now_time < $start_time || $now_time > $end_time ) {
            $this->errorMsg = '此活动已经结束';
            return false;
        }

        // 4. 状态
        if ($this->activeM->status == 2) {
            $this->errorMsg = '此活动暂时停用';
            return false;
        }


    }

    /**
     * 投注量
     * @param $firstday
     *
     * @return string|null
     */
    public function getPossibleTotal($firstday, $lotterySign = 0)
    {
        $amount = AccountChangeReport::where(['partner_sign'=>$this->partner_sign,'user_id' =>$this->player->id,'type_sign'=>'bet_cost'])->where('created_at', '>=', $firstday);
        if ($lotterySign) {
            if (is_array($lotterySign)) {
                $amount->whereIn('lottery_sign', $lotterySign);
            } else {
                $amount->where('lottery_sign', $lotterySign);
            }
        }
        $amount = $amount->sum('amount');
        return bcdiv($amount,$this->moneyUnit,2);
    }

    /**
     * 充值量
     * @param $firstday
     *
     * @return string|null
     */
    public function getRechargeTotal($firstday)
    {
        $amount = AccountChangeReport::where(['partner_sign'=>$this->partner_sign,'user_id' =>$this->player->id,'type_sign'=>'recharge'])->where('created_at', '>=', $firstday)->sum('amount');

        return bcdiv($amount,$this->moneyUnit,2);

    }

    /**
     * 首冲金额
     * @param $amount
     *
     * @return mixed
     */
    public function getFirstRecharge($amount = 0)
    {
        return ReportStatStack::where(['partner_sign'=>$this->partner_sign, 'user_id' =>$this->player->id, 'type_sign'=>'recharge', 'is_first' => 1])->where('amount', '>=', bcmul($amount, $this->moneyUnit,2))->first();
    }

    public function getNotFirstRecharge($firstday, $amount = 0)
    {
        $amountSum   = ReportStatStack::where(['partner_sign'=>$this->partner_sign, 'user_id' => $this->player->id, 'type_sign'=>'recharge'])->where('amount', '>=', bcmul($amount, $this->moneyUnit,2))->where('created_at', '>=', $firstday)->sum('amount');
        $amountCount = ReportStatStack::where(['partner_sign'=>$this->partner_sign, 'user_id' =>$this->player->id, 'type_sign'=>'recharge'])->where('amount', '>=', bcmul($amount, $this->moneyUnit,2))->where('created_at', '>=', $firstday)->count('id');

        return ['amountSum' => bcdiv($amountSum,$this->moneyUnit,2), 'amountCount' => $amountCount];
    }

    /**
     * 获取当天最大充值
     * @param       $firstday
     * @param  int  $amount
     */
    public function maxTodayRecharge($firstday, $amount = 0)
    {
        $amountMax   = ReportStatStack::where(['partner_sign'=>$this->partner_sign, 'user_id' =>$this->player->id, 'type_sign'=>'recharge'])->where('amount', '>=', bcmul($amount, $this->moneyUnit,2))->where('created_at', '>=', $firstday)->max('amount');

        $amountId   = ReportStatStack::where(['partner_sign'=>$this->partner_sign, 'user_id' =>$this->player->id, 'type_sign'=>'recharge', 'amount' => $amountMax])->where('created_at', '>=', $firstday)->first('id');

        if (is_null($amountId)) {
            $this->errorMsg = '充值不足';
            return false;
        }

        return ['amountMax' => bcdiv($amountMax, $this->moneyUnit,2), 'amountId' => $amountId->id];
    }


    public function getRechargeCount($firstday)
    {
        return AccountChangeReport::where('partner_sign', $this->partner_sign)->where('user_id', $this->player->id)->where('created_at', '>=', $firstday)->where('type_sign', 'recharge')->count('id');

    }

    /**
     * 帐变 + 记录
     *
     */
    public function addLogAndRecharge($paramsNew, $type_child, $obtainType, $check, $activeM, $desc)
    {
        try {
            $clientIp   = ip2long(real_ip());
            $userId     = $this->player->id;

            $avtiveLogData['possible']     = $paramsNew['possible'];  // 领取类型
            $avtiveLogData['possible_val'] = $paramsNew['possible_val'];  // 领取类型
            $avtiveLogData['lottery_sign'] = $paramsNew['lottery_sign'] ?? '';  // 参与活动的彩票

            $avtiveLogData['partner_sign']
                                           = $this->partner_sign;  // 合作者标记
            $avtiveLogData['type']         = $this->type;  // 活动类型
            $avtiveLogData['type_name']    = $activeM->name;  // 活动类型
            $avtiveLogData['type_child']   = $type_child;  // 活动类型
            $avtiveLogData['active_id']    = $activeM->id;  // 活动id
            $avtiveLogData['obtain_type']  = $obtainType;  // 领取类型
            $avtiveLogData['check']        = $check;  // 领取类型
            $avtiveLogData['user_id']      = $this->player->id;  // 会员id
            $avtiveLogData['username']
                                           = $this->player->username;  // 会员名称
            $avtiveLogData['top_id']
                                           = $this->player->top_id;  // 顶级id
            $avtiveLogData['parent_id']
                                           = $this->player->parent_id;  // 顶级id
            $avtiveLogData['rid']
                                           = $this->player->rid;  // 所有上级id
            $avtiveLogData['created_at']   = now();
            $avtiveLogData['client_ip']   = $clientIp;

            // 活动礼品类型  10 每日签到无奖励 连续签到几天才有奖励
            // 礼品金额
            $avtiveLogData['prize']     = $paramsNew['prize'] ?? 0;
            $avtiveLogData['prize_val'] = $paramsNew['prize_value'] ?? 0;

            // 是否立即发放
            if ($obtainType == 1 && $check == 2) {
                $avtiveLogData['status'] = 1;
            } else {
                $avtiveLogData['status'] = 2;
            }


            // 是否立即发放
            db()->beginTransaction();

            // 1. 获取账户锁
            $accountLocker = new AccountLocker(
                $this->player->id, "active-" . $this->type . '-' . $this->player->id
            );

            if ( ! $accountLocker->getLock()) {
                $accountLocker->release();
                db()->rollback();
                $this->errorMsg = "对不起, 获取账户锁失败, 请稍后再试1!";
                return false;
            }
            switch ($this->type) {
                case 'checkin':
                    $partnerActivityLog = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'type' => $this->type, 'type_child' => $type_child])->where(function ($query) use($userId, $clientIp) {
                        $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
                    })->where(
                        'created_at', '>=', date("Y-m-d")
                    )->count('id');

                    if ($partnerActivityLog >= 1) {
                        $this->errorMsg = '今日已签到过';
                        return false;
                    }
                    break;

                case 'turntable':
                    $firstday = date('Y-m-d 00:00:00');  //本月第一天

                    $partnerActivityCount = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'type' => $this->type, 'type_child' => $type_child, 'possible' => $paramsNew['possible'], 'possible_val' => $paramsNew['possible_val']])->where(function ($query) use($userId, $clientIp) {
                        $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
                    })->where(
                        'created_at', '>=', $firstday
                    )->count('id');

                    if ($paramsNew['turn_num'] <= $partnerActivityCount) {
                        $this->errorMsg = '已参与过';
                        return false;
                    }

                    break;

                case 'turntable_one':

                    $firstday = date('Y-m-d 00:00:00');  //当天

                    $partnerActivityCount = PartnerActivityLog::where(['partner_sign' => $this->partner_sign, 'type' => $this->type, 'type_child' => $type_child])->where(function ($query) use($userId, $clientIp) {
                        $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
                    })->where(
                        'created_at', '>=', $firstday
                    )->count('id');

                    if ($paramsNew['turn_num'] <= $partnerActivityCount) {
                        $this->errorMsg = '已参与过';
                        return false;
                    }

                    break;

                case 'first_recharge':

                    $partnerActivityCount = PartnerActivityLog::where('partner_sign', $this->partner_sign)->whereIn('type',[$this->type, 'gift_recharge'])->where(function ($query) use($userId, $clientIp) {
                        $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
                    })->count('id');

                    if ($partnerActivityCount >= 1) {
                        $this->errorMsg = '已参与过';
                        return false;
                    }

                    break;
                case 'gift_recharge':

                    $firstday = date('Y-m-d 00:00:00');  //当天
                    $partnerActivityCount = PartnerActivityLog::where('partner_sign', $this->partner_sign)->whereIn('type', ['first_recharge', $this->type])->where('created_at', '>=', $firstday)->where(function ($query) use($userId, $clientIp) {
                        $query->where('user_id', $userId)->orWhere('client_ip', $clientIp);
                    })->count('id');

                    if ($partnerActivityCount >= 1) {
                        $this->errorMsg = '已参与过';
                        return false;
                    }

                    $amountMaxArr = $this->maxTodayRecharge($firstday, $paramsNew['prize_value']);

                    if (!$amountMaxArr)
                        return false;
                    if ($paramsNew['give_type'] == 2) {
                        $avtiveLogData['prize_val'] = $amountMaxArr['amountMax'] * $paramsNew['give_val'] / 100 ?? 0;
                        $avtiveLogData['order_id']  = $amountMaxArr['amountId']  ?? '';
                    }

                    break;
                default:
                    break;
            }

            if ($obtainType == 1 && $check == 2) {

                // 2. 添加活动记录
                $insertId = PartnerActivityLog::insertGetId($avtiveLogData);

                // 1. 如果金品类型为1 则添加余额 帐变
                switch ($avtiveLogData['prize']) {
                    case 3:  // 金额
                        // 2 . 帐变处理
                        $accountChange = new AccountChange();
                        // 3. 真实扣款
                        $account = Account::findAccountByUserId($this->player->id);
                        if ( ! $account) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = "对不起,  账户信息不存在, 请稍后再试2!";
                            return false;
                        }

                        $params = [
                            'user_id'              => $this->player,
                            'amount'               => $avtiveLogData['prize_val'] * $this->moneyUnit,
                            'activity_sign'        => $this->type,
                            'desc'                 => $desc,
                        ];

                        $res = $accountChange->change(
                            $account, 'active_amount', $params,
                            $this->player->is_robot
                        );
                        if ($res !== true) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = $res;
                            return false;
                        }

                        break;
                    case 2: // 积分

                        $params = [
                            'related_type' => $this->type,
                            'related_id'   => $insertId,
                            'desc'          => $desc,
                        ];

                        $AccountLogicStatus = AccountLogic::addScore($this->player, $avtiveLogData['prize_val'], $params);
                        if ( $AccountLogicStatus != true) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = $AccountLogicStatus;

                            return false;
                        }

                        break;
                    case 1: // 礼金
                        $params = [
                            'related_type' => $this->type,
                            'related_id'   => $insertId,
                            'desc'          => $desc,
                        ];

                        $AccountLogicStatus = AccountLogic::addGift($this->player, $avtiveLogData['prize_val'], $params);
                        if ( $AccountLogicStatus != true) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = $AccountLogicStatus;

                            return false;
                        }
                        break;
                    default:
                        break;
                }
                $accountLocker->release();
            } else {
                $insertId = PartnerActivityLog::insertGetId($avtiveLogData);
            }

            db()->commit();

            return true;//执行成功删除key并跳出循环
        }catch (\Exception $e) {
            db()->rollback();
            var_dump($activeM->name . "-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());die;
            Clog::activeLog($activeM->name . "-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
        }
    }

    /**
     * @return string
     */
    public function getReward($c, $id, $desc)
    {
        try{
            $status = $c['check_status'] ?? 0;
            $partnerAdminUser   = $c['partner_admin_username'] ?? 0;
            $partnerAdminUserId = $c['partner_admin_user_id'] ?? 0;

            $reason = $c['reason'] ?? '0';

            if (!$status) {
                $this->errorMsg = '本条信息已被其他管理员操作';
                return false;
            }
            $activityLog = PartnerActivityLog::find($id);

            // 状态锁定 并且不是 本人操作 则 提示错误
            if ($activityLog->status == 6 && $activityLog->admin_id != $partnerAdminUserId) {
                $this->errorMsg = '本条信息已被其他管理员操作';
                return false;
            }

            db()->beginTransaction();


            $prize     = $activityLog->prize;
            $prize_val = $activityLog->prize_val;
            $type      = $activityLog->type;
            $userId    = $activityLog->user_id;

            // 1. 获取账户锁
            $accountLocker = new AccountLocker(
                $userId, "active-amount-" . $type . $userId
            );
            if ( ! $accountLocker->getLock()) {
                $accountLocker->release();
                db()->rollback();
                $this->errorMsg = "对不起, 获取账户锁失败, 请稍后再试1!";
                return false;
            }


            // 如果状态值为1则发放
            if ($status == 1) {
                switch ($prize) {
                    case 3:
                        // 2 . 帐变处理
                        $accountChange = new AccountChange();
                        // 3. 真实扣款
                        $account = Account::findAccountByUserId($userId);
                        if ( ! $account) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = "对不起, 账户信息不存在, 请稍后再试2!";
                            return false;
                        }

                        $params = [
                            'user_id'              => $userId,
                            'amount'               => moneyUnitTransferIn($prize_val),
                            'activity_sign'        => $type,
                            'desc'                 => $activityLog->type_name . '|管理员审核通过',
                        ];

                        $res = $accountChange->change(
                            $account, 'active_amount', $params
                        );
                        if ($res !== true) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = $res;
                            return false;
                        }
                        break;
                    case 2: // 积分
                        $params = [
                            'related_type' => $this->type,
                            'related_id'   => $id,
                            'desc'          => $desc,
                        ];

                        $AccountLogicStatus = AccountLogic::addScore($this->player, $prize_val, $params);
                        if ( $AccountLogicStatus != true) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = $AccountLogicStatus;

                            return false;
                        }

                        break;
                    case 1: // 礼金
                        $params = [
                            'related_type' => $this->type,
                            'related_id'   => $id,
                            'desc'          => $desc,
                        ];

                        $AccountLogicStatus = AccountLogic::addGift($this->player, $prize_val, $params);
                        if ( $AccountLogicStatus != true) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = $AccountLogicStatus;

                            return false;
                        }
                        break;
                    default:
                        break;
                }
            }
            $accountLocker->release();

            // 修改状态
            $activityLog->status   = $status;
            $activityLog->admin_id = $partnerAdminUserId;
            $activityLog->admin_name = $partnerAdminUser;
            if ($reason) {
                $activityLog->reason = $reason;
            }
            $activityLog->save();
            db()->commit();

            return true;
        }catch (\Exception $e) {
            db()->rollback();
            var_dump("发送奖品-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());die;
            Clog::activeLog("发送奖品-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
        }
    }

}