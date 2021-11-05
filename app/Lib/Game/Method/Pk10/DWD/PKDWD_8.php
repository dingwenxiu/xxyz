<?php namespace App\Lib\Game\Method\Pk10\DWD;

use App\Lib\Game\Method\Pk10\Base;

class PKDWD_8 extends Base
{

    public static $filterArr = array('01' => 1, '02' => 1, '03' => 1, '04' => 1, '05' => 1, '06' => 1, '07' => 1, '08' => 1, '09' => 1, '10' => 1);

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand = rand(1, 10);
        return implode('&', (array)array_rand(self::$filterArr, $rand));
    }

    public function fromOld($sCodes)
    {
        return implode('&', explode('|', $sCodes));
    }

    public function regexp($sCodes)
    {
        // 格式
        if (!preg_match("/^((0[1-9]&)|(10&)){0,10}((0[1-9])|(10))$/", $sCodes)) {
            return false;
        }

        $filterArr = self::$filterArr;

        $aCode = explode("|", $sCodes);
        foreach ($aCode as $sCode) {
            $t = explode("&", $sCode);
            $iUniqueCount = count(array_filter(array_unique($t, SORT_STRING), function ($v) use ($filterArr) {
                return isset($filterArr[$v]);
            }));

            if ($iUniqueCount != count($t)) {
                return false;
            }

            if ($iUniqueCount == 0) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        return count(explode("&", $sCodes));
    }

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = array_keys(self::$filterArr);

        foreach ($numbers as $pos => $code) {
            $tmp = [];
            foreach ($arr as $_code) {
                $tmp[] = intval($code == $_code);
            }
            $result[$pos] = $tmp;
        }

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $exists = array_flip($numbers);
        $aCodes = explode('&', $sCodes);

        foreach ($aCodes as $c) {
            if (isset($exists[$c])) return 1;
        }
        return 0;
    }
}
