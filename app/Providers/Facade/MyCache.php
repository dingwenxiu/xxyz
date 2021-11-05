<?php
namespace App\Providers\Facade;

use Illuminate\Support\Facades\Facade;

class MyCache extends Facade
{
    /**
    * 获取组件注册名称
    *
    * @return string
    */
    protected static function getFacadeAccessor() {
        return 'MyCache';
    }
}