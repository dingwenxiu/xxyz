<?php

return [
    'home_component' => [
        'banner'          => '轮播图',
        'ranking'         => '中奖排行',
        'popular_lottery' => '热门彩种',
        'popular_chess'   => '热门棋牌',
        'popular_e_game'  => '热门体育',
        'notice'          => '公告',
        'cs_url'          => '客服',
        'logo'            => '图标Logo',
        'qr_code'         => "下载二维码",
    ],

    'telegram_channel' => [
        'send_code'             => '登录验证码',
        'send_challenge'        => '单挑_限额',
        'send_stat'             => '统计接收',
        'send_finance'          => '财务接收',
        'send_check'            => '后台审核',
    ],

    // 默认群组
    'telegram_channel_id' => [
        "YX"    => [
            'send_code'             => '-386616504',
            'send_challenge'        => '-319117860',
            'send_stat'             => '-331525116',
            'send_finance'          => '-382209325',
            'send_check'            => '-351759942',
        ],
        "KLC"    => [
            'send_code'             => '-386616504',
            'send_challenge'        => '-319117860',
            'send_stat'             => '-331525116',
            'send_finance'          => '-382209325',
            'send_check'            => '-351759942',
        ],
        "system" => [
            'send_code'             => '-386616504',
            'send_challenge'        => '-319117860',
            'send_stat'             => '-331525116',
            'send_finance'          => '-382209325',
            'send_check'            => '-351759942',
        ],
    ],

    'home_cache'     => [
        1 => [
            'name' => '头部导航',
            'type' => [
                ['name' => '链接', 'val' => ''],
                ['name' => '娱乐城平台', 'route' => 'casino/getPlatType'],
            ]
        ],
        2 => [
            'name'  => '左侧导航',
            'route' => 'lottery/issueRuleList',
        ],
        3 => [
            'name'  => 'banner',
            'route' => 'activityList',
        ],
        4 => [
            'name'  => '公告',
            'route' => 'system/noticeList',
        ],
        5 => [
            'name'  => '中1',
            'route' => 'lottery/methodList',
        ],
        7 => [
            'name' => '中2',
            'type' => [
                ['name' => '彩票', 'val' => 'lottery/methodList'],
                ['name' => '娱乐城游戏', 'val' => 'casino/getGameList']
            ],
        ],
        8 => [
            'name'  => '开奖公告',
            'route' => 'lottery/methodList',
        ]
    ]
];
