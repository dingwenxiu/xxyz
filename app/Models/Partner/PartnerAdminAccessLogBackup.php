<?php

namespace App\Models\Partner;

use App\Models\Game\BaseGame;
use Illuminate\Support\Carbon;

class PartnerAdminAccessLogBackup extends BaseGame
{
    protected $table = 'partner_admin_access_logs_backup';

    /**
     * 获取日志列表
     * @param $c
     * @return mixed
     */
    static function getList($c)
    {
        $timeToday = Carbon::now();
        $timeNow = strtotime($timeToday) - 60 * 60 * 24 * 6;
        $timeFuture = strtotime($timeToday);

        $query = self::orderBy('id', 'DESC');

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != "all") {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户名
        if (isset($c['partner_admin_id']) && $c['partner_admin_id'] && $c['partner_admin_id'] != "all") {
            $query->where('partner_admin_id', $c['partner_admin_id']);
        }

        // 路由
        if (isset($c['route']) && $c['route']) {
            $query->where('route', $c['route']);
        }

        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {


            if (strtotime($c['end_time']) - strtotime($c['start_time']) >= 60 * 60 * 24 * 30) {
                self::$errStatic = '最长只能查询一个月';
                return false;
            }

            $query->whereBetween('created_at',[$c['start_time'], $c['end_time']]);
        }else{
            $query->whereBetween('created_at',[date('Y-m-d H:i:s', $timeNow), date('Y-m-d H:i:s', $timeFuture)]);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

}
