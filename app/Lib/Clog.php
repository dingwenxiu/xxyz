<?php namespace App\Lib;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Tom 2019
 * Class Clog
 * @package App\Lib
 */
class Clog  {
    /** ================== 游戏 =================== */

    /**
     * 测试专用
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function test($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/test.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 游戏错误专用
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function gameError($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/error.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 游戏错误专用
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function jackpot($msg, $lotterySign = "main", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/jackpot/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * telegram
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function telegramLog($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/telegram/error.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 派奖
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function sendBonus($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/send-bonus/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 投注
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameBet($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/bet/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 撤单
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameCancel($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/cancel/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 撤单 = 奖期
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function issueCancel($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/issue-cancel/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 录号
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameEncode($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/encode/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 开奖
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameOpen($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/open/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 开奖
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameOpenProcess($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/open_process/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 超额奖金
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function prizeProcess($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/prize_process/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 开奖
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameStat($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/stat/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 开奖
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function statSend($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/stat_send/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 派奖
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameSend($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/send/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 返点
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function commission($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/commission/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 返点
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function commissionProcess($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/commission_process/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 追号
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function gameTrace($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/trace/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 追号
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function traceProcess($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/trace_process/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 追号
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function openWatch($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/watch_open/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 追号
     * @param $msg
     * @param string $lotterySign
     * @param array $data
     * @throws \Exception
     */
    static function traceWatch($msg, $lotterySign = "default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/trace_watch/{$lotterySign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 奖期生成
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function issueGen($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/issue-gen.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 奖期缓存日志
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function issueCache($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lottery/issue-cache.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**   ================  商户  ================   */

    /**
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function partner($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/partner/main.log";
        self::writeLog($logFile, $msg, $data);
    }

    /** ================== 用户日志 =================== */

    /**
     * 投注日志
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function userBet($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/user/bet.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 撤单日志
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function userCancelProject($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/user/cancel.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 用户缓存日志
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function userCache($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/user/cache.log";
        self::writeLog($logFile, $msg, $data);
    }


    /**
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function userAddChild($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/user/add-child.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }


    /** ========================= 财务日志 ========================== */

    /**
     * 充值日志
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function rechargeLog($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/finance/recharge.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 提现
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function withdrawLog($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/finance/withdraw.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 提现查询
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function withdrawQuery($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/finance/withdraw-query.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 充值回调
     * @param $msg
     * @param $sign
     * @param array $data
     * @throws \Exception
     */
    static function rechargeCallback($msg, $sign="default", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/finance/recharge-callback-{$sign}.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * 提现
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function curlPostLog($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/finance/curlPost.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * HandleLog日志
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function getHandleLog($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/finance/handleLog.log";
        self::writeLog($logFile, $msg, $data);
    }

    /** ============= 账户日志 ============== */

    /**
     * 张便日志
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function accountChange($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/account/process.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /**
     * 锁日志
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function accountLocker($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/account/locker.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /** ============= 管理日志 ============== */
    static function adminBehavior($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/admin/behavior.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /** ============= 商户日志 ============== */
    static function partnerBehavior($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/partner/behavior.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /** ============= 统计日志 ============== */

    /**
     * 商户统计
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function statPartner($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/stat/partner.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /**
     * 玩家统计
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function statUser($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/stat/user.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /**
     * stack 统计
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function statStack($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/stat/stack.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /**
     * 彩种统计
     * @param $msg
     * @param $lottery
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function statLottery($msg, $lottery = "xxxx", $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/stat/lottery-{$lottery}.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /**
     * 统计生成日志
     * @param $msg
     * @param array $data
     * @throws \Exception
     */
    static function statGen($msg, $data = []) {
        $dateStr = date("Y-md-");
        $logFile = "logs/$dateStr/stat/gen.log";
        self::writeLog($logFile, $msg, $data);
    }

    /**
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function userSalary($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/user/salary.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /**
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function userBonus($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/user/bonus.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /** ============= 锁日志 ============== */

    /**
     * 锁日志
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function lockError($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/lock/error.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /** ============= 娱乐城日志 ============== */
    /**
     * api接口日志
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function casinoApiLog($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/casino/Api.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /** ============= 娱乐城日志 ============== */
    /**
     * 活动接口日志
     * @param $msg
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    static function activeLog($msg, $data = []) {
        $dateStr = date("Y-m-d");
        $logFile = "logs/{$dateStr}/active/err.log";
        self::writeLog($logFile, $msg, $data);
        return true;
    }

    /**
     * 写入日志
     * @param $path
     * @param $msg
     * @param $context
     * @throws \Exception
     */
    static function writeLog($path, $msg, $context) {
        $logger = new Logger('custom_log');
        $logger->pushHandler(new StreamHandler(storage_path($path)));
        $logger->info($msg, $context);
    }
}
