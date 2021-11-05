<?php namespace App\Lib\Game\Method\Ssc\EX;

use App\Lib\Game\Method\Ssc\Base;

// 组选包胆
class HZU2BD extends Base
{

    public $allCount = 90;
    public static $filterArr = array(0 => 9, 1 => 9, 2 => 9, 3 => 9, 4 => 9, 5 => 9, 6 => 9, 7 => 9, 8 => 9, 9 => 9);

    public function regexp($sCodes)
    {
        return isset(self::$filterArr[$sCodes]);
    }

    public function count($sCode)
    {
        return self::$filterArr[$sCode];
    }

    public function bingoCode(Array $numbers)
    {
        //对子号
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
    public function assertLevel($levelId, $sCode, Array $numbers)
    {
        // 不包含对子
        if ($numbers[0] != $numbers[1]) {
            if (in_array($sCode, $numbers)) {
                return 1;
            }
        }

        return 0;

    }


    // 控水处理
    public function doControl($data, $sCode, $prizes)
    {
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    // 必须有一个号码一样
                    if($s != $sCode && $g != $sCode) {
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
