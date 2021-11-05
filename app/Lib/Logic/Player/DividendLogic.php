<?php namespace App\Lib\Logic\Player;

use App\Lib\Clog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\BaseLogic;
use App\Models\Account\Account;
use App\Models\Player\Player;
use App\Models\Report\ReportUserDividend;
use Illuminate\Support\Facades\DB;

/**
 * Class DividendLogic
 * @package App\Lib\Player
 */
class DividendLogic extends BaseLogic {

    /**
     * @param $month
     * @param $sort
     * @param $startDay
     * @param $endDay
     * @return bool
     * @throws \Exception
     */
    static function process($month, $sort, $startDay, $endDay) {
        self::_initDividend($month, $sort, $startDay, $endDay);
    }

    /**
     * @param $month
     * @param $sort
     * @param $startDay
     * @param $endDay
     * @return bool
     * @throws \Exception
     */
    static function _initDividend($month, $sort, $startDay, $endDay) {

        // 1. 获取所有用户
        $totalPlayer = Player::where('bonus_percentage', ">", 0)->count();

        Clog ::userBonus("init-start:month-{$month}-sort:{$sort}-start_day:{$startDay}-end_day:{$endDay}-count-" . $totalPlayer);

        $pageSize   = 2000;
        $totalPage  = ceil($totalPlayer / $pageSize);

        $i = 0;
        do {
            $offset     = $pageSize * $i;
            $playerArr  = Player::select(
                "users.bonus_percentage",
                "users.id",
                "users.partner_sign",
                "users.top_id",
                "users.parent_id",
                "users.user_level",
                "users.username",

                DB::raw('SUM(report_stat_user_day.bets) as bets'),
                DB::raw('SUM(report_stat_user_day.cancel) as cancel'),
                DB::raw('SUM(report_stat_user_day.bonus) as bonus'),
                DB::raw('SUM(report_stat_user_day.he_return) as he_return'),
                DB::raw('SUM(report_stat_user_day.commission_from_bet) as commission_from_bet'),
                DB::raw('SUM(report_stat_user_day.commission_from_child) as commission_from_child'),
                DB::raw('SUM(report_stat_user_day.gift) as gift'),
                DB::raw('SUM(report_stat_user_day.salary) as salary'),
                DB::raw('SUM(report_stat_user_day.dividend) as dividend'),

                DB::raw('SUM(report_stat_user_day.team_bets) as team_bets'),
                DB::raw('SUM(report_stat_user_day.team_cancel) as team_cancel'),
                DB::raw('SUM(report_stat_user_day.team_he_return) as team_he_return'),
                DB::raw('SUM(report_stat_user_day.team_commission_from_bet) as team_commission_from_bet'),
                DB::raw('SUM(report_stat_user_day.team_commission_from_child) as team_commission_from_child'),
                DB::raw('SUM(report_stat_user_day.team_bonus) as team_bonus'),
                DB::raw('SUM(report_stat_user_day.team_gift) as team_gift'),
                DB::raw('SUM(report_stat_user_day.team_salary) as team_salary')

            )->leftJoin('report_stat_user_day', 'users.id', '=', 'report_stat_user_day.user_id')
                ->where("report_stat_user_day.day", ">=", $startDay)
                ->where("report_stat_user_day.day", "<=", $endDay)
                ->where("users.bonus_percentage", ">", 0)
                ->groupBy("users.id")
                ->skip($offset)->take($pageSize)->get();

            $data = [];
            foreach ($playerArr as $playerData) {
                $item = [
                    "partner_sign"                      => $playerData->partner_sign,
                    "top_id"                            => $playerData->top_id,
                    "user_level"                        => $playerData->user_level,
                    "user_id"                           => $playerData->id,
                    "parent_id"                         => $playerData->parent_id??0,
                    "parent_username"                   => '',
                    "username"                          => $playerData->username,

                    "total_bets"                        => $playerData->bets + $playerData->team_bets,
                    "total_bonus"                       => $playerData->bonus + $playerData->team_bonus,
                    "total_cancel"                      => $playerData->cancel + $playerData->team_cancel,
                    "total_he_return"                   => $playerData->he_return +  $playerData->team_he_return,
                    "total_commission_from_bet"         => $playerData->commission_from_bet +  $playerData->team_commission_from_bet,
                    "total_commission_from_child"       => $playerData->commission_from_child +  $playerData->team_commission_from_child,
                    "total_gift"                        => $playerData->gift +  $playerData->team_gift,
                    "total_salary"                      => $playerData->salary +  $playerData->team_salary,
                    "total_dividend"                    => $playerData->dividend,

                    "rate"                              => $playerData->bonus_percentage,
                    "month"                             => $month,
                    "sort"                              => $sort,

                    "send_day"                          => date("Ymd"),
                    "end_day"                           => $endDay,
                    "from_day"                          => $startDay,
                    "send_time"                         => 0,
                    "status"                            => 0,
                    'init_time'                         => time()
                ];

                $item['profit'] = $item['total_bonus'] + $item['total_gift'] + $item['total_salary'] + $item['total_commission_from_bet']
                                    + $item['total_commission_from_child'] + $item['total_he_return'] + $item['total_cancel'] + $item['total_dividend']
                                    - $item['total_bets'];

                // 只有亏损了 才计算
                if ($item['profit'] < 0) {
                    $item["amount"] = intval(abs($item['profit']) * $item['rate'] / 100);
                } else {
                    $item["amount"] = 0;
                    $item["status"] = 2;
                }

                $data[] = $item;
            }

            ReportUserDividend::insert($data);

            $i ++;
        } while ($i < $totalPage);

        Clog ::userBonus("init-end:month-{$month}-sort:{$sort}-start_day:{$startDay}-end_day:{$endDay}");

        return true;
    }

