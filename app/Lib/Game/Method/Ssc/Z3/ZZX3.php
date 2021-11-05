<?php namespace App\Lib\Game\Method\Ssc\Z3;

use App\Lib\Game\Method\Ssc\Base;

// 前直选３
class ZZX3 extends Base
{
    // 0&1&2&3&4&5&6&7&8&9|0&1&2&3&4&5&6&7&8&9|0&1&2&3&4&5&6&7&8&9
    public $all_count =1000;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $arr=[];
        $rand=rand(1,10);
        $arr[]=implode('&',(array)array_rand(self::$filterArr,$rand));
        $rand=rand(1,10);
        $arr[]=implode('&',(array)array_rand(self::$filterArr,$rand));
        $rand=rand(1,10);
        $arr[]=implode('&',(array)array_rand(self::$filterArr,$rand));

        return implode('|',$arr);
    }

    public function regexp($sCodes)
    {
        //去重?
        $regexp = '/^(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])$/';
        if(!preg_match($regexp,$sCodes)) return false;

        $filterArr = self::$filterArr;

        //去重
        $sCodes = explode("|", $sCodes);
        foreach($sCodes as $codes){
            $temp = explode('&',$codes);
            if(count($temp) != count(array_filter(array_unique($temp),function($v) use($filterArr) {
                    return isset($filterArr[$v]);
                }))) return false;

            if(count($temp)==0){
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        // n1*n2*n3
        $cnt    = 1;
        $temp   = explode('|',$sCodes);

        foreach($temp as $c){
            $cnt *= count(explode('&', $c));
        }

        return $cnt;
    }

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = array_keys(self::$filterArr);

        foreach($numbers as $pos => $code){
            $tmp    = [];
            foreach($arr as $_code){
                $tmp[] = intval($code == $_code);
            }
            $result[$pos] = $tmp;
        }

        return $result;
    }

    public function assertLevel($levelId, $sCodes, Array $numbers)
    {

        $aCodes = explode('|', $sCodes);

        $preg = "|[" . str_replace('&', '', $aCodes[0]) . "][" . str_replace('&', '', $aCodes[1]) . "][" . str_replace('&', '', $aCodes[2]) . "]|";

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
        $codeArr[2]     = explode('&', $codeArr[2]);

        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($tmp as $w) {
            foreach ($codeArr[0] as $q) {
                foreach ($codeArr[1] as $b) {
                    foreach ($codeArr[2] as $s) {
                        foreach ($tmp as $g) {
                            $key = $w . $q . $b . $s . $g;
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
