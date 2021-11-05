<?php

namespace App\Models\Game;

class LotteryTraceList extends BaseGame
{
    protected $table = 'lottery_trace_detail';

    const STATUS_TRACE_INIT             = 0;
    const STATUS_TRACE_CANCEL           = 1;
    const STATUS_TRACE_SYSTEM_CANCEL    = 2;
    const STATUS_TRACE_WIN_STOP         = 3;
    const STATUS_TRACE_FINISHED         = 4;

    // 获取列表
    static function getList($c) {
        $query = self::orderBy('id', 'desc');

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id',  $c['user_id']);
        }

        // 彩种
        if (isset($c['lottery_sign']) && $c['lottery_sign']) {
            $query->where('lottery_sign', $c['lottery_sign']);
        }

        // 玩法
        if (isset($c['method_sign']) && $c['method_sign']) {
            $query->where('method_sign', $c['method_sign']);
        }

        // 订单号
        if (isset($c['trace_id']) && $c['trace_id']) {
            $query->where('trace_id', $c['trace_id']);
        }

        // 开始时间
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('add_time', ">=", $c['start_time']);
        }

        // 结束时间
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('add_time', "<=", $c['end_time']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        $_data = [];
        foreach ($data as $item) {
            $_data[] = [
                'id'                => hashId()->encode($item->id),
                'lottery_name'      => $item->lottery_name,
                'method_name'       => $item->method_name,
                'issue'             => $item->issue,
                'bet_number'        => $item->bet_number,
                'times'             => $item->times,
                'total_cost'        => number4($item->total_cost),
                'is_challenge'      => $item->is_challenge,
                'bonus'             => number4($item->bonus),
                'status'            => $item->status,
            ];
        }

        return ['data' => $_data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }
}
