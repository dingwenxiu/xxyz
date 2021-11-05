<?php namespace App\Lib\Game\Method\Pcdd\BO;

use App\Lib\Game\Method\Pcdd\Base;

// 波
class BO extends Base
{
    public $totalCount  = 1000;

    public static $methods = [
        'red'       => "红波",
        'green'     => "绿波",
        'blue'      => "蓝波",
    ];

    public static $codes = [
        'green'     => [1 => 1, 4 => 1, 7 => 1, 10 => 1, 16 => 1, 19 => 1, 22 => 1, 25 => 1],
        'blue'      => [2 => 1, 5 => 1, 8 => 1, 11 => 1, 17 => 1, 20 => 1, 23 => 1, 25 => 1],
        'red'       => [3 => 1, 6 => 1, 9 => 1, 12 => 1, 15 => 1, 18 => 1, 21 => 1, 24 => 1],
    ];

    public static $heCodes = [0 => 1, 13 => 1, 14 => 1, 27 => 1];

    public static $level = [
        'red'       => 1,
        'blue'      => 2,
        'green'     => 3,
    ];

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$methods);
    }

    public function regexp($sCode)
    {
        if (isset(self::$methods[$sCode])) {
            return true;
        }
        return false;
    }

    public function count($sCodes)
    {
        return 1;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCode, Array $aOpenNumber)
    {
        $count = $aOpenNumber[0] + $aOpenNumber[1] + $aOpenNumber[2];
        switch ($sCode) {
            case 'red':
                if (isset(self::$codes['red'][$count]) && $levelId == self::$level['red']) {
                    return 1;
                }
                break;
            case 'green':
                if (isset(self::$codes['green'][$count]) && $levelId == self::$level['green']) {
                    return 1;
                }
                break;
            case 'blue':
                if (isset(self::$codes['blue'][$count]) && $levelId == self::$level['blue']) {
                    return 1;
                }
                break;
            default:
        }


        // 是否是和局模式
        if (isset(self::$heCodes[$count])) {
            return 88888888;
        }

        return 0;
    }
}
