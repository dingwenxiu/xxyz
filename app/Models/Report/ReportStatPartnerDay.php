<?php

namespace App\Models\Report;

use App\Jobs\Stat\PartnerStatNotify;
use App\Models\Base;
use App\Models\Partner\Partner;
use Illuminate\Support\Facades\DB;

/**
 * 商户日结算
 * Class ReportStatPartnerDay
 * @package App\Models\Report
 */
class ReportStatPartnerDay extends Base {
    protected $table = 'report_stat_partner_day';

    /**
     * 获取列表
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 15) {
        $query = self::orderBy('id', 'desc');

        // 平台
        if(isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 日期 开始
        if(isset($c['start_day']) && $c['start_day']) {
            $query->where('day', ">=", $c['start_day']);
        }

        // 日期 结束
        if(isset($c['end_day']) && $c['end_day']) {
            $query->where('day', "<=", $c['end_day']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * @param $startTime
     * @param int $day
     * @return mixed
     */
    static function initStat($startTime, $day = 3) {

        $endTime    = time() + 86400 * $day;
        $daySet     = getDaySet($startTime, $endTime);

        $res    = Partner::all();

        $data   = [];
        foreach ($res as $partner) {
            foreach ($daySet as $day) {
                $check = self::where("partner_sign", $partner->sign)->where("day", $day)->first();
                if ($check) {
                    continue;
                }

                $data[] = [
                    'partner_sign'      => $partner->sign,
                    'partner_name'      => $partner->name,
                    'day'               => $day,
                ];
            }
        }

        return self::insert($data);
    }

    // 日结算
    static function doDayStat($day, $sign) {
        $query = ReportStatUserDay::select(
            "partner_sign",
            "day",

            DB::raw('SUM(first_register) as first_register'),
            DB::raw('SUM(have_bet) as have_bet'),
            DB::raw('SUM(recharge_count) as recharge_count'),
            DB::raw('SUM(first_recharge_count) as first_recharge_count'),
            DB::raw('SUM(repeat_recharge_count) as repeat_recharge_count'),
            DB::raw('SUM(withdraw_count) as withdraw_count'),

            DB::raw('SUM(recharge_amount) as recharge_amount'),
            DB::raw('SUM(withdraw_amount) as withdraw_amount'),

            DB::raw('SUM(bets) as bets'),
            DB::raw('SUM(cancel) as cancel'),
            DB::raw('SUM(he_return) as he_return'),
            DB::raw('SUM(bonus) as bonus'),
            DB::raw('SUM(salary) as salary'),
            DB::raw('SUM(dividend) as dividend'),
            DB::raw('SUM(gift) as gift'),

            DB::raw('SUM(commission_from_child) as commission_from_child'),
            DB::raw('SUM(commission_from_bet) as commission_from_bet'),
            DB::raw('SUM(system_transfer_add) as system_transfer_add'),
            DB::raw('SUM(system_transfer_reduce) as system_transfer_reduce')
        );

        $query->where('partner_sign', $sign);
        $query->where('day', $day);

        $item   =  $query->first();

        $_data = [];

        $profit = $item->bonus + $item->cancel + $item->he_return + $item->commission_from_child + $item->commission_from_bet + $item->system_transfer_add + $item->gift + $item->dividend + $item->gift + $item->salary
                - ($item->bets + $item->system_transfer_reduce);

        $_data[] = [
            "partner_sign"              => $sign,
            "day"                       => $day,
            "first_register"            => $item->first_register,
            "have_bet"                  => $item->have_bet,
            "recharge_count"            => $item->recharge_count,
            "first_recharge_count"      => $item->first_recharge_count,
            "repeat_recharge_count"     => $item->repeat_recharge_count,
            "withdraw_count"            => $item->withdraw_count,

            "recharge_amount"           => $item->recharge_amount,
            "withdraw_amount"           => $item->withdraw_amount,

            "bets"                      => $item->bets,
            "cancel"                    => $item->cancel,
            "he_return"                 => $item->he_return,
            "bonus"                     => $item->bonus,
            "salary"                    => $item->salary,
            "dividend"                  => $item->dividend,
            "gift"                      => $item->gift,

            "commission_from_child"     => $item->commission_from_child,
            "commission_from_bet"       => $item->commission_from_bet,

            "system_transfer_add"       => $item->system_transfer_add,
            "system_transfer_reduce"    => $item->system_transfer_reduce,

            "profit"                    => $profit,
        ];

        self::insert($_data);

        jtq(new PartnerStatNotify($day, $sign), "notify");
        return true;
    }

}
