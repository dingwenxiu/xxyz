<?php

return [

    // 缓存
    'config' => [

        'lottery'   => [
            'key'           => 'c_lottery',
            'has_child'     => true,
            'child_list'    => ['cqssc', 'xjssc', 'hljssc', 'tjssc', 'sd115', 'gd115', 'jx115', 'sx115', 'gs115', 'jsk3', 'ahk3'],
            'expire_time'   => 0,
            'name'          => '单个游戏缓存'
        ],

        'lottery_all'   => [
            'key'           => 'c_lottery_all',
            'expire_time'   => 0,
            'has_child'     => false,
            'name'          => '所有游戏缓存'
        ],

        'lottery_loop_config'   => [
            'key'           => 'c_lottery_loop_config',
            'expire_time'   => 0,
            'has_child'     => false,
            'name'          => '游戏拉起脚本配置'
        ],

        'lottery_for_frontend' => [
            'key'           => 'c_lottery_for_frontend',
            'expire_time'   => 0,
            'has_child'     => false,
            'name'          => '游戏缓存-前端'
        ],

        'method_config'   => [
            'key'           => 'c_method_config_all',
            'expire_time'   => 0,
            'has_child'     => false,
            'name'          => '玩法配置缓存'
        ],

        'method_object'     => [
            'key'           => 'c_method_object',
            'expire_time'   => 0,
            'has_child'     => false,
            'name'          => '玩法对象缓存'
        ],

        'account_change_type'   => [
            'key'           => 'c_account_change_type',
            'expire_time'   => 0,
            'has_child'     => false,
            'name'          => '帐变类型缓存'
        ],

        'notice'   => [
            'key'           => 'c_notice',
            'expire_time'   => 0,
            'has_child'     => false,
            'name'          => '公告列表'
        ],

        'sys_configure'   => [
            'has_child'     => false,
            'key'           => 'c_sys_configure',
            'expire_time'   => 0,
            'name'          => '系统配置',
        ],

        'casino_record'   => [
            'has_child'     => true,
            'key'           => 'c_casino_record',
            'expire_time'   => 0,
            'name'          => '娱乐城投注记录',
        ],

        'common'    => [
            'key'           => 'c_common',
            'expire_time'   => 3600,
            'has_child'     => false,
            'name'          => '常规缓存'
        ]
    ],


];
