<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminModule extends Model
{
    protected $table = 'admin_modules';

    // 获取列表
    static function getList($c) {
        $query = self::orderBy('id', 'desc');

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    static function  getMethodConfig($lotterySign) {
        return self::where("lottery_sign", $lotterySign)->where('status', 1)->where('show', 1)->get();
    }
}
