<?php
namespace App\Providers\Facade;

use Illuminate\Support\Facades\Facade;

class Help extends Facade
{
    /**
    * 获取组件注册名称
    *
    * @return string
    */
    protected static function getFacadeAccessor() {
        return 'Help';
    }
}