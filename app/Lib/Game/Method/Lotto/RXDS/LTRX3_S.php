<?php namespace App\Lib\Game\Method\Lotto\RXDS;

use App\Lib\Game\Method\Lotto\Base;

class LTRX3_S extends Base
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
        $rand=3;
        return implode(' ',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($codes)
    {
        return implode(',',explode('|',$codes));
    }

    public function regexp($sCodes)
    {
        //格式
        if (!preg_match("/^(((0[1-9]\s)|(1[01]\s)){2}((0[1-9])|(1[01]))\,)*(((0[1-9]\s)|(1[01]\s)){2}((0[1-9])|(1[01])))$/", $sCodes)) {
            return false;
        }

        $aCode = explode(",",$sCodes);

        //去重
        if(count($aCode) != count(array_filter(array_unique($aCode)))) return true;

        //校验
        foreach ($aCode as $sTmpCode) {
            $aTmpCode = explode(" ", $sTmpCode);
            if (count($aTmpCode) != 3) {
                return false;
            }
            if (count($aTmpCode) != count(array_filter(array_unique($aTmpCode)))) {
                return false;
            }
            foreach ($aTmpCode as $c) {
                if (!isset(self::$filterArr[$c])) {
                    return false;
                }
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        return count(explode(",",$sCodes));
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $aCodes = explode(',',$sCodes);
        $i=0;
        foreach ($aCodes as $code) {
            if(count(array_intersect(explode(' ',$code),$numbers)) >=3 ) $i++;
        }

        return $i;
    }


}
