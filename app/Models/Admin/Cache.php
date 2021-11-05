<?php

namespace App\Models\Admin;


class Cache extends Base
{
    protected $table = 'sys_configures';

    static function getList() {
        $config = config("web.cache");

        $data = [];
        foreach ($config as $key => $item) {
            $cacheData = self::_getCacheData($key);
            $item['data']       = $cacheData ? count($cacheData) : 0;
            $item['cache_key']  = $key;
            $data[]             = $item;
        }

        return $data;
    }

}
