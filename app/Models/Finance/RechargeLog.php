<?php

namespace App\Models\Finance;

use App\Models\Base;
use App\Models\User;
use App\Models\Admin\Province;
use App\Lib\Moon\AccountChange;
use App\Lib\Logic\AccountLocker;

class RechargeLog extends Base
{

    protected $table = 'user_recharge_log';

    static function getList($c, $pageSize = 20) {
        $query = self::orderBy('updated_at', 'desc');

        // 用ID
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 昵称
        if (isset($c['nickname']) && $c['nickname']) {
            $query->where('nickname', $c['nickname']);
        }

        // 上级
        if (isset($c['back_status']) && $c['back_status'] && $c['back_status'] != 'all') {
            if (is_array($c['back_status'])) {
                $query->whereIn('back_status', $c['back_status']);
            } else {
                $query->where('back_status', $c['back_status']);
            }
        }

        // 上级
        if (isset($c['order_id']) && $c['order_id']) {
            $query->where('order_id', trim($c['order_id']));
        }

        // 时间
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('init_time', ">=", strtotime($c['start_time']));
        }

        // 时间
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('init_time', "<=", strtotime($c['end_time']));
        }

        // 开始时间
        if (isset($c['start_created_at']) && $c['start_created_at']) {
            $query->where('created_at', ">=", $c['start_created_at']);
        }

        // 结束时间
        if (isset($c['end_created_at']) && $c['end_created_at']) {
            $query->where('created_at', "<=", $c['end_created_at']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $pageSize       = isset($c['pageSize']) ? intval($c['pageSize']) : $pageSize;

        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    static function initLog($user, $order, $params = "") {

        $model = new self();
        $model->ip                  = real_ip();
        $model->user_id             = $user->id;
        $model->username            = $user->username;
        $model->nickname            = $user->nickname;
        $model->partner_sign        = $user->partner_sign;

        $model->order_id            = $order->id;
        $model->amount              = $order->amount;

        $model->request_time        = time();
        $model->request_params      = $params ? json_encode($params) : '';
        $model->request_back        = '';
        $model->content             = "";
        $model->save();
        return $model;
    }
}
