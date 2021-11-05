<?php namespace App\Lib\Logic\Cache;

use App\Lib\Clog;
use App\Models\Admin\Configure;
use App\Models\Partner\PartnerConfigure;

/**
 * Tom 2019 整理 配置缓存
 * Class ConfigureCache
 * @package App\Lib\Cache
 */
class ConfigureCache
{

    public static $prefix   = "sc_";
    public static $expired  = 12;
    public static $store    = "redis";
    public static $tag      = "system_configure";

    /**
     * 刷新所有缓存
     * @throws \Exception
     */
    static function clearSystemConfigureCache() {
        $cache      = cache()->store(self::$store);
        $itemArr    = Configure::where('status', 1)->get();

        foreach ($itemArr as $item) {
            $key    = self::$prefix . $item->sign;
            $cache->tags([self::$tag])->forget($key);
        }

        return true;
    }

    /**
     * @param $partnerSign
     * @return bool
     * @throws \Exception
     */
    static function clearPartnerConfigureCache($partnerSign) {
        $tag    = "partner_configure_" . $partnerSign;

        $cache      = cache()->store(self::$store);
        $itemArr    = PartnerConfigure::where('status', 1)->where("partner_sign", $partnerSign)->get();

        foreach ($itemArr as $item) {
            $key    = self::$prefix . $item->sign;
            $cache->tags([$tag])->forget($key);
        }

        return true;
    }


    /**
     * 获取单个系统配置
     * @param $sign
     * @param $default
     * @return mixed
     * @throws \Exception
     */
    static function getSystemConfigure($sign, $default) {
        $cache  = cache()->store(self::$store);

        try {
            $key    = self::$prefix . $sign;
            if ($cache->tags([self::$tag])->has($key)) {
                return $cache->tags([self::$tag])->get($key);
            }

            $item = Configure::where('sign', $sign)->where('status', 1)->first();

            if ($item) {
                $cache->tags([self::$tag])->put($key, $item->value, now()->addDays(7));
                return $item->value;
            }
        } catch (\Exception $e) {
            Clog::userCache($e->getMessage());
            return $default;
        }

        return $default;
    }


    /**
     * @param $partnerSign
     * @param $sign
     * @param $default
     * @return mixed
     * @throws \Exception
     */
    static function getPartnerConfigure($partnerSign, $sign, $default) {
        $cache  = cache()->store(self::$store);

        $key    = self::$prefix . $sign;
        $tag    = "partner_configure_" . $partnerSign;

        if ($cache->tags([$tag])->has($key)) {
            return $cache->tags([$tag])->get($key);
        }

        $item = PartnerConfigure::where('sign', $sign)->where("partner_sign", $partnerSign)->where('status', 1)->first();

        if ($item) {
            $cache->tags([$tag])->forever($key, $item->value);
            return $item->value;
        }

        return $default;
    }

    /**
     * 获取缓存
     * @param $item
     *
     * @return \Illuminate\Contracts\Cache\Repository
     * @throws \Exception
     */
    static function get($key)
    {
        return cache()->get($key);
    }

    static function put($key, $data, $minutes = null)
    {
        cache()->put($key, $data, $minutes);
    }

    static function has($key)
    {
        return cache()->has($key);
    }

    static function forget($key)
    {
        return cache()->forget($key);
    }

    static function forever($key, $data)
    {
        return cache()->forever($key, $data);
    }
}
