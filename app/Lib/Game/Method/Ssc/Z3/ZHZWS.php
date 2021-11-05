<?php  namespace App\Lib\Game\Method\Ssc\Z3;

use App\Lib\Game\Method\Ssc\Base;

// 和值尾数
class ZHZWS extends Base
{
    //0&1&2&3&4&5&6&7&8&9 [0-9]
    public $allCount =10;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);


    public function regexp($sCodes)
    {
        if (!preg_match("/^([0-9]&){0,9}[0-9]$/", $sCodes)) {
            return false;
        }

        //去重
        $temp=explode("&",$sCodes);
        $arr = self::$filterArr;

        $iNums = count(array_filter(array_unique($temp),function($v) use ($arr) {
            return isset($arr[$v]);
        }));

        if($iNums==0){
            return false;
        }

        return $iNums == count($temp);
    }

    public function count($sCodes)
    {
        //C(n,1)
        $n = count(explode("&",$sCodes));
        return $this->getCombinCount($n,1);
    }

    public function bingoCode(Array $numbers)
    {
        $val=array_sum($numbers) % 10;
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
        $vals = str_split(array_sum($numbers));
        $val = array_pop($vals);

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
        $codes  = explode('&', $sCodes);
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($codes as $_code) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    foreach ($tmp as $d) {
                        $hw = ($b + $c + $d) % 10;
                        if ($hw == $_code) {
                            foreach ($tmp as $a) {
                                foreach ($tmp as $e) {
                                    $key = $a.$b.$c.$d.$e;
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
            }
        }

        return $data;
    }
}
