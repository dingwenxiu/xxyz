<?php

namespace App\Models\Report;

use App\Models\Base;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Lib\Clog;

class ReportUserDividend extends Base {
    protected $table = 'report_user_dividend';

    const STATUS_INIT       = 0;
    const STATUS_SEND       = 1;
    const STATUS_NO_BONUS   = 2;
    /**
     * 获取列表
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 15) {
        $firstDay  = Carbon::now()->firstOfMonth()->format('Ymd');
        $haflMonth = Carbon::now()->firstOfMonth()->addDays(15)->format('Ymd');
        $lastHalf  = Carbon::now()->firstOfMonth()->endOfMonth()->format('Ymd');
        $timeNow   =   Carbon::now()->format('Ymd');

        $query = self::orderBy('id', 'desc');

        // 商户
        if(isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 上级
        if(isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', $c['parent_id']);
        }

        // 用户
        if(isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // 用户名
        if(isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 日期 开始
        if(isset($c['start_time']) && $c['start_time']) {

            $query->where('send_time', ">=", strtotime($c['start_time']));
        }

        // 日期 结束
        if(isset($c['end_time']) && $c['end_time']) {
            $query->where('send_time', "<=", strtotime($c['end_time']));
        }

        
        // 开始日期
        if(isset($c['start_day']) && $c['start_day']) {
            $query->where('from_day', ">=", date("Ymd", strtotime($c['start_day'])));
        }

        // 结束日期
        if(isset($c['end_day']) && $c['end_day']) {
            $query->where('end_day', "<=", date("Ymd", strtotime($c['end_day'])));
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }


    static function getCount($c) {
        $query = self::select(
            DB::raw('SUM(total_cancel) as total_cancel'),//总撤销
            DB::raw('SUM(total_he_return) as total_he_return'),//和值返款
            DB::raw('SUM(total_commission_from_bet) as total_commission_from_bet'),//总投注返点
            DB::raw('SUM(total_bets) as total_bets'),//投注总额
            DB::raw('SUM(total_bonus) as total_bonus'),//派奖总额
            DB::raw('SUM(total_dividend) as total_dividend'),//分红金额
            DB::raw('SUM(real_amount) as real_amount'),//发送分红金额
            DB::raw('SUM(amount) as amount'),//理论应发送分红金额
            DB::raw('SUM(total_commission_from_child) as total_commission_from_child'),//返点总额
            DB::raw('SUM(total_gift) as total_gift'),//促销红利
            DB::raw('SUM(total_salary) as total_salary'),//日工资
            DB::raw('SUM(profit) as profit'), //净盈亏
            DB::raw('rate'),//分红比例
            DB::raw('username')//用户名
            // 投注人数总计 暂无
        )->orderBy('id', 'desc');

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
            $query->where('from_day', ">=", date("Ymd", strtotime($c['start_day'])));
        }

        // 结束日期
        if(isset($c['end_day']) && $c['end_day']) {
            $query->where('end_day', "<=", date("Ymd", strtotime($c['end_day'])));
        }
        return $query->first();
    }
}
