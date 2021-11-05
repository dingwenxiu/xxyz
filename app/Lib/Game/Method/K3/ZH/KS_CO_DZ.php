<?php namespace App\Lib\Game\Method\K3\ZH;

use App\Lib\Game\Method\K3\Base;

// 对子
class KS_CO_DZ extends Base
{
    public static $filterArr = [
        11   => 1,
        22   => 1,
        33   => 1,
        44   => 1,
        55   => 1,
        66   => 1,
    ];

    // 判定格式
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

    // 计算注数
    public function count($sCodes)
    {
        return 1;
    }

    public function bingoCode(Array $numbers)
    {
        // 必须有相同号
        $counts = array_count_values($numbers);

        $tmp    = array_fill(0,count(self::$filterArr),0);
        if(count($counts) != 2) return [$tmp,$tmp];

        $arr = array_keys(self::$filterArr);

        $result = [];
        // 同号
        $t = [];
        foreach($arr as $code){
            $t[]    = intval(isset($counts[$code]) && $counts[$code] == 2);
        }

        $result[]   = $t;
        // 不同号
        $bt = [];
        foreach($arr as $code) {
            $bt[] = intval(isset($counts[$code]) && $counts[$code] == 1);
        }
        $result[] = $bt;

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCode, Array $aOpenNumbers)
    {
        // 如果是3不同 则必不中
        if ($aOpenNumbers[0] != $aOpenNumbers[1] && $aOpenNumbers[1] != $aOpenNumbers[2]) {
            return 0;
        }

        // 所有可能中奖的号码
        $aCodes = [$sCode . "1" => 1, $sCode . "2" => 1, $sCode . "3" => 1, $sCode . "4" => 1, $sCode . "5" => 1, $sCode . "6" => 1,];

        if ($aOpenNumbers[0] == $aOpenNumbers[1]) {
            $sOpenNumbers = $aOpenNumbers[0].$aOpenNumbers[1].$aOpenNumbers[2];
        } else if ($aOpenNumbers[0] == $aOpenNumbers[2]){
            $sOpenNumbers = $aOpenNumbers[0].$aOpenNumbers[2].$aOpenNumbers[1];
        } else {
            $sOpenNumbers = $aOpenNumbers[1].$aOpenNumbers[2].$aOpenNumbers[0];
        }

        // 是否存在
        if (isset($aCodes[$sOpenNumbers])) {
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
                    if ($a != $b && $b != $c) {
                        continue;
                    }

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
