<?php

$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

// 2 / count / total * 1800 / 1980
return array(
    // 三星 - 前三 - 直选3
    'LTQ3ZX3' => array(
        'name'      => '前三直选复式',
        'group'     => 'SM',
        'row'       => 'zhixuan',
        'method'    => 'LTQ3ZX3',
        'total'     => 990,
        'levels'    => array(
            '1'     => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 1800.00
            ),
        ),
    ),

    // 三星 - 前三 - 直选3 - 单式
    'LTQ3ZX3_S' => array(
        'name'      => '前三直选单式',
        'group'     => 'SM',
        'row'       => 'zhixuan',
        'method'    => 'LTQ3ZX3_S',
        'total'     => 990,
        'levels' => array(
            '1'=>array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 1800.00
            ),
        ),
    ),

    // 三星 - 前三 - 组选 - 复式
    'LTQ3ZU3' => array(
        'name'      => '前三组选复式',
        'group'     => 'SM',
        'row'       => 'zuxuan',
        'method'    => 'LTQ3ZU3',
        'total'     => 165,
        'levels' => array(
            '1' => array(
                'position'  => array('1', '2', '3'),
                "count"     => 1,
                "prize"     => 300.00
            ),
        ),
    ),

    // 三星 - 前三 - 组选 - 单式
    'LTQ3ZU3_S' => array(
        'name'      => '前三组选单式',
        'group'     => 'SM',
        'row'       => 'zuxuan',
        'method'    => 'LTQ3ZU3_S',
        'total'     => 165,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3'),
                "count"     => 1,
                "prize"     => 300.00
            ),
        ),
    ),

    // 三星 - 前三 - 组选 - 胆拖
    'LTQ3ZU3DT' => array(
        'name'      => '前三组选胆拖',
        'group'     => 'SM',
        'row'       => 'zuxuan',
        'method'    => 'LTQ3ZU3DT',
        'total'     => 165,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3'),
                "count"     => 1,
                "prize"     => 300.00
            ),
        ),
    ),

    // 前二星 - 直选- 复试 2 / count / total * 1800 / 1980
    'LTQ2ZX2' => array(
        'name'      => '前二直选复式',
        'group'     => 'EM',
        'row'       => 'zhixuan',
        'method'    => 'LTQ2ZX2',
        'total'     => 110,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2'),
                "count"     => 1,
                "prize"     => 200.00
            ),
        ),
    ),

    // 前二星 - 直选- 单式
    'LTQ2ZX2_S' => array(
        'name'      => '前二直选单式',
        'group'     => 'EM',
        'row'       => 'zhixuan',
        'method'    => 'LTQ2ZX2_S',
        'total'     => 110,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2'),
                "count"     => 1,
                "prize"     => 200.00
            ),
        ),
    ),

    // 前二星 - 组选- 复式
    'LTQ2ZU2' => array(
        'name'      => '前二组选复式',
        'group'     => 'EM',
        'row'       => 'zuxuan',
        'method'    => 'LTQ2ZU2',
        'total'     => 55,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2'),
                "count"     => 1,
                "prize"     => 100.00
            ),
        ),
    ),

    // 前二星 - 组选- 单式
    'LTQ2ZU2_S' => array(
        'name'      => '前二组选单式',
        'group'     => 'EM',
        'row'       => 'zuxuan',
        'method'    => 'LTQ2ZU2_S',
        'total'     => 55,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2'),
                "count"     => 1,
                "prize"     => 100.00
            ),
        ),
    ),

    // 前二星 - 组选- 胆拖
    'LTQ2DTZU2' => array(
        'name'      => '前二组选胆拖',
        'group'     => 'EM',
        'row'       => 'zuxuan',
        'method'    => 'LTQ2DTZU2',
        'total'     => 55,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2'),
                "count"     => 1,
                "prize"     => 100.00
            ),
        ),
    ),

    //不定位 - 前三
    'LTBDW' => array(
        'name'      => '不定位',
        'group'     => 'BDW',
        'row'       => 'budingwei',
        'method'    => 'LTBDW',
        "total"     => 11,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3'),
                "count"     => 3,
                "prize"     => 6.67
            ),
        ),
    ),

    // 定位胆 - 1
    'LTDWD_1' => array(
        'name'      => '定位胆第一位',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'method'    => 'LTDWD_1',
        "total"     => 11,
        'levels' => array(
            '1' => array(
                'position'  => array('1'),
                "count"     => 1,
                "prize"     => 20.00
            ),
        ),
    ),

    // 定位胆 - 2
    'LTDWD_2' => array(
        'name'      => '定位胆第二位',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'method'    => 'LTDWD_2',
        "total"     => 11,
        'levels' => array(
            '1' => array(
                'position'  => array('2'),
                "count"     => 1,
                "prize"     => 20.00
            ),
        ),
    ),

    // 定位胆 - 3
    'LTDWD_3' => array(
        'name'      => '定位胆第三位',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        'method'    => 'LTDWD_3',
        "total"     => 11,
        'levels' => array(
            '1' => array(
                'position'  => array('3'),
                "count"     => 1,
                "prize"     => 20.00
            ),
        ),
    ),

    // 趣味 - 定单双
    'LTDDS' => array(
        'name'          => '定单双',
        'group'         => 'QW',
        'row'           => 'quwei',
        'method'        => 'LTDDS',
        "code_change"   => true,
        "total"         => 462,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '0单5双',
                "count"     => 1,
                "prize"     => 840.00
            ),
            '2' => array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '5单0双',
                "count"     => 6,
                "prize"     => 140.00
            ),
            '3' => array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '1单4双',
                "count"     => 30,
                "prize"     => 28
            ),
            '4' => array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '4单1双',
                "count"     => 75,
                "prize"     => 11.20
            ),
            '5' => array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '2单3双',
                "count"     => 150,
                "prize"     => 5.6
            ),
            '6'=>array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '3单2双',
                "count"     => 200,
                "prize"     => 4.2
            ),
        ),
    ),

    // 猜中位
    'LTCZW' => array(
        'name'      => '猜中位',
        'group'     => 'QW',
        'row'       => 'quwei',
        'method'    => 'LTCZW',
        "total"     => 462,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '中位:3或9',
                "count"     => 28,
                "prize"     => 30
            ),
            '2' =>  array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '中位:4或8',
                "count"     => 63,
                "prize"     => 13.33
            ),
            '3' =>  array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '中位:5或7',
                "count"     => 90,
                "prize"     => 9.33
            ),
            '4' =>  array(
                'position'  => array('1','2','3','4','5'),
                'levelName' => '中位:6',
                "count"     => 100,
                "prize"     => 8.40
            ),
        ),
    ),

    // 任选
    'LTRX1' => array(
        'name'      => '任选一中一',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX1',
        "total"     => 11,
        'levels'    => array(
            '1' => array(
                'position'   => array('1','2','3','4','5'),
                "count"     => 5,
                "prize"     => 4
            ),
        ),
    ),

    // 任选二中二
    'LTRX2' => array(
        'name'      => '任选二中二',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX2',
        "total"     => 55,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 10,
                "prize"     => 10
            ),
        ),
    ),

    // 任选三中三
    'LTRX3' => array(
        'name'      => '任选三中三',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX3',
        "total"     => 165,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 10,
                "prize"     => 30
            ),
        ),
    ),

    // 任选四中四
    'LTRX4' => array(
        'name'      => '任选四中四',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX4',
        "total"     => 330,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 5,
                "prize"     => 120
            ),
        ),
    ),

    // 任选五中五
    'LTRX5' => array(
        'name'      => '任选五中五',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX5',
        "total"     => 462,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 1,
                "prize"     => 840
            ),
        ),
    ),

    // 任选六中五
    'LTRX6' => array(
        'name' => '任选六中五',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX6',
        "total"     => 462,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 6,
                "prize"     => 140
            ),
        ),
    ),

    // 任选七中五
    'LTRX7' => array(
        'name'      => '任选七中五',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX7',
        "total"     => 330,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 15,
                "prize"     => 40
            ),
        ),
    ),

    // 任选八中五
    'LTRX8' => array(
        'name'      => '任选八中五',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        'method'    => 'LTRX8',
        "total"     => 165,
        'levels'    => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 20,
                "prize"     => 15
            ),
        ),
    ),

    // 任选单式 - 任选一中一
    'LTRX1_S' => array(
        'name'      => '任选一中一',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX1_S',
        "total"     => 11,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 5,
                "prize"     => 4
            ),
        ),
    ),

    // 任选单式 - 任选二中二
    'LTRX2_S' => array(
        'name'      => '任选二中二',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX2_S',
        "total"     => 55,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 10,
                "prize"     => 10
            ),
        ),
    ),

    // 任选单式 - 任选三中三
    'LTRX3_S' => array(
        'name'      => '任选三中三',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX3_S',
        "total"     => 165,
        'levels' => array(
            '1'=>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 10,
                "prize"     => 30
            ),
        ),
    ),

    // 任选单式 - 任选四中四
    'LTRX4_S' => array(
        'name'      => '任选四中四',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX4_S',
        "total"     => 330,
        'levels'    => array(
            '1'     => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 5,
                "prize"     => 120
            ),
        ),
    ),

    // 任选单式 - 任选五中五
    'LTRX5_S' => array(
        'name'      => '任选五中五',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX5_S',
        "total"     => 462,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 1,
                "prize"     => 840
            ),
        ),
    ),

    // 任选单式 - 任选六中五
    'LTRX6_S' => array(
        'name'      => '任选六中五',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX6_S',
        "total"     => 462,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 6,
                "prize"     => 140
            ),
        ),
    ),

    // 任选单式 - 任选七中五
    'LTRX7_S' => array(
        'name'      => '任选七中五',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX7_S',
        "total"     => 330,
        'levels'    => array(
            '1'     =>array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 15,
                "prize"     => 40
            ),
        ),
    ),

    // 任选单式 - 任选八中五
    'LTRX8_S' => array(
        'name'      => '任选八中五',
        'group'     => 'RXDS',
        'row'       => 'renxuandanshi',
        'method'    => 'LTRX8_S',
        "total"     => 165,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 20,
                "prize"     => 15
            ),
        ),
    ),

     // 任选胆托 - 二中二
    'LTRXDT2' => array(
        'name'      => '任选胆托二中二',
        'group'     => 'RXDT',
        'row'       => 'renxuandantuo',
        'method'    => 'LTRXDT2',
        "total"     => 55,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 10,
                "prize"     => 10
            ),
        ),
    ),

    // 任选胆托 - 三中三
    'LTRXDT3' => array(
        'name'      => '任选胆托三中三',
        'group'     => 'RXDT',
        'row'       => 'renxuandantuo',
        'method'    => 'LTRXDT3',
        "total"     => 165,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 10,
                "prize"     => 30
            ),
        ),
    ),

    // 任选胆托 - 四中四
    'LTRXDT4' => array(
        'name'      => '任选胆托四中四',
        'group'     => 'RXDT',
        'row'       => 'renxuandantuo',
        'method'    => 'LTRXDT4',
        "total"     => 330,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 5,
                "prize"     => 120
            ),
        ),
    ),

    // 任选胆托 - 五中五
    'LTRXDT5' => array(
        'name'      => '任选胆托五中五',
        'group'     => 'RXDT',
        'row'       => 'renxuandantuo',
        'method'    => 'LTRXDT5',
        "total"     => 462,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 1,
                "prize"     => 840
            ),
        ),
    ),

    // 任选胆托 - 六中五
    'LTRXDT6' => array(
        'name'      => '任选胆托六中五',
        'group'     => 'RXDT',
        'row'       => 'renxuandantuo',
        'method'    => 'LTRXDT6',
        "total"     => 462,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 6,
                "prize"     => 140
            ),
        ),
    ),

    // 任选胆托 - 七中五
    'LTRXDT7' => array(
        'name'      => '任选胆托七中五',
        'group'     => 'RXDT',
        'row'       => 'renxuandantuo',
        'method'    => 'LTRXDT7',
        "total"     => 330,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 15,
                "prize"     => 40
            ),
        ),
    ),

    // 任选胆托 - 八中五
    'LTRXDT8' => array(
        'name'      => '任选胆托八中五',
        'group'     => 'RXDT',
        'row'       => 'renxuandantuo',
        'method'    => 'LTRXDT8',
        "total"     => 165,
        'levels'    => array(
            '1'     => array(
                'position'  => array('1','2','3','4','5'),
                "count"     => 20,
                "prize"     => 15
            ),
        ),
    ),
);
