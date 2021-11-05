<?php

return [

    'theme' => [
        'default'   => '默认',
    ],

    'device' => [
        '1'   => 'PC端',
        '2'   => '安卓',
        '3'   => '苹果',
    ],

    // 玩家类型
    'type'  => [
        1 => '直属',
        2 => '代理',
        3 => '玩家',
    ],

    // 转账类型
    'transfer_mode' => [
        'add'       => "增加",
        'reduce'    => "减少"
    ],

    // 日工资配置
    'salary_config' => [
        'condition'  => [
            'bet'               => "投注",
            'recharge'          => "充值",
            'active_player'     => "活跃用户",
        ],
        'rate_default_max'      => 30,
        'rate_type'             => [
            1       => "固定",
            2       => "千分比",
        ]
    ],

    // 后台转帐　减少
    'transfer_add' => [
        1 => [
            'name'          => '普通理赔',
            'change_type'   => 'system_transfer_add'
        ],
        2 => [
            'name'          => '分红理赔',
            'change_type'   => 'system_transfer_add'
        ],
        3 => [
            'name'          => '充值理赔',
            'change_type'   => 'system_transfer_add'
        ],
        4 => [
            'name'          => '红包理赔',
            'change_type'   => 'system_transfer_add'
        ],
        5 => [
            'name'          => '活动礼金',
            'change_type'   => 'system_transfer_add'
        ],
    ],

    // 后台转帐　增加　
    'transfer_reduce' => [
        1 => [
            'name'          => '系统扣减',
            'change_type'   => 'system_transfer_reduce'
        ],
        2 => [
            'name'          => '充值错误扣减',
            'change_type'   => 'system_transfer_reduce'
        ],
        3 => [
            'name'          => '礼金错误扣减',
            'change_type'   => 'system_transfer_reduce'
        ],
        4 => [
            'name'          => '奖金错误扣减',
            'change_type'   => 'system_transfer_reduce'
        ],
        5 => [
            'name'          => '提现扣减',
            'change_type'   => 'system_transfer_reduce'
        ]
    ],

    'frozen_type' => [
        0 => "未冻结",
        1 => '禁止登录',
        2 => '禁止投注',
        3 => '禁止提现',
        4 => '禁止转账',
        5 => '禁止资金'
    ],

    'account_change_types' => [
        1 => "增加",
        2 => "减少",
    ],

    // 渠道
    'market_channel' => [
        1 => "渠道1",
        2 => "渠道2"
    ],

    'register_expire_options' => [
        [
            'value' => 1,
            'label' => "1天"
        ],
        [
            'value' => 7,
            'label' => "7天"
        ],
        [
            'value' => 30,
            'label' => "30天"
        ],
        [
            'value' => 90,
            'label' => "90天"
        ],
        [
            'value' => 0,
            'label' => "永久有效"
        ],
    ],

    'user_icon' => [
        '10001' => '10001.jpg',
        '10002' => '10002.jpg',
        '10003' => '10003.jpg',
        '10004' => '10004.jpg',
        '10005' => '10005.jpg',
        '10006' => '10006.jpg',
        '10007' => '10007.jpg',
        '10008' => '10008.jpg',
        '10009' => '10009.jpg',
        '10010' => '10010.jpg',
        '10011' => '10011.jpg',
        '10012' => '10012.jpg',
        '10013' => '10013.jpg',
        '10014' => '10014.jpg',
        '10015' => '10015.jpg',
        '10016' => '10016.jpg',
        '10017' => '10017.jpg',
        '10018' => '10018.jpg',
        '10019' => '10019.jpg',
        '10020' => '10020.jpg',
    ],
];
