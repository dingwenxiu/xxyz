<?php namespace App\Lib\Game\Method\Ssc;

class ZX1 extends Base
{

    public $totalCount = 10;

    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand = rand(1, 10);
        return implode('&', (array)array_rand(self::$filterArr, $rand));
    }

    public function fromOld($codes)
    {
        return implode('&', explode('|', $codes));
    }

    public function regexp($sCodes)
    {
        $regexp = '/^(([0-9]&){0,9}[0-9])$/';
        if (!preg_match($regexp, $sCodes)) return false;

        $filterArr = self::$filterArr;

        // 去重
        $sCodes = explode("|", $sCodes);
        foreach ($sCodes as $codes) {
            $temp = explode('&', $codes);
            if (count($temp) != count(array_filter(array_unique($temp), function ($v) use ($filterArr) {
                    return isset($filterArr[$v]);
                }))) return false;

            if (count($temp) == 0) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        // n
        return count(explode('&', $sCodes));
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

    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $codes  = explode('&', $sCodes);
        $exists = array_flip($numbers);
        foreach ($codes as $c) {
            if (isset($exists[$c])) {
                return 1;
            }
        }
        return 0;
    }


}
