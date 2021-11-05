<?php
$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

return array(
    // 特码
    'TM' => array(
        'name'      => '特码',
        'group'     => 'TM',
        'row'       => 'tm',
        'method'    => 'TM',
        "total"     => 1000,
        'levels'    => array(
            "1"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 1674
            ),
            "2"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 558
            ),
            "3"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 279
            ),
            "4"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 167.4
            ),
            "5"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 111.6
            ),
            "6"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 79.7
            ),
            "7"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 59.76
            ),
            "8"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 46.48
            ),
            "9"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 37.18
            ),
            "10"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 30.42
            ),
            "11"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 26.56
            ),
            "12"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 24.24
            ),
            "13"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 22.90
            ),
            "14"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 22.32
            ),
            "15"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 22.32
            ),
            "16"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 22.90
            ),
            "17"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 24.24
            ),
            "18"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 26.56
            ),
            "19"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 30.42
            ),
            "20"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 37.18
            ),
            "21"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 46.48
            ),
            "22"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 59.76
            ),
            "23"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 79.7
            ),
            "24"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 111.60
            ),
            "25"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 167.4
            ),
            "26"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 279
            ),
            "27"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 558
            ),
            "28"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 1674
            ),
        ),
    ),

    // 大小单双
    'PCDDDXDS' => array(
        'name'          => '大小单双',
        'group'         => 'DXDS',
        'row'           => 'dxds',
        'method'        => 'PCDDDXDS',
        "total"         => 1000,
        "code_change"   => true,
        'levels'        => array(
            // 大
            "1"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 3.6
            ),
            // 小
            "2"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 3.6
            ),
            // 单
            "3"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 3.6
            ),
            // 双
            "4"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 3.6
            ),
            // 大单
            "5"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 7.2
            ),
            // 大双
            "6"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 6.18
            ),
            // 小单
            "7"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 6.18
            ),
            // 小双
            "8"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 7.2
            ),
            "9"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 31.50
            ),
            "10"     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 500,
                "prize"     => 31.50
            ),
        ),
    ),

    // 豹子
    'BAOZI' => array(
        'name'          => '豹子',
        'group'         => 'BZ',
        'row'           => 'bz',
        'method'        => 'BAOZI',
        "code_change"   => true,
        "total"         => 1000,
        'levels'        => array(
            "1"         => array(
                'position'  => array('1', '2', '3'),
                "count"     => 10,
                "prize"     => 167.4
            ),
        ),
    ),

    // 波
    'BO' => array(
        'name'          => '波',
        'group'         => 'BO',
        'row'           => 'bo',
        'method'        => 'BO',
        "total"         => 1000,
        "he_mode"       => 1,
        "code_change"   => true,
        'levels'        => array(
            // 红
            "1"         => array(
                'position'  => array('1', '2', '3'),
                "count"     => 332,
                "prize"     => 4.5974
            ),
            // 蓝
            "2"         => array(
                'position'  => array('1', '2', '3'),
                "count"     => 332,
                "prize"     => 4.5974
            ),
            // 绿
            "3"         => array(
                'position'  => array('1', '2', '3'),
                "count"     => 332,
                "prize"     => 4.5974
            ),
        ),
    ),
);
