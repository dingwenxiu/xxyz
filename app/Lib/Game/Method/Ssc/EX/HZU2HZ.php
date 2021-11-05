<?php namespace App\Lib\Game\Method\Ssc\EX;

use App\Lib\Game\Method\Ssc\Base;

// 组选和值
class HZU2HZ extends Base
{
    // 1&2&3&4&5&6
    public $allCount = 45;
    public static $filterArr = array(
        1   => 1,
        2   => 1,
        3   => 2,
        4   => 2,
        5   => 3,
        6   => 3,
        7   => 4,
        8   => 4,
        9   => 5,
        10  => 4,
        11  => 4,
        12  => 3,
        13  => 3,
        14  => 2,
        15  => 2,
        16  => 1,
        17  => 1
    );

    /**
     * 转成数组形式
     * @param $codes
     * @return array
     */
    public function transferCodeToArray($codes)
    {
        return explode('&', $codes);
    }

    public function regexp($sCodes)
    {
        // 去重
        $t      = explode("&", $sCodes);
        $temp   = array_unique($t);
        $arr    = self::$filterArr;

        $temp = array_filter($temp,function($v) use ($arr) {
            return isset($arr[$v]);
        });

        if(count($temp) == 0){
            return false;
        }

        return count($temp) == count($t);
    }

    public function count($sCodes)
    {
        //枚举之和
        $n = 0;
        $temp = explode('&',$sCodes);
        foreach($temp as $c){
            $n += self::$filterArr[$c];
        }

        return $n;
    }

    public function bingoCode(Array $numbers)
    {
        //对子号
        if(count(array_count_values($numbers))==1) return [];

        $val=array_sum($numbers);
        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $pos=>$_code){
            $result[$pos]=intval($_code == $val);
        }

        return [$result];
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {

        $val = array_sum($numbers);

        $aCodes = explode('&', $sCodes);

        //不包含对子
        if ($numbers[0] != $numbers[1]) {
            foreach ($aCodes as $code) {
                if ($val == $code) {
                    return 1;
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

        foreach ($tmp as $s) {
            foreach ($tmp as $g) {
                // 和值必须存在
                $sum = $s + $g;
                if(!isset($exists[$sum])) {
                    continue;
                }

                // 排除豹子号
                if($s == $g) {
                    continue;
                }

                foreach ($tmp as $w) {
                    foreach ($tmp as $q) {
                        foreach ($tmp as $b) {
                            $key = $w . $q . $b . $s. $g;
                            if (isset($data[$key])) {
                                $data[$key] += $prizes[1];
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
