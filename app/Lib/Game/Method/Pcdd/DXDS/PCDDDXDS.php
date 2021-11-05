<?php namespace App\Lib\Game\Method\Pcdd\DXDS;

use App\Lib\Game\Method\Pcdd\Base;

// 大
class PCDDDXDS extends Base
{
    public $all_count = 1000;

    public static $methods = [
        'b'     => "大",
        's'     => "小",
        'o'     => "单",
        'e'     => "双",
        'bo'    => "大单",
        'be'    => "大双",
        'so'    => "小单",
        'se'    => "小双",
        'sb'    => "极大",
        'ss'    => "极小",
    ];

    public static $level = [
        'b'     => 1,
        's'     => 2,
        'o'     => 3,
        'e'     => 4,
        'bo'    => 5,
        'be'    => 6,
        'so'    => 7,
        'se'    => 8,
        'sb'    => 9,
        'ss'    => 10,
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        return ["b"];
    }

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$methods);
    }

    /**
     * 判定格式
     * @param $sCode
     * @return bool
     */
    public function regexp($sCode)
    {
        if (!isset(self::$methods[$sCode])) {
            return false;
        }

        return true;
    }

    public function count($sCodes)
    {
        return 1;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCode, Array $aOpenNumber)
    {
        $count = $aOpenNumber[0] + $aOpenNumber[1] + $aOpenNumber[2];

        $_level = self::$level[$sCode];
        if ($levelId == $_level) {
            switch ($sCode) {
                case 'b':
                    return $levelId == 1 && $count >= 14 ? 1 : 0;
                    break;
                case 's':
                    return  $levelId == 2 && $count < 14 ? 1 : 0;
                    break;
                case 'o':
                    return  $levelId == 3 && $count % 2 > 0 ? 1 : 0;
                    break;
                case 'e':
                    return  $levelId == 4 && $count % 2 == 0 ? 1 : 0;
                    break;

                case 'bo':
                    return  $levelId == 5 && $count >= 14 && $count % 2 > 0 ? 1 : 0;
                    break;
                case 'be':
                    return  $levelId == 6 && $count >= 14 && $count % 2 == 0 ? 1 : 0;
                    break;
                case 'so':
                    return  $levelId == 7 && $count < 14 && $count % 2 > 0 ? 1 : 0;
                    break;
                case 'se':
                    return  $levelId == 8 && $count < 14 && $count % 2 == 0 ? 1 : 0;
                    break;

                case 'sb':
                    return  $levelId == 9 && $count >= 22 ? 1 : 0;
                    break;
                case 'ss':
                    return $levelId == 10 &&  $count <= 5 ? 1 : 0;
                    break;
            }
        }
        return 0;
    }
}
