<?php namespace App\Lib\Game\Method\Ssc\EX;

use App\Lib\Game\Method\Ssc\Base;

//组选2
class QZU2 extends Base
{
    //1&2&3&4&5&6
    public $allCount = 45;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    public function regexp($sCodes)
    {
        $temp       = explode("&", $sCodes);
        $filterArr  = self::$filterArr;

        $iNums = count(array_filter(array_unique($temp),function($v) use ($filterArr) {
            return isset($filterArr[$v]);
        }));

        if($iNums == 0) {
            return false;
        }

        return count($temp) == $iNums;
    }

    public function count($sCodes)
    {
        //C(n,2)

        $n = count(explode("&",$sCodes));

        return $this->getCombinCount($n,2);
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
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        // 非对子
        if ($numbers[0] != $numbers[1]) {
            $preg = "|[" . str_replace('&', '', $sCodes) . "]{2}|";
            if (preg_match($preg, implode("", $numbers))) {
                return 1;
            }
        }

    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $allCodeArr = $this->unpPackZu2($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($allCodeArr as $_code => $val) {
            foreach ($tmp as $b) {
                foreach ($tmp as $s) {
                    foreach ($tmp as $g) {

                        $key = $b . $s . $g. $_code;
                        if (isset($data[$key])) {
                            $data[$key] += $prizes[1];
                        } else {
                            $data[$key] = $prizes[1];
                        }

                    }
                }
            }
        }

        return $data;
    }
}
