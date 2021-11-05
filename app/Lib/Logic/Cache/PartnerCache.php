<?php namespace App\Lib\Logic\Cache;

use App\Models\Casino\CasinoCategorie;
use App\Models\Partner\Partner;

/**
 * Tom 2019 整理 商户缓存
 * Class UserCache
 * @package App\Lib
 */
class PartnerCache
{

    public static $prefix   = "partner_";
    public static $expired  = 12;
    public static $store    = "redis";

    static function getPartner($sign) {
        $key    = self::$prefix . "_" . $sign;
        $tag    = "partner";

        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $partner = Partner::findPartnerBySign($sign);
        if ($partner) {
            $cache->tags($tag)->put($key, $partner, now()->addMinutes(self::$expired * 60));
            return $partner;
        }

        return [];
    }

    static function flushPartner($sign) {
        $key    = self::$prefix . "_" . $sign;
        $tag    = "partner";

        $cache  = cache()->store(self::$store);

        // 如果存在 则删除
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->forget($key);
        }

        // 重新生成
        self::getPartner($sign);

        return [];
    }

    /**
     * 获取所有商户
     * @return array
     * @throws \Exception
     */
    static function getAllPartnerOptions() {
        $key    = self::$prefix . "_all";
        $tag    = "partner";

        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $allPartner = Partner::getOptions();
        if ($allPartner) {
            $cache->tags($tag)->put($key, $allPartner, now()->addMinutes(self::$expired * 60));
            return $allPartner;
        }

        return [];
    }

    /**
     * 刷新
     * @return array
     * @throws \Exception
     */
    static function flushPartnerOptions() {
        $key    = self::$prefix . "_all";
        $tag    = "partner";

        $cache  = cache()->store(self::$store);

        // 如果存在 则删除
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->forget($key);
        }

        // 重新生成
        self::getAllPartnerOptions();

        return [];
    }

    // =============== 登录 ====================start=====

    /**
     * 登录保存验证码
     * @param $email
     * @param $code
     *
     * @return bool
     * @throws \Exception
     */
    static function saveLoginCode($email, $code)
    {
        $key   = self::getLoginCodeKey($email);
        cache()->put($key, $code, now()->addMinutes(5));
        return true;
    }

    /**
     * 获取登录code
     * @param $email
     *
     * @return \Illuminate\Contracts\Cache\Repository
     * @throws \Exception
     */
    static function getLoginCode($email)
    {
        $key   = self::getLoginCodeKey($email);
        return cache()->get($key);
    }

    /**
     * 删除登录code
     * @param $email
     *
     * @return bool
     * @throws \Exception
     */
    static function delLoginCode($email)
    {
        $key   = self::getLoginCodeKey($email);
        cache()->forget($key);
        return true;
    }

    /**
     * 登录key
     * @param $email
     *
     * @return string
     */
    static function getLoginCodeKey($email)
    {
        $key   = 'tg_login_code_' . md5($email);
        return $key;
    }
    // =============== 登录 ====================end=====

    /**
     * 清空所有配置缓存
     * @param $cacheAll
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function partnerAdminCacheClean($partnerSign)
    {
        // 中奖 热门 导航  热门电游
        $popularArr = [];
        $CasinoCategorie = CasinoCategorie::get();
        foreach ($CasinoCategorie as $item) {
            $popularArr[] = 'site-popular-' . $item->code. '-' . $partnerSign;
        }


        $cacheKeyCacheArr = [
            "site-ranking-" . $partnerSign,
            "site-popular-lottery-" . $partnerSign,
            "home-navigation-" . $partnerSign,
            "template-colors-" . $partnerSign,
        ];
        $cacheAll = array_merge($cacheKeyCacheArr, $popularArr);

        foreach ($cacheAll as $item) {
            cache()->delete($item);
        }
    }

    /**
     * 踢线加入缓存
     * @param $player
     *
     * @throws \Exception
     */
    static function kickLine($player)
    {
        cache()->put($player->partner_sign.'_'.$player->username.'_password',true);
    }

    static function onlineArray($onlineArray)
    {
         return cache()->many($onlineArray);
    }

}
