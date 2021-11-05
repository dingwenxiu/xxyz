<?php
namespace App\Providers\Facade;

use Illuminate\Support\Facades\Facade;

class FieldCheck extends Facade
{
    /**
    * 获取组件注册名称
    *
    * @return string
    */
    protected static function getFacadeAccessor() {
        return 'FieldCheck';
    }
}