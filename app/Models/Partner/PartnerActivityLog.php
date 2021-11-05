<?php

namespace App\Models\Partner;

use App\Lib\Logic\Player\AccountLogic;
use App\Models\Account\Account;
use App\Models\Base;
use App\Models\Player\Player;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;

class PartnerActivityLog extends Base {

    public $errorMsg = '';
    public $models = '';


    static function getList($c, $pageSize = 15)
    {
        $query  = self::orderBy('id','desc');

        if (isset($c['partner_sign'])) {
            $query->where('partner_sign', $c['partner_sign']);
        }
        //规则名称
        if(isset($c['username'])) {
            $query->where('username', $c['username']);
        }

        if(isset($c['admin_name'])) {
            $query->where('admin_name', $c['admin_name']);
        }

        // 会员id
        if(isset($c['user_id'])) {
            $query->where('user_id', $c['user_id']);
        }
        //领取的方式 1 及时领取 2 第二天赠送 3 客服领取 4 每日签到领取无奖励
        if(isset($c['obtain_type'])) {
            $query->where('obtain_type', $c['obtain_type']);
        }
        //1 => 需要审核, 2 => 不需要审核
        if(isset($c['check'])) {
            $query->where('check', $c['check']);
        }
        //领取类型 1 已领去/审核同意  2 未领取, 3 审核-拒绝, 4 次日发放失败, 5 客服拒绝发放, 6 锁住
        if(isset($c['status'])) {
            $query->where('status', $c['status']);
        }
        //活动id
        if(isset($c['type'])) {
            $query->where('type', $c['type']);
        }
        // 奖品类型
        if(isset($c['prize'])) {
            $query->where('prize', $c['prize']);
        }
        // 开始时间
        if (isset($c['startTime']) && $c['startTime']) {
            $query -> where('created_at', '>=', $c['startTime']);
        }

        // 结束时间
        if (isset($c['endTime']) && $c['endTime']) {
            $query -> where('created_at', '<=', $c['endTime']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get()->makeHidden('rule_details');

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    public function checkLock($c, $id)
    {

        $status = $c['check_status'] ?? 0;
        $partnerAdminUser   = $c['partner_admin_username'] ?? 0;
        $partnerAdminUserId = $c['partner_admin_user_id'] ?? 0;


        $reason = $c['reason'] ?? '0';
        $moneyUnit = config('game.main.money_unit');

        if (!$status || !$partnerAdminUserId) {
            $this->errorMsg = '本条信息已被其他管理员操作';
            return false;
        }
        $activityLog = self::find($id);

        // 状态锁定 并且不是 本人操作 则 提示错误
        if ($activityLog->status == 6 && $activityLog->admin_id != $partnerAdminUserId) {
            $this->errorMsg = '本条信息已被其他管理员操作';
            return false;
        }

        db()->beginTransaction();
        // 如果状态值为1则发放
        if ($status == 1) {

            $prize = $activityLog->prize;
            $prize_val = $activityLog->prize_val;
            $type      = $activityLog->type;
            $userId    = $activityLog->user_id;

            $player = Player::find($userId);

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
                        'amount'               => $prize_val * $moneyUnit,
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
                        'related_type' => $type,
                        'related_id'   => $id,
                        'desc'          => '成功',
                    ];

                    $AccountLogicStatus = AccountLogic::addScore($player, $prize_val, $params);
                    if ( $AccountLogicStatus != true) {
                        $accountLocker->release();
                        db()->rollback();
                        $this->errorMsg = $AccountLogicStatus;

                        return false;
                    }
                    break;
                case 1: // 礼金
                    $params = [
                        'related_type' => $type,
                        'related_id'   => $id,
                        'desc'          => '成功',
                    ];

                    $AccountLogicStatus = AccountLogic::addGift($player, $prize_val, $params);
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
        }

        // 修改状态
        $activityLog->status   = $status;
        $activityLog->admin_id = $partnerAdminUserId;
        $activityLog->admin_name = $partnerAdminUser;
        if ($reason) {
            $activityLog->reason = $reason;
        }
        $activityLog->save();
        db()->commit();


        $this->errorMsg = '修改成功';
        return true;
    }

}
