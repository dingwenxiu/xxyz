<?php

$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

return array(
    // 和值
    'KS_CO_HZ' => array(
        'name'      => '和值',
        'group'     => 'ZH',
        'row'       => 'zh',
        'method'    => 'KS_CO_HZ',
        'type'      => "casino",
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
                "prize" => 18.5142
            ),

            "7" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:9,12',
                "count" => 25,
                "prize" => 15.552
            ),

            "8" => array(
                'position' => array(1, 2, 3),
                'levelName' => '和值:10,11',
                "count" => 27,
                "prize" => 14.40
            ),
        ),
    ),

    // 和值大小单双
    'KS_CO_HZDXDS' => array(
        'name'          => '和值大小单双',
        'group'         => 'ZH',
        'row'           => 'zh',
        'type'          => "casino",
        'method'        => 'KS_CO_HZDXDS',
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

    // 两连号
    'KS_CO_EL' => array(
        'name'      => '两连',
        'group'     => 'ZH',
        'row'       => 'zh',
        'method'    => 'KS_CO_EL',
        "total"     => 216,
        'type'      => "casino",
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 6,
                "prize" => 12.96
            ),
        ),
    ),

    // 独胆
    'KS_CO_DD' => array(
        'name'      => '独胆',
        'group'     => 'ZH',
        'row'       => 'zh',
        'method'    => 'KS_CO_DD',
        "total"     => 216,
        'type'      => "casino",
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count"     => 91,
                "prize"     => 4.2725
            ),
        ),
    ),

    // 豹子
    'KS_CO_BZ' => array(
        'name'      => '豹子',
        'group'     => 'ZH',
        'row'       => 'zh',
        'method'    => 'KS_CO_BZ',
        "total"     => 216,
        'type'      => "casino",
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 1,
                "prize" => 388.80
            ),
        ),
    ),

    // 对子
    'KS_CO_DZ' => array(
        'name'      => '对子',
        'group'     => 'ZH',
        'row'       => 'zh',
        'method'    => 'KS_CO_DZ',
        "total"     => 216,
        'type'      => "casino",
        'levels' => array(
            '1' => array(
                'position' => array(1, 2, 3),
                "count" => 15,
                "prize" => 24.3
            ),
        ),
    ),
);
