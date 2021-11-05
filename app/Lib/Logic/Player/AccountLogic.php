<?php namespace App\Lib\Logic\Player;

use App\Lib\Logic\AccountGiftLocker;
use App\Lib\Logic\AccountScoreLocker;
use App\Lib\Logic\BaseLogic;
use App\Models\Account\Account;
use App\Models\Account\AccountGiftChangeReport;
use App\Models\Account\AccountScoreChangeReport;

/**
 * Class AccountLogic
 * @package App\Lib\Player
 */
class AccountLogic extends BaseLogic {

    // 添加积分
    static function addScore($player, $amount, $params = []) {
        if (!$amount || !is_numeric($amount)) {
            return "对不起, 无效的金额!";
        }

        $lockerObj = new AccountScoreLocker($player->id, "add-score");
        if (!$locker = $lockerObj->getLock() ) {
            return "获取用户锁积分失败";
        }

        $account = Account::findAccountByUserId($player->id);
        if (!$account) {
            return "对不起, 账户不存在";
        }

        $amount = moneyUnitTransferIn($amount);

        db() -> beginTransaction();
        try {
            // 记录
            $data = [
                'partner_sign'      => $player->partner_sign,
                'user_id'           => $player->id,
                'top_id'            => $player->top_id,
                'parent_id'         => $player->parent_id,
                'username'          => $player->username,
                'is_tester'         => $player->is_tester,

                'flow_type'         => 1,
                'related_type'      => isset($params['related_type']) ? $params['related_type'] : 1,
                'related_id'        => isset($params['related_id']) ? $params['related_id'] : 0,

                'day_m'             => date("YmdHi"),
                'amount'            => $amount,
                'before_amount'     => $account->score,
                'current_amount'    => bcadd($account->score, $amount),
                'process_time'      => time(),
                'desc'              => isset($params['desc']) ? $params['desc'] : "",
            ];

            AccountScoreChangeReport::insert($data);

            // 变更
            $account->score = $data['current_amount'];
            $account->save();

            db()->commit();

        } catch (\Exception $e) {
            db()->rollback();
            $lockerObj->release();
            return "对不起, " . $e->getMessage();
        }

        $lockerObj->release();
        return true;
    }

    // 减少积分
    static function reduceScore($player, $amount, $params = []) {
        if (!$amount || !is_numeric($amount)) {
            return "对不起, 无效的金额!";
        }

        $lockerObj = new AccountScoreLocker($player->id, "add-score");
        if (!$locker = $lockerObj->getLock() ) {
            return "获取用户积分锁失败";
        }

        $account = Account::findAccountByUserId($player->id);
        if (!$account) {
            return "对不起, 账户不存在";
        }

        $amount = moneyUnitTransferIn($amount);

        if ($account->score < $amount) {
            return "对不起, 剩余积分不足";
        }

        db() -> beginTransaction();
        try {
            // 记录
            $data = [
                'partner_sign'      => $player->partner_sign,
                'user_id'           => $player->id,
                'top_id'            => $player->top_id,
                'parent_id'         => $player->parent_id,
                'username'          => $player->username,
                'is_tester'         => $player->is_tester,

                'flow_type'         => 2,
                'related_type'      => isset($params['related_type']) ? $params['related_type'] : 1,
                'related_id'        => isset($params['related_id']) ? $params['related_id'] : 0,

                'day_m'             => date("YmdHi"),
                'amount'            => $amount,
                'before_amount'     => $account->score,
                'current_amount'    => bcsub($account->score, $amount),
                'process_time'      => time(),
                'desc'              => isset($params['desc']) ? $params['desc'] : "",
            ];

            AccountScoreChangeReport::insert($data);

            // 变更
            $account->score = $data['current_amount'];
            $account->save();

            db()->commit();

        } catch (\Exception $e) {
            db()->rollback();
            $lockerObj->release();
            return "对不起, " . $e->getMessage();
        }

        $lockerObj->release();
        return true;
    }

    // 添加礼金
    static function addGift($player, $amount, $params = []) {
        if (!$amount || !is_numeric($amount)) {
            return "对不起, 无效的金额!";
        }

        $lockerObj = new AccountGiftLocker($player->id, "add-gift");
        if (!$locker = $lockerObj->getLock() ) {
            return "获取用户锁礼金失败";
        }

        $account = Account::findAccountByUserId($player->id);
        if (!$account) {
            return "对不起, 账户不存在";
        }

        $amount = moneyUnitTransferIn($amount);

        db() -> beginTransaction();
        try {
            // 记录
            $data = [
                'partner_sign'      => $player->partner_sign,
                'user_id'           => $player->id,
                'top_id'            => $player->top_id,
                'parent_id'         => $player->parent_id,
                'username'          => $player->username,
                'is_tester'         => $player->is_tester,

                'flow_type'         => 1,
                'related_type'      => isset($params['related_type']) ? $params['related_type'] : 1,
                'related_id'        => isset($params['related_id']) ? $params['related_id'] : 0,

                'day_m'             => date("YmdHi"),
                'amount'            => $amount,
                'before_amount'     => $account->gift,
                'current_amount'    => bcadd($account->gift, $amount),
                'process_time'      => time(),
                'desc'              => isset($params['desc']) ? $params['desc'] : "",
            ];

            AccountGiftChangeReport::insert($data);

            // 变更
            $account->gift = $data['current_amount'];
            $account->save();

            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            $lockerObj->release();
            return "对不起, " . $e->getMessage();
        }
        $lockerObj->release();
        return true;
    }

    // 减少礼金
    static function reduceGift($player, $amount, $params = []) {
        if (!$amount || !is_numeric($amount)) {
            return "对不起, 无效的金额!";
        }

        $lockerObj = new AccountGiftLocker($player->id, "add-gift");
        if (!$locker = $lockerObj->getLock() ) {
            return "获取用户锁礼金失败";
        }

        $account = Account::findAccountByUserId($player->id);
        if (!$account) {
            return "对不起, 账户不存在";
        }

        $amount = moneyUnitTransferIn($amount);

        if ($account->gift < $amount) {
            return "对不起, 剩余礼金不足";
        }

        db() -> beginTransaction();
        try {
            // 记录
            $data = [
                'partner_sign'      => $player->partner_sign,
                'user_id'           => $player->id,
                'top_id'            => $player->top_id,
                'parent_id'         => $player->parent_id,
                'username'          => $player->username,
                'is_tester'         => $player->is_tester,

                'flow_type'         => 2,
                'related_type'      => isset($params['related_type']) ? $params['related_type'] : 1,
                'related_id'        => isset($params['related_id']) ? $params['related_id'] : 0,

                'day_m'             => date("YmdHi"),
                'amount'            => $amount,
                'before_amount'     => $account->gift,
                'current_amount'    => bcsub($account->gift, $amount),
                'process_time'      => time(),
                'desc'              => isset($params['desc']) ? $params['desc'] : "",
            ];

            AccountGiftChangeReport::insert($data);

            // 变更
            $account->gift = $data['current_amount'];
            $account->save();

            db()->commit();

        } catch (\Exception $e) {
            db()->rollback();
            $lockerObj->release();
            return "对不起, " . $e->getMessage();
        }

        $lockerObj->release();
        return true;
    }
}
