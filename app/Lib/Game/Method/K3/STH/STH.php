<?php namespace App\Lib\Game\Method\K3\STH;

use App\Lib\Game\Method\K3\Base;

// 三同号
class STH extends Base
{

    public static $filterArr = array('1' => '111','2' => '222','3' => '333','4' => '444','5' => '555','6' => '666');

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $cnt    = count(self::$filterArr);
        $rand   = rand(1, $cnt);
        return implode('|', (array)array_rand(self::$filterArr, $rand));
    }

    public function regexp($sCodes)
    {
        $aCode = explode('|', $sCodes);

        if(count($aCode) != count(array_unique($aCode))) {
            return false;
        }

        foreach ($aCode as $code) {
            // 是数字
            if (preg_match('/^\d{3}$/', $code) !== 1) {
                return false;
            }

            if (!in_array($code, self::$filterArr)) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        $aCodes = explode('|',$sCodes);
        return count(array_unique($aCodes));
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
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        // 三同号单选：当期开奖号码的三个号码相同，且投注号码与当期开奖号码相符，即中奖。
        $aBetCodes = explode('|', $sBetCodes);

        // 全相等
        if($aOpenNumbers[0] == $aOpenNumbers[1] && $aOpenNumbers[1] == $aOpenNumbers[2]) {
            $strOpenNumber = $aOpenNumbers[0]. $aOpenNumbers[1] . $aOpenNumbers[2];
            if(in_array($strOpenNumber, self::$filterArr) && in_array($strOpenNumber, $aBetCodes)) {
                return 1;
            }
        }
        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('|', $sCodes);
        foreach ($aCodes as $code) {
            if (isset($data[$code])) {
                $data[$code] = bcadd($data[$code], $prizes[1], 4);
            } else {
                $data[$code] = $prizes[1];
            }
        }

        return $data;
    }
}
