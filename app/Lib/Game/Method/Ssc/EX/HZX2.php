<?php namespace App\Lib\Game\Method\Ssc\EX;

use App\Lib\Game\Method\Ssc\Base;

class HZX2 extends Base
{
    // 0&1&2&3&4&5&6&7&8&9|0&1&2&3&4&5&6&7&8&9
    public $allCount = 100;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    public function regexp($sCodes)
    {
        $regexp = '/^(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])$/';
        if(!preg_match($regexp,$sCodes)) return false;

        $filterArr = self::$filterArr;

        // 去重
        $aCodes = explode("|", $sCodes);
        if (count($aCodes) != 2) {
            return false;
        }

        foreach($aCodes as $codes) {
            $temp = explode('&', $codes);
            if(count($temp) != count(array_filter(array_unique($temp),function($v) use($filterArr) {
                    return isset($filterArr[$v]);
                }))) return false;

            if(count($temp) == 0) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        //n1*n2
        $cnt = 1;
        $temp = explode('|', $sCodes);

        foreach($temp as $c){
            $cnt *= count(explode('&',$c));
        }

        return $cnt;
    }

    public function bingoCode(Array $numbers)
    {
        $result=[];
        $arr=array_keys(self::$filterArr);

        foreach($numbers as $pos=>$code){
            $tmp=[];
            foreach($arr as $_code){
                $tmp[]=intval($code==$_code);
            }
            $result[$pos]=$tmp;
        }

        return $result;
    }

    public function assertLevel($levelId, $sCodes, Array $numbers)
    {

        $aCodes = explode('|', $sCodes);

        $preg = "|[" . str_replace('&', '', $aCodes[0]) . "][" . str_replace('&', '', $aCodes[1]) . "]|";

        if (preg_match($preg, implode("", $numbers))) {
            return 1;
        }

    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $codeArr        = explode('|', $sCodes);
        $codeArr[0]     = explode('&', $codeArr[0]);
        $codeArr[1]     = explode('&', $codeArr[1]);

        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($codeArr[0] as $s) {
                        foreach ($codeArr[1] as $g) {
                            $key = $w . $q . $b . $s . $g;
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
