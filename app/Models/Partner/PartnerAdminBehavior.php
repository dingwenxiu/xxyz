<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\Partner\PartnerBehaviorJob;
use Illuminate\Support\Facades\Route;

class PartnerAdminBehavior extends Model
{
    protected $table = 'partner_admin_behavior';

    /**
     * 获取行为列表
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $pageSize = 15) {
        $query = self::orderBy('id', 'DESC');

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != "all") {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户名
        if (isset($c['admin_id']) && $c['admin_id']) {
            $query->where('admin_id', $c['admin_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 分类
        if (isset($c['type_id']) && $c['type_id'] && $c['type_id'] != "all") {
            $query->where('type_id', $c['type_id']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

     // 保存 管理员 行为
    static function saveItem($partnerUser, $type, $content) {

        $routeName  = Route::getCurrentRoute()->getName();
        $params     = request()->all();

        $data = [
            'partner_admin_username'  => $partnerUser ? $partnerUser->username : "---",
            'partner_admin_id'        => $partnerUser ? $partnerUser->id : 0,
            'partner_sign'            => $partnerUser->partner_sign ,
            'route'                   => $routeName,
            'ip'                      => real_ip(),
            'proxy_ip'                => real_ip(),
            'params'                  => json_encode($params,JSON_UNESCAPED_UNICODE),
            'day'                     => date("Ymd"),
            'action'                  => $type,
            'context'                 => json_encode($content,JSON_UNESCAPED_UNICODE),
            'device'                  => \Browser::deviceFamily() . "_" . \Browser::deviceModel(),
            'platform'                => \Browser::platformName() . "_" . \Browser::platformVersion(),
            'browser'                 => \Browser::browserName() . "_" . \Browser::browserVersion(),
            'agent'                   => \Browser::userAgent(),
        ];

        // 分发
        jtq(new PartnerBehaviorJob($data), 'behavior');

        return true;
    }
}
