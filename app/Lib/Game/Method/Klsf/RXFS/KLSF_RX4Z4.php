<?php namespace App\Lib\Game\Method\Klsf\RXFS;

use App\Lib\Game\Method\Klsf\Base;

// 任选四中四
class KLSF_RX4Z4 extends Base
{
    public static $filterArr = [
        "01" => 1,
        "02" => 1,
        "03" => 1,
        "04" => 1,
        "05" => 1,
        "06" => 1,
        "07" => 1,
        "08" => 1,
        "09" => 1,
        "10" => 1,
        "11" => 1,
        "12" => 1,
        "13" => 1,
        "14" => 1,
        "15" => 1,
        "16" => 1,
        "17" => 1,
        "18" => 1,
        "19" => 1,
        "20" => 1,
    ];

    public function regexp($sCodes) {

        $_aCodeArr  = explode("&", $sCodes);
        $aCodeArr   = array_unique($_aCodeArr, SORT_STRING);

        if (count($_aCodeArr) != count($aCodeArr)) {
            return false;
        }

        if (count($aCodeArr) < 4) {
            return false;
        }

        foreach ($aCodeArr as $code) {
            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        return $this->getCombinCount(count(array_unique(explode("&", $sCodes), SORT_STRING)),4);
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $len    = 4;
        $aCodes = explode('&', $sCodes);
        $iRates = count(array_intersect($aCodes, $numbers));
        if ($iRates < $len) {
            return 0;
        }

        return $this->GetCombinCount($iRates, $len);
    }
}
