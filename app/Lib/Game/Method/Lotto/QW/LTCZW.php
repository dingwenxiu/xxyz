<?php namespace App\Lib\Game\Method\Lotto\QW;

use App\Lib\Game\Method\Lotto\Base;

// 猜中位
class LTCZW extends Base
{
    public static $filterArr = array('3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09');

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $rand=rand(1,count(self::$filterArr));
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($sCodes){
        return implode('&',explode('|',strtr($sCodes,array_flip(self::$filterArr))));
    }

    public function regexp($sCodes)
    {
        //格式
        if (!preg_match("/^(([0-9]&)*[0-9])$/", $sCodes)) {
            return false;
        }

        //去重
        $t=explode("&",$sCodes);
        $filterArr = self::$filterArr;

        $temp = array_filter(array_unique($t),function($v) use ($filterArr) {
            return isset($filterArr[$v]);
        });

        if(count($temp)==0){
            return false;
        }

        return count($temp) == count($t);
    }

    public function count($sCodes)
    {
        //n

        $n = count(explode("&",$sCodes));

        return $n;
    }

    public function bingoCode(Array $numbers)
    {
        sort($numbers);
        $val=$numbers[2];
        $result=[];
        $arr=self::$filterArr;
        foreach($arr as $v){
            $result[]=intval($v==$val);
        }
        return [$result];
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $aCodes = explode("&", $sCodes);

        sort($numbers);

        $z = intval($numbers[2]);

        if($levelId == '1'){
            //3,9
            if(in_array($z,array(3,9)) && in_array($z,$aCodes)){
                return 1;
            }
        }elseif($levelId == '2'){
            //4,8
            if(in_array($z,array(4,8)) && in_array($z,$aCodes)){
                return 1;
            }
        }elseif($levelId == '3'){
            //5,7
            if(in_array($z,array(5,7)) && in_array($z,$aCodes)){
                return 1;
            }
        }elseif($levelId == '4'){
            //6
            if($z==6 && in_array($z,$aCodes)){
                return 1;
            }
        }
    }
}
