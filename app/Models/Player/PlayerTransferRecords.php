<?php

namespace App\Models\Player;

use App\Models\Base;


class PlayerTransferRecords extends Base
{
    protected $table = 'user_transfer_records';

    static function getList($c, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        // 转出用户ID
        if (isset($c['from_user_id']) && $c['from_user_id']) {
            $query->where('from_user_id', $c['from_user_id']);
        }

        // 转出用户名
        if (isset($c['from_nickname']) && $c['from_nickname']) {
            $query->where('from_nickname', $c['from_nickname']);
        }

        //  TO user ID
        if (isset($c['to_user_id']) && $c['to_user_id']) {
            $query->where('to_user_id', $c['to_user_id']);
        }

        // 转入用户名
        if (isset($c['to_nickname']) && $c['to_nickname']) {
            $query->where('to_nickname', $c['to_nickname']);
        }

        // 转入用户名
        if (isset($c['mode']) && $c['mode']) {
            $query->where('mode', $c['mode']);
        }

        // 转入用户名
        if (isset($c['type']) && $c['type']) {
            $query->where('type', $c['type']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    static function addItem($fromUser, $toUser,  $amount) {
        $model = new self();
        $model->from_username       = $fromUser->username;
        $model->from_user_id        = $fromUser->id;
        $model->from_parent_id      = $fromUser->parent_id;
        $model->partner_sign        = $fromUser->partner_sign;

        $model->to_username         = $toUser->username;
        $model->to_user_id          = $toUser->id;
        $model->to_parent_id        = $toUser->parent_id;

        $model->amount              = $amount;
        $model->day                 = date("Ymd");
        $model->add_time            = time();
        return $model->save();
    }
}
