<?php namespace App\Lib\Game\Method\Klsf\SX;

use App\Lib\Game\Method\Klsf\Base;

// 直选3 后
class KLSF_H_ZX3 extends Base
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

    public function regexp($sCodes)
    {
        $aCodeArr = explode("|", $sCodes);
        if (count($aCodeArr) != 3) {
            return false;
        }

        // 逐行判断
        foreach ($aCodeArr as $_aCodeRow) {
            $_aRowCodeArr   = explode("&", $_aCodeRow);
            $aRowCodeArr    = array_unique($_aRowCodeArr, SORT_STRING);

            if (count($_aRowCodeArr) != count($aRowCodeArr)) {
                return false;
            }

            foreach ($aRowCodeArr as $code) {
                if (!isset(self::$filterArr[$code])) {
                    return false;
                }
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        $iNums = 0;
        $aNums = [];
        $aCode = explode("|", $sCodes);
        foreach ($aCode as $sCode) {
            $aNums[] = explode("&", $sCode);
        }

        if (count($aNums[0]) > 0 && count($aNums[1]) > 0 && count($aNums[2]) > 0) {
            for ($i = 0; $i < count($aNums[0]); $i++) {
                for ($j = 0; $j < count($aNums[1]); $j++) {
                    for ($k = 0; $k < count($aNums[2]); $k++) {
                        if ($aNums[0][$i] != $aNums[1][$j] && $aNums[0][$i] != $aNums[2][$k] && $aNums[1][$j] != $aNums[2][$k]) {
                            $iNums++;
                        }
                    }
                }
            }
        }

        return $iNums;
    }

    public function bingoCode(Array $numbers)
    {
        $result=[];
        $arr=array_keys(self::$filterArr);

        foreach($numbers as $pos=>$code){
            $tmp=[];
            foreach($arr as $_code){
                $tmp[]=intval($code==$_code);
            }
            $result[$pos]=$tmp;
        }

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $aBetCodes  = explode('|', $sBetCodes);
        $aBetCodes  = $this->convertLtCodes($aBetCodes);
        $numbers    = $this->convertLtCodes($aOpenNumbers);

        $preg = "|[" . str_replace('&', '', $aBetCodes[0]) . "][" . str_replace('&', '', $aBetCodes[1]) . "][" . str_replace('&', '', $aBetCodes[2]) . "]|";

        if (preg_match($preg, implode("", $numbers))) {
            return 1;
        }
    }

}
