<?php namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait BaseCache {

    /** ========== 缓存处理 ========== */
    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    static function getCacheData($key) {
        $cacheConfig = self::getCacheConfig($key);
        return Cache::get($cacheConfig['key'], []);
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    static function saveCacheData($key, $value) {
        $cacheConfig = self::getCacheConfig($key);
        if ($cacheConfig['expire_time'] <= 0) {
            return Cache::forever($cacheConfig['key'], $value);
        } else {
            $expireTime = Carbon::now()->addSeconds($cacheConfig['expire_time']);
            return Cache::put($cacheConfig['key'], $value, $expireTime);
        }
    }

    /**
     * 刷新缓存
     * @param $key
     * @return bool
     */
    static function flushCache($key) {
        $cacheConfig = self::getCacheConfig($key);
        return Cache::forget($cacheConfig['key'], []);
    }

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    static function getCacheConfig($key) {
        $cacheConfig = config('web.cache.config');
        if (isset($cacheConfig[$key])) {
            return $cacheConfig[$key];
        } else {
            return $cacheConfig['common'];
        }
    }

    /**
     * 检查缓存是否存在
     * @param $key
     * @return bool
     */
    static function hasCache($key) {
        $cacheConfig = self::getCacheConfig($key);
        return Cache::has($cacheConfig['key']);
    }
}
