<?php namespace App\Lib\Game\Method\Lhc\ZF;

use App\Lib\Game\Method\Lhc\Base;

// 总分
class LHC_ZF extends Base
{
    public $allCount = 8;
    public static $filterArr = array(
        "b" => "大",
        "s" => "小",
        "o" => "单",
        "e" => "双",

        "bo" => "大单",
        "be" => "大双",
        "so" => "小单",
        "se" => "小双",
    );

    // 格式解析
    public function codeChange($code)
    {
        return self::$filterArr[$code];
    }

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
        $sum = array_sum($numbers);

        $bs     = $sum >= 175 ? "b" : "s";
        $oe     = $sum % 2 == 1 ? "o" : "e";

        $bsoe = $bs . $oe;

        if ($levelId == 1) {
            if (in_array($sCode, ["b", "s", "o", "e"])) {



                if ($sCode == $bs || $sCode == $oe) {
                    return 1;
                }
            }
        }

        if ($levelId == 2) {
            if (in_array($sCode, ["bo", "be", "so", "se"])) {
                if ($sCode == $bsoe) {
                    return 1;
                }
            }
        }

        return 0;
    }
}
