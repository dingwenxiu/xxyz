<?php namespace App\Lib\Logic\Player;

use App\Lib\Clog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\BaseLogic;
use App\Models\Account\Account;
use App\Models\Player\Player;
use App\Models\Report\ReportUserSalary;

/**
 * Class SalaryLogic
 * @package App\Lib\Player
 */
class SalaryLogic extends BaseLogic {

    static function process($day, $autoSend = true) {

        self::_initSalary($day);
        $res = self::_countSalary($day);

        if ($res && $autoSend) {
            self::_sendSalary($day);
        }
    }

    /**
     * @param $day
     * @return bool
     * @throws \Exception
     */
    static function _initSalary($day) {

        // 1. 获取所有用户
        $totalPlayer = Player::where('salary_percentage', ">", 0)->count();

        Clog ::userSalary("init-start:day-{$day}-count-" . $totalPlayer);

        $pageSize   = 1000;
        $totalPage  = ceil($totalPlayer / $pageSize);

        $i = 0;
        do {
            $offset     = $pageSize * $i;
            $playerArr  = Player::select(
                "users.salary_percentage",
                "users.id",
                "users.partner_sign",
                "users.top_id",
                "users.parent_id",
                "users.user_level",
                "users.username",

                "report_stat_user_day.bets",
                "report_stat_user_day.cancel",
                "report_stat_user_day.he_return",
                "report_stat_user_day.commission_from_bet",
                "report_stat_user_day.commission_from_child",


                "report_stat_user_day.team_bets",
                "report_stat_user_day.team_cancel",
                "report_stat_user_day.team_he_return",
                "report_stat_user_day.team_commission_from_bet",
                "report_stat_user_day.team_commission_from_child"

            )->leftJoin('report_stat_user_day', 'users.id', '=', 'report_stat_user_day.user_id')
                ->where("report_stat_user_day.day", $day)
                ->where("users.salary_percentage", ">", 0)->skip($offset)->take($pageSize)->get();

            $data = [];
            foreach ($playerArr as $playerData) {
               $item = [
                    "partner_sign"      => $playerData->partner_sign,
                    "top_id"            => $playerData->top_id,
                    "user_level"        => $playerData->user_level,
                    "user_id"           => $playerData->id,
                    "parent_id"         => $playerData->parent_id??0,
                    "parent_username"   => '',
                    "username"          => $playerData->username,

                    "self_bets"         => $playerData->bets,
                    "self_cancel"       => $playerData->cancel,
                    "self_he_return"    => $playerData->he_return,
                    "self_real_bet"     => $playerData->bets - $playerData->cancel - $playerData->commission_from_bet- $playerData->commission_from_child,

                    "team_bets"         => $playerData->team_bets,
                    "team_cancel"       => $playerData->team_cancel,
                    "team_he_return"    => $playerData->team_he_return,
                    "team_real_bet"     => $playerData->team_bets - $playerData->team_cancel - $playerData->team_commission_from_bet - $playerData->team_commission_from_child,

                    "rate"              => $playerData->salary_percentage,
                    "day"               => $day,
                   'init_time'          => time()
                ];

                $item['total_salary']   = ($item['team_real_bet'] + $item['self_real_bet']) * $playerData->salary_percentage / 100;
                $item['self_salary']    =  $item['total_salary'];

                if ($item['total_salary'] > 0) {
                    $data[] = $item;
                }
            }

            ReportUserSalary::insert($data);

            $i ++;
        } while ($i < $totalPage);

        Clog ::userSalary("init-end:day-{$day}-count-" . $totalPlayer);

        return true;
    }

    /**
     * @param $day
     * @return bool
     * @throws \Exception
     */
    static function _countSalary($day) {

        $maxLevel = ReportUserSalary::where('day', $day)->where("status", 0)->max("user_level");

        $totalFailCount = 0;
        for($z = $maxLevel; $z > 0; $z --) {
            // 1. 获取所有用户
            $totalPlayer = ReportUserSalary::where('day', $day)->where("status", 0)->where("user_level", $z)->count();

            Clog ::userSalary("count-end:day-{$day}-count-" . $totalPlayer . "-level-{$z}");
            $pageSize   = 1000;
            $totalPage  = ceil($totalPlayer / $pageSize);

            $failCount  = 0;

            $i = 0;
            do {
                $offset     = $failCount;
                $reportArr  = ReportUserSalary::where('day', $day)->where("status", 0)->where("user_level", $z)->skip($offset)->take($pageSize)->get();

                // 更新 要么成功 要么失败
                db() -> beginTransaction();
                try {
                    $finishedPlayerArr = [];
                    foreach ($reportArr as $reportItem) {

                        if (!$reportItem->parent_id) {
                            $finishedPlayerArr[] = $reportItem->user_id;
                            continue;
                        }

                        $sql = "update `report_user_salary` set `child_salary` = `child_salary` + {$reportItem->total_salary}, `self_salary` = `self_salary` - {$reportItem->total_salary}  where `user_id` = '{$reportItem->parent_id}' and `day` = '{$day}'";
                        $ret = db()->update($sql);

                        if ($ret ) {
                            $finishedPlayerArr[] = $reportItem->user_id;
                        } else {
                            $failCount ++;
                        }
                    }

                    ReportUserSalary::whereIn("user_id", $finishedPlayerArr)->where("day", $day)->update(
                        [
                            'status'        => 1,
                            'count_time'    => time(),
                        ]
                    );

                    db()->commit();
                } catch (\Exception $e) {
                    db()->rollback();
                    $failCount += count($reportArr);

                    Clog ::userSalary("count-exception:". $e -> getMessage() . "-" . $e -> getLine() . "-" . $e -> getFile());
                    $i ++;
                    continue;
                }

                $i ++;
            } while ($i < $totalPage);

            $totalFailCount += $failCount;
            Clog ::userSalary("count-end:day-{$day}-count-" . $totalPlayer . "-level-{$z}-fail-" . $failCount);
        }

        if ($totalFailCount > 0) {

            // 报警
            return false;
        }

        return true;
    }

