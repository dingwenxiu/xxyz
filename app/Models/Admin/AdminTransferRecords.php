<?php
namespace App\Models\Admin;

use App\Models\Base;

/**
 * 后台转账
 * Class AdminTransferRecords
 * @package App\Models\Player
 */
class AdminTransferRecords extends Base
{
    const MODE_ADD      = 1;
    const MODE_REDUCE   = 1;

    static $mode = [
        1 => "理赔",
        2 => "扣减",
    ];

    protected $table = 'admin_transfer_records';

    static function getList($c, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        // 用户名
        if (isset($c['username'])) {
            $query->where('username', $c['username']);
        }

        // 用户名
        if (isset($c['nickname'])) {
            $query->where('nickname', $c['nickname']);
        }

        // 上级
        if (isset($c['user_id'])) {
            $query->where('user_id', $c['user_id']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    static function addItem($user, $mode, $type,  $amount, $desc, $admin = null) {
        $model = new self();
        $model->username             = $user->username;
        $model->nickname             = $user->nickname;
        $model->user_id              = $user->id;
        $model->top_id               = $user->top_id;
        $model->parent_id            = $user->parent_id;

        $model->mode                 = $mode;
        $model->type                 = $type;

        $model->amount               = $amount;
        $model->reason               = $desc;

        $model->admin_id             = $admin ? $admin->id : '' ;
        $model->admin_name           = $admin ? $admin->username : '' ;

        $model->add_time             = time();

        $model->save();
        return $model;
    }

    // 获取玩家总理赔金额
    static function getPlayerTotalClaim($userId) {
        $totalAmount = self::where('user_id', $userId)->where('mode', self::MODE_ADD)->sum('amount');
        return $totalAmount;
    }
}
