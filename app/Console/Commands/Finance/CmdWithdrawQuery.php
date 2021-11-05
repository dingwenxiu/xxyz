<?php namespace App\Console\Commands\Finance;

use App\Console\Commands\Command;
use App\Lib\Clog;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use App\Lib\Pay\Pay;
use App\Models\Player\Player;
use App\Models\Finance\Withdraw;

/**
 * tom 2019
 * Class CmdWithdrawQuery
 * @package App\Console\Commands\Finance
 */
class CmdWithdrawQuery extends Command {

    protected $signature    = 'withdraw:query';
    protected $description  = "代付订单查询!";

    public function handle()
    {
        $lockKey = "withdraw_query_888";

        if (!cache()->add($lockKey, 1, now()->addMinutes(5))) {
            Clog::withdrawQuery('对不起, 当前脚本正在运行中!');
            return true;
        }


        $aWithdrawals = Withdraw::where([
            ["status", '=', Withdraw::STATUS_SEND_SUCCESS]
        ])->whereBetween('request_time', [ time() - 86400, time()])->take(60)->get();

        $totalCount = 0;
        Clog::withdrawQuery('CmdWithdrawQuery', ['data' => $aWithdrawals->toArray(), 'time' => microtime(true)]);
        foreach ($aWithdrawals as $oWithdrawal) {
            $totalCount ++;

            $handle = Pay::getHandle('panda');

            $handle->setWithdrawOrder($oWithdrawal);

            $resp   = $handle->queryWithdrawOrderStatus($oWithdrawal);

            if($resp['status'] == 1) {
                $locker = new AccountLocker($oWithdrawal->user_id);
                if(!$locker->getLock()){
                    db()->rollback();
                    Clog::withdrawQuery("对不起, 获取用户锁失败!!:");
                    continue;
                }

                db()->beginTransaction();
                try {

                    $user       = Player::find($oWithdrawal->user_id);
                    $account    = $user->account();

                    $params = [
                        'user_id'       => $user->id,
                        'amount'        => $oWithdrawal->amount,
                        'desc'          => "提现成功"
                    ];

                    $accountChange = new AccountChange();
                    $res = $accountChange->change($account, 'withdraw_finish',  $params);

                    if ($res !== true) {
                        $locker->release();
                        db()->rollback();
                        Clog::withdrawQuery("对不起, 提现回调帐变失败!!:", [$res]);
                        continue;
                    }

                    $oWithdrawal->real_amount   = $resp['amount'] * 10000;
                    $oWithdrawal->process_time  = time();

                    $oWithdrawal->status = Withdraw::STATUS_CALLBACK_SUCCESS;
                    $oWithdrawal->save();

                    db()->commit();
                } catch (\Exception $e) {
                    db()->rollback();
                    $locker->release();
                    Clog::withdrawQuery("提现异常:" . $e->getMessage() . "-" . $e->getLine());
                    continue;
                }

                $locker->release();

            } else if($resp['status'] == 2) {
                $locker = new AccountLocker($oWithdrawal->user_id);
                if(!$locker->getLock()){
                    db()->rollback();
                    Clog::withdrawQuery("对不起, 获取用户锁失败!!:");
                    continue;
                }

                db()->beginTransaction();
                try {

                    $user       = Player::find($oWithdrawal->user_id);
                    $account    = $user->account();

                    $params = [
                        'user_id'       => $user->id,
                        'amount'        => $oWithdrawal->amount,
                        'desc'          => "提现解冻-"
                    ];

                    $accountChange = new AccountChange();
                    $res = $accountChange->change($account, 'withdraw_un_frozen',  $params);

                    if ($res !== true) {
                        $locker->release();
                        db()->rollback();
                        Clog::withdrawQuery("对不起, 提现回调帐变失败!!:", [$res]);
                        continue;
                    }

                    $oWithdrawal->process_time  = time();

                    $oWithdrawal->status = Withdraw::STATUS_CALLBACK_FAIL;
                    $oWithdrawal->save();

                    db()->commit();
                } catch (\Exception $e) {
                    db()->rollback();
                    $locker->release();
                    Clog::withdrawQuery("提现异常:" . $e->getMessage() . "-" . $e->getLine());
                    continue;
                }

                $locker->release();
            }
        }

        cache()->forget($lockKey);
        Clog::withdrawQuery("End 本次一共回收{$totalCount}条数据!!");
        return true;
    }

}
