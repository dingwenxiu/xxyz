<?php

return [
    // 资金修正
    'money_unit' => 10000,

    "challenge" => [
        "times"         => 45,
        "max_bonus"     => 20000,
    ],

    // 系列
    'series' => [
        'ssc'       => '时时彩',
        'lotto'     => '11选5',
        'k3'        => '快三',
        'sd'        => '3D',
        'ssl'       => '时时乐',
        'p3p5'      => '排列35',
        'lhc'       => '六合彩',
        'pk10'      => 'pk10',
        'pcdd'      => 'PC蛋蛋',
        'klsf'      => '快乐十分',
    ],

    // 模式
    'modes' => [
        '1'         => ['title' => '元', 'val' => 1],
        '2'         => ['title' => '角', 'val' => 0.1],
        '3'         => ['title' => '分', 'val' => 0.01],
        '4'         => ['title' => '厘', 'val' => 0.001]
    ],

    'price' => [
        '1'         => ['title' => '一元模式', 'val' => 1],
        '2'         => ['title' => '二元模式', 'val' => 2],
    ],

    // 奖期类型
    'issue_type' => [
        'day'       => '每日累加',
        'increase'  => '整体累加',
        'random'    => '随机',
    ],

    // 合法号码
    'valid_code' => [
        1       => '1,2,3,4,5,6',
        2       => '0,1,2,3,4,5,6,7,8,9',
        3       => '01,02,03,04,05,06,07,08,09,10,11',
        4       => '01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49',
    ],

    // 位置
    'positions' => [
        '1'     => '1,2,3,4,5',
        '2'     => '1,2,3',
        '3'     => '1,2,3,4,5,6,7,8,9,10',
        '4'     => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49',
    ],

    // 奖期类型
    'he_type_method' => [
        "LHWQ", "LHWB", "LHWS", "LHWG", "LHQB", "LHQS", "LHQG", "LHBS", "LHBG", "LHSG",
        "CO_LHWQ", "CO_LHWB", "CO_LHWS", "CO_LHWG", "CO_LHQB", "CO_LHQS", "CO_LHQG", "CO_LHBS", "CO_LHBG", "CO_LHSG",
    ],

    "open_jackpot_series"   => ['ssc', 'k3'],

    "logic_project_slot"    => env("PROJECT_SLOT", 5),
    "logic_trace_slot"      => env("TRACE_SLOT", 5),
    "logic_commission_slot" => env("COMMISSION_SLOT", 5),
    "logic_stat_slot"       => env("STAT_SLOT", 5),
];
