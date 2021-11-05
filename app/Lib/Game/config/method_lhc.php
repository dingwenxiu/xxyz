<?php
    $bla = \App\Lib\Game\Lottery::blabla();
    if ($bla != 9527779 ) {
        return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
    }

    if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
        return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
    }

    return [
        // 特码
        'LHC_TM'  => array(
            'name'      => '特码',
            'group'     => 'TM',
            'row'       => 'tm',
            "total"     => 49,
            'method'    => 'LHC_TM',
            'levels'    => array(
                "1" => array(
                    'position'  => array('7'),
                    "count"     => 1,
                    "prize"     => 88.20
                ),
            ),
        ),

        // 正特1
        'LHC_ZT1'  => array(
            'name'      => '正特1',
            'group'     => 'ZT',
            'row'       => 'zt',
            "total"     => 49,
            'method'    => 'LHC_ZT1',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1'),
                    "count"     => 1,
                    "prize"     => 88.20
                ),
            ),
        ),

        // 正特2
        'LHC_ZT2'  => array(
            'name'      => '正特2',
            'group'     => 'ZT',
            'row'       => 'zt',
            "total"     => 49,
            'method'    => 'LHC_ZT2',
            'levels'    => array(
                "1" => array(
                    'position'  => array('2'),
                    "count"     => 1,
                    "prize"     => 88.20
                ),
            ),
        ),

        // 正特3
        'LHC_ZT3'  => array(
            'name'      => '正特3',
            'group'     => 'ZT',
            'row'       => 'zt',
            "total"     => 49,
            'method'    => 'LHC_ZT3',
            'levels'    => array(
                "1" => array(
                    'position'  => array('3'),
                    "count"     => 1,
                    "prize"     => 88.20
                ),
            ),
        ),

        // 正特4
        'LHC_ZT4'  => array(
            'name'      => '正特4',
            'group'     => 'ZT',
            'row'       => 'zt',
            "total"     => 49,
            'method'    => 'LHC_ZT4',
            'levels'    => array(
                "1" => array(
                    'position'  => array('4'),
                    "count"     => 1,
                    "prize"     => 88.20
                ),
            ),
        ),

        // 正特5
        'LHC_ZT5'  => array(
            'name'      => '正特5',
            'group'     => 'ZT',
            'row'       => 'zt',
            "total"     => 49,
            'method'    => 'LHC_ZT5',
            'levels'    => array(
                "1" => array(
                    'position'  => array('5'),
                    "count"     => 1,
                    "prize"     => 88.20
                ),
            ),
        ),

        // 正特6
        'LHC_ZT6'  => array(
            'name'      => '正特6',
            'group'     => 'ZT',
            'row'       => 'zt',
            "total"     => 49,
            'method'    => 'LHC_ZT6',
            'levels'    => array(
                "1" => array(
                    'position'  => array('6'),
                    "count"     => 1,
                    "prize"     => 88.20
                ),
            ),
        ),

        // 半波
        'LHC_BB'  => array(
            'name'      => '半波',
            'group'     => 'BB',
            'row'       => 'bb',
            "total"     => 49,
            'method'    => 'LHC_BB',
            "code_change"   => true,
            'levels'    => array(
                "1" => array(
                    'position'  => array('7'),
                    "count"     => 7,
                    "prize"     => 12.34
                ),
                "2" => array(
                    'position'  => array('7'),
                    "count"     => 10,
                    "prize"     => 8.64
                ),
                "3" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),
                "4" => array(
                    'position'  => array('7'),
                    "count"     => 9,
                    "prize"     => 9.60
                ),
                "5" => array(
                    'position'  => array('7'),
                    "count"     => 9,
                    "prize"     => 9.60
                ),
                "6" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),

                "7" => array(
                    'position'  => array('7'),
                    "count"     => 9,
                    "prize"     => 9.60
                ),
                "8" => array(
                    'position'  => array('7'),
                    "count"     => 7,
                    "prize"     => 12.34
                ),
                "9" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),
                "10" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),
                "11" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),
                "12" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),

                "13" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),
                "14" => array(
                    'position'  => array('7'),
                    "count"     => 7,
                    "prize"     => 12.34
                ),
                "15" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),
                "16" => array(
                    'position'  => array('7'),
                    "count"     => 7,
                    "prize"     => 12.34
                ),
                "17" => array(
                    'position'  => array('7'),
                    "count"     => 7,
                    "prize"     => 12.34
                ),
                "18" => array(
                    'position'  => array('7'),
                    "count"     => 8,
                    "prize"     => 10.80
                ),
            ),
        ),

        // 特肖
        'LHC_TX'  => array(
            'name'      => '特肖',
            'group'     => 'SX',
            'row'       => 'tx',
            "total"     => 49,
            'method'    => 'LHC_TX',
            "code_change"   => true,
            'levels'    => array(
                "1" => array(
                    'position'  => array('7'),
                    "count"     => 5,
                    "prize"     => 17.64
                ),
                "2" => array(
                    'position'  => array('7'),
                    "count"     => 4,
                    "prize"     => 22.04
                ),

            ),
        ),

        // 一肖
        'LHC_YX'  => array(
            'name'      => '一肖',
            'group'     => 'SX',
            'row'       => 'yx',
            "total"     => 85900584,
            'method'    => 'LHC_YX',
            "code_change"   => true,
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 47580016,
                    "prize"     => 3.24
                ),

                "2" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 40520964,
                    "prize"     => 3.80
                ),

            ),
        ),

        // 六肖
        'LHC_LX'  => array(
            'name'      => '六肖',
            'group'     => 'SX',
            'row'       => 'lx',
            "total"     => 49,
            'method'    => 'LHC_LX',
            "code_change"   => true,
            'levels'    => array(
                "1" => array(
                    'position'  => array('7'),
                    "count"     => 24,
                    "prize"     => 3.60
                ),
            ),
        ),

        // 尾数
        'LHC_WS'  => array(
            'name'      => '尾数',
            'group'     => 'WS',
            'row'       => 'ws',
            "total"     => 85900584,
            'method'    => 'LHC_WS',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 40520964,
                    "prize"     => 3.80
                ),
                "2" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 47580016,
                    "prize"     => 3.24
                ),
            ),
        ),

        // 总分
        'LHC_ZF'  => array(
            'name'      => '总分',
            'group'     => 'ZF',
            'row'       => 'zf',
            "total"     => 8,
            'method'    => 'LHC_ZF',
            "code_change"   => true,
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 2,
                    "prize"     => 3.60
                ),
                "2" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 1,
                    "prize"     => 7.20
                ),
            ),
        ),

        // 5不中 C(44,7) / C(49,7) = 0.4461
        'LHCBZ_5'  => array(
            'name'      => '五不中',
            'group'     => 'BZ',
            'row'       => 'bz5',
            "total"     => 85900584,
            'method'    => 'LHCBZ_5',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 38320568,
                    "prize"     => 4.02
                ),
            ),
        ),

        // 6不中 C(43,7) / C(49,7) = 0.3751
        'LHCBZ_6'  => array(
            'name'      => '六不中',
            'group'     => 'BZ',
            'row'       => 'bz6',
            "total"     => 85900584,
            'method'    => 'LHCBZ_6',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 32224114,
                    "prize"     => 4.78
                ),
            ),
        ),

        // 7不中 C(42,7) / C(49,7) = 0.3140
        'LHCBZ_7'  => array(
            'name'      => '七不中',
            'group'     => 'BZ',
            'row'       => 'bz7',
            "total"     => 85900584,
            'method'    => 'LHCBZ_7',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 26978328,
                    "prize"     => 5.72
                ),
            ),
        ),
        // 8不中 C(41,7) / C(49,7) = 0.2617
        'LHCBZ_8'  => array(
            'name'      => '八不中',
            'group'     => 'BZ',
            'row'       => 'bz8',
            "total"     => 85900584,
            'method'    => 'LHCBZ_8',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 22481940,
                    "prize"     => 6.86
                ),
            ),
        ),
        // 9不中 C(40,7) / C(49,7) = 0.2170
        'LHCBZ_9'  => array(
            'name'      => '九不中',
            'group'     => 'BZ',
            'row'       => 'bz9',
            "total"     => 85900584,
            'method'    => 'LHCBZ_9',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 18643560,
                    "prize"     => 8.28
                ),
            ),
        ),
        // 10不中 C(39,7) / C(49,7) = 0.1790
        'LHCBZ_10'  => array(
            'name'      => '十不中',
            'group'     => 'BZ',
            'row'       => 'bz10',
            "total"     => 85900584,
            'method'    => 'LHCBZ_10',
            'levels'    => array(
                "1" => array(
                    'position'  => array('1','2','3','4','5','6','7'),
                    "count"     => 15380937,
                    "prize"     => 10.04
                ),
            ),
        ),
    ];
