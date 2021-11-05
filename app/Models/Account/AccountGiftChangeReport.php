<?php namespace App\Models\Account;

use App\Models\Base;
use Illuminate\Support\Carbon;

class AccountGiftChangeReport extends Base
{
    protected $table = 'account_gift_change_report';

    static function getList($c, $pageSize  = 15) {

        $query = self::orderBy('id', 'desc');

        $timeToday = Carbon::now()->startOfWeek();
        $timeTom   = Carbon::now()->endOfWeek();
        $timeNow = strtotime($timeToday);
        $timeFuture = strtotime($timeTom);

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', trim($c['partner_sign']));
        }

        // 直属下级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', trim($c['username']));
        }

        // 游戏
        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', trim($c['lottery_sign']));
        }

        // 玩法
        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != "all") {
            $query->where('method_sign', trim($c['method_sign']));
        }

        // 用户名
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', trim($c['user_id']));
        }

        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', trim($c['parent_id']));
        }

        // 类型
        if (isset($c['type']) && $c['type'] && $c['type'] != 'all') {
            if (is_array($c['type'])) {
                $query->whereIn('type_sign', $c['type']);
            } else {
                $query->where('type_sign', $c['type']);
            }
        }

        // project id
        if (isset($c['project_id']) && $c['project_id']) {
            $query->where('project_id', $c['project_id']);
        }

        // start time
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('process_time', ">=", strtotime($c['start_time']));
        }

        // end time
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('process_time', "<=", strtotime($c['end_time']));
        }

        // 开始时间
        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
            $query->whereBetween('process_time',[strtotime($c['start_time']), strtotime($c['end_time'])]);
        }else{
            $query->whereBetween('process_time',[$timeNow,$timeFuture]);
        }

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    static function getSumBySign($username, $day = "") {

        $R = db()->table('account_gift_change_report')->select(
            'type_sign',
            'user_id',
            db()->raw('SUM(amount) as amount')
        );

        $R->where("username", $username);
        if ($day) {
            $startTime = strtotime($day);
            $endTime   = $startTime + 86400;

            $R->whereBetween("process_time", [$startTime, $endTime]);
        }

        $R->groupBy("type_sign");
        return $R->get();

    }
}
