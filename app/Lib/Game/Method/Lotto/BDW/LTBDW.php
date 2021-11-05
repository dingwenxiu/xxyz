<?php namespace App\Lib\Game\Method\Lotto\BDW;

use App\Lib\Game\Method\Lotto\Base;

// 不定位
class LTBDW extends Base
{
    public static $filterArr = [
        '01'    => 1,
        '02'    => 1,
        '03'    => 1,
        '04'    => 1,
        '05'    => 1,
        '06'    => 1,
        '07'    => 1,
        '08'    => 1,
        '09'    => 1,
        '10'    => 1,
        '11'    => 1
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand   = rand(1, count(self::$filterArr));
        return implode('&', (array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($sCodes){
        return implode('&', explode('|',$sCodes));
    }

    public function regexp($sCodes)
    {
        // 格式
        if (!preg_match("/^((0[1-9]&)|(1[01]&)){0,10}((0[1-9])|(1[01]))$/", $sCodes)) {
            return false;
        }

        // 去重
        $t = explode("&", $sCodes);
        $filterArr = self::$filterArr;

        $temp = array_filter(array_unique($t),function($v) use ($filterArr) {
            return isset($filterArr[$v]);
        });

        if(count($temp) == 0){
            return false;
        }

        return count($temp) == count($t);
    }

    public function count($sCodes)
    {
        $n = count(explode("&", $sCodes));

        return $n;
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

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        return count(array_intersect($numbers, explode("&", $sCodes)));
    }

}
