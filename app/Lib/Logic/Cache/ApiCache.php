<?php namespace App\Lib\Logic\Cache;

use App\Models\Account\Account;
use App\Models\Player\PlayerLog;

/**
 * Tom 2019 整理 用户缓存
 * Class UserCache
 * @package App\Lib
 */
class ApiCache
{

    public static $prefix   = "api_";
    public static $expired  = 12;
    public static $store    = "redis";


    static function saveMemKey($memKey, $v, $ttl)
    {
       return cache()->add($memKey, $v, $ttl);
    }


    static function cleanMemKey($memKey)
    {
        cache()->forget($memKey);
    }

    /**
     *  清除 踢线缓存
     * @param $user
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException\
     */
    static function cleanKickLine($user)
    {
        if(cache()->has($user->partner_sign.'_'.$user->username.'_password'))
        {
            cache()->forget($user->partner_sign.'_'.$user->username.'_password');
        }
    }

    /**
     * 缓存30S
     * @param $child
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function save30($child)
    {
        $key = "team_total_balance_" . $child->id;
        if (cache()->has($key)) {
            $totalBalance = cache()->get($key);
        } else {
            $totalBalance = Account::where("top_id", $child->top_id)->where('rid', 'like', $child->rid . "%")->sum('balance');
            cache()->put($key, $totalBalance, now()->addSeconds(30));
        }

        return $totalBalance;
    }

    /**
     * 登录缓存
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function login()
    {
        $player = auth() -> guard('api') -> user();

        if ($player) {
            if(cache()->has($player->partner_sign.'_'.$player->username.'_password'))
            {
                auth('api')->logout();
                cache()->forget($player->partner_sign.'_'.$player->username.'_password');
            }
            else
            {
                PlayerLog::saveItem($player);
                cache()->add($player->partner_sign.'_'.$player->id, 1, now()->addMinute(2));
            }
        }
    }
}
