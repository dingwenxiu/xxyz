<?php namespace App\Lib\Game\Method\Lotto\RXFS;

use App\Lib\Game\Method\Lotto\Base;

class LTRX5 extends Base
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
        $rand=rand(5,10);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($sCodes){
        return implode('&',explode('|',$sCodes));
    }

    public function regexp($sCodes)
    {
        //格式
        if (!preg_match("/^((0[1-9]&)|(1[01]&)){0,10}((0[1-9])|(1[01]))$/", $sCodes)) {
            return false;
        }

        $filterArr= self::$filterArr;

        $aCode = explode("|", $sCodes);
        foreach ($aCode as $sCode) {
            $t=explode("&", $sCode);
            $iUniqueCount = count(array_filter(array_unique($t),function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }));
            if ($iUniqueCount != count($t)) {
                return false;
            }
            if($iUniqueCount<5){
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        return $this->getCombinCount(count(explode("&", $sCodes)),5);
    }

    public function bingoCode(Array $numbers)
    {
        $exists=array_flip($numbers);
        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $pos=>$_code){
            $result[]=intval(isset($exists[$_code]));
        }

        return [$result];
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $len=5;
        $aCodes = explode('&', $sCodes);
        $iRates = count(array_intersect($aCodes, $numbers));
        if ($iRates < $len) {
            return 0;
        }

        return $this->GetCombinCount($iRates, $len);
    }
}
