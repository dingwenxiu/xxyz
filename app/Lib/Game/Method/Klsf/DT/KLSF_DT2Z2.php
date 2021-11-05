<?php namespace App\Lib\Game\Method\Klsf\DT;

use App\Lib\Game\Method\Klsf\Base;

class KLSF_DT2Z2 extends Base
{
    public static $filterArr = [
        "01" => 1,
        "02" => 1,
        "03" => 1,
        "04" => 1,
        "05" => 1,
        "06" => 1,
        "07" => 1,
        "08" => 1,
        "09" => 1,
        "10" => 1,
        "11" => 1,
        "12" => 1,
        "13" => 1,
        "14" => 1,
        "15" => 1,
        "16" => 1,
        "17" => 1,
        "18" => 1,
        "19" => 1,
        "20" => 1,
    ];

    public function regexp($sCodes)
    {
        $aTmp = explode('|', $sCodes);


        $_aDan = explode('&', $aTmp[0]);
        $aDan  = array_unique($_aDan, SORT_STRING);

        // 长度最小为1 不存在重复
        if (count($aDan) < 1 || count($_aDan) != count($aDan)) {
            return false;
        }

        foreach ($aDan as $code) {
            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        // 检测拖吗
        $_aTuo = explode('&', $aTmp[1]);
        $aTuo  = array_unique($_aTuo, SORT_STRING);

        // 长度最小为1 不存在重复
        if (count($aTuo) < 1 || count($_aTuo) != count($aTuo)) {
            return false;
        }

        foreach ($aTuo as $code) {
            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        // 有重复的
        if (count(array_intersect($aDan, $aTuo)) > 0) {
            return false;
        }

        return true;
    }

    public function count($sCodes)
    {
        // C(n2,2-n1)
        $aTmp = explode('|', $sCodes);
        $aDan = array_unique(explode('&', $aTmp[0]), SORT_STRING);
        $aTuo = array_unique(explode('&', $aTmp[1]), SORT_STRING);
        return $this->getCombinCount(count($aTuo), 2 - count($aDan));
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        // 分别从胆码和拖码的01-20中，至少选择1个胆码和1个拖码组成一注，只要当期顺序摇出的8个开奖号码中同时包含所选的1个胆码和1个拖码，即为中奖。
        $aTmp = explode("|", $sCodes);
        $iLen = 2;
        $aDan = explode('&', $aTmp[0]);
        $aTuo = explode('&', $aTmp[1]);
        $iRates = count(array_intersect($aTuo, $numbers));

        $aTuoCombins = $this->getCombination($aTuo, $iLen - count($aDan));
        foreach($aTuoCombins as $v){
            if(count(array_intersect(array_merge($aDan,explode(' ', $v)), $numbers)) == $iLen) {
                return $this->GetCombinCount($iRates, $iLen - count($aDan)); // 中奖倍数C(拖码与中奖号码相同的个数,玩法必须选择的号码个数-胆码个数)
            }
        }

        return 0;
    }


}
