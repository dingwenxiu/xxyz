<?php namespace App\Lib\Game\Method\Klsf\EX;

use App\Lib\Game\Method\Klsf\Base;

// 前2直选2
class KLSF_Q_ZX2 extends Base
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
        if (count($aCodeArr) != 2) {
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

        if (count($aNums[0]) > 0 && count($aNums[1]) > 0) {
            for ($i = 0; $i < count($aNums[0]); $i++) {
                for ($j = 0; $j < count($aNums[1]); $j++) {
                    if ($aNums[0][$i] != $aNums[1][$j]) {
                        $iNums++;
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
    public function assertLevel($levelId, $sCodes, Array $aOpenNumbers)
    {
        // 组装number
        $winNumber = [
            $aOpenNumbers[0] . $aOpenNumbers[1] => 1,
            $aOpenNumbers[1] . $aOpenNumbers[2] => 1,
            $aOpenNumbers[2] . $aOpenNumbers[3] => 1,
            $aOpenNumbers[3] . $aOpenNumbers[4] => 1,
            $aOpenNumbers[4] . $aOpenNumbers[5] => 1,
            $aOpenNumbers[5] . $aOpenNumbers[6] => 1,
            $aOpenNumbers[6] . $aOpenNumbers[7] => 1,
        ];

        info("zx2------",$winNumber);

        $aCodes     = explode('|', $sCodes);
        $aCode1     = explode('&', $aCodes[0]);
        $aCode2     = explode('&', $aCodes[1]);

        $count = 0;
        foreach ($aCode1 as $c1) {
            foreach ($aCode2 as $c2) {
                $key = $c1.$c2;
                if (isset($winNumber[$key])) {
                    $count ++;
                }
            }
        }


        return $count;
    }

}
