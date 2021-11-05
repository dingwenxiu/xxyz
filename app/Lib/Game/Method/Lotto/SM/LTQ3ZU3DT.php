<?php namespace App\Lib\Game\Method\Lotto\SM;

use App\Lib\Game\Method\Lotto\Base;

// 组选胆拖
class LTQ3ZU3DT extends Base
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
            }))) { // 不能有重复的号码
            return false;
        }

        $aTuo = explode('&', $aTmp[1]);
        if (count($aTuo) != count(array_filter(array_unique($aTuo),function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }))) {  // 不能有重复的号码
            return false;
        }

        if(count($aDan) == 0 || count($aTuo) == 0) {
            return false;
        }

        if(count($aDan) == 1 && count($aTuo) < 2) {
            return false;
        }

        if(count($aDan) == 2 && count($aTuo) < 1) {
            return false;
        }

        if (count($aDan) >= 3) {
            return false;
        }

        // 有重复的
        if (count(array_intersect($aDan, $aTuo)) > 0) {
            return false;
        }

        return true;
    }

    public function count($sCodes)
    {
        $aTmp = explode('|', $sCodes);
        $aDan = explode('&', $aTmp[0]);
        $aTuo = explode('&', $aTmp[1]);
        return $this->getCombinCount(count($aTuo), 3 - count($aDan));
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        // 1胆码 & 2拖码 顺序不限
        $sCodes = $this->convertLtCodes($sCodes);
        $numbers = $this->convertLtCodes($numbers);

        $aTmp = explode('|', $sCodes);
        $aDan = explode('&',$aTmp[0]);
        $aTuo = explode('&',$aTmp[1]);

        $iNum = count($aDan);
        // 胆码都存在
        if(count(array_intersect($aDan,$numbers)) == $iNum){
            $iCnt = 3-$iNum;
            $i=0;
            $arr = array_diff($numbers, $aDan);
            foreach($aTuo as $t){
                if(in_array($t,$arr)){
                    $i++;
                }
            }

            if($i >= $iCnt){
                return 1;
            }
        }
    }
}
