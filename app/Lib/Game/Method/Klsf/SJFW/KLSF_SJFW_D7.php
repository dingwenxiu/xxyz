<?php namespace App\Lib\Game\Method\Klsf\SJFW;

use App\Lib\Game\Method\Klsf\Base;

// 四级方位
class KLSF_SJFW_D7 extends Base
{
    public $allCount = 20;

    public static $methods = [
        '1'     => "春",
        '2'     => "夏",
        '3'     => "秋",
        '4'     => "冬",
        '5'     => "东",
        '6'     => "南",
        '7'     => "西",
        '8'     => "北",
    ];

    public static $codeMap = [
        '1'     => ["01" => 1, "02" => 1, "03" => 1, "04" => 1, "05" => 1],
        '2'     => ["06" => 1, "07" => 1, "08" => 1, "09" => 1, "10" => 1],
        '3'     => ["11" => 1, "12" => 1, "13" => 1, "14" => 1, "15" => 1],
        '4'     => ["16" => 1, "17" => 1, "18" => 1, "19" => 1, "20" => 1],
        '5'     => ["01" => 1, "05" => 1, "09" => 1, "13" => 1, "17" => 1],
        '6'     => ["02" => 1, "06" => 1, "10" => 1, "14" => 1, "18" => 1],
        '7'     => ["03" => 1, "05" => 1, "11" => 1, "15" => 1, "19" => 1],
        '8'     => ["04" => 1, "08" => 1, "12" => 1, "16" => 1, "20" => 1],
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
