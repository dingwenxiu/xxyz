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
    // 整合
    'CO_ZH' => array(
        'name'      => '第一球直选',
        'type'      => "casino",
        'group'     => 'COZHENGHE',
        'row'       => '1q',
        'method'    => 'CO_ZX_W',
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(1),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 两面盘
    'CO_LMP' => array(
        'name'      => '第一球直选',
        'type'      => "casino",
        'group'     => 'COLMP',
        'row'       => '1q',
        'method'    => 'CO_ZX_W',
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(1),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 万
    'CO_ZX_W' => array(
        'name'      => '第一球直选',
        'type'      => "casino",
        'group'     => 'COD1Q',
        'row'       => '1q',
        'method'    => 'CO_ZX_W',
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(1),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    'CO_ZX_W_DXDS' => array(
        'name'      => '第一球大小单双',
        'type'      => "casino",
        'group'     => 'COD1Q',
        'row'       => '1q',
        'method'    => 'CO_ZX_W_DXDS',
        "code_change"   => true,
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(1),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 千
    'CO_ZX_Q' => array(
        'name'      => '第二球直选',
        'type'      => "casino",
        'group'     => 'COD2Q',
        'row'       => '2q',
        'method'    => 'CO_ZX_Q',
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(2),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    'CO_ZX_Q_DXDS' => array(
        'name'          => '第二球大小单双',
        'type'          => "casino",
        'group'         => 'COD2Q',
        'row'           => '2q',
        'method'        => 'CO_ZX_Q_DXDS',
        "code_change"   => true,
        "total"         => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(2),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 百
    'CO_ZX_B' => array(
        'name'      => '第三球直选',
        'type'      => "casino",
        'group'     => 'COD3Q',
        'row'       => '3q',
        'method'    => 'CO_ZX_Q',
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(3),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    'CO_ZX_B_DXDS' => array(
        'name'      => '第三球大小单双',
        'type'      => "casino",
        'group'     => 'COD3Q',
        'row'       => '3q',
        'method'    => 'CO_ZX_Q_DXDS',
        "code_change"   => true,
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(3),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 十
    'CO_ZX_S' => array(
        'name'      => '第四球直选',
        'type'      => "casino",
        'group'     => 'COD4Q',
        'row'       => '4q',
        'method'    => 'CO_ZX_S',
        "total"     => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(4),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    'CO_ZX_S_DXDS' => array(
        'name'          => '第四球大小单双',
        'type'          => "casino",
        'group'         => 'COD4Q',
        'row'           => '4q',
        'method'        => 'CO_ZX_S_DXDS',
        "total"         => 10,
        "code_change"   => true,
        'levels' => array(
            "1" => array(
                'position'  => array(4),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 个
    'CO_ZX_G' => array(
        'name'          => '第五球直选',
        'type'          => "casino",
        'group'         => 'COD5Q',
        'row'           => '5q',
        'method'        => 'CO_ZX_G',
        "total"         => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(5),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    'CO_ZX_G_DXDS' => array(
        'name'          => '第五球大小单双',
        'type'          => "casino",
        'group'         => 'COD5Q',
        'row'           => '5q',
        'method'        => 'CO_ZX_G_DXDS',
        "code_change"   => true,
        "total"         => 10,
        'levels' => array(
            "1" => array(
                'position'  => array(5),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 总和
    'CO_ZHDXDS' => array(
        'name'      => '总和',
        'type'      => "casino",
        'group'     => 'COZH',
        'row'       => 'zhdxds',
        'method'    => 'CO_ZHDXDS',
        "code_change"   => true,
        "total"     => 100000,
        'levels'    => array(
            "1" => array(
                'position'  => array(1, 2, 3, 4, 5),
                "count"     => 25000,
                "prize"     => 3.6
            ),
        ),
    ),

    // 龙虎
    'CO_LHWQ' =>array(
        'name'          => '龙虎万千',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHWQ',
        "total"         => 100,
        "code_change"   => true,
        'levels'    => array(
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

    'CO_LHWB' =>array(
        'name'          => '龙虎万百',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHWB',
        "total"         => 100,
        "code_change"   => true,
        'levels'    => array(
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

    'CO_LHWS' =>array(
        'name'          => '龙虎万十',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHWS',
        "code_change"   => true,
        "total"         => 100,
        'levels'    => array(
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

    'CO_LHWG' =>array(
        'name'          => '龙虎万个',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHWG',
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

    'CO_LHQB' =>array(
        'name'          => '龙虎千百',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHQB',
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

    'CO_LHQS' => array(
        'name'          => '龙虎千十',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHQS',
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
    'CO_LHQG' =>array(
        'name'          => '龙虎千个',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHQG',
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

    'CO_LHBS' =>array(
        'name'          => '龙虎百十',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHBS',
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

    'CO_LHBG' =>array(
        'name'          => '龙虎百个',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHBG',
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

    'CO_LHSG' => array(
        'name'          => '龙虎十个',
        'type'          => "casino",
        'group'         => 'COLH',
        'row'           => 'lhh',
        'method'        => 'CO_LHSG',
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

    // 全五中一
    'CO_QWZY' => array(
        'name'      => '全五中一',
        'type'      => "casino",
        'group'     => 'COQWZY',
        'row'       => 'qwzy',
        'method'    => 'CO_QWZY',
        "total"     => 100000,
        'levels'    => array(
            "1" => array(
                'position'  => array(1, 2, 3, 4, 5),
                "count"     => 40951,
                "prize"     => 4.3955
            ),
        ),
    ),
);
