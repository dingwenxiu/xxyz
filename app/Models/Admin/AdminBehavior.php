<?php

namespace App\Models\Admin;

use App\Jobs\Admin\AdminBehaviorJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class AdminBehavior extends Model
{
    protected $table = 'admin_behavior';

    /**
     * 获取行为列表
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $pageSize = 15) {
        $query = self::orderBy('id', 'DESC');

        // 用户名
        if (isset($c['admin_username']) && $c['admin_username']) {
            $query->where('admin_username', $c['admin_username']);
        }

        // 分类
        if (isset($c['action']) && $c['action'] && $c['action'] != "all") {
            $query->where('action', $c['action']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存 管理员 行为
    static function saveItem($adminUser, $type, $content) {

        $routeName  = Route::getCurrentRoute()->getName();
        $params     = request()->all();

        $data = [
            'admin_username'    => $adminUser ? $adminUser->username : "---",
            'admin_id'          => $adminUser ? $adminUser->id : 0,
            'route'             => $routeName,
            'ip'                => real_ip(),
            'proxy_ip'          => real_ip(),
            'params'            => json_encode($params),
            'day'               => date("Ymd"),
            'action'            => $type,
            'context'           => serialize($content),
            'device'            => \Browser::deviceFamily() . "_" . \Browser::deviceModel(),
            'platform'          => \Browser::platformName() . "_" . \Browser::platformVersion(),
            'browser'           => \Browser::browserName() . "_" . \Browser::browserVersion(),
            'agent'             => \Browser::userAgent(),
        ];

        // 分发
        jtq(new AdminBehaviorJob($data), 'behavior');
        return true;
    }

    // 登录
    static function doLogin($adminUser) {
        self::saveItem($adminUser, 'login', []);
    }

    // 修改自己登录密码
    static function doChangeSelfLoginPassword($adminUser) {
        self::saveItem($adminUser, 'change_self_login_password', []);
    }

    // 修改自己资金密码
    static function doChangeSelfFundPassword($adminUser) {
        self::saveItem($adminUser, 'change_self_fund_password', []);
    }

    // 修改玩家资金密码
    static function doChangePlayerFundPassword($adminUser) {
        self::saveItem($adminUser, 'change_player_fund_password', []);
    }

    // 修改玩家登录密码
    static function doChangePlayerLoginPassword($adminUser) {
        self::saveItem($adminUser, 'change_player_login_password', []);
    }

    // 修改玩家冻结状态
    static function doChangePlayerFrozenStatus($adminUser) {
        self::saveItem($adminUser, 'change_player_frozen_status', []);
    }

    // 系统理赔
    static function doSystemAddMoney($adminUser, $context = []) {
        self::saveItem($adminUser, 'system_add', $context);
    }

    // 系统扣减
    static function doSystemReduceMoney($adminUser, $context = []) {
        self::saveItem($adminUser, 'system_reduce', $context);
    }

    // 添加管理员
    static function doAddAdminUser($adminUser, $context = []) {
        self::saveItem($adminUser, 'add_admin_user', $context);
    }

    // 修改权限
    static function doChangeAdminAcl($adminUser, $context = []) {
        self::saveItem($adminUser, 'change_acl', $context);
    }
}
