<?php namespace App\Lib;

use Illuminate\Support\Facades\Cache;

trait BaseCache
{
    /**
     * 获取缓存
     * @param $key
     * @param $sign
     * @return mixed
     * @throws \Exception
     */
    static function _getCacheData($key, $sign = "") {
        $config = self::_getCacheConfig($key);
        if ($config['has_child']) {
            $key = $config['key'] . "_" . $sign;
        } else {
            $key = $config['key'];
        }

        return cache()->get($key, []);
    }

    /**
     * @param $key
     * @param $sign
     * @param $value
     * @return bool
     * @throws \Exception
     */
    static function _saveCacheData($key, $value, $sign = "") {
        $config = self::_getCacheConfig($key);
        if ($config['has_child']) {
            $key = $config['key'] . "_" . $sign;
        } else {
            $key = $config['key'];
        }

        if ($config['expire_time'] <= 0) {
            return Cache::forever($key, $value);
        } else {
            $expireTime = now()->addSeconds($config['expire_time']);
            return cache()->put($key, $value, $expireTime);
        }
    }

    /**
     * 刷新缓存
     * @param $key
     * @param $sign
     * @return bool
     * @throws \Exception
     */
    static function _flushCache($key, $sign = "") {
        $config = self::_getCacheConfig($key);
        if ($config['has_child']) {
            $key = $config['key'] . "_" . $sign;
        } else {
            $key = $config['key'];
        }
        return cache()->forget($key);
    }

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    static function _getCacheConfig($key) {
        $cacheConfig = config('web.cache.config');
        if (isset($cacheConfig[$key])) {
            return $cacheConfig[$key];
        } else {
            return $cacheConfig['common'];
        }
    }

    /**
     * @param $key
     * @param $sign
     * @return bool
     * @throws mixed
     */
    static function _hasCache($key, $sign = '') {
        $config = self::_getCacheConfig($key);
        if ($config['has_child']) {
            $key = $config['key'] . "_" . $sign;
        } else {
            $key = $config['key'];
        }
        return cache()->has($key);
    }
}
