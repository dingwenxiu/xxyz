<?php namespace App\Lib\Game\Method\Ssc\BDW;

use App\Lib\Game\Method\Ssc\Base;

//5星3码不定位
class BDW53 extends Base
{
    //1&2&3&4&5&6&7&8
    public $all_count =120;
    public static $filterArr = array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1);

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $rand=rand(3,10);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($codes)
    {
        return implode('&',explode('|',$codes));
    }


    public function regexp($sCodes)
    {
        if (!preg_match("/^(([0-9]&){0,9}[0-9])$/", $sCodes)) {
            return false;
        }

        $filterArr = self::$filterArr;

        $iNums = count(array_filter(array_unique(explode("&", $sCodes)),function($v) use ($filterArr) {
            return isset($filterArr[$v]);
        }));

        if($iNums==0){
            return false;
        }

        return $iNums == count(explode("&", $sCodes));
    }

    public function count($sCodes)
    {
        //C(n,3)
        $iNums = count(explode("&",$sCodes));
        return $this->getCombinCount($iNums,3);
    }

    public function bingoCode(Array $numbers)
    {
        $numbers=array_flip($numbers);
        $result=[];
        $arr=array_keys(self::$filterArr);
        foreach($arr as $v){
            $result[]=intval(isset($numbers[$v]));
        }
        return [$result];
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $temp=array();
        $aCodes = explode("&", $sCodes);
        $i = 0;
        foreach ($aCodes as $code) {
            if(isset($temp[$code])) continue;
            $temp[$code]=1;
            if (in_array($code, $numbers)) {
                $i++;
            }
        }

        if ($i >= 3) {
            return $this->getCombinCount($i,3);
        }
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $aCodes = $this->getBdwCode3($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            $validCodeArr = $this->getBdwCode3([$w, $q, $b, $s, $g]);
                            $sameCode = array_intersect($aCodes, $validCodeArr);
                            if (count($sameCode) < 1) {
                                continue;
                            }

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
