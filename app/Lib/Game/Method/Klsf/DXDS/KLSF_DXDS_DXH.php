<?php namespace App\Lib\Game\Method\Klsf\DXDS;

use App\Lib\Game\Method\Klsf\Base;

// 大小和
class KLSF_DXDS_DXH extends Base
{
    public $allCount = 20;

    public static $methods = [
        'b'     => "大",
        's'     => "小",
        'h'     => "和",
    ];

    public static $level = [
        'b'     => 1,
        's'     => 1,
        'h'     => 2,
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
        $totalCount = 0;
        foreach ($aOpenNumber as $_num) {
            $num = self::$codeTransfer[$_num];
            $totalCount += $num;
        }

        if ($totalCount > 84) {
            $winCode = "b";
        } else if ($totalCount < 84) {
            $winCode = "s";
        } else {
            $winCode = "h";
        }

        $codeArr = explode('&', $sCodes);

        foreach ($codeArr as $code) {
            if ($levelId == 1 && in_array($code, ['b', 's']) && $winCode == $code) {
                return 1;
            }

            if ($levelId == 2 && $code == 'h' && $winCode == $code) {
                return 1;
            }
        }

        return 0;
    }
}
