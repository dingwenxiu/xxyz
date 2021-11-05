<?php

namespace App\Models\Game;

use Illuminate\Support\Carbon;

class LotteryTrace extends BaseGame
{
    protected $table = 'lottery_traces';

    const STATUS_INIT       = 0;
    const STATUS_WIN_STOP   = 1;
    const STATUS_FINISHED   = 2;

    const STATUS_FINISHED_PLAYER_CANCEL     = 3;
    const STATUS_FINISHED_ADMIN_CANCEL      = 4;
    const STATUS_FINISHED_SYSTEM_EXCEPTION  = 5;

    // 获取列表
    static function getList($c) {

        $timeToday = Carbon::now()->startOfWeek();
        $timeTom   = Carbon::now()->endOfWeek();
        $timeNow = strtotime($timeToday);
        $timeFuture = strtotime($timeTom);

        $query = self::orderBy('id', 'desc');
        
        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != 'all') {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id',  $c['user_id']);
        }

        // 彩种
        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', $c['lottery_sign']);
        }

        // 玩法
        if (isset($c['method_sign']) && $c['method_sign']) {
            $query->where('method_sign', $c['method_sign']);
        }

        // 订单号
        if (isset($c['id']) && $c['id']) {
            $id = hashId()->decode($c['id']);
            if ($id){
                $query->where('id', $id);
            } else {
                $query->where('id', '');
            }
        }

        // 开始奖期
        if (isset($c['start_issue']) && $c['start_issue']) {
            $query->where('start_issue', $c['start_issue']);
        }

        // 追停
//        if (isset($c['win_stop']) && $c['win_stop'] && $c['status'] != 'win_stop') {
//            $query->where('win_stop', $c['win_stop']);
//        }
        if (isset($c['win_stop'])) {
            $query->where('win_stop', $c['win_stop']);
        }

//        // 状态
//        if (isset($c['status']) && $c['status'] && $c['status'] != 'all') {
//            $query->where('status', $c['status']);
//        }
        if (isset($c['status'])){
            $query->where('status', $c['status']);
        }

        // ip
        if (isset($c['ip']) && $c['ip']) {
            $query->where('ip', $c['ip']);
        }

        // 开始时间
        // 结束时间
        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
            $query->whereBetween('time_bought',[strtotime($c['start_time']), strtotime($c['end_time'])]);
        }else{
            $query->whereBetween('time_bought',[$timeNow,$timeFuture]);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }
}
