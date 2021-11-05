<?php namespace App\Lib\Game\Method\Lhc\WS;

use App\Lib\Game\Method\Lhc\Base;

// 半波
class LHC_WS extends Base
{
    public $allCount = 1;
    public static $filterArr = array(
        0 => [10 => 1, 20 => 1, 30 => 1, 40 => 1],
        1 => [1 => 1, 11 => 1, 21 => 1, 31 => 1, 41 => 1],
        2 => [2 => 1, 12 => 1, 22 => 1, 32 => 1, 42 => 1],
        3 => [3 => 1, 13 => 1, 23 => 1, 33 => 1, 43 => 1],
        4 => [4 => 1, 14 => 1, 24 => 1, 34 => 1, 44 => 1],
        5 => [5 => 1, 15 => 1, 25 => 1, 35 => 1, 45 => 1],
        6 => [6 => 1, 16 => 1, 26 => 1, 36 => 1, 46 => 1],
        7 => [7 => 1, 17 => 1, 27 => 1, 37 => 1, 47 => 1],
        8 => [8 => 1, 18 => 1, 28 => 1, 38 => 1, 48 => 1],
        9 => [9 => 1, 19 => 1, 29 => 1, 39 => 1, 49 => 1],
    );

    public function regexp($sCode)
    {
        if (!isset(self::$filterArr[$sCode])) {
            return false;
        }

        return true;
    }

    public function count($sCode)
    {
        return 1;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCode, Array $numbers)
    {
        if ($levelId == 1) {
            if ($sCode == 0) {
                foreach (self::$filterArr[$sCode] as $_code => $_mark) {
                    if (in_array($_code, $numbers)) {
                        return 1;
                    }
                }
            }
        }

        if ($levelId == 2) {
            if (isset(self::$filterArr[$sCode]) && $sCode != 0) {
                foreach (self::$filterArr[$sCode] as $_code => $_mark) {
                    if (in_array($_code, $numbers)) {
                        return 1;
                    }
                }
            }
        }

        return 0;
    }
}
