<?php namespace App\Lib\Game\Method\Ssc\H3;

use App\Lib\Game\Method\Ssc\Base;

// 后3 直选和值
class HZXHZ extends Base
{
    public $allCount = 1000;
    public static $filterArr = array(
        0 => 1,
        1 => 3,
        2 => 6,
        3 => 10,
        4 => 15,
        5 => 21,
        6 => 28,
        7 => 36,
        8 => 45,
        9 => 55,
        10 => 63,
        11 => 69,
        12 => 73,
        13 => 75,
        14 => 75,
        15 => 73,
        16 => 69,
        17 => 63,
        18 => 55,
        19 => 45,
        20 => 36,
        21 => 28,
        22 => 21,
        23 => 15,
        24 => 10,
        25 => 6,
        26 => 3,
        27 => 1
    );

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand = rand(1, count(self::$filterArr));
        return implode('&', (array)array_rand(self::$filterArr, $rand));
    }

    public function fromOld($codes)
    {
        return implode('&', explode('|', $codes));
    }

    public function regexp($sCodes)
    {
        // 去重
        $t      = explode("|", $sCodes);
        $temp   = array_unique($t);
        $arr    = self::$filterArr;

        $temp = array_filter($temp, function ($v) use ($arr) {
            return isset($arr[$v]);
        });

        if (count($temp) == 0) {
            return false;
        }

        return count($temp) == count($t);
    }

    public function count($sCodes)
    {
        //枚举之和
        $n = 0;
        $temp = explode('|', $sCodes);
        foreach ($temp as $c) {
            $n += self::$filterArr[$c];
        }

        return $n;
    }

    public function bingoCode(Array $numbers)
    {
        $val    = array_sum($numbers);
        $arr    = array_keys(self::$filterArr);
        $result = [];
        foreach ($arr as $_code) {
            $result[] = intval($_code == $val);
        }

        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $val = array_sum($numbers);

        $aCodes = explode('|', $sCodes);

        foreach ($aCodes as $code) {
            if ($code == $val) {
                return 1;
            }
        }

    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $exists = array_flip($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $b) {
            foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    // 和值必须存在
                    $sum = $b + $s + $g;
                    if(!isset($exists[$sum])) {
                        continue;
                    }

                    foreach ($tmp as $w) {
                        foreach ($tmp as $q) {
                            $key = $w . $q . $b . $s. $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $prizes[1], 4);
                            } else {
                                $data[$key] = $prizes[1];
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}
