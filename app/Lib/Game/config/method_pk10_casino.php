<?php

$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

return array(
    // 冠亚和大小单双
    'PK_CO_GYHDXDS' => array(
        'name'          => '冠亚和大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_GYHDXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 4.05
            ),
            '2' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 3.24
            ),
            '3' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 3.24
            ),
            '4' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 4.05
            ),
        ),
    ),

    // 冠军大小单双
    'PK_CO_D1DXDS' => array(
        'name'          => '冠军大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D1DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 亚军大小单双
    'PK_CO_D2DXDS' => array(
        'name'          => '亚军大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D2DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('2', "9"),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 季军大小单双
    'PK_CO_D3DXDS' => array(
        'name'          => '季军大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D3DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('3', "8"),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 第四名大小单双
    'PK_CO_D4DXDS' => array(
        'name'          => '第四名大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D4DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('4', '7'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 第五名大小单双
    'PK_CO_D5DXDS' => array(
        'name'          => '第五名大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D5DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('5', '6'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 第六名大小单双
    'PK_CO_D6DXDS' => array(
        'name'          => '第六名大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D6DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('6'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 第七名大小单双
    'PK_CO_D7DXDS' => array(
        'name'          => '第七名大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D7DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('7'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 第八名大小单双
    'PK_CO_D8DXDS' => array(
        'name'          => '第八名大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D8DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('8'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 第九名大小单双
    'PK_CO_D9DXDS' => array(
        'name'          => '第九名大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D9DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('9'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 第十名大小单双
    'PK_CO_D10DXDS' => array(
        'name'          => '第十名大小单双',
        'group'         => 'LM',
        'row'           => 'lm',
        'method'        => 'PK_CO_D10DXDS',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 4,
        'levels' => array(
            '1' => array(
                'position'  => array('10'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 冠亚和
    'PK_CO_GYH' => array(
        'name'          => '冠亚和',
        'group'         => 'CHZ',
        'row'           => 'gyh',
        'method'        => 'PK_CO_GYH',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '3' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 81
            ),
            '4' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 81
            ),
            '5' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '6' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '7' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 27
            ),
            '8' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 27
            ),
            '9' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '10' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '11' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 16.2
            ),
            '12' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '13' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '14' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 27
            ),
            '15' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 27
            ),
            '16' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '17' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '18' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 81
            ),
            '19' => array(
                'position'  => array('1', '2'),
                "count"     => 1,
                "prize"     => 81
            ),
        ),
    ),

    // 冠亚季和
    'PK_CO_GYJH' => array(
        'name'          => '冠亚季和',
        'group'         => 'CHZ',
        'row'           => 'gyjh',
        'method'        => 'PK_CO_GYJH',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '6' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 216
            ),
            '7' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 216
            ),
            '8' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 72
            ),
            '9' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 72
            ),
            '10' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 43.2
            ),
            '11' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 43.2
            ),
            '12' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 30.8570
            ),
            '13' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 27
            ),
            '14' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 23.9998
            ),
            '15' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 21.6
            ),
            '16' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 21.6
            ),
            '17' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 21.6
            ),
            '18' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 21.6
            ),
            '19' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 23.9998
            ),
            '20' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 27
            ),
            '21' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 30.8570
            ),
            '22' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 43.2
            ),
            '23' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 43.2
            ),
            '24' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 72
            ),
            '25' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 72
            ),
            '26' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 216
            ),
            '27' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 216
            ),
        ),
    ),

    // 首尾和
    'PK_CO_SWH' => array(
        'name'          => '首尾和',
        'group'         => 'CHZ',
        'row'           => 'swh',
        'method'        => 'PK_CO_SWH',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '3' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 81
            ),
            '4' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 81
            ),
            '5' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '6' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '7' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 27
            ),
            '8' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 27
            ),
            '9' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '10' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '11' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 16.2
            ),
            '12' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '13' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 20.25
            ),
            '14' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 27
            ),
            '15' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 27
            ),
            '16' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '17' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 40.5
            ),
            '18' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 81
            ),
            '19' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 81
            ),
        ),
    ),


    // 猜冠军
    'PK_CO_D1' => array(
        'name'          => '冠军',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D1',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('1'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 亚军
    'PK_CO_D2' => array(
        'name'          => '亚军',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D2',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('2'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 季军
    'PK_CO_D3' => array(
        'name'          => '季军',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D3',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('3'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第四名
    'PK_CO_D4' => array(
        'name'          => '第四名',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D4',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('4'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第五名
    'PK_CO_D5' => array(
        'name'          => '第五名',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D5',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('5'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第六名
    'PK_CO_D6' => array(
        'name'          => '第六名',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D6',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('6'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第七名
    'PK_CO_D7' => array(
        'name'          => '第七名',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D7',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('7'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第八名
    'PK_CO_D8' => array(
        'name'          => '第八名',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D8',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('8'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第九名
    'PK_CO_D9' => array(
        'name'          => '第九名',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D9',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('9'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第十名
    'PK_CO_D10' => array(
        'name'          => '第十名',
        'group'         => 'D1TO10',
        'row'           => 'd1to10',
        'method'        => 'PK_CO_D10',
        'type'          => "casino",
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('10'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 龙虎1v10
    'PK_CO_LH1V10' => array(
        'name'          => '冠军VS第十名',
        'group'         => 'LHD',
        'row'           => 'lhd',
        'method'        => 'PK_CO_LH1V10',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('1', '10'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 龙虎2v9
    'PK_CO_LH2V9' => array(
        'name'          => '亚军VS第九名',
        'group'         => 'LHD',
        'row'           => 'lhd',
        'method'        => 'PK_CO_LH2V9',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('2', '9'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 龙虎3v8
    'PK_CO_LH3V8' => array(
        'name'          => '季军VS第八名',
        'group'         => 'LHD',
        'row'           => 'lhd',
        'method'        => 'PK_CO_LH3V8',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('3', '8'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 龙虎4v7
    'PK_CO_LH4V7' => array(
        'name'          => '第四名VS第七名',
        'group'         => 'LHD',
        'row'           => 'lhd',
        'method'        => 'PK_CO_LH4V7',
        'type'          => "casino",
        "code_change"   => true,
        'total'         => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('4', '7'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),

    // 龙虎5v6
    'PK_CO_LH5V6' => array(
        'name'          => '第五名VS第六名',
        'group'         => 'LHD',
        'row'           => 'lhd',
        'method'        => 'PK_CO_LH5V6',
        'type'          => "casino",
        'total'         => 10,
        "code_change"   => true,
        'levels' => array(
            '1' => array(
                'position'  => array('5', '6'),
                "count"     => 1,
                "prize"     => 3.6
            ),
        ),
    ),
);
