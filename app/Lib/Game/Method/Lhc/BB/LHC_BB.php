<?php namespace App\Lib\Game\Method\Lhc\BB;

use App\Lib\Game\Method\Lhc\Base;

// 半波
class LHC_BB extends Base
{
    public $all_count = 49;
    public static $filterArr = array(
        'RB'    =>   ['29', '30', '34', '35', '40', '45', '46'],                'RS'        =>  ['01', '02', '07', '08', '12', '13', '18', '19', '23', '24'],
        'RO'    =>   ['01', '07', '13', '19', '23', '29', '35', '45'],          'RE'        =>  ['02', '08', '12', '18', '24', '30', '34', '40', '46'],
        'RAO'   =>   ['01', '07', '12', '18', '23', '29', '30', '34', '45'],    'RAE'       =>  ['02', '08', '13', '19', '24', '35', '40', '46'],

        'BB'    =>   ['25', '26', '31', '36', '37', '41', '42', '47', '48'],    'BS'        =>  ['03', '04', '09', '10', '14', '15', '20'],
        'BO'    =>   ['03', '09', '15', '25', '31', '37', '41', '47'],          'BE'        =>  ['04', '10', '14', '20', '26', '36', '42', '48'],
        'BAO'   =>   ['03', '09', '10', '14', '25', '36', '41', '47'],          'BAE'       =>  ['04', '15', '20', '26', '31', '37', '42', '48'],

        'GB'    =>   ['27', '28', '32', '33', '38', '39', '43', '44'],          'GS'        =>  ['05', '06', '11', '16', '17', '21', '22'],
        'GO'    =>   ['05', '11', '17', '21', '27', '33', '39', '43'],          'GE'        =>  ['06', '16', '22', '28', '32', '38', '44'],
        'GAO'   =>   ['05', '16', '21', '27', '32', '38', '43'],                'GAE'       =>  ['06', '11', '17', '22', '28', '33', '39', '44'],
    );

    // 奖级对应的玩法
    public static $levelToMethod = array(
        1   => 'RB',        2   => 'RS',
        3   => 'RO',        4   => 'RE',
        5   => 'RAO',       6   => 'RAE',

        7   => 'BB',         8  => 'BS',
        9   => 'BO',        10  => 'BE',
        11  => 'BAO',       12  => 'BAE',

        13  => 'GB',        14  => 'GS',
        15  => 'GO',        16  => 'GE',
        17  => 'GAO',       18  => 'GAE',
    );

    // 汉字转
    public static $transfer = array(
        'RB'    => "红大",
        'RS'    => "红小",
        'RO'    => "红单",
        'RE'    => "红双",
        'RAO'   => "红合单",
        'RAE'   => "红合双",

        'BB'    => "蓝大",
        'BS'    => "蓝小",
        'BO'    => "蓝单",
        'BE'    => "蓝双",
        'BAO'   => "蓝合单",
        'BAE'   => "蓝合双",

        'GB'    => "绿大",
        'GS'    => "绿小",
        'GO'    => "绿单",
        'GE'    => "绿双",
        'GAO'   => "绿合单",
        'GAE'   => "绿合双",
    );


    // 是否复式
    public function isMulti()
    {
        return true;
    }

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        return implode('', (array)array_rand(self::$filterArr, 1));
    }

    public function parse64($codes)
    {
        return true;
    }

    public function encode64($codes)
    {
        return $this->_encode64(explode(',', $codes));
    }

    // 格式解析
    public function codeChange($code)
    {
        return self::$transfer[$code];
    }

    /**
     * 检测是否合合法的代码
     * @param $sCode
     * @return bool
     */
    public function regexp($sCode)
    {
        if (!isset(self::$filterArr[$sCode])) {
            return false;
        }

        return true;
    }

    /**
     * 计算注数
     * @param $sCodes
     * @return int
     */
    public function count($sCodes)
    {
        return 1;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCode, Array $openCodes)
    {
        $tCode      = $openCodes[0];

        // 和局
        if ($tCode == 49) {
            return 88888888;
        }

        $methodSign = self::$levelToMethod[$levelId];

        // 当前奖级对应的号码和投注号码不一样
        if ($methodSign != $sCode) {
            return 0;
        }

        $methodCodes = self::$filterArr[$methodSign];
        // 奖级对应玩法包含的号码　被开了出来
        if ($methodCodes && in_array($tCode, $methodCodes)) {
            return 1;
        }

        return 0;
    }
}
