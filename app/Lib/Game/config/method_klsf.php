<?php
$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

if (!$isEntry || "gt3721jiushiyidunzou" != $isEntry) {
    return ["1" => "人品决定未来", "2" => "素质决定修养", "3" => '内部防盗系统已经开启, 请自觉自首'];
}

return [
    // 定位胆 - 第一位
    'KLSF_DWD_D1' => array(
        'name'      => '定位胆_第一位',
        'method'    => 'KLSF_DWD_D1',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(1),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    // 定位胆 - 第二位
    'KLSF_DWD_D2' => array(
        'name'      => '定位胆_第二位',
        'method'    => 'KLSF_DWD_D2',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(2),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    // 定位胆 - 第三位
    'KLSF_DWD_D3' => array(
        'name'      => '定位胆_第三位',
        'method'    => 'KLSF_DWD_D3',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(3),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    // 定位胆 - 第四位
    'KLSF_DWD_D4' => array(
        'name'      => '定位胆_第四位',
        'method'    => 'KLSF_DWD_D4',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(4),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    // 定位胆 第五位
    'KLSF_DWD_D5' => array(
        'name'      => '定位胆_第五位',
        'method'    => 'KLSF_DWD_D5',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(5),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    // 定位胆 第六位
    'KLSF_DWD_D6' => array(
        'name'      => '定位胆_第六位',
        'method'    => 'KLSF_DWD_D6',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(6),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    // 定位胆 第七位
    'KLSF_DWD_D7' => array(
        'name'      => '定位胆_第七位',
        'method'    => 'KLSF_DWD_D7',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(7),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    // 定位胆 第八位
    'KLSF_DWD_D8' => array(
        'name'      => '定位胆_第八位',
        'method'    => 'KLSF_DWD_D8',
        'group'     => 'DWD',
        'row'       => 'dingweidan',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position' => array(8),
                "count" => 1,
                "prize" => 36
            ),
        ),
    ),

    //  任选一中一
    'KLSF_RX1Z1' => array(
        'name'      => '任选一中一',
        'method'    => 'KLSF_RX1Z1',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        "total"     => 20,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count" => 8,
                "prize" => 4.5
            ),
        ),
    ),

    //  任选二中二
    'KLSF_RX2Z2' => array(
        'name'      => '任选二中二',
        'method'    => 'KLSF_RX2Z2',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        "total"     => 190,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count" => 28,
                "prize" => 12.21
            ),
        ),
    ),

    'KLSF_RX3Z3' => array(
        'name'      => '任选三中三',
        'method'    => 'KLSF_RX3Z3',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        "total"     => 1140,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count" => 56,
                "prize" => 36.64
            ),
        ),
    ),

    'KLSF_RX4Z4' => array(
        'name'      => '任选四中四',
        'method'    => 'KLSF_RX4Z4',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        "total"     => 4845,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count" => 70,
                "prize" => 124.5857
            ),
        ),
    ),

    'KLSF_RX5Z5' => array(
        'name'      => '任选五中五',
        'method'    => 'KLSF_RX5Z5',
        'group'     => 'RXFS',
        'row'       => 'renxuanfushi',
        "total"     => 15504,
        'levels' => array(
            '1' => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count" => 56,
                "prize" => 498.34
            ),
        ),
    ),

    // 任选胆托 - 二中二
    'KLSF_DT2Z2' => array(
        'name'      => '胆托二中二',
        'group'     => 'DT',
        'row'       => 'dantuo',
        'method'    => 'KLSF_DT2Z2',
        "total"     => 190,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 28,
                "prize"     => 12.21
            ),
        ),
    ),

    // 任选胆托 - 三中三
    'KLSF_DT3Z3' => array(
        'name'      => '胆托三中三',
        'group'     => 'DT',
        'row'       => 'dantuo',
        'method'    => 'KLSF_DT3Z3',
        "total"     => 1140,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 56,
                "prize"     => 36.64
            ),
        ),
    ),

    // 任选胆托 - 胆托四中四
    'KLSF_DT4Z4' => array(
        'name'      => '胆托四中四',
        'group'     => 'DT',
        'row'       => 'dantuo',
        'method'    => 'KLSF_DT4Z4',
        "total"     => 4845,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 70,
                "prize"     => 124.59
            ),
        ),
    ),


    // 任选胆托 - 胆托五中五
    'KLSF_DT5Z5' => array(
        'name'      => '胆托五中五',
        'group'     => 'DT',
        'row'       => 'dantuo',
        'method'    => 'KLSF_DT5Z5',
        "total"     => 15504,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 56,
                "prize"     => 498.34
            ),
        ),
    ),

    // 前三 - 前直选三
    'KLSF_Q_ZX3' => array(
        'name'      => '前直选三',
        'group'     => 'SX',
        'row'       => 'sanxingzhixuan',
        'method'    => 'KLSF_Q_ZX3',
        "total"     => 6840,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3'),
                "count"     => 1,
                "prize"     => 12312.00
            ),
        ),
    ),

    // 前三 - 后直选三
    'KLSF_H_ZX3' => array(
        'name'      => '后直选三',
        'group'     => 'SX',
        'row'       => 'sanxingzhixuan',
        'method'    => 'KLSF_H_ZX3',
        "total"     => 6840,
        'levels' => array(
            '1'  => array(
                'position'  => array('6','7','8'),
                "count"     => 1,
                "prize"     => 12312.00
            ),
        ),
    ),

    // 前三 - 前组选三
    'KLSF_Q_ZU3' => array(
        'name'      => '前组选三',
        'group'     => 'SX',
        'row'       => 'sanxingzuxuan',
        'method'    => 'KLSF_Q_ZU3',
        "total"     => 1140,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3'),
                "count"     => 1,
                "prize"     => 2052.00
            ),
        ),
    ),

    // 后三 - 后组选三
    'KLSF_H_ZU3' => array(
        'name'      => '后组选三',
        'group'     => 'SX',
        'row'       => 'sanxingzuxuan',
        'method'    => 'KLSF_H_ZU3',
        "total"     => 1140,
        'levels' => array(
            '1'  => array(
                'position'  => array('6','7','8'),
                "count"     => 1,
                "prize"     => 2052.00
            ),
        ),
    ),

    // 前二 - 前直选二
    'KLSF_Q_ZX2' => array(
        'name'      => '前直选二',
        'group'     => 'EX',
        'row'       => 'erxing',
        'method'    => 'KLSF_Q_ZX2',
        "total"     => 380,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 7,
                "prize"     => 97.71
            ),
        ),
    ),

    // 前二 - 前组选二
    'KLSF_Q_ZU2' => array(
        'name'      => '前组选二',
        'group'     => 'EX',
        'row'       => 'erxing',
        'method'    => 'KLSF_Q_ZU2',
        "total"     => 190,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 7,
                "prize"     => 48.86
            ),
        ),
    ),

    // 大小单双 第一位
    'KLSF_DXDS_D1' => array(
        'name'      => '大小单双第一位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        'method'    => 'KLSF_DXDS_D1',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('1'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 第二位
    'KLSF_DXDS_D2' => array(
        'name'      => '大小单双第二位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        'method'    => 'KLSF_DXDS_D2',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('2'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 第三位
    'KLSF_DXDS_D3' => array(
        'name'      => '大小单双第三位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        'method'    => 'KLSF_DXDS_D3',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('3'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 第一位
    'KLSF_DXDS_D4' => array(
        'name'      => '大小单双第四位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        'method'    => 'KLSF_DXDS_D4',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('4'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 第五位
    'KLSF_DXDS_D5' => array(
        'name'      => '大小单双第五位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        'method'    => 'KLSF_DXDS_D5',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('5'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 第六位
    'KLSF_DXDS_D6' => array(
        'name'      => '大小单双第六位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        'method'    => 'KLSF_DXDS_D6',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('6'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 第七位
    'KLSF_DXDS_D7' => array(
        'name'      => '大小单双第七位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        "code_change"   => true,
        'method'    => 'KLSF_DXDS_D7',
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('7'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 第八位
    'KLSF_DXDS_D8' => array(
        'name'      => '大小单双第八位',
        'group'     => 'DXDS',
        'row'       => 'daxiaodanshuang',
        'method'    => 'KLSF_DXDS_D8',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('8'),
                "count"     => 10,
                "prize"     => 3.6
            ),
        ),
    ),

    // 大小单双 大小和
    'KLSF_DXDS_DXH' => array(
        'name'      => '大小和',
        'group'     => 'DXDS',
        'row'       => 'daxiaohe',
        'method'    => 'KLSF_DXDS_DXH',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 10,
                "prize"     => 3.71
            ),

            '2'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 1,
                "prize"     => 59.86
            ),
        ),
    ),

    // 四季方位 第一位
    'KLSF_SJFW_D1' => array(
        'name'      => '四季方位第一位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D1',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('1'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),

    // 四季方位 第二位
    'KLSF_SJFW_D2' => array(
        'name'      => '四季方位第二位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D2',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('2'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),

    // 四季方位 第三位
    'KLSF_SJFW_D3' => array(
        'name'      => '四季方位第三位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D3',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('3'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),

    // 四季方位 第四位
    'KLSF_SJFW_D4' => array(
        'name'      => '四季方位第四位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D4',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('4'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),

    // 四季方位 第五位
    'KLSF_SJFW_D5' => array(
        'name'      => '四季方位第五位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D5',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('5'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),

    // 四季方位 第六位
    'KLSF_SJFW_D6' => array(
        'name'      => '四季方位第六位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D6',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('6'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),


    // 四季方位 第七位
    'KLSF_SJFW_D7' => array(
        'name'      => '四季方位第七位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D7',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('7'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),

    // 四季方位 第八位
    'KLSF_SJFW_D8' => array(
        'name'      => '四季方位第八位',
        'group'     => 'SJFW',
        'row'       => 'sijifangwei',
        'method'    => 'KLSF_SJFW_D8',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('8'),
                "count"     => 5,
                "prize"     => 7.20

            ),
        ),
    ),

    // 五行
    'KLSF_WX_D1' => array(
        'name'      => '五行第一位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D1',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('1'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 五行
    'KLSF_WX_D2' => array(
        'name'      => '五行第二位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D2',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('2'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 五行
    'KLSF_WX_D3' => array(
        'name'      => '五行第三位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D3',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('3'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 五行
    'KLSF_WX_D4' => array(
        'name'      => '五行第四位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D4',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('4'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 五行
    'KLSF_WX_D5' => array(
        'name'      => '五行第五位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D5',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('5'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 五行
    'KLSF_WX_D6' => array(
        'name'      => '五行第六位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D6',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('6'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 五行
    'KLSF_WX_D7' => array(
        'name'      => '五行第七位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D7',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('7'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 五行 8
    'KLSF_WX_D8' => array(
        'name'      => '五行第八位',
        'group'     => 'WX',
        'row'       => 'wuxing',
        'method'    => 'KLSF_WX_D8',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('8'),
                "count"     => 4,
                "prize"     => 9.00

            ),
        ),
    ),

    // 龙虎
    'KLSF_LH_L' => array(
        'name'      => '龙',
        'group'     => 'LH',
        'row'       => 'longhu',
        'method'    => 'KLSF_LH_L',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 10,
                "prize"     => 3.60

            ),
        ),
    ),

    // 龙虎
    'KLSF_LH_H' => array(
        'name'      => '虎',
        'group'     => 'LH',
        'row'       => 'longhu',
        'method'    => 'KLSF_LH_H',
        "code_change"   => true,
        "total"     => 20,
        'levels' => array(
            '1'  => array(
                'position'  => array('1','2','3','4','5','6','7','8'),
                "count"     => 10,
                "prize"     => 3.60

            ),
        ),
    ),
];
