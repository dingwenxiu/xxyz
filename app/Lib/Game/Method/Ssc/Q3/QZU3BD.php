<?php namespace App\Lib\Game\Method\Ssc\Q3;

use App\Lib\Game\Method\Ssc\Base;

// 前三组选　组选包胆
class QZU3BD extends Base
{
    //1
    public $allCount =486;
    public static $filterArr = array(0 => 54, 1 => 54, 2 => 54, 3 => 54, 4 => 54, 5 => 54, 6 => 54, 7 => 54, 8 => 54, 9 => 54);

    public function regexp($sCodes)
    {
        return isset(self::$filterArr[$sCodes]);
    }

    public function count($sCodes)
    {
        // 枚举之和
        $n = 0;
        $temp = explode('&',$sCodes);
        foreach($temp as $c){
            $n += self::$filterArr[$c];
        }

        return $n;
    }

    public function bingoCode(Array $numbers)
    {
        // 豹子号
        if(count(array_count_values($numbers))==1) return [];

        $exists=array_flip($numbers);
        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $pos=>$_code){
            $result[$pos]=intval(isset($exists[$_code]));
        }

        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $aCodes = explode('&', $sCodes);

        if ($levelId == '1') {
            $flip = array_filter(array_count_values($numbers), function ($v) {
                return $v == 2;
            });

            // 组三
            if (count($flip) == 1) {
                foreach ($aCodes as $code) {
                    if (in_array($code, $numbers)) {
                        return 1;
                    }
                }
            }
        } elseif ($levelId == '2') {
            // 排除组3
            $flip = array_filter(array_count_values($numbers), function ($v) {
                return $v >= 2;
            });

            // 组六
            if (count($flip) == 0) {
                foreach ($aCodes as $code) {
                    if (in_array($code, $numbers)) {
                        return 1;
                    }
                }
            }
        }

    }

    // 控水处理
    public function doControl($data, $sCode, $prizes)
    {
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    // 必须有一个号码一样
                    if($w != $sCode && $q != $sCode && $w != $sCode) {
                        continue;
                    }

                    // 排除豹子号
                    if($w == $q && $q == $b) {
                        continue;
                    }

                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            // 组三　组六　奖金分开
                            if($w == $q || $q == $b || $w == $b) {
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