    /**
     * @param array $itemArr
     * @return array
     * @throws \Exception
     */
    static function sendBonus($itemArr = []) {

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

            $locker = new AccountLocker($reportItem -> user_id, "bonus");
            if (!$locker -> getLock()) {
                db() -> rollback();
                Clog ::userBonus("send-error-获取用户锁失败-{$reportItem -> username}");
                $return['fail_count']   += 1;
                $return['player'][$reportItem->username]['msg'] = "获取用户锁失败";
                $return['status'] = 0;
                continue;
            }

            // 上级转下级
            $parentLocker = null;
            if ($reportItem->top_id) {
                $parentLocker = new AccountLocker($reportItem -> parent_id, "bonus");
                if (!$parentLocker -> getLock()) {
                    db() -> rollback();
                    $locker->release();
                    Clog ::userBonus("send-error-获取上级用户锁失败-player:{$reportItem -> username}-parent:{$reportItem -> parent_id}");
                    $return['fail_count']           += 1;
                    $return['player'][$reportItem->username]['msg'] = "获取上级用户锁失败";
                    $return['status'] = 0;
                    continue;
                }
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

                // 充值上分
                $params = [
                    'user_id' => $account->user_id,
                    'amount'  => $reportItem->amount,
                    'desc'    => "-"
                ];

                if (!$reportItem->top_id) {
                    $params['from_id'] = "-1";

                    $accountChange = new AccountChange();
                    $res           = $accountChange -> change($account, 'dividend_from_parent', $params);

                    if ($res !== true) {
                        $locker -> release();
                        db() -> rollback();
                        Clog ::userSalary("send-error-帐变失败-{$reportItem -> username}-{$res}");

                        $return['fail_count']   += 1;
                        $return['player'][$reportItem->username]['msg'] = "帐变失败-{$res}";
                        $return['status'] = 0;

                        continue;
                    }
                } else {
                    $params['from_id'] = $reportItem->parent_id;

                    // 从上级来
                    $accountChange = new AccountChange();
                    $res           = $accountChange -> change($account, 'dividend_from_parent', $params);

                    if ($res !== true) {
                        $locker -> release();
                        if ($parentLocker) {
                            $parentLocker -> release();
                        }

                        db() -> rollback();
                        Clog ::userSalary("send-error-帐变失败-to:{$reportItem -> username}-{$res}");

                        $return['fail_count']           += 1;
                        $return['player'][$reportItem->username]['msg'] = "帐变失败-{$res}";
                        $return['status'] = 0;

                        continue;
                    }

                    // 上级王下级转
                    $accountParentChange    = new AccountChange();
                    $parentAccount          = Account::findAccountByUserId($reportItem->parent_id);

                    $params["to_id"]    = $reportItem->user_id;
                    $res                = $accountParentChange -> change($parentAccount, 'dividend_to_child', $params);

                    if ($res !== true) {
                        $locker -> release();
                        if ($parentLocker) {
                            $parentLocker -> release();
                        }
                        db() -> rollback();
                        Clog ::userSalary("send-error-帐变失败-from:{$reportItem -> parent_id}-{$res}");

                        $return['fail_count']           += 1;
                        $return['player'][$reportItem->username]['msg'] = "上级帐变失败-{$res}";
                        $return['status'] = 0;

                        continue;
                    }
                }

                $reportItem->status         = 1;
                $reportItem->send_time      = time();
                $reportItem->real_amount    = $reportItem->amount;
                $reportItem->save();

                db()->commit();
            } catch (\Exception $e) {
                db() -> rollback();
                $locker -> release();
                if ($parentLocker) {
                    $parentLocker -> release();
                }

                Clog ::userBonus("send-exception:{$reportItem->user_id}" . $e -> getMessage() . "-" . $e -> getLine() . "-" . $e -> getFile());
                $return['fail_count']   += 1;
                $return['player'][$reportItem->username]['msg'] = "异常-" . $e -> getMessage() . "-" . $e -> getLine();
                $return['status'] = 0;
                continue;
            }
        }

        return $return;
    }


}
