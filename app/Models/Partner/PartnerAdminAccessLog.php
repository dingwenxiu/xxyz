<?php

namespace App\Models\Partner;

use App\Jobs\Partner\PartnerLogJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use GeoIP;
class PartnerAdminAccessLog extends Model
{
    protected $table = 'partner_admin_access_logs';

    /**
     * 获取日志列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
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

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存日志到队列
    static function saveItem($adminUser = null) {
        $routeName  = Route::getCurrentRoute()->getName();
        $params     = request()->all();

        $location   = GeoIP::getLocation(real_ip());
        $data = [
            'partner_sign'              => $adminUser ? $adminUser->partner_sign : '000',
            'partner_admin_username'    => $adminUser ? $adminUser->username : "---",
            'partner_admin_id'          => $adminUser ? $adminUser->id : 0,
            'route'                     => $routeName,
            'ip'                        => real_ip(),
            'proxy_ip'                  => real_ip(),
            'params'                    => json_encode($params),
            'day'                       => date("Ymd"),
            'city'                      => $location['city'],
            'country'                   => $location['country'],
            'device'                    => \Browser::deviceFamily() . "|" . \Browser::deviceModel(),
            'platform'                  => \Browser::platformName() . "|" . \Browser::platformVersion(),
            'browser'                   => \Browser::browserName()  . "|" . \Browser::browserVersion(),
            'agent'                     => \Browser::userAgent(),
            'add_time'                  => time(),
        ];

        // 分发
        jtq(new PartnerLogJob($data), 'log');

        return true;
    }
}
