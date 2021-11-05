<?php
$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

// 时时彩类型 - 数字型
return array(
    // 五星
    'ZX5' => array(
        'name'      => '五星直选复式',
        'group'     => 'WX',
        'row'       => 'zhixuan',
        'method'    => 'ZX5',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 1,
                "prize" => 180000.00
            )

        ),
    ),

    // 五星 - 直选复试
    'ZX5_S' => array(
        'name'      => '五星直选单式',
        'group'     => 'WX',
        'row'       => 'zhixuan',
        'method'    => 'ZX5_S',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 1,
                "prize" => 180000.00
            )
        ),
    ),

    'ZH5' => array(
        'name'      => '五星直选组合',
        'group'     => 'WX',
        'row'       => 'zhixuan',
        'method'    => 'ZH5',
        'jzjd'      => 1,
        "total" => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                'levelName' => '一等奖',
                "count" => 1,
                "prize" => 180000.00
            ),

            '2' => array(
                'position' => array(2, 3, 4, 5),
                'levelName' => '二等奖',
                "count" => 10,
                "prize" => 18000.00
            ),

            '3' => array(
                'position' => array(3, 4, 5),
                'levelName' => '三等奖',
                "count" => 100,
                "prize" => 1800.00
            ),

            '4' => array(
                'position' => array(4, 5),
                'levelName' => '四等奖',
                "count" => 1000,
                "prize" => 180.00
            ),

            '5' => array(
                'position' => array(5),
                'levelName' => '五等奖',
                "count" => 10000,
                "prize" => 18.00
            )
        ),
    ),

    // 组选 - 120
    'WXZU120' => array(
        'name'      => '五星组选120',
        'group'     => 'WX',
        'row'       => 'zuxuan',
        'method'    => 'WXZU120',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 120,
                "prize" => 1500.00
            ),
        ),
    ),

    // 组选 - 60
    'WXZU60' => array(
        'name'      => '五星组选60',
        'group'     => 'WX',
        'row'       => 'zuxuan',
        'method'    => 'WXZU60',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 60,
                "prize" => 3000.00
            ),
        ),
    ),

    // 组选 - 30
    'WXZU30' => array(
        'name'      => '五星组选30',
        'group'     => 'WX',
        'row'       => 'zuxuan',
        'method'    => 'WXZU30',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 30,
                "prize" => 6000.00
            ),
        ),
    ),

    // 组选 - 20
    'WXZU20' => array(
        'name'      => '五星组选20',
        'group'     => 'WX',
        'row'       => 'zuxuan',
        'method'    => 'WXZU20',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 20,
                "prize" => 9000.00
            ),
        ),
    ),

    // 组选 - 10
    'WXZU10' => array(
        'name'      => '五星组选10',
        'group'     => 'WX',
        'row'       => 'zuxuan',
        'method'    => 'WXZU10',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 10,
                "prize" => 18000.00
            ),
        ),
    ),

    // 组选 - 5
    'WXZU5' => array(
        'name'      => '五星组选5',
        'group'     => 'WX',
        'row'       => 'zuxuan',
        'method'    => 'WXZU5',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 5,
                "prize" => 36000.00
            ),
        ),
    ),

    // 四星 - 直选
    'ZX4' => array(
        'name'      => '四星直选复式',
        'group'     => 'SX',
        'row'       => 'zhixuan',
        'method'    => 'ZX4',
        "total"     => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 1,
                "prize" => 18000.00
            ),
        ),
    ),

    // 四星 - 直选单式
    'ZX4_S' => array(
        'name'      => '四星直选单式',
        'group'     => 'SX',
        'row'       => 'zhixuan',
        'method'    => 'ZX4_S',
        "total"     => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 1,
                "prize" => 18000.00
            ),
        ),
    ),

    // 组合4
    'ZH4' => array(
        'name'      => '四星直选组合',
        'group'     => 'SX',
        'row'       => 'zhixuan',
        'method'    => 'ZH4',
        'jzjd'      => 1,
        "total" => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                'levelName' => '一等奖',
                "count" => 1,
                "prize" => 18000.00
            ),

            '2' => array(
                'position' => array(3, 4, 5),
                'levelName' => '二等奖',
                "count" => 10,
                "prize" => 1800.00
            ),

            '3' => array(
                'position' => array(4, 5),
                'levelName' => '三等奖',
                "count" => 100,
                "prize" => 180.00
            ),

            '4' => array(
                'position' => array(5),
                'levelName' => '四等奖',
                "count" => 1000,
                "prize" => 18.00
            ),
        ),
    ),

    // 四星 - 组选24
    'SXZU24' => array(
        'name'      => '四星组选24',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        'method'    => 'SXZU24',
        "total"     => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 24,
                "prize" => 750.00
            ),
        ),
    ),

    // 四星 - 组选12
    'SXZU12' => array(
        'name'      => '四星组选12',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        'method'    => 'SXZU12',
        "total"     => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 12,
                "prize" => 1500.00
            ),
        ),
    ),

    // 四星 - 组选6
    'SXZU6' => array(
        'name'      => '四星组选6',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        'method'    => 'SXZU6',
        "total"     => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 6,
                "prize" => 3000.00
            ),
        ),
    ),

    // 四星 - 组选4
    'SXZU4' => array(
        'name'      => '四星组选4',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        'method'    => 'SXZU4',
        "total"     => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 4,
                "prize" => 4500.00
            ),
        ),
    ),


    //三星 - 前三
    'QZX3' => array(
        'name'      => '前三直选复式',
        'group'     => 'Q3',
        'row'       => 'zhixuan',
        'method'    => 'QZX3',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 前三单式
    'QZX3_S' => array(
        'name'      => '前三直选单式',
        'group'     => 'Q3',
        'row'       => 'zhixuan',
        'method'    => 'QZX3_S',
        "total"     => 1000,
        'levels'    => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 前三组合
    'QZH3' => array(
        'name'      => '前三直选组合',
        'group'     => 'Q3',
        'row'       => 'zhixuan',
        'method'    => 'QZH3',
        'jzjd'      => 1,
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),

            '2' => array(
                'position' => array(2, 3),
                "count" => 10,
                "prize" => 180.00
            ),

            '3' => array(
                'position' => array(3),
                "count" => 100,
                "prize" => 18.00
            ),
        ),
    ),

    // 三星 - 前三直选和值
    'QZXHZ' => array(
        'name'      => '前三直选和值',
        'group'     => 'Q3',
        'row'       => 'zhixuan',
        'method'    => 'QZXHZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 前三直选跨度
    'QZXKD' => array(
        'name'      => '前三直选跨度',
        'group'     => 'Q3',
        'row'       => 'zhixuan',
        'method'    => 'QZXKD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三线 组三
    'QZU3' => array(
        'name'      => '前三组三',
        'group'     => 'Q3',
        'row'       => 'zuxuan',
        'method'    => 'QZU3',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    // 三星 - 组三单式
    'QZU3_S' => array(
        'name'      => '前三组三单式',
        'group'     => 'Q3',
        'row'       => 'zuxuan',
        'method'    => 'QZU3_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    // 三星 - 前三组六
    'QZU6' => array(
        'name'      => '前三组六',
        'group'     => 'Q3',
        'row'       => 'zuxuan',
        'method'    => 'QZU6',
        "total"     => 1000,
        'levels'    => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 三星 - 前三组六单式
    'QZU6_S' => array(
        'name'      => '前三组六单式',
        'group'     => 'Q3',
        'row'       => 'zuxuan',
        'method'    => 'QZU6_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 前三 - 前三混合组选
    'QHHZX' => array(
        'name'      => '前三混合组选',
        'group'     => 'Q3',
        'row'       => 'zuxuan',
        'method'    => 'QHHZX',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(1, 2, 3),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 前三 - 组选和值
    'QZUHZ' => array(
        'name'      => '前三组选和值',
        'group'     => 'Q3',
        'row'       => 'zuxuan',
        'method'    => 'QZUHZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(1, 2, 3),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 前三组选包胆
    'QZU3BD' => array(
        'name'      => '前三组选包胆',
        'group'     => 'Q3',
        'row'       => 'zuxuan',
        'method'    => 'QZU3BD',
        "total"     => 1000,
        'levels'    => array(
            '1' => array(
                'position' => array(1, 2, 3),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(1, 2, 3),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 三星 - 趣味 - 和值尾数
    'QHZWS' => array(
        'name'      => '前三和值尾数',
        'group'     => 'Q3',
        'row'       => 'quwei',
        'method'    => 'QHZWS',
        "total"     => 10,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 18.00
            ),
        ),
    ),

    // 三星 - 趣味 - 特殊号
    'QTS3' => array(
        'name'      => '前三特殊号',
        'group'     => 'Q3',
        'row'       => 'quwei',
        'method'    => 'QTS3',
        "total"     => 1000,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'levelName' => '豹子',
                'position' => array(1, 2, 3),
                "count" => 10,
                "prize" => 180.00
            ),

            '2' => array(
                'position' => array(1, 2, 3),
                'levelName' => '顺子',
                "count" => 60,
                "prize" => 30.00
            ),

            '3' => array(
                'position' => array(1, 2, 3),
                'levelName' => '对子',
                "count" => 270,
                "prize" => 6.60
            ),
        ),
    ),

    // =========================== 中三 ========================

    //三星 - 中三 - 直选复式
    'ZZX3' => array(
        'name'      => '中三直选复式',
        'group'     => 'Z3',
        'row'       => 'zhixuan',
        'method'    => 'ZZX3',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 中三 - 单式
    'ZZX3_S' => array(
        'name'      => '中三直选单式',
        'group'     => 'Z3',
        'row'       => 'zhixuan',
        'method'    => 'ZZX3_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 中三 - 组合
    'ZZH3' => array(
        'name'      => '中三直选组合',
        'group'     => 'Z3',
        'row'       => 'zhixuan',
        'method'    => 'ZZH3',
        'jzjd'      => 1,
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 1,
                "prize" => 1800.00
            ),

            '2' => array(
                'position' => array(3, 4),
                "count" => 10,
                "prize" => 180.00
            ),

            '3' => array(
                'position' => array(4),
                "count" => 100,
                "prize" => 18.00
            ),
        ),
    ),

    // 三星 - 中三 - 直选和值
    'ZZXHZ' => array(
        'name'      => '中三直选和值',
        'group'     => 'Z3',
        'row'       => 'zhixuan',
        'method'    => 'ZZXHZ',
        "total"     => 1000,
        'levels'    => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 中三 跨度
    'ZZXKD' => array(
        'name'      => '中三直选跨度',
        'group'     => 'Z3',
        'row'       => 'zhixuan',
        'method'    => 'ZZXKD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 中三 组三
    'ZZU3' => array(
        'name'      => '中三组三',
        'group'     => 'Z3',
        'row'       => 'zuxuan',
        'method'    => 'ZZU3',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    // 三星 - 中三 - 单式
    'ZZU3_S' => array(
        'name'      => '中三组三单式',
        'group'     => 'Z3',
        'row'       => 'zuxuan',
        'method'    => 'ZZU3_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    // 三星 - 中三 - 组六
    'ZZU6' => array(
        'name'      => '中三组六',
        'group'     => 'Z3',
        'row'       => 'zuxuan',
        'method'    => 'ZZU6',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 三星 - 中三 - 组六单式
    'ZZU6_S' => array(
        'name'      => '中三组六单式',
        'group'     => 'Z3',
        'row'       => 'zuxuan',
        'method'    => 'ZZU6_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 中三 - 中三混合组选
    'ZHHZX' => array(
        'name'      => '中三混合组选',
        'group'     => 'Z3',
        'row'       => 'zuxuan',
        'method'    => 'ZHHZX',
        "total"     => 1000,
        'levels'    => array(
            '1' => array(
                'position'  => array(2, 3, 4),
                'levelName' => '组三',
                "count"     => 3,
                "prize"     => 600.00
            ),
            '2' => array(
                'position'  => array(2, 3, 4),
                'levelName' => '组六',
                "count"     => 6,
                "prize"     => 300.00
            ),
        ),
    ),

    // 中三 - 组选和值
    'ZZUHZ' => array(
        'name'      => '中三组选和值',
        'group'     => 'Z3',
        'row'       => 'zuxuan',
        'method'    => 'ZZUHZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(2, 3, 4),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 中三 组选包胆
    'ZZU3BD' => array(
        'name'      => '中三组选包胆',
        'group'     => 'Z3',
        'row'       => 'zuxuan',
        'method'    => 'ZZU3BD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(2, 3, 4),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 中三 - 趣味 - 和值尾数
    'ZHZWS' => array(
        'name'      => '中三和值尾数',
        'group'     => 'Z3',
        'row'       => 'quwei',
        'method'    => 'ZHZWS',
        "total"     => 10,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4),
                "count" => 1,
                "prize" => 18.00
            ),
        ),
    ),

    // 中三 - 趣味 - 特殊号
    'ZTS3' => array(
        'name'      => '中三特殊号',
        'group'     => 'Z3',
        'row'       => 'quwei',
        'method'    => 'ZTS3',
        "total"     => 1000,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'levelName' => '豹子',
                'position' => array(2, 3, 4),
                "count" => 10,
                "prize" => 180.00
            ),

            '2' => array(
                'position' => array(2, 3, 4),
                'levelName' => '顺子',
                "count" => 60,
                "prize" => 30.00
            ),

            '3' => array(
                'position' => array(2, 3, 4),
                'levelName' => '对子',
                "count" => 270,
                "prize" => 6.60
            ),
        ),
    ),

    // ============================= 后三 ================================

    // 三星 - 后三
    'HZX3' => array(
        'name'      => '后三直选复式',
        'group'     => 'H3',
        'row'       => 'zhixuan',
        'method'    => 'HZX3',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 后三 - 单式
    'HZX3_S' => array(
        'name'      => '后三直选单式',
        'group'     => 'H3',
        'row'       => 'zhixuan',
        'method'    => 'HZX3_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 后三 - 组合
    'HZH3' => array(
        'name'      => '后三直选组合',
        'group'     => 'H3',
        'row'       => 'zhixuan',
        'method'    => 'HZH3',
        'jzjd'      => 1,
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 1,
                "prize" => 1800.00
            ),

            '2' => array(
                'position' => array(4, 5),
                "count" => 10,
                "prize" => 180.00
            ),

            '3' => array(
                'position' => array(5),
                "count" => 100,
                "prize" => 18.00
            ),
        ),
    ),

    // 三星 - 后三 - 直选和值
    'HZXHZ' => array(
        'name'      => '后三直选和值',
        'group'     => 'H3',
        'row'       => 'zhixuan',
        'method'    => 'HZXHZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三星 - 后三 - 直选跨度
    'HZXKD' => array(
        'name'      => '后三直选跨度',
        'group'     => 'H3',
        'row'       => 'zhixuan',
        'method'    => 'HZXKD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    // 三线 后三 - 组三
    'HZU3' => array(
        'name'      => '后三组三',
        'group'     => 'H3',
        'row'       => 'zuxuan',
        'method'    => 'HZU3',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    // 三星 - 后三 - 组三单式
    'HZU3_S' => array(
        'name'      => '后三组三单式',
        'group'     => 'H3',
        'row'       => 'zuxuan',
        'method'    => 'HZU3_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    // 三星 - 后三 - 前三组六
    'HZU6' => array(
        'name'      => '后三组六',
        'group'     => 'H3',
        'row'       => 'zuxuan',
        'method'    => 'HZU6',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position'  => array(3, 4, 5),
                "count"     => 6,
                "prize"     => 300.00
            ),
        ),
    ),

    // 三星 - 后三 - 前三组六单式
    'HZU6_S' => array(
        'name'      => '后三组六单式',
        'group'     => 'H3',
        'row'       => 'zuxuan',
        'method'    => 'HZU6_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position'  => array(3, 4, 5),
                "count"     => 6,
                "prize"     => 300.00
            ),
        ),
    ),

    // 前三 - 后三 - 前三混合组选
    'HHHZX' => array(
        'name'      => '后三混合组选',
        'group'     => 'H3',
        'row'       => 'zuxuan',
        'method'    => 'HHHZX',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(3, 4, 5),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 前三 - 后三 - 组选和值
    'HZUHZ' => array(
        'name'      => '后三组选和值',
        'group'     => 'H3',
        'row'       => 'zuxuan',
        'method'    => 'HZUHZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(3, 4, 5),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 三星  - 后三 - 组选包胆
    'HZU3BD' => array(
        'name'      => '后三组选包胆',
        'group'     => 'H3',
        'row'       => 'zuxuan',
        'method'    => 'HZU3BD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                'levelName' => '组三',
                "count" => 3,
                "prize" => 600.00
            ),
            '2' => array(
                'position' => array(3, 4, 5),
                'levelName' => '组六',
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    // 三星 - 后三 - 趣味 - 和值尾数
    'HHZWS' => array(
        'name'      => '后三和值尾数',
        'group'     => 'H3',
        'row'       => 'quwei',
        'method'    => 'HHZWS',
        "total"     => 10,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 1,
                "prize" => 18.00
            ),
        ),
    ),

    // 三星 - 后三 - 趣味 - 特殊号
    'HTS3' => array(
        'name'      => '后三特殊号',
        'group'     => 'H3',
        'row'       => 'quwei',
        'method'    => 'HTS3',
        "total"     => 1000,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'levelName' => '豹子',
                'position' => array(3, 4, 5),
                "count" => 10,
                "prize" => 180.00
            ),

            '2' => array(
                'position' => array(3, 4, 5),
                'levelName' => '顺子',
                "count" => 60,
                "prize" => 30.00
            ),

            '3' => array(
                'position' => array(3, 4, 5),
                'levelName' => '对子',
                "count" => 270,
                "prize" => 6.60
            ),
        ),
    ),


    /**=================== 后二 ==================== */

    // 后二 - 直选复式
    'HZX2' => array(
        'name'      => '后二直选复式',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'HZX2',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 后二 - 直选复式 - 单式
    'HZX2_S' => array(
        'name'      => '后二直选单式',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'HZX2_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 后二 - 直选和值
    'HZX2HZ' => array(
        'name'      => '后二直选和值',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'HZX2HZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 后二 - 直选跨度
    'HZX2KD' => array(
        'name'      => '后二直选跨度',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'HZX2KD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 后二 - 组选
    'HZU2' => array(
        'name'      => '后二组选复式',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'ZU2',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 后二 - 组选单式
    'HZU2_S' => array(
        'name'      => '后二组选单式',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'ZU2_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 后二 - 组选和值
    'HZU2HZ' => array(
        'name'      => '后二组选和值',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'HZU2HZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 后二 - 组选包胆
    'HZU2BD' => array(
        'name'      => '后二组选包胆',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'HZU2BD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 前二 - 直选复式
    'QZX2' => array(
        'name'      => '前二直选复式',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'QZX2',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 前二 - 直选单式
    'QZX2_S' => array(
        'name'      => '前二直选单式',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'QZX2_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 前二 - 直选和值
    'QZX2HZ' => array(
        'name'      => '前二直选和值',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'QZX2HZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 前二 - 直选跨度
    'QZX2KD' => array(
        'name'      => '前二直选跨度',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        'method'    => 'QZX2KD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 10,
                "prize" => 180.00
            ),
        ),
    ),

    // 前二 - 组选
    'QZU2' => array(
        'name'      => '前二组选复式',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'QZU2',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 前二 - 组选单式
    'QZU2_S' => array(
        'name'      => '前二组选单式',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'QZU2_S',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 前二 - 组选和值
    'QZU2HZ' => array(
        'name'      => '前二组选和值',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'QZU2HZ',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 前二 - 组选包胆
    'QZU2BD' => array(
        'name'      => '前二组选包胆',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        'method'    => 'QZU2BD',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 20,
                "prize" => 90.00
            ),
        ),
    ),

    // 定位胆 -  DWD 总控
    'DWD' => array(
        'name'      => '定位胆',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'method'    => 'DWD',
        "total"     => 10,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 1,
                "prize" => 18.00
            ),
        ),
        'expands' => array(
            'name' => '定位胆_{str}',
            'num' => 1,
        ),
    ),

    // 定位胆 -  DWD 万
    'DWD_W' => array(
        'name'          => '定位胆_万',
        'group'         => 'DWD',
        'hidden'        => true,
        'row'           => 'dingweidan',
        'method'        => 'DWD_W',
        "total"         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array(1),
                "count"     => 1,
                "prize"     => 18.00
            ),
        ),
    ),

    // 定位胆 -  DWD 千
    'DWD_Q' => array(
        'name'      => '定位胆_千',
        'method'    => 'DWD_Q',
        'group'     => 'DWD',
        'hidden'    => true,
        'row'       => 'dingweidan',
        "total"     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array(2),
                "count"     => 1,
                "prize"     => 18.00
            ),
        ),
    ),

    // 定位胆 -  DWD 百
    'DWD_B' => array(
        'name'          => '定位胆_百',
        'method'        => 'DWD_B',
        'group'         => 'DWD',
        'hidden'        => true,
        'row'           => 'dingweidan',
        "total"         => 10,
        'levels'        => array(
            '1' => array(
                'position'  => array(3),
                "count"     => 1,
                "prize"     => 18.00
            ),
        ),
    ),

    // 定位胆 -  DWD 十
    'DWD_S' => array(
        'name'          => '定位胆_十',
        'method'        => 'DWD_S',
        'group'         => 'DWD',
        'hidden'        => true,
        'row'           => 'dingweidan',
        "total"         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array(4),
                "count"     => 1,
                "prize"     => 18.00
            ),
        ),
    ),

    // 定位胆 -  DWD 个
    'DWD_G' => array(
        'name'          => '定位胆_个',
        'method'        => 'DWD_G',
        'group'         => 'DWD',
        'hidden'        => true,
        'row'           => 'dingweidan',
        "total"         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array(5),
                "count"     => 1,
                "prize"     => 18.00
            ),
        ),
    ),

    /** ====================== 不定位 ======================== */

    // 不定位 后三一码不定位
    'HBDW31' => array(
        'name'      => '后三一码不定位',
        'group'     => 'BDW',
        'row'       => '3budingwei',
        'method'    => 'HBDW31',
        "total"     => 1000,
        'levels'    => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 271,
                "prize" => 6.64
            ),
        ),
    ),

    // 不定位 - 前三一码不定位
    'QBDW31' => array(
        'name'      => '前三一码不定位',
        'group'     => 'BDW',
        'row'       => '3budingwei',
        'method'    => 'QBDW31',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 271,
                "prize" => 6.64
            ),
        ),
    ),

    // 不定位 - 后三二码不定位
    'HBDW32' => array(
        'name'      => '后三二码不定位',
        'group'     => 'BDW',
        'row'       => '3budingwei',
        'method'    => 'HBDW32',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 54,
                "prize" => 33.33
            ),
        ),
    ),

    // 不定位 - 前三二码不定位
    'QBDW32' => array(
        'name' => '前三二码不定位',
        'group'     => 'BDW',
        'row'       => '3budingwei',
        'method'    => 'QBDW32',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 54,
                "prize" => 33.33
            ),
        ),
    ),

    // 不定位 - 四星一码不定位
    'BDW41' => array(
        'name'          => '四星一码不定位',
        'group'         => 'BDW',
        'row'           => '4budingwei',
        'method'        => 'BDW41',
        "total"         => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 3439,
                "prize" => 5.23
            ),
        ),
    ),

    // 不定位 - 四星二码不定位
    'BDW42' => array(
        'name'      => '四星二码不定位',
        'group'     => 'BDW',
        'row'       => '4budingwei',
        'method'    => 'BDW42',
        "total"     => 10000,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3, 4, 5),
                "count" => 974,
                "prize" => 18.48
            ),
        ),
    ),

    // 不定位 - 五星二码不定位
    'BDW52' => array(
        'name'      => '五星二码不定位',
        'group'     => 'BDW',
        'row'       => '5budingwei',
        'method'    => 'BDW52',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 14670,
                "prize" => 12.27
            ),
        ),
    ),

    // 不定位 - 五星三码不定位
    'BDW53' => array(
        'name'      => '五星三码不定位',
        'group'     => 'BDW',
        'row'       => '5budingwei',
        'method'    => 'BDW53',
        "total"     => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 4350,
                "prize" => 41.3793
            ),
        ),
    ),

    /**  ================== 大小单双 ===============  */
    // 大小单双 - 前二大小单双
    'Q2DXDS' => array(
        'name'          => '前二大小单双',
        'group'         => 'DXDS',
        'row'           => 'dxds',
        'method'        => 'Q2DXDS',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 25,
                "prize" => 7.20
            ),
        ),
    ),

    // 大小单双 - 后二大小单双
    'H2DXDS' => array(
        'name'          => '后二大小单双',
        'group'         => 'DXDS',
        'row'           => 'dxds',
        'method'        => 'H2DXDS',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'position' => array(4, 5),
                "count" => 25,
                "prize" => 7.20
            ),
        ),
    ),

    // 大小单双 - 前三大小单双
    'Q3DXDS' => array(
        'name'          => '前三大小单双',
        'group'         => 'DXDS',
        'row'           => 'dxds',
        'method'        => 'Q3DXDS',
        "total"         => 1000,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 125,
                "prize" => 14.40
            ),
        ),
    ),

    // 大小单双 - 后三大小单双
    'H3DXDS' => array(
        'name'          => '后三大小单双',
        'group'         => 'DXDS',
        'row'           => 'dxds',
        'method'        => 'H3DXDS',
        "total"         => 1000,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'position' => array(3, 4, 5),
                "count" => 125,
                "prize" => 14.40
            ),
        ),
    ),

    // 趣味 - 一帆风顺
    'YFFS' => array(
        'name'          => '一帆风顺',
        'group'         => 'QW',
        'row'           => 'quwei',
        'method'        => 'YFFS',
        "total"         => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 40951,
                "prize" => 4.3955
            ),
        ),
    ),

    // 趣味 - 好事成双
    'HSCS' => array(
        'name'          => '好事成双',
        'group'         => 'QW',
        'row'           => 'quwei',
        'method'        => 'HSCS',
        "total"         => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 8146,
                "prize" => 22.0967
            ),
        ),
    ),

    // 趣味 - 三星报喜
    'SXBX' => array(
        'name'          => '三星报喜',
        'group'         => 'QW',
        'row'           => 'quwei',
        'method'        => 'SXBX',
        "total"         => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 856,
                "prize" => 210.2804
            ),
        ),
    ),

    // 趣味 - 四季发财
    'SJFC' => array(
        'name'          => '四季发财',
        'group'         => 'QW',
        'row'           => 'quwei',
        'method'        => 'SJFC',
        "total"         => 100000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3, 4, 5),
                "count" => 46,
                "prize" => 3913.0435
            ),
        ),
    ),

    // 龙虎
    'LHWQ' => array(
        'name'          => '龙虎万千',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHWQ',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(1, 2),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(1, 2),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(1, 2),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHWB' => array(
        'name'      => '龙虎万百',
        'group'     => 'LH',
        'row'       => 'lhh',
        'method'    => 'LHWB',
        "total"     => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(1, 3),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(1, 3),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(1, 3),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHWS' => array(
        'name'          => '龙虎万十',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHWS',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(1, 4),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(1, 4),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(1, 4),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHWG' => array(
        'name'          => '龙虎万个',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHWG',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(1, 5),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(1, 5),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(1, 5),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHQB' => array(
        'name'          => '龙虎千百',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHQB',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(2, 3),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(2, 3),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(2, 3),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHQS' => array(
        'name'          => '龙虎千十',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHQS',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(2, 4),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(2, 4),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(2, 4),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    // 千个
    'LHQG' => array(
        'name'          => '龙虎千个',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHQG',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(2, 5),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(2, 5),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(2, 5),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHBS' => array(
        'name'          => '龙虎百十',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHBS',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(3, 4),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(3, 4),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(3, 4),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHBG' => array(
        'name'          => '龙虎百个',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHBG',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(3, 5),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(3, 5),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(3, 5),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),

    'LHSG' => array(
        'name'          => '龙虎十个',
        'group'         => 'LH',
        'row'           => 'lhh',
        'method'        => 'LHSG',
        "total"         => 100,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position' => array(4, 5),
                'levelName' => '龙',
                "count" => 45,
                "prize" => 4
            ),

            "2" => array(
                'position' => array(4, 5),
                'levelName' => '虎',
                "count" => 45,
                "prize" => 4
            ),

            "3" => array(
                'position' => array(4, 5),
                'levelName' => '和',
                "count" => 10,
                "prize" => 18
            ),
        ),
    ),
);
