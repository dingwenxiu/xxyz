<?php namespace App\Lib\Game\Method\Lhc\BZ;

use App\Lib\Game\Method\Lhc\Base;

// 六合彩 不中 5
class LHCBZ_6 extends Base
{
    public $allCount = 49;
    public static $filterArr = array(
        "01"  => 1,  "02"  => 1,  "03"  => 1,  "04" => 1, "05"  => 1, "06" => 1, "07" => 1, "08" => 1, "09" => 1, "10"  => 1,
        "11"  => 1,  "12"  => 1,  "13"  => 1,  "14" => 1, "15"  => 1, "16" => 1, "17" => 1, "18" => 1, "19" => 1, "20"  => 1,
        "21"  => 1,  "22"  => 1,  "23"  => 1,  "24" => 1, "25"  => 1, "26" => 1, "27" => 1, "28" => 1, "29" => 1, "30"  => 1,
        "31"  => 1,  "32"  => 1,  "33"  => 1,  "34" => 1, "35"  => 1, "36" => 1, "37" => 1, "38" => 1, "39" => 1, "40"  => 1,
        "41"  => 1,  "42"  => 1,  "43"  => 1,  "44" => 1, "45"  => 1, "46" => 1, "47" => 1, "48" => 1, "49" => 1,
    );

    public function parse64($codes)
    {
        return true;
    }

    public function encode64($codes)
    {
        return $this->_encode64(explode(',', $codes));
    }

    public function regexp($sCodes)
    {
        $aCode          = explode("&", $sCodes);
        $aCodeUnique    = array_unique($aCode);

        // 不能有重复
        if (count($aCode) != count($aCodeUnique)) {
            return false;
        }

        // 不能小于6 大于49
        if (count($aCodeUnique) < 6 || count($aCodeUnique) > 49) {
            return false;
        }

        foreach ($aCodeUnique as $_code) {
            if (!isset(self::$filterArr[$_code])) {
                return false;
            }
        }

        return true;
    }

    // 组选 c(n, 6)
    public function count($sCodes)
    {
        $aCode          = explode("&", $sCodes);
        $aCodeUnique    = array_unique($aCode);
        return $this->getCombinCount(count($aCodeUnique),6);
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $len    =   6;
        $aCodes = explode('&', $sCodes);

        $notInCodes = [];

        // 去除已经中奖的号码
        foreach ($aCodes as $code) {
            if (!in_array($code, $numbers)) {
                $notInCodes[] = $code;
            }
        }

        if (count($notInCodes) < $len) {
            return 0;
        }

        if (count($notInCodes) == $len) {
            return 1;
        }

        return  $this->GetCombinCount(count($notInCodes), $len);
    }
}
