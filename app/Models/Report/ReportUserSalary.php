<?php

namespace App\Models\Report;

use App\Models\Base;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportUserSalary extends Base {
    protected $table = 'report_user_salary';

    const STATUS_INIT       = 0;
    const STATUS_COUNT      = 1;
    const STATUS_SEND       = 2;

    /**
     * 获取薪水列表
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 15) {
        $yesterday  = Carbon::yesterday()->format('Ymd');

        $query = self::orderBy('id', 'desc');

        // 结束时间
        if (isset($c['start_day'], $c['end_day']) && $c['start_day'] && $c['end_day']) {
            $query->whereBetween('day',[date("Ymd", strtotime($c['start_day'])), date("Ymd", strtotime($c['end_day']))]);
        }else{
            $query->where('day', $yesterday);
        }

        // 平台
        if(isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户名
        if(isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 父级ID
        if(isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', $c['parent_id']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    static function getCount($playerId, $c) {
        $query = self::select(
            DB::raw('SUM(self_bets) as self_bets'),
            DB::raw('SUM(self_cancel) as self_cancel'),
            DB::raw('SUM(self_he_return) as self_he_return'),
            DB::raw('SUM(self_real_bet) as self_real_bet'),
            DB::raw('SUM(team_bets) as team_bets'),
            DB::raw('SUM(team_cancel) as team_cancel'),
            DB::raw('SUM(team_he_return) as team_he_return'),
            DB::raw('SUM(team_real_bet) as team_real_bet'),
            DB::raw('SUM(total_salary) as total_salary'),
            DB::raw('SUM(child_salary) as child_salary'),
            DB::raw('SUM(self_salary) as self_salary'),
            DB::raw('SUM(real_salary) as real_salary'),
            DB::raw('SUM(team_cancel) as team_cancel'),
            DB::raw('rate'),
            DB::raw('SUM(self_commission_from_bet) as self_commission_from_bet'),
            DB::raw('SUM(self_commission_from_child) as self_commission_from_child'),
            DB::raw('SUM(team_commission_from_child) as team_commission_from_child'),
            DB::raw('SUM(team_commission_from_bet) as team_commission_from_bet')
        )->orderBy('id', 'desc');

        $query->where('report_user_salary.user_id', $playerId);
        // 平台
        if(isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户名
        if(isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 父级ID
        if(isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', $c['parent_id']);
        }

        // 开始日期
        if(isset($c['start_day']) && $c['start_day']) {
            $query->where('day', ">=", date("Ymd", strtotime($c['start_day'])));
        }

        // 结束日期
        if(isset($c['end_day']) && $c['end_day']) {
            $query->where('day', "<=", date("Ymd", strtotime($c['end_day'])));
        }

        $item =  $query->first();
        if ($item) {
            $item->team_bets     = $item->team_bets + $item->self_bets;
            $item->team_real_bet = $item->team_real_bet + $item->self_real_bet;
        }

        return $item;
    }

    /**
     * 获取玩家某天的日工资
     * @param $playerId
     * @param $day
     * @return mixed
     */
    static function getPlayerTotalSalaryByDay($playerId, $day) {
        $day    = date("Ymd", strtotime($day));

        $item = self ::where("user_id", $playerId)->where('day', $day)->first();
        if (is_null($item)) {
            return 0;
        }
        $totalSalary = number4($item->real_salary);
        return $totalSalary;
    }
}
