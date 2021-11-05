<?php
$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

return array(
    // 猜冠军
    'PKQZX1' => array(
        'name'      => '第一名复式',
        'group'     => 'CD1',
        'row'       => 'diyiming',
        'method'    => 'PKQZX1',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('1'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 猜冠军
    'PKQZX1_S' => array(
        'name'      => '第一名单式',
        'group'     => 'CD1',
        'row'       => 'diyiming',
        'method'    => 'PKQZX1_S',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('1'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第二名
    'PKQD2' => array(
        'name'      => '第二名复式',
        'group'     => 'CQ2',
        'row'       => 'dierming',
        'method'    => 'PKQD2',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('2'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第二名单式
    'PKQD2_S' => array(
        'name'      => '第二名单式',
        'group'     => 'CQ2',
        'row'       => 'dierming',
        'method'    => 'PKQD2_S',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('2'),
                "count"     =>1,
                "prize"     => 18
            ),
        ),
    ),

    // 猜前二
    'PKQZX2' => array(
        'name'      => '猜前二复式',
        'group'     => 'CQ2',
        'row'       => 'caiqianer',
        'method'    => 'PKQZX2',
        'total'     => 90,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2'),
                "count"     => 1,
                "prize"     => 162
            ),
        ),
    ),

    // 猜前二 - 单式
    'PKQZX2_S' => array(
        'name'      => '猜前二单式',
        'group'     => 'CQ2',
        'row'       => 'caiqianer',
        'method'    => 'PKQZX2_S',
        'total'     => 90,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2'),
                "count"     => 1,
                "prize"     => 162
            ),
        ),
    ),

    // 第三名
    'PKQD3' => array(
        'name'      => '第三名复式',
        'group'     => 'CQ3',
        'row'       => 'disanming',
        'method'    => 'PKQD3',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('3'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第三名单式
    'PKQD3_S' => array(
        'name'      => '第三名单式',
        'group'     => 'CQ3',
        'row'       => 'disanming',
        'method'    => 'PKQD3_S',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('3'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 猜前三 - 直选复式
    'PKQZX3' => array(
        'name'      => '猜前三复式',
        'group'     => 'CQ3',
        'row'       => 'caiqiansan',
        'method'    => 'PKQZX3',
        'total'     => 720,
        'levels'    => array(
            '1' => array(
                'position'  => array('1','2','3'),
                "count"     => 1,
                "prize"     => 1296
            ),
        ),
    ),

    // 猜前三 - 单式
    'PKQZX3_S' => array(
        'name'      => '猜前三单式',
        'group'     => 'CQ3',
        'row'       => 'caiqiansan',
        'method'    => 'PKQZX3_S',
        'total'     => 720,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3'),
                "count"     => 1,
                "prize"     => 1296
            ),
        ),
    ),

    // 第四名
    'PKQD4' => array(
        'name'      => '第四名复式',
        'group'     => 'CQ4',
        'row'       => 'disiming',
        'method'    => 'PKQD4',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('4'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第四名单式
    'PKQD4_S' => array(
        'name'      => '第四名单式',
        'group'     => 'CQ4',
        'row'       => 'disiming',
        'method'    => 'PKQD4_S',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('4'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 猜前四 - 单式
    'PKQZX4' => array(
        'name'      => '猜前四复式',
        'group'     => 'CQ4',
        'row'       => 'caiqiansi',
        'method'    => 'PKQZX4',
        "total"     => 5040,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4'),
                "count"     => 1,
                "prize"     => 9072
            ),
        ),
    ),

    // 猜前四 - 单式
    'PKQZX4_S' => array(
        'name'      => '猜前四单式',
        'group'     => 'CQ4',
        'row'       => 'caiqiansi',
        'method'    => 'PKQZX4_S',
        "total"     => 5040,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4'),
                "count"     => 1,
                "prize"     => 9072
            ),
        ),
    ),

    // 第五名
    'PKQD5' => array(
        'name'      => '第五名复式',
        'group'     => 'CQ5',
        'row'       => 'diwuming',
        'method'    => 'PKQD5',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('5'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 第五名单式
    'PKQD5_S' => array(
        'name'      => '第五名单式',
        'group'     => 'CQ5',
        'row'       => 'diwuming',
        'method'    => 'PKQD5_S',
        'total'     => 10,
        'levels' => array(
            '1' => array(
                'position'  => array('5'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 猜前五 - 复式
    'PKQZX5' => array(
        'name'      => '猜前五复式',
        'group'     => 'CQ5',
        'row'       => 'caiqianwu',
        'method'    => 'PKQZX5',
        "total"     => 30240,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 1,
                "prize"     => 54432
            ),
        ),
    ),

    // 猜前五 - 单式
    'PKQZX5_S' => array(
        'name'      => '猜前五单式',
        'group'     => 'CQ5',
        'row'       => 'caiqianwu',
        'method'    => 'PKQZX5_S',
        "total"     => 30240,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 1,
                "prize"     => 54432
            ),
        ),
    ),


    // 定位胆 -  DWD 总控
    'PKDWD' => array(
        'name'      => '定位胆',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'method'    => 'PKDWD',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('1','2','3','4','5','6','7','8','9','10'),
                "count"     => 1,
                "prize"     => 18.00
            ),
        ),
        'expands' => array(
            'name'  => '定位胆_{str}',
            'num'   => 1,
        ),
    ),


    // 定位胆 - 1
    'PKDWD_1' => array(
        'name'      => '定位胆_冠军',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_1',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position' => array('1'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 2
    'PKDWD_2' => array(
        'name'      => '定位胆_亚军',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_2',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('2'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 3
    'PKDWD_3' => array(
        'name'      => '定位胆_第三名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_3',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('3'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 4
    'PKDWD_4'=>array(
        'name'      => '定位胆_第四名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_4',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('4'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 5
    'PKDWD_5' => array(
        'name'      => '定位胆_第五名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_5',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('5'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 6
    'PKDWD_6' => array(
        'name'      => '定位胆_第六名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_6',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('6'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 7
    'PKDWD_7' => array(
        'name'      => '定位胆_第七名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_7',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('7'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 8
    'PKDWD_8' => array(
        'name'      => '定位胆_第八名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_8',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('8'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 9
    'PKDWD_9' => array(
        'name'      => '定位胆_第九名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_9',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('9'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),

    // 定位胆 - 10
    'PKDWD_10' => array(
        'name'      => '定位胆_第十名',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'hidden'    => true,
        'method'    => 'PKDWD_10',
        "total"     => 10,
        'levels'    => array(
            '1' => array(
                'position'  => array('10'),
                "count"     => 1,
                "prize"     => 18
            ),
        ),
    ),
);
