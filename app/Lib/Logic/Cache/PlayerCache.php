<?php namespace App\Lib\Logic\Cache;

use App\Models\Player\Player;

/**
 * Tom 2019 整理 用户缓存
 * Class UserCache
 * @package App\Lib
 */
class PlayerCache
{

    public static $prefix   = "player_";
    public static $expired  = 12;
    public static $store    = "user";
    public static $tag      = "player";

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     */
    static function getUser($userId) {
        $key    = self::$prefix . $userId;
        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags(self::$tag)->has($key)) {
            return $cache->tags(self::$tag)->get($key);
        }

        $user = Player::find($userId);
        if ($user) {
            $cache->tags(self::$tag)->put($key, $user, now()->addMinutes(self::$expired * 60));
            return $user;
        }

        return [];
    }

    /**
     * @param $user
     * @return array
     * @throws \Exception
     */
    static function update($user) {
        if (is_object($user)) {
            $key    = self::$prefix . $user->id;
        } else {
            $key    = self::$prefix . $user;
            $user   = Player::find($user);
        }

        $cache  = cache()->store(self::$store);

        if ($user) {
            $cache->tags(self::$tag)->put($key, $user, now()->addMinutes(self::$expired * 60));
            return $user;
        } else {
            $cache->tags(self::$tag)->forget($key);
        }
        return [];
    }

}
