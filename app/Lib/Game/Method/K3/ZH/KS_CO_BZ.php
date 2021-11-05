<?php namespace App\Lib\Game\Method\K3\ZH;

use App\Lib\Game\Method\K3\Base;

// 豹子
class KS_CO_BZ extends Base
{
    public static $filterArr = [
        111 => 1,
        222 => 1,
        333 => 1,
        444 => 1,
        555 => 1,
        666 => 1
    ];

    public function regexp($sCode)
    {
        // 是数字
        if (preg_match('/^\d{3}$/', $sCode) !== 1) {
            return false;
        }

        if (!isset(self::$filterArr[$sCode])) {
            return false;
        }

        return true;
    }

    // 计算注数
    public function count($sCode)
    {
        return 1;
    }

    public function bingoCode(Array $numbers)
    {
        // 必须有相同号
        $counts = array_count_values($numbers);
        if(count($counts) != 3) return [array_fill(0, count(self::$filterArr),0)];

        $arr = array_keys(self::$filterArr);

        $result = [];
        foreach($arr as $code){
            $result[] = intval(isset($counts[$code]) && $counts[$code] == 3);
        }
        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sBetCode, Array $aOpenNumbers)
    {
        // 全相等
        if($aOpenNumbers[0] == $aOpenNumbers[1] && $aOpenNumbers[1] == $aOpenNumbers[2]) {
            $strOpenNumber = $aOpenNumbers[0] . $aOpenNumbers[1] . $aOpenNumbers[2];
            if(isset(self::$filterArr[$strOpenNumber]) && $strOpenNumber == $sBetCode) {
                return 1;
            }
        }
        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $betCode    = $sCodes;

        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    $codeStr = $a . $b . $c;
                    if ($betCode == $codeStr) {
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
