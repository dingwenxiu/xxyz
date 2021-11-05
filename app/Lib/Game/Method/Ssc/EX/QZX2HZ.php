<?php  namespace App\Lib\Game\Method\Ssc\EX;

use App\Lib\Game\Method\Ssc\Base;

// 2星直选和值
class QZX2HZ extends Base
{
    //1&2&3&4&5&6
    public $allCount = 100;
    public static $filterArr = array(0 => 1,1 => 2,2 => 3,3 => 4,4 => 5,5 => 6,6 => 7,7 => 8,8 => 9,9 => 10,10 => 9,11 => 8,12 => 7,13 => 6,14 => 5,15 => 4,16 => 3,17 => 2,18 => 1);

    public function regexp($sCodes)
    {
        // 去重
        $t      = explode("&", $sCodes);
        $temp   = array_unique($t);

        // 不能有重复
        if (count($t) != count($temp)) {
            return false;
        }

        $arr    = self::$filterArr;

        $temp   = array_filter($temp,function($v) use ($arr) {
            return isset($arr[$v]);
        });

        if(count($temp) == 0) {
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
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {

                // 和值必须存在
                $sum = $w + $q;
                if(!isset($exists[$sum])) {
                    continue;
                }

                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
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
