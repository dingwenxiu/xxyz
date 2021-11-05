<?php namespace App\Lib\Game\Method\Lotto\RXDT;

use App\Lib\Game\Method\Lotto\Base;

class LTRXDT3 extends Base
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

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $n=3;
        $d=1;
        $t=$n-$d;
        $cnt=count(self::$filterArr);
        $rand1=$d;
        $rand2=rand($t,$cnt);

        $temp=(array)array_rand(self::$filterArr,$rand1);
        $diffs=array_diff(array_keys(self::$filterArr),$temp);

        if($rand2>count($diffs)) $rand2=count($diffs);

        $arr[]=implode('&',$temp);

        $arr[]=implode('&',(array)array_rand(array_flip($diffs),$rand2));

        return implode('|',$arr);
    }

    public function fromOld($sCodes){
        return implode('|',array_map(function($v){
            return implode('&',explode(' ',$v));
        },explode('|',$sCodes)));
    }

    public function regexp($sCodes)
    {
        if (!preg_match("/^(((0[1-9]&)|(1[01]&)){0,6}((0[1-9])|(1[01]))\|){1}(((0[1-9]&)|(1[01]&)){0,10}((0[1-9])|(1[01])))$/", $sCodes)) {
            return false;
        }

        $filterArr = self::$filterArr;

        $aTmp = explode('|', $sCodes);
        $aDan = explode('&', $aTmp[0]);
        if (count($aDan) != count(array_filter(array_unique($aDan),function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }))) { //不能有重复的号码
            return false;
        }
        $aTuo = explode('&', $aTmp[1]);
        if (count($aTuo) != count(array_filter(array_unique($aTuo),function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }))) { //不能有重复的号码
            return false;
        }
        if (count($aDan) >= 3) {
            return false;
        }

        //有重复的
        if (count(array_intersect($aDan, $aTuo)) > 0) {
            return false;
        }

        return true;
    }

    public function count($sCodes)
    {
        //C(n2,3-n1)
        $aTmp = explode('|', $sCodes);
        $aDan = explode('&', $aTmp[0]);
        $aTuo = explode('&', $aTmp[1]);
        return $this->getCombinCount(count($aTuo), 3 - count($aDan));
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        //分别从胆码和拖码的01-11中，至少选择1个胆码和1个拖码组成一注，只要当期顺序摇出的5个开奖号码中同时包含所选的1个胆码和1个拖码，即为中奖。
        $aTmp = explode("|", $sCodes);
        $iLen = 3;
        $aDan = explode('&', $aTmp[0]);
        $aTuo = explode('&', $aTmp[1]);
        $iRates=count(array_intersect($aTuo,$numbers));

        $aTuoCombins = $this->getCombination($aTuo, $iLen - count($aDan));
        foreach($aTuoCombins as $v){
            if(count(array_intersect(array_merge($aDan,explode(' ',$v)),$numbers)) == $iLen){
                return $this->GetCombinCount($iRates, $iLen - count($aDan)); // 中奖倍数C(拖码与中奖号码相同的个数,玩法必须选择的号码个数-胆码个数)
            }
        }

        return 0;
    }

}
