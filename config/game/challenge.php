<?php
$maxBonus   = 20000;
$maxHeBonus = 40000;
return [
    'default'   => 0.02,
    'type' => [
        1 => "默认2%",
        2 => "最小注数",
        3 => "指定号码",
        4 => "混合组选",
        5 => "必定单挑",
        6 => "N个号出M个",
        7 => "和模式",
    ],
    "config"    => [
        'ssc'   => [
            'ZX5'   => [
                'total' => 100000,
                "type"  => 2,
                'min'   => 2000,
                'bonus' => $maxBonus,
            ],

            'ZX5_S'   => [
                'total' => 100000,
                "type"  => 2,
                'min'   => 2000,
                'bonus' => $maxBonus,
            ],

            'ZH5'   => [
                'total' => 500000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 10000
            ],

            'WXZU120'   => [
                'total' => 100000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 16
            ],

            'WXZU60'   => [
                'total' => 100000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 33
            ],

            'WXZU30'   => [
                'total' => 100000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 66
            ],

            'WXZU20'   => [
                'total' => 100000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 100
            ],

            'WXZU10'   => [
                'total' => 100000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 200
            ],

            'WXZU5'   => [
                'total' => 100000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 400
            ],

            'ZX4'   => [
                'total' => 10000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 200
            ],

            'ZX4_S'   => [
                'total' => 10000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 200
            ],

            'ZH4'   => [
                'total' => 40000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 800
            ],

            'SXZU24'   => [
                'total' => 10000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 7
            ],

            'SXZU12'   => [
                'total' => 10000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 15
            ],

            'SXZU6'   => [
                'total' => 10000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 32
            ],

            'SXZU4'   => [
                'total' => 10000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 50
            ],

            'QZX3'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QZX3_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QZH3'   => [
                'total' => 3000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 60,
            ],

            'QZXHZ'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QZXKD'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QZU3'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'QZU3_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'QZU6'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'QZU6_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'QHHZX'   => [
                'total'     => 1000,
                "type"      => 2,
                'bonus'     => $maxBonus,
                'min'       => 7,
            ],

            'QZUHZ'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 7
            ],

            'QZU3BD'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QHZWS'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QTS3'   => [
                'total'     => 1000,
                "type"      => 3,
                'bonus'     => $maxBonus,
                'min'       => 1,
                "config"    => ['code' => "b"]
            ],

            'ZZX3'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'ZZX3_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'ZZH3'   => [
                'total' => 3000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 60,
            ],

            'ZZXHZ'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'ZZXKD'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'ZZU3'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'ZZU3_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'ZZU6'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'ZZU6_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'ZHHZX'   => [
                'total'     => 1000,
                "type"      => 2,
                'bonus'     => $maxBonus,
                'min'       => 7,
            ],

            'ZZUHZ'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 7
            ],

            'ZZU3BD'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'ZHZWS'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'ZTS3'   => [
                'total'     => 1000,
                "type"      => 3,
                'bonus'     => $maxBonus,
                'min'       => 1,
                "config"    => ['code' => "b"]
            ],

            'HZX3'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'HZX3_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'HZH3'   => [
                'total' => 3000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 60,
            ],

            'HZXHZ'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'HZXKD'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'HZU3'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'HZU3_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'HZU6'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'HZU6_S'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'HHHZX'   => [
                'total'     => 1000,
                "type"      => 2,
                'bonus'     => $maxBonus,
                'min'       => 7,
            ],

            'HZUHZ'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 7
            ],

            'HZU3BD'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'HHZWS'   => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'HTS3'   => [
                'total'     => 1000,
                "type"      => 3,
                'bonus'     => $maxBonus,
                'min'       => 1,
                "config"    => ['code' => "b"]
            ],

            "HZX2"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "HZX2_S"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "HZX2HZ"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "HZX2KD"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "HZU2"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 1,
            ],

            "HZU2_S"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 1,
            ],

            "HZU2HZ"  => [
                'total'     => 100,
                "type"      => 6,
                'bonus'     => $maxBonus,
                'min'       => 2,
                'config'    => ['code' => [1 => 1, 2 => 1, 16 => 1, 17 => 1], 'min' => 1],
            ],

            "HZU2BD"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "QZX2"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "QZX2_S"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "QZX2HZ"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "QZX2KD"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "QZU2"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 1,
            ],

            "QZU2_S"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 1,
            ],

            "QZU2HZ"  => [
                'total'     => 100,
                "type"      => 6,
                'bonus'     => $maxBonus,
                'min'       => 2,
                'config'    => ['code' => [1 => 1, 2 => 1, 16 => 1, 17 => 1], 'min' => 1],
            ],

            "QZU2BD"  => [
                'total' => 100,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2,
            ],

            "DWD_W"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "DWD_Q"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "DWD_B"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 1,
            ],

            "DWD_S"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "DWD_G"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "HBDW31"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "QBDW31"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "HBDW32"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "QBDW32"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "BDW41"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "BDW42"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "BDW52"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "BDW53"  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "Q2DXDS"  => [
                'total' => 16,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "H2DXDS"  => [
                'total' => 16,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0,
            ],

            "Q3DXDS"  => [
                'total' => 64,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 1,
            ],

            "H3DXDS"  => [
                'total' => 64,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 1,
            ],

            "YFFS"  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0,
            ],

            "HSCS"  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0,
            ],

            "SXBX"  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 5,
                'min'   => 1,
            ],

            "SJFC"  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 5,
                'min'   => 1,
            ],

            "LHWQ"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHWB"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHWS"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHWG"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHQB"  => [
                'total'     => 3,
                "type"      => 7,
                'min'       => 1,
                'bonus'     => $maxHeBonus,
                'config'    => ['code' => 3],
            ],

            "LHQS"  => [
                'total'     => 3,
                'bonus'     => $maxHeBonus,
                "type"      => 7,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHQG"  => [
                'total'     => 3,
                'bonus'     => $maxHeBonus,
                "type"      => 7,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHBS"  => [
                'total'     => 3,
                'bonus'     => $maxHeBonus,
                "type"      => 7,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHBG"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "LHSG"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 1,
                'config'    => ['code' => 3],
            ],

            "CO_ZX_W"  => [
                'total'     => 10,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_W_DXDS"  => [
                'total'     => 4,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_Q"  => [
                'total'     => 10,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_Q_DXDS"  => [
                'total'     => 4,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_B"  => [
                'total'     => 10,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_B_DXDS"  => [
                'total'     => 4,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],
            "CO_ZX_S"  => [
                'total'     => 10,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_S_DXDS"  => [
                'total'     => 4,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_G"  => [
                'total'     => 10,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZX_G_DXDS"  => [
                'total'     => 4,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_ZHDXDS"  => [
                'total'     => 4,
                "type"      => 1,
                'bonus'     => $maxBonus,
                'min'       => 0,
                'config'    => '',
            ],

            "CO_LHWQ"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHWB"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHWS"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHWG"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHQB"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHQS"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHQG"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHBS"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHBG"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
            "CO_LHSG"  => [
                'total'     => 3,
                "type"      => 7,
                'bonus'     => $maxHeBonus,
                'min'       => 0,
                'config'    => ['code' => 3],
            ],
        ],

        'lotto' => [
            'LTQ3ZX3'   => [
                'total' => 990,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 19
            ],

            'LTQ3ZX3_S'   => [
                'total' => 990,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 19
            ],

            'LTQ3ZU3'   => [
                'total' => 990,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'LTQ3ZU3_S'   => [
                'total' => 990,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'LTQ3ZU3DT'   => [
                'total' => 990,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'LTQ2ZX2'   => [
                'total' => 110,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2
            ],

            'LTQ2ZX2_S'   => [
                'total' => 110,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 2
            ],

            'LTQ2ZU2'   => [
                'total' => 110,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 1
            ],

            'LTQ2ZU2_S'   => [
                'total' => 110,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 1
            ],

            'LTQ2DTZU2'   => [
                'total' => 110,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 1
            ],

            'LTBDW'   => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTDWD_1'   => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTDWD_2'   => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTDWD_3'   => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTDDS'   => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTCZW'   => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX1'   => [
                'total' => 11,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX1_S'   => [
                'total' => 11,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX2'   => [
                'total' => 55,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX2_S'   => [
                'total' => 55,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX3'   => [
                'total' => 165,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX3_S'   => [
                'total' => 165,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX4'   => [
                'total' => 330,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 1
            ],

            'LTRX4_S'   => [
                'total' => 330,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 1
            ],

            'LTRX5'   => [
                'total' => 462,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'LTRX5_S'   => [
                'total' => 462,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 6
            ],

            'LTRX6'   => [
                'total' => 462,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 1
            ],

            'LTRX6_S'   => [
                'total' => 462,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 1
            ],

            'LTRX7'   => [
                'total' => 330,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX7_Ss'   => [
                'total' => 330,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX8'   => [
                'total' => 165,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRX8_S'   => [
                'total' => 165,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRXDT2'   => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRXDT3'   => [
                'total' => 45,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRXDT4'   => [
                'total' => 120,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRXDT5'   => [
                'total' => 210,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRXDT6'   => [
                'total' => 252,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRXDT7'   => [
                'total' => 210,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'LTRXDT8'   => [
                'total' => 120,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],
        ],

        'k3'    => [
            'KSHZDXDS'  => [
                'total' => 4,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'KSHZ'  => [
                'total' => 18,
                "type"  => 6,
                'bonus' => $maxBonus,
                'min'   => 0,
                'config'    => ['code' => ["03" => 1, "04"  => 1, "17" => 1, "18" => 1], 'min' => 2]
            ],

            'SBTH'  => [
                'total' => 20,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'STH'  => [
                'total' => 6,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 4
            ],

            'SLH'  => [
                'total' => 4,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'K3BS'  => [
                'total' => 4,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'ETH'  => [
                'total' => 30,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'EBTH'  => [
                'total' => 15,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DTYS'  => [
                'total' => 6,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KS_CO_HZDXDS'  => [
                'total' => 4,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'KS_CO_HZ'  => [
                'total' => 18,
                "type"  => 6,
                'bonus' => $maxBonus,
                'min'   => 0,
                'config'    => ['code' => ["03" => 1, "04"  => 1, "17" => 1, "18" => 1], 'min' => 2]
            ],

            'KS_CO_EL'  => [
                'total' => 15,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KS_CO_DD'  => [
                'total' => 6,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KS_CO_BZ'  => [
                'total' => 1,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KS_CO_DZ'  => [
                'total' => 16,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],
        ],

        'ssl'   => [
            'QZX3'  => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QZX3_S'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 20
            ],

            'QZXHZ'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 20
            ],

            'QZU3'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 6
            ],

            'QZU3_S'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 6
            ],

            'QZU6'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 3
            ],

            'QZU6_S'  => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'QHHZX'  => [
                'total'     => 1000,
                'bonus'     => $maxBonus,
                "type"      => 2,
                'min'       => 7,
            ],

            'QZUHZ'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 7
            ],

            'QZX2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'QZX2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'HZX2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'HZX2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'QZU2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'QZU2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'HZU2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'HZU2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'DWD'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_W'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_Q'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_B'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_G'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'QBDW31'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'QBDW32'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'Q2DXDS'  => [
                'total' => 4,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'H2DXDS'  => [
                'total' => 4,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],
        ],
        'sd'    => [
            'QZX3'  => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QZX3_S'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 20
            ],

            'QZXHZ'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 20
            ],

            'QZU3'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 6
            ],

            'QZU3_S'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 6
            ],

            'QZU6'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 3
            ],

            'QZU6_S'  => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'QHHZX'  => [
                'total'     => 1000,
                'bonus'     => $maxBonus,
                "type"      => 2,
                'min'       => 7
            ],

            'QZUHZ'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 7
            ],

            'QZX2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'QZX2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'HZX2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'HZX2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'QZU2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'QZU2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'HZU2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'HZU2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'DWD'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_W'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_Q'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_B'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_G'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'QBDW31'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'QBDW32'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'Q2DXDS'  => [
                'total' => 4,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'H2DXDS'  => [
                'total' => 4,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],
        ],

        'p3p5'  => [
            'QZX3'  => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 20
            ],

            'QZX3_S'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 20
            ],

            'QZXHZ'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 20
            ],

            'QZU3'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 6
            ],

            'QZU3_S'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 6
            ],

            'QZU6'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 3
            ],

            'QZU6_S'  => [
                'total' => 1000,
                "type"  => 2,
                'bonus' => $maxBonus,
                'min'   => 3
            ],

            'QHHZX'  => [
                'total'     => 1000,
                'bonus'     => $maxBonus,
                "type"      => 2,
                'min'       => 7,
            ],

            'QZUHZ'  => [
                'total' => 1000,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 7
            ],

            'QZX2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'QZX2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'HZX2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'HZX2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 2
            ],

            'QZU2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'QZU2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'HZU2'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'HZU2_S'  => [
                'total' => 100,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'DWD'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_W'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_Q'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_B'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'DWD_G'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'QBDW31'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'QBDW32'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'Q2DXDS'  => [
                'total' => 4,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'H2DXDS'  => [
                'total' => 4,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],
        ],

        'pk10'  => [
            'PKQZX1'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQZX1_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQD2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQD2_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQZX2'  => [
                'total' => 90,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'PKQZX2_S'  => [
                'total' => 90,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 1
            ],

            'PKQD3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQD3_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQZX3'  => [
                'total' => 720,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 14
            ],

            'PKQZX3_S'  => [
                'total' => 720,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 14
            ],

            'PKQD4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQD4_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQZX4'  => [
                'total' => 5040,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 100
            ],

            'PKQZX4_S'  => [
                'total' => 5040,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 100
            ],

            'PKQD5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQD5_S'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKQZX5'  => [
                'total' => 30240,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 604
            ],

            'PKQZX5_S'  => [
                'total' => 30240,
                'bonus' => $maxBonus,
                "type"  => 2,
                'min'   => 604
            ],

            'PKDWD'  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'PKDWD_1'  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'PKDWD_2'  => [
                'total' => 10,
                "type"  => 1,
                'bonus' => $maxBonus,
                'min'   => 0
            ],

            'PKDWD_3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKDWD_4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKDWD_5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKDWD_6'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKDWD_7'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKDWD_8'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKDWD_9'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PKDWD_10'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_GYHDXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D1DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D2DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D3DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D4DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D5DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D6DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D7DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D8DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D9DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D10DXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_GYH'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_GYJH'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_SWH'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D1'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D6'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],
            'PK_CO_D7'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D8'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D9'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'PK_CO_D10'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],
        ],

        'pcdd'  => [
            'TM'  => [
                'total'     => 49,
                'bonus'     => $maxBonus,
                "type"      => 6,
                'min'       => 0,
                'config'    => ['code' => [0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 23 => 1, 24 => 1, 25 => 1, 26 => 1, 27 => 1], 'min' => 1],
            ],

            'PCDDDXDS'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'BAOZI'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'BO'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],
        ],

        'klsf'  => [
            'KLSF_DWD'  => [
                'total'     => 49,
                'bonus'     => $maxBonus,
                "type"      => 6,
                'min'       => 0,
                'config'    => ['code' => [0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 23 => 1, 24 => 1, 25 => 1, 26 => 1, 27 => 1], 'min' => 1],
            ],

            'KLSF_DWD_D1'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DWD_D2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DWD_D3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DWD_D4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DWD_D5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DWD_D6'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DWD_D7'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DWD_D8'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_RX1Z1'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],


            'KLSF_RX2Z2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_RX3Z3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_RX4Z4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_RX5Z5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DT2Z2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DT3Z3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DT4Z4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DT5Z5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_Q_ZX3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_H_ZX3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_Q_ZU3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_H_ZU3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],


            'KLSF_Q_ZX2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],


            'KLSF_Q_ZU2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D1'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D6'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D7'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_D8'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_DXDS_DXH'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_SJFW_D1'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_SJFW_D2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],


            'KLSF_SJFW_D3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_SJFW_D4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_SJFW_D5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_SJFW_D6'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_SJFW_D7'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_SJFW_D8'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_WX_D1'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_WX_D2'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],


            'KLSF_WX_D3'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_WX_D4'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_WX_D5'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_WX_D6'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_WX_D7'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_WX_D8'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_LH_L'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],

            'KLSF_LH_H'  => [
                'total' => 10,
                'bonus' => $maxBonus,
                "type"  => 1,
                'min'   => 0
            ],
        ],
    ],

];
