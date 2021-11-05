<?php namespace App\Models\Admin;

use App\Jobs\User\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/**
 * Tom 2019
 * Class AdminAccessLog
 * @package App\Models\Admin
 */
class AdminAccessLog extends Model
{
    protected $table = 'admin_access_logs';

    /**
     * 获取日志列表
     * @param $c
     * @return mixed
     */
    static function getList($c)
    {
        $query = self::orderBy('id', 'DESC');

        // 用户名
        if (isset($c['admin_username']) && $c['admin_username'] && $c['admin_username'] != "all") {
            $query->where('admin_username', $c['admin_username']);
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
    static function saveItem($user = null, $action = "login")
    {
        $routeName  = Route::getCurrentRoute()->getName();
        $params     = request()->all();

        $data = [
            'admin_username'    => $user ? $user->username : "---",
            'admin_id'          => $user ? $user->id : 0,
            'route'             => $routeName,
            'ip'                => real_ip(),
            'proxy_ip'          => real_ip(),
            'params'            => json_encode($params),
            'day'               => date("Ymd"),
            'action'            => $action,
            'device'            => \Browser::deviceFamily() . "_" . \Browser::deviceModel(),
            'platform'          => \Browser::platformName() . "_" . \Browser::platformVersion(),
            'browser'           => \Browser::browserName() . "_" . \Browser::browserVersion(),
            'agent'             => \Browser::userAgent(),
        ];

        // 分发
        jtq(new Log($data), 'log');

        return true;
    }
}
