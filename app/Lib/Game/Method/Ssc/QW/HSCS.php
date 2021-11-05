<?php namespace App\Lib\Game\Method\Ssc\QW;

use App\Lib\Game\Method\Ssc\Base;

// 好事成双
class HSCS extends Base
{
    //0&1&2&3&4&5&6&7&8&9
    public $all_count =10;
    public static $filterArr = array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1);

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $rand=rand(1,10);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($codes)
    {
        return implode('&',explode('|',$codes));
    }

    public function regexp($sCodes)
    {
        if (!preg_match("/^([0-9]&){0,9}[0-9]$/", $sCodes)) {
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
        //C(n,1)
        return count(explode("&",$sCodes));
    }

    public function bingoCode(Array $numbers)
    {
        $arr=array_keys(self::$filterArr);
        $counts=array_count_values($numbers);

        $result=[];
        foreach($arr as $pos=>$_code){
            $result[$pos]=intval(isset($counts[$_code]) && $counts[$_code]>=2);
        }

        return [$result];
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {

        $aCodes = array_flip(explode('&', $sCodes));

        $flip = array_filter(array_count_values($numbers), function ($v) {
            return $v >= 2;
        });

        $e = array_intersect_key($flip, $aCodes);

        $cnt = count($e);

        if ($cnt > 0) {
            return $cnt;
        }
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $aCodes = $this->getRepeat2($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            $validCodeArr   = $this->getRepeatCombination2([$w, $q, $b, $s, $g]);
                            $sameCodeArr    = array_intersect($aCodes, $validCodeArr);
                            if (count($sameCodeArr) > 0) {
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
        }

        return $data;
    }

    /**
     * "7&8&9" => ['77', '88', '99']
     * @param $aCode
     * @return array
     */
    public function getRepeat2($aCode)
    {
        $result = [];
        for ($i = 0; $i < sizeof($aCode); $i++) {
            $result[$i] = $aCode[$i].$aCode[$i];
        }
        return $result;
    }

    public function getRepeatCombination2($codeArr)
    {
        $data       = [];
        $codeArr    = $this->getCombination($codeArr, 2);

        foreach ($codeArr as $_code) {
            $_codeArr = explode(' ', $_code);
            if (count(array_unique($_codeArr)) == 1) {
                $data[] = implode('', $_codeArr);
            }
        }

        return $data;
    }
}
