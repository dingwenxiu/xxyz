<?php

namespace App\Models\Player;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Jobs\Player\PlayerLogJob;
use Illuminate\Support\Facades\Route;
use GeoIP;

class PlayerLog extends Model
{
    protected $table = 'user_player_log';

    public $rules = [
        'user_id'           => 'required|min:1|max:32',
        'domain'            => 'required',
        'device_type'       => 'required',
        'platform'          => 'required',
        'browse'            => 'required',
        'agent'             => 'required',
        'ip'                => 'required|ip',
    ];

    /**
     * 获取用户日志列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
        $query = self::orderBy('id', 'DESC');

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // status
        if (isset($c['status']) && $c['status']) {
            $query->where('status', $c['status']);
        }

        // use_id
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // partner_sign
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // day
        if (isset($c['day']) && $c['day']) {
            $query->where('day', $c['day']);
        }

        // 创建日期
        if(isset($c['created_at'])) {
            $query->where('route','like','%'.'login'.'%')->where('updated_at', ">=", $c['created_at']);
        }

        // 更新日期
        if(isset($c['updated_at'])) {
            $query->where('route','like','%'.'login'.'%')->where('updated_at', "<=", $c['updated_at']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();


        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    static function saveItem($user = null, $action = "login")
    {
        $routeName  = Route::getCurrentRoute()->getName();
        $params     = request()->all();

        $location   = GeoIP::getLocation(real_ip());
        $data = [
            'partner_sign'              => $user ? $user->partner_sign : '000',
            'username'                  => $user ? $user->username : "---",
            'user_id'                   => $user ? $user->id : 0,
            'route'                     => $routeName,
            'ip'                        => real_ip(),
            'proxy_ip'                  => real_ip(),
            'params'                    => json_encode($params),
            'day'                       => date("Ymd"),
            'city'                      => $location['city'],
            'country'                   => $location['country'],
            'action'                    => $action,
            'device'                    => \Browser::deviceFamily() . "_" . \Browser::deviceModel(),
            'platform'                  => \Browser::platformName() . "_" . \Browser::platformVersion(),
            'browser'                   => \Browser::browserName() . "_" . \Browser::browserVersion(),
            'agent'                     => \Browser::userAgent(),
        ];

        // 分发
        jtq(new PlayerLogJob($data), 'log');
        return true;
    }

    /**
     * 获取select选项 ip
     * @param $userId
     * @return array
     */
    static function getIpListByUserId($userId) {
        $items = self::where('user_id', $userId)->get();
        $data = [];
        foreach($items as $item) {
            $data[] = $item->ip;
        }
        return $data;
    }
}
