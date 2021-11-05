<?php namespace App\Lib\Game\Method\Lotto\RXDT;

use App\Lib\Game\Method\Lotto\Base;

class LTRXDT6 extends Base
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
        $n=6;
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
        if (count($aDan) >= 6) {
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
        return $this->getCombinCount(count($aTuo), 6 - count($aDan));
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        //中奖规则:
        //1.购买号码包括所有中奖号码:^(.*[24689].*){5}$
        //2.在满足第一个条件的情况下，购买号码胆码中至少包括的中奖号码个数等于胆码个数减($iParam-5):如果减出的结果为非正数,则表示胆码中可以不包括中奖号码
        //  "^(.*[24689].*){1,2}\|.*$" ):其中2为胆码个数:胆码为2时,至少有一个中奖号码在胆码当中,以此类推
        //只要任何一个条件不满足都不中奖

        // 中奖倍数:
        //1.先确定中奖的五个号码
        //2.除去中奖号码之后必须选择的号码个数=$iParam-5
        //3.剩下号码先从胆码中选择与中奖号码不匹配的号码:$iDanLen-$iDanMatchLen
        //4.除去上面选择的号码之后剩下必须选择的个数为:$iParam-5-($iDanLen-$iDanMatchLen)=n
        //5.最后可选择的号码个数:$iTuoLen-$iRates=m
        //6.中奖倍数:C(m,n)

        $iParam=6;
        $aCodes = explode("|", $sCodes);

        $aDan = explode("&", $aCodes[0]); //胆码
        $aTuo = explode("&", $aCodes[1]); //拖码
        $iDanLen = count($aDan); //胆码个数
        $iTuoLen = count($aTuo); //拖码个数
        $iDanMatchLen = count(array_intersect($aDan, $numbers)); //胆码与中奖号码匹配次数
        $iTuoMatchLen = count(array_intersect($aTuo, $numbers)); // 拖码与中奖号码匹配次数

        if ($iTuoMatchLen + $iDanMatchLen != 5) { //所有号码与中奖号码的匹配次数不等于5:则不能中奖
            return 0;
        }
        if ($iDanLen - $iDanMatchLen > $iParam - 5) { //胆码中与开奖号码不匹配的个数不能大于:$iParam-5
            return 0;
        }

        return $this->GetCombinCount($iTuoLen - $iTuoMatchLen, $iParam - 5 - ($iDanLen - $iDanMatchLen));
    }


    public function getDtCodes($sCodes)
    {
        $iLen = 6;
        $aTmp = explode("|", $sCodes);
        $aDan = explode('&', $aTmp[0]);
        $aTuo = explode('&', $aTmp[1]);
        $aTuoCombins = $this->getCombination($aTuo, $iLen - count($aDan));
        $aCode = [];
        foreach ($aTuoCombins as $at) {
            $aTp = array_merge($aDan, explode(' ', $at));
            sort($aTp);
            $aCode[] = implode(' ', $aTp);
        }
        $aTemp = [];
        foreach ($aCode as $ac) {
            $acTmp = explode(' ', $ac);
            $aCom = $this->getCombination($acTmp, 5);
            foreach ($aCom as $sNum) {
                $aTemp[$sNum] = isset($aTemp[$sNum]) ? $aTemp[$sNum] + 1 : 1;
            }
        }

        $codes=[];
        $aCode = $this->_ArrayFlip($aTemp);
        foreach ($aCode as $k => $v) {
            foreach($v as $c){
                $codes[$c]=1;
            }
        }

        return array_keys($codes);
    }

}
