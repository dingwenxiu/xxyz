<?php namespace App\Lib\Game\Method\Lotto\RXDT;

use App\Lib\Game\Method\Lotto\Base;

class LTRXDT8 extends Base
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
        $n=8;
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
        if (count($aDan) >= 8) {
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
        return $this->getCombinCount(count($aTuo), 8 - count($aDan));
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $iParam=8;
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
        $iLen = 8;
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
