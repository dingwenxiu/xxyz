<?php namespace App\Lib\Game\Method\Ssc\BDW;

use App\Lib\Game\Method\Ssc\Base;

// 3星2码不定位
class HBDW32 extends Base
{
    //1&2&3&4&5&6&7&8
    public $all_count =45;

    public static $filterArr = array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1);

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $rand=rand(2,10);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($codes)
    {
        return implode('&',explode('|',$codes));
    }

    //
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
        //C(n,2)
        $iNums = count(explode("&",$sCodes));
        return $this->getCombinCount($iNums,2);
    }

    //冷热 & 遗漏
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
        $exists=array_flip($numbers);
        $temp=array();
        $aCodes = explode("&", $sCodes);
        foreach ($aCodes as $code) {
            if (isset($exists[$code])) {
                $temp[$code]=1;
            }
        }
        $i=count($temp);
        if ($i >= 2) {
            return $this->getCombinCount($i,2);
        }
        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $aCodes = $this->getBdwCode2($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $b) {
            foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    $validCodeArr = $this->getBdwCode2([$b, $s, $g]);
                    $sameCode = array_intersect($aCodes, $validCodeArr);
                    if(count($sameCode) < 1) {
                        continue;
                    }
                    foreach ($tmp as $w) {
                        foreach ($tmp as $q) {
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
