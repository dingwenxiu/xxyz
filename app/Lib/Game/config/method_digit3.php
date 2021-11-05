<?php

$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

return [
    // 排三直选复式
    'QZX3' => array(
        'name'      => '直选复式',
        'group'     => 'SX',
        'row'       => 'zhixuan',
        'method'        => 'QZX3',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    'QZX3_S' => array(
        'name'      => '直选单式',
        'method'    => 'QZX3_S',
        'group'     => 'SX',
        'row'       => 'zhixuan',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    'QZXHZ' => array(
        'name'      => '直选和值',
        'method'    => 'QZXHZ',
        'group'     => 'SX',
        'row'       => 'zhixuan',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 1800.00
            ),
        ),
    ),

    'QZU3' => array(
        'name'      => '组三复式',
        'method'    => 'QZU3',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    'QZU3_S' => array(
        'name'      => '组三单式',
        'method'    => 'QZU3_S',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 3,
                "prize" => 600.00
            ),
        ),
    ),

    // 排三组六
    'QZU6' => array(
        'name'      => '组六复式',
        'method'    => 'QZU6',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    'QZU6_S' => array(
        'name'      => '组六单式',
        'method'    => 'QZU6_S',
        'group'     => 'SX',
        'row'       => 'zuxuan',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 6,
                "prize" => 300.00
            ),
        ),
    ),

    'QHHZX' => array(
        'name'      => '混合组选',
        'method'    => 'QHHZX',
        'group'     => 'SX',
        'row'       => 'zuxuan',
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

    'QZUHZ' => array(
        'name'      => '组选和值',
        'method'    => 'QZUHZ',
        'group'     => 'SX',
        'row'       => 'zuxuan',
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

    /**   ====== 二星 ====== */
    'QZX2' => array(
        'name'      => '前二直选复式',
        'method'    => 'QZX2',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        "total"     => 100,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 1,
                "prize" => 180.00
            ),
        ),
    ),
    'QZX2_S' => array(
        'name'      => '前二直选单式',
        'method'    => 'QZX2_S',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        "total"     => 100,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 1,
                "prize" => 180.00
            ),
        ),
    ),

    'HZX2' => array(
        'name'      => '后二直选复式',
        'method'    => 'HZX2',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        "total"     => 100,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3),
                "count" => 1,
                "prize" => 180.00
            ),
        ),
    ),
    'HZX2_S' => array(
        'name'      => '后二直选单式',
        'method'    => 'HZX2_S',
        'group'     => 'EX',
        'row'       => 'zhixuan',
        "total"     => 100,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3),
                "count" => 1,
                "prize" => 180.00
            ),
        ),
    ),

    'QZU2' => array(
        'name' => '前二组选复式',
        'method' => 'ZU2',
        'group' => 'EX',
        'row' => 'zuxuan',
        "total" => 100,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 2,
                "prize" => 90.00
            ),
        ),
    ),

    'QZU2_S' => array(
        'name'      => '前二组选单式',
        'method'    => 'QZU2_S',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        "total"     => 100,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 2,
                "prize" => 90.00
            ),
        ),
    ),

    'HZU2' => array(
        'name'      => '后二组选复式',
        'method'    => 'HZU2',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        "total"     => 100,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3),
                "count" => 2,
                "prize" => 90.00
            ),
        ),
    ),

    'HZU2_S' => array(
        'name'      => '后二组选单式',
        'method'    => 'HZU2_S',
        'group'     => 'EX',
        'row'       => 'zuxuan',
        "total" => 100,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3),
                "count" => 2,
                "prize" => 90.00
            ),
        ),
    ),

    // 定位胆
    'DWD' => array(
        'name'      => '定位胆',
        'method'    => 'DWD',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 100,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 10,
                "prize" => 18.00
            ),
        ),
    ),

    // 定位胆 - 百
    'DWD_B' => array(
        'name'      => '定位胆_百',
        'method'    => 'DWD_B',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 10,
        'hidden'    => true,
        'levels' => array(
            '1' => array(
                'position' => array(1),
                "count" => 1,
                "prize" => 18.00
            ),
        ),
    ),

    // 定位胆 - 十
    'DWD_S' => array(
        'name'      => '定位胆_十',
        'method'    => 'DWD_S',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 10,
        'hidden'    => true,
        'levels' => array(
            '1' => array(
                'position' => array(2),
                "count" => 1,
                "prize" => 18.00
            ),
        ),
    ),

    // 定位胆 - 个
    'DWD_G' => array(
        'name'      => '定位胆_个',
        'method'    => 'DWD_G',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 10,
        'hidden' => true,
        'levels' => array(
            '1' => array(
                'position' => array(3),
                "count" => 1,
                "prize" => 18.00
            ),
        ),
    ),

    // 不定位 - 一码
    'QBDW31' => array(
        'name'      => '一码不定位',
        'method'    => 'QBDW31',
        'group'     => 'BDW',
        'row'       => 'budingwei',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 271,
                "prize" => 6.60
            ),
        ),
    ),

    'QBDW32' => array(
        'name'      => '二码不定位',
        'method'    => 'QBDW32',
        'group'     => 'BDW',
        'row'       => 'budingwei',
        "total"     => 1000,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 54,
                "prize" => 33.00
            ),
        ),
    ),

    // 大小单双
    'Q2DXDS' => array(
        'name'      => '前二大小单双',
        'method'    => 'Q2DXDS',
        'group'     => 'DXDS',
        'row'       => 'dxds',
        "total"     => 1000,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2),
                "count" => 250,
                "prize" => 7.20
            ),
        ),
    ),

    'H2DXDS' => array(
        'name'      => '后二大小单双',
        'method'    => 'H2DXDS',
        'group'     => 'DXDS',
        'row'       => 'dxds',
        "total"     => 1000,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'position' => array(2, 3),
                "count" => 250,
                "prize" => 7.20
            ),
        ),
    ),
];
