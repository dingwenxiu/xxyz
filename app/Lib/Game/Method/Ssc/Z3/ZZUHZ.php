<?php namespace App\Lib\Game\Method\Ssc\Z3;

use App\Lib\Game\Method\Ssc\Base;

/**
 * Tom 2019 中三　组选和值
 * Class ZZUHZ
 * @package App\Lib\Game\Method\Ssc\Z3
 */
class ZZUHZ extends Base
{
    public $allCount = 1000;
    public static $filterArr = array(
        1 => 1,
        2 => 2,
        3 => 2,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 8,
        8 => 10,
        9 => 11,
        10 => 13,
        11 => 14,
        12 => 14,
        13 => 15,
        14 => 15,
        15 => 14,
        16 => 14,
        17 => 13,
        18 => 11,
        19 => 10,
        20 => 8,
        21 => 6,
        22 => 5,
        23 => 4,
        24 => 2,
        25 => 2,
        26 => 1
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
        $val    = array_sum($numbers);

        $aCodes = explode('|', $sCodes);

        if ($levelId == '1') {
            $flip = array_filter(array_count_values($numbers), function ($v) {
                return $v == 2;
            });

            //组三
            if (count($flip) == 1) {
                foreach ($aCodes as $code) {
                    if ($val == $code) {
                        return 1;
                    }
                }
            }
        } elseif ($levelId == '2') {
            $flip = array_filter(array_count_values($numbers), function ($v) {
                return $v >= 2;
            });

            //组六
            if (count($flip) == 0) {
                foreach ($aCodes as $code) {
                    if ($val == $code) {
                        return 1;
                    }
                }
            }
        }

    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $exists = array_flip($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $q) {
            foreach ($tmp as $b) {
                foreach ($tmp as $s) {
                    // 和值必须存在
                    $sum = $q + $b + $s;
                    if(!isset($exists[$sum])) {
                        continue;
                    }

                    // 排除豹子号
                    if($q == $b && $b == $s) {
                        continue;
                    }

                    foreach ($tmp as $w) {
                        foreach ($tmp as $g) {
                            // 组三　组六　奖金分开
                            if($q == $b || $b == $s || $s == $g) {
                                $prize = $prizes[1];
                            } else {
                                $prize = $prizes[2];
                            }

                            $key = $w . $q . $b . $s. $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $prize, 4);
                            } else {
                                $data[$key] = $prize;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

}
