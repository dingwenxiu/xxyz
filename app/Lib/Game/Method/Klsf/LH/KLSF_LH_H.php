<?php namespace App\Lib\Game\Method\Klsf\LH;

use App\Lib\Game\Method\Klsf\Base;

// 龙虎 - 虎
class KLSF_LH_H extends Base
{
    public $allCount = 20;

    public static $methods = [
        '12'     => "1V2",
        '13'     => "1V3",
        '14'     => "1V4",
        '15'     => "1V5",
        '16'     => "1V6",
        '17'     => "1V7",
        '18'     => "1V8",

        '23'     => "2V3",
        '24'     => "2V4",
        '25'     => "2V5",
        '26'     => "2V6",
        '27'     => "2V7",
        '28'     => "2V8",

        '34'     => "3V4",
        '35'     => "3V5",
        '36'     => "3V6",
        '37'     => "3V7",
        '38'     => "3V8",

        '45'     => "4V5",
        '46'     => "4V6",
        '47'     => "4V7",
        '48'     => "4V8",

        '56'     => "5V6",
        '57'     => "5V7",
        '58'     => "5V8",

        '67'     => "6V7",
        '68'     => "6V8",

        '78'     => "7V8",
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

        $aCodeArr   = explode("&", $sCodes);

        $totalCount = 0;

        foreach ($aCodeArr as $code) {
            $_codeSplit = str_split($code);
            $number1 = $aOpenNumber[$_codeSplit[0] - 1];
            $number2 = $aOpenNumber[$_codeSplit[1] - 1];

            if ($number1 < $number2) {
                $totalCount += 1;
            }
        }

        return $totalCount;
    }
}
