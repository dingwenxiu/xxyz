<?php namespace App\Lib\Game\Method\K3\SLH;

use App\Lib\Game\Method\K3\Base;

// 三连号
class SLH extends Base
{
    // 三连号
    public static $filterArr = array('123', '234', '345', '456');

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
        $aCodes = explode('|', $sCodes);
        return count(array_unique($aCodes));
    }

    public function bingoCode(Array $numbers)
    {
        sort($numbers);
        if( ($numbers[2]-$numbers[1]) != 1 || ($numbers[1] - $numbers[0]) != 1) return [[0]];

        return [[1]];
    }

    // 判定中奖
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $aBetCodes = explode('|', $sBetCodes);

        // 合并号码
        $strOpenNumber = $aOpenNumbers[0]. $aOpenNumbers[1] . $aOpenNumbers[2];

        // 号码为连续 并且购买号码之中
        if (in_array($strOpenNumber, self::$filterArr) && in_array($strOpenNumber, $aBetCodes)) {
            return 1;
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
