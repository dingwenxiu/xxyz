<?php

namespace App\Models\Player;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use App\Jobs\Player\PlayerIpLogJob;
use GeoIP;

class PlayerIp extends Model
{
    protected $table = 'user_ip_log';

    public $rules = [
        'user_id'           => 'required|min:1|max:32',
        'ip'                => 'required|ip',
    ];

    /**
     * 获取用户列表
     * @param $c
     * @param $offset
     * @param $pageSize
     * @return mixed
     */
    static function getList($c) {
        $query = self::orderBy('id', 'DESC');

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // partner_sign
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
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
            'user_id'                   => $user ? $user->id : 0,
            'top_id'                    => $user ? $user->top_id : 0,
            'parent_id'                 => $user ? $user->parent_id : 0,
            'username'                  => $user ? $user->username : '---',
            'nickname'                  => $user ? $user->nickname : '---',
            'ip'                        => real_ip(),
            'city'                      => $location['city'],
            'country'                   => $location['country'],
        ];

        // 分发
        jtq(new PlayerIpLogJob($data), 'log');

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
