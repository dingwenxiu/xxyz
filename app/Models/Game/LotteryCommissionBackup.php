<?php

namespace App\Models\Game;

use Illuminate\Support\Carbon;

class LotteryCommissionBackup extends BaseGame
{
    protected $table = 'lottery_commissions_backup';

    public $timestamps = false;

    public static $status = [
        0       => "待处理",
        1       => "已处理",
        -1      => "和局撤销",
        -2      => "撤单撤销",
    ];

    // 获取列表
    static function getList($c, $pageSize = 15)
    {
        $timeToday = Carbon::now();
        $timeNow = strtotime($timeToday) - 60 * 60 * 24 * 6;
        $timeFuture = strtotime($timeToday);

        $query = self::orderBy('id', 'desc');


        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {

            if (strtotime($c['end_time']) - strtotime($c['start_time']) >= 60 * 60 * 24 * 30) {
                self::$errStatic = '最长只能查询一个月';
                return false;
            }

            $query->whereBetween('add_time',[strtotime($c['start_time']), strtotime($c['end_time'])]);
        }else{
            $query->whereBetween('add_time',[$timeNow,$timeFuture]);
        }

        // 彩种
        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign',  $c['lottery_sign']);
        }

        //  商户号
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        //  用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        //  帐变ID
        if (isset($c['account_change_id']) && $c['account_change_id']) {
            $query->where('account_change_id', $c['account_change_id']);
        }

        //  类型
        if (isset($c['type']) && $c['type'] && in_array($c['type'], ['parent', 'self'])) {
            $query->where('from_type', $c['type']);
        }

        // 玩法
        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != "all") {
            $query->where('method_sign', $c['method_sign']);
        }

        // 注单编号
        if (isset($c['hash_id']) && $c['hash_id']) {
            $id = hashId()->decode($c['hash_id']);
            if ($id){
                $query->where('id', $id);
            } else {
                $query->where('id', '');
            }
        }

        // 订单号
        if (isset($c['project_id']) && $c['project_id']) {
            $c['project_id'] = hashId()->decode($c['project_id']);
            $query->where('project_id', $c['project_id']);
        }

        // 奖期
        if (isset($c['issue']) && $c['issue']) {
            $query->where('issue', $c['issue']);
        }

        // 开始时间
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('add_time', '>=', $c['start_time']);
        }

        // 结束时间
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('add_time', '<=', $c['end_time']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 获取列表
    static function getProjectCommission($projectId) {
        $set = self::where('project_id', $projectId)->orderBy('id', 'desc')->get();
        $data = [];
        foreach ($set as $item) {
            $data[] = [
                "lottery_name"      => $item->lottery_name,
                "method_name"       => $item->method_name,
                "user_id"           => $item->user_id,
                "from_type"         => $item->from_type,
                "amount"            => number4($item->amount),
                "self_prize_group"  => $item->self_prize_group,
                "child_prize_group" => $item->child_prize_group,
                "bet_prize_group"   => $item->bet_prize_group,
                "status"            => $item->status,
            ];
        }
        return $data;
    }

    /**
     * 获取指定日期的 返点统计
     * @param $playerId
     * @param $day
     * @return array
     */
    static function getPlayerDaySum($playerId, $day) {
        $startTime = strtotime($day);
        $endTime   = $startTime + 86400;
        $items = self::where('user_id', $playerId)->whereBetween('process_time', [$startTime, $endTime])->get();
        $data = [
            'commission_from_bet'       => 0,
            'commission_from_child'     => 0,
        ];

        foreach ($items as $item) {
            if ($item->status == 1 && $item->from_type == 'self') {
                $data['commission_from_bet'] += $item->amount;
            }

            if ($item->status == 1 && $item->from_type == 'parent' ) {
                $data['commission_from_child'] += $item->amount;
            }

        }

        return $data;
    }
}
