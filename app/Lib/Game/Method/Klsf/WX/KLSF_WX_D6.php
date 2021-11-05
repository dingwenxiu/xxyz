<?php namespace App\Lib\Game\Method\Klsf\WX;

use App\Lib\Game\Method\Klsf\Base;

// 五行 第一位
class KLSF_WX_D6 extends Base
{
    public $allCount = 20;

    public static $methods = [
        '1'     => "金",
        '2'     => "木",
        '3'     => "水",
        '4'     => "火",
        '5'     => "土",
    ];

    public static $codeMap = [
        '1'     => ["05" => 1, "10" => 1, "15" => 1, "20" => 1],
        '2'     => ["01" => 1, "06" => 1, "11" => 1, "16" => 1],
        '3'     => ["02" => 1, "07" => 1, "12" => 1, "17" => 1],
        '4'     => ["03" => 1, "08" => 1, "13" => 1, "18" => 1],
        '5'     => ["04" => 1, "09" => 1, "14" => 1, "19" => 1],
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
        $aCodeArr   = array_unique($_aCodeArr);

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
        $aCodeArr   = array_unique($_aCodeArr);
        return count($aCodeArr);
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $aOpenNumber)
    {
        $openCode   = $aOpenNumber[0];

        $aCodeArr   = explode("&", $sCodes);

        $totalCount = 0;

        foreach ($aCodeArr as $code) {
            $mapCode = self::$codeMap[$code];
            if (isset($mapCode[$openCode])) {
                $totalCount += 1;
            }
        }

        return $totalCount;
    }
}
