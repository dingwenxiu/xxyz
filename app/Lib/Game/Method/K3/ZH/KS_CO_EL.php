<?php namespace App\Lib\Game\Method\K3\ZH;

use App\Lib\Game\Method\K3\Base;

// 两连
class KS_CO_EL extends Base
{
    public static $filterArr = [
        "12" => 1, "13" => 1, "14" => 1, "15" => 1, "16" => 1,
        "23" => 1, "24" => 1, "25" => 1, "26" => 1, "34" => 1,
        "35" => 1, "36" => 1, "45" => 1, "46" => 1, "56" => 1
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $cnt    = count(self::$filterArr);
        $rand   = rand(2,$cnt);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    // 判定号码格式是否正确
    public function regexp($sCode)
    {
        // 是数字
        if (preg_match('/^\d{2}$/', $sCode) !== 1) {
            return false;
        }

        if (!isset(self::$filterArr[$sCode])) {
            return false;
        }

        return true;
    }

    // 计奖
    public function count($sCodes)
    {
        return 1;
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
    public function assertLevel($levelId, $sCode, Array $aOpenNumbers)
    {
        // 排除豹子号
        if ($aOpenNumbers[0] == $aOpenNumbers[1] && $aOpenNumbers[1] == $aOpenNumbers[2]) {
            return 0;
        }

        sort($aOpenNumbers);

        $canWinCodeArr  = [
            $aOpenNumbers[0].$aOpenNumbers[1],
            $aOpenNumbers[0].$aOpenNumbers[2],
            $aOpenNumbers[1].$aOpenNumbers[2],
        ];

        $canWinCodeArr = array_unique($canWinCodeArr);

        if (in_array($sCode, $canWinCodeArr)) {
            return 1;
        }

        return 0;
    }

    // 控水处理
    public function doControl($data, $sCode, $prizes)
    {
        $_aCodes    = [$sCode . "1" => 1, $sCode . "2" => 1, $sCode . "3" => 1, $sCode . "4" => 1, $sCode . "5" => 1, $sCode . "6" => 1,];

        $aCodes     = [];
        foreach ($_aCodes as $_sCode) {
            $aCodes[$this->strOrder($_sCode)] = 1;
        }

        $tmp    = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    // 三同号　排除
                    if ($a == $b && $b == $c) {
                        continue;
                    }

                    $_codeStr = $this->strOrder($a . $b . $c);
                    if (isset($aCodes[$_codeStr])) {
                        $codeStr = $a . $b . $c;
                        if (isset($data[$codeStr])) {
                            $data[$codeStr] = bcadd($data[$codeStr], $prizes[1], 4);
                        } else {
                            $data[$codeStr] = $prizes[1];
                        }
                    }
                }
            }
        }

        return $data;
    }
}