    /**
     * @param $day
     * @return bool|string
     * @throws \Exception
     */
    static function _sendSalary($day) {

        // 1. 是否存在计算失败的用户
        $totalNeedCountPlayer = ReportUserSalary::where('day', $day)->where("status", 0)->count();
        if ($totalNeedCountPlayer > 0) {
            Clog ::userSalary("send-error-工资计算未完成-{$totalNeedCountPlayer}");
            return "对不起, 工资计算未完成";
        }

        $totalReportArr = ReportUserSalary::where('day', $day)->where("status", 1)->count();

        $pageSize   = 2;
        $totalPage  = ceil($totalReportArr / $pageSize);

        $failCount = 0;

        $i = 0;
        do {
            $offset     = $failCount;
            $reportArr  = ReportUserSalary::where('day', $day)->where("status", 1)->skip($offset)->take($pageSize)->get();

            foreach ($reportArr as $reportItem) {
                $locker = new AccountLocker($reportItem -> user_id, "salary");
                if (!$locker -> getLock()) {
                    Clog ::userSalary("send-error-获取用户锁失败-{$reportItem -> user_id}");
                    return "对不起, 获取用户锁失败!!";
                }

                db() -> beginTransaction();
                try {

                    $account = Account::findAccountByUserId($reportItem->user_id);

                    // 充值上分
                    $params = [
                        'user_id' => $account -> user_id,
                        'amount'  => $reportItem->self_salary,
                        'desc'    => "-"
                    ];

                    $accountChange = new AccountChange();
                    $res           = $accountChange -> change($account, 'day_salary', $params);
                    if ($res !== true) {
                        $locker -> release();
                        db() -> rollback();
                        Clog ::userSalary("send-error-帐变失败-{$reportItem -> user_id}-{$res}");

                        $failCount ++;

                        continue;
                    }

                    $reportItem->status         = 2;
                    $reportItem->send_time      = time();
                    $reportItem->real_salary    = $reportItem->self_salary;
                    $reportItem->save();

                    db()->commit();
                } catch (\Exception $e) {
                    db() -> rollback();
                    Clog ::userSalary("send-exception:{$reportItem->user_id}" . $e -> getMessage() . "-" . $e -> getLine() . "-" . $e -> getFile());

                    $failCount ++;
                    continue;
                }
            }

            $i ++;
        } while ($i < $totalPage);

        if ($failCount > 0) {
            // 报警
        }

        return true;
    }


    /**
     * @param array $itemArr
     * @return array
     * @throws \Exception
     */
    static function sendSalary($itemArr = []) {

        $return = [
            'status'            => 1,
            'total_player'      => count($itemArr),
            'fail_count'        => 0,
            'player'            => [],
        ];

        foreach ($itemArr as $reportItem) {
            $return['player'][$reportItem->username] = [
                'username'  => $reportItem->username,
                'msg'       => "成功",
            ];

            $locker = new AccountLocker($reportItem -> user_id, "salary");
            if (!$locker -> getLock()) {
                Clog::userSalary("send-salary-error-获取用户锁失败-player:{$reportItem -> username}-day-$reportItem->day");
                $return['fail_count']   += 1;
                $return['player'][$reportItem->username]['msg'] = "获取用户锁失败";
                $return['status'] = 0;
                continue;
            }

            db() -> beginTransaction();
            try {

                $account = Account::findAccountByUserId($reportItem->user_id);
                if (!$account) {
                    Clog ::userSalary("send-error-账户不存在-player:{$reportItem -> user_id}");
                    $return['fail_count']   += 1;
                    $return['player'][$reportItem->username]['msg'] = "账户不存在-{$reportItem -> user_id}";
                    $return['status'] = 0;

                    continue;
                }

                // 日工资 上分
                $params = [
                    'user_id' => $account->user_id,
                    'amount'  => $reportItem->self_salary,
                    'desc'    => "-"
                ];

                $accountChange = new AccountChange();
                $res           = $accountChange -> change($account, 'day_salary', $params);

                if ($res !== true) {
                    $locker -> release();
                    db() -> rollback();
                    Clog ::userSalary("send-error-帐变失败-{$reportItem->username}-{$res}");

                    $return['fail_count']   += 1;
                    $return['player'][$reportItem->username]['msg'] = "帐变失败-{$res}";
                    $return['status'] = 0;

                    continue;
                }

                $reportItem->status         = ReportUserSalary::STATUS_SEND;
                $reportItem->send_time      = time();
                $reportItem->real_salary    = $reportItem->self_salary;
                $reportItem->save();

                db()->commit();
            } catch (\Exception $e) {
                db() -> rollback();
                $locker -> release();
                Clog ::userSalary("send-exception:{$reportItem->user_id}" . $e -> getMessage() . "-" . $e -> getLine() . "-" . $e -> getFile());
                $return['fail_count']   += 1;
                $return['player'][$reportItem->username]['msg'] = "异常-" . $e -> getMessage() . "-" . $e -> getLine();
                $return['status'] = 0;
                continue;
            }
        }

        return $return;
    }
}
