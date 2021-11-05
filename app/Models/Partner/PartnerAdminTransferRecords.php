<?php
namespace App\Models\Partner;

use App\Models\Base;

/**
 * 后台转账
 * Class PartnerAdminTransferRecords
 * @package App\Models\Player
 */
class PartnerAdminTransferRecords extends Base
{
    const MODE_ADD      = 1;
    const MODE_REDUCE   = 1;

    static $mode = [
        1 => "理赔",
        2 => "扣减",
    ];

    protected $table = 'partner_admin_transfer_records';

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
    static function addItem($user, $mode, $type,  $amount, $partnerAdmin) {

        if ($mode == 'add') {
            $addTypes = config("user.main.transfer_add");
        } else if ($mode == 'reduce') {
            $addTypes = config("user.main.transfer_reduce");
        }


        $model = new self();
        $model->partner_sign         = $user->partner_sign;
        $model->username             = $user->username;
        $model->user_id              = $user->id;
        $model->top_id               = $user->top_id;
        $model->parent_id            = $user->parent_id;

        $model->is_tester            = $user->is_tester;
        $model->rid                  = $user->rid;

        $model->mode                 = $mode;
        $model->type                 = $type;

        $model->type_name            = $addTypes[$type]['name'];

        $model->amount               = $amount;

        $model->process_admin_id             = $partnerAdmin->id;
        $model->process_admin_name           = $partnerAdmin->username;

        $model->process_time         = time();
        $model->day_m                = date("YmdHi");

        $model->save();
        return true;
    }

    // 获取玩家总理赔金额
    static function getPlayerTotalClaim($userId) {
        $totalAmount = self::where('user_id', $userId)->where('mode', self::MODE_ADD)->sum('amount');
        return $totalAmount;
    }
}
