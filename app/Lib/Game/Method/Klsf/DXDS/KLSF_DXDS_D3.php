<?php namespace App\Lib\Game\Method\Klsf\DXDS;

use App\Lib\Game\Method\Klsf\Base;

// 大
class KLSF_DXDS_D3 extends Base
{
    public $allCount = 20;

    public static $methods = [
        'b'     => "大",
        's'     => "小",
        'o'     => "单",
        'e'     => "双",
        'wb'    => "尾大",
        'ws'    => "尾小",
        'ho'    => "和单",
        'he'    => "和双",
    ];

    public static $level = [
        'b'     => 1,
        's'     => 2,
        'o'     => 3,
        'e'     => 4,
        'wb'    => 5,
        'ws'    => 6,
        'ho'    => 7,
        'he'    => 8,
    ];

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$methods);
    }

    /**
     * 判定格式
     * @param $sCodes
     * @return bool
     */
    public function regexp($sCodes)
    {
        $_aCodeArr  = explode("&", $sCodes);
        $aCodeArr   = array_unique($_aCodeArr, SORT_STRING);

        if (count($_aCodeArr) != count($aCodeArr)) {
            return false;
        }

        foreach ($aCodeArr as $code) {
            if (!isset(self::$methods[$code])) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        $_aCodeArr  = explode("&", $sCodes);
        $aCodeArr   = array_unique($_aCodeArr, SORT_STRING);
        return count($aCodeArr);
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $aOpenNumber)
    {
        $count      = $aOpenNumber[0];
        $countArr   = str_split($count);

        $wNumber    = $countArr[1];
        $hNumber    = $countArr[0] + $countArr[1];
        $count      = self::$codeTransfer[$count];

        // 找到可以中奖号码
        $canWinCode = [];

        $canWinCode[] = $count > 10 ? "b" : "s";
        $canWinCode[] = $count % 2 > 0 ? "o" : "e";

        $canWinCode[] = $wNumber > 4 ? "wb" : "ws";
        $canWinCode[] = $hNumber % 2 > 0 ? "ho" : "he";

        // 展开号码
        $_aCodeArr  = explode("&", $sCodes);
        $aCodeArr   = array_unique($_aCodeArr, SORT_STRING);

        $totalCount = 0;
        foreach ($aCodeArr as $code) {
            if (in_array($code, $canWinCode)) {
                $totalCount += 1;
            }
        }

        return $totalCount;
    }
}
