<?php

namespace App\Models\Player;


use App\Models\Game\BaseGame;
use Illuminate\Support\Carbon;

class PlayerIpBackup extends BaseGame
{
    protected $table = 'user_ip_log_backup';

    public $rules = [
        'user_id'           => 'required|min:1|max:32',
        'ip'                => 'required|ip',
    ];

    /**
     * 获取用户列表
     * @param $c
     * @param $offset
     * @param $pageSize
     * @return mixed
     */
    //玩家IP记录备份 backup/playerIp
    static function getList($c)
    {


        $timeToday = Carbon::now();
        //抓三天潛到現在備份的資料
        $timeNow = strtotime($timeToday) - 60 * 60 * 24 * 6;
        $timeFuture = strtotime($timeToday);

        $query = self::orderBy('id', 'DESC');

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }
        if (isset($c['partner_name']) && $c['partner_name']) {
            $query->where('username', $c['partner_name']);
        }
        // status
        if (isset($c['status']) && $c['status']) {
            $query->where('status', $c['status']);
        }

        // partner_sign
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
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


    /**
     * 获取select选项 ip
     * @param $userId
     * @return array
     */
    static function getIpListByUserId($userId) {
        $items = self::where("status", 1)->where('user_id', $userId)->get();
        $data = [];
        foreach($items as $item) {
            $data[] = $item->ip;
        }
        return $data;
    }
}
