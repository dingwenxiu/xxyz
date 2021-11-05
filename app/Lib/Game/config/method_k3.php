<?php
$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

return array(
    // 和值大小单双
    'KSHZDXDS' => array(
        'name'          => '和值大小单双',
        'group'         => 'DXDS',
        'row'           => 'dxds',
        'method'        => 'KSHZDXDS',
        "code_change"   => true,
        "total"     => 216,
        'levels' => array(
            "1" => array(
                'position' => array(1, 2, 3),
                "count" => 108,
                "prize" => 3.6
            ),
        ),
    ),

    // 和值
    'KSHZ' => array(
        'name'      => '和值',
        'group'     => 'HZ',
        'row'       => 'hezhi',
        'method'    => 'KSHZ',
        "total"     => 216,
        'levels' => array(
            "1" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:3,18',
                "count" => 1,
                "prize" => 388.80
            ),

            "2" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:4,17',
                "count" => 3,
                "prize" => 129.60
            ),

            "3" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:5,16',
                "count" => 6,
                "prize" => 64.80
            ),

            "4" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:6,15',
                "count" => 10,
                "prize" => 38.88
            ),

            "5" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:7,14',
                "count" => 15,
                "prize" => 25.92
            ),

            "6" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:8,13',
                "count" => 21,
                "prize" => 18.50
            ),

            "7" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:9,12',
                "count" => 25,
                "prize" => 15.50
            ),

            "8" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:10,11',
                "count" => 27,
                "prize" => 14.00
            ),
        ),
    ),

    // 三不同号
    'SBTH' => array(
        'name'      => '三不同号',
        'group'     => 'SBTH',
        'row'       => 'sanbutonghao',
        'method'    => 'SBTH',
        "total"     => 216,
        'levels'    => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count"     => 6,
                "prize"     => 64.80
            ),
        ),
    ),

    // 三同号
    'STH' => array(
        'name'      => '三同号',
        'group'     => 'STH',
        'row'       => 'santonghao',
        'method'    => 'STH',
        "total"     => 216,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 388.80
            ),
        ),
    ),

    // 三连号
    'SLH' => array(
        'name'      => '三连号',
        'group'     => 'SLH',
        'row'       => 'sanlianhao',
        'method'    => 'SLH',
        "total"     => 216,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 6,
                "prize" => 64.8
            ),
        ),
    ),

    // 半顺
    'K3BS' => array(
        'name'      => '半顺',
        'group'     => 'BS',
        'row'       => 'bs',
        'method'    => 'K3BS',
        "total"     => 216,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 6,
                "prize" => 64.8
            ),
        ),
    ),

    // 二同号
    'ETH' => array(
        'name'      => '二同号',
        'group'     => 'ETH',
        'row'       => 'ertonghao',
        'method'    => 'ETH',
        "total"     => 216,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 3,
                "prize" => 129.60
            ),
        ),
    ),

    // 二不同号
    'EBTH' => array(
        'name'      => '二不同号',
        'group'     => 'EBTH',
        'row'       => 'erbutong',
        'method'    => 'EBTH',
        "total"     => 216,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 30,
                "prize" => 12.96
            ),
        ),
    ),

    // 单挑一骰
    'DTYS' => array(
        'name'      => '单挑一骰',
        'group'     => 'DTYS',
        'row'       => 'dantiaoyishai',
        'method'    => 'DTYS',
        "total"     => 216,
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count"     => 91,
                "prize"     => 4.27
            ),
        ),
    ),
);
