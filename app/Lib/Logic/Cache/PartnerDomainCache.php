<?php namespace App\Lib\Logic\Cache;

use App\Models\Partner\PartnerDomain;

/**
 * Tom 2019 整理 商户缓存
 * Class PartnerDomainCache
 * @package App\Lib\Logic\Cache
 */
class PartnerDomainCache
{

    public static $prefix   = "p_d_";
    public static $expired  = 12;
    public static $store    = "redis";

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getPartnerDomain($partnerSign) {
        $key    = self::$prefix . $partnerSign . "_all";
        $tag    = "partner";

        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $allDomain = PartnerDomain::getPartnerDomain($partnerSign);
        if ($allDomain) {
            $cache->tags($tag)->put($key, $allDomain, now()->addMinutes(self::$expired * 60));
            return $allDomain;
        }

        return [];
    }

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function flushPartnerDomain($partnerSign) {
        $key    = self::$prefix . $partnerSign . "_all";
        $tag    = "partner";

        $cache  = cache()->store(self::$store);

        // 如果存在 则删除
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->forget($key);
        }

        // 重新生成
        return self::getPartnerDomain($partnerSign);
    }

}
