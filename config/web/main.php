<?php

return [

    'from' => [
        0 => "Web",
        1 => "IOS",
        2 => "Android",
        3 => "未知",
    ],

    // 缓存
    'cache' => [
        'lottery'   => [
            'key'           => 'c_lottery_all',
            'expire_time'   => 0,
            'name'          => '游戏缓存'
        ],

        'account_change_type'   => [
            'key'           => 'c_account_change_type',
            'expire_time'   => 0,
            'name'          => '帐变类型缓存'
        ],


        'notice'   => [
            'key'           => 'c_notice',
            'expire_time'   => 0,
            'name'          => '公告列表'
        ],

        'lottery_for_frontend' => [
            'key'           => 'c_lottery_for_frontend',
            'expire_time'   => 0,
            'name'          => '游戏缓存-前端'
        ],

        'method_config'   => [
            'key'           => 'c_method_config_all',
            'expire_time'   => 0,
            'name'          => '玩法配置缓存'
        ],

        'method_object'     => [
            'key'           => 'c_method_object',
            'expire_time'   => 0,
            'name'          => '玩法对象缓存'
        ],

        'common'    => [
            'key'           => 'c_common',
            'expire_time'   => 3600,
            'name'          => '常规缓存'
        ]
    ],


];
