<?php namespace App\Lib\Game\Method\Pk10\CD1;

//　猜冠军 单式
use App\Lib\Game\Method\Pk10\Base;

class PKQZX1_S extends Base
{
    public static $filterArr = [
        '01' => 1,
        '02' => 1,
        '03' => 1,
        '04' => 1,
        '05' => 1,
        '06' => 1,
        '07' => 1,
        '08' => 1,
        '09' => 1,
        '10' => 1
    ];

    //　供测试用 生成随机投注
    public function randomCodes()
    {
        $rand = rand(1, 10);
        return implode('&', (array)array_rand(self::$filterArr, $rand));
    }

    public function fromOld($sCodes)
    {
        return implode('&', explode('|', $sCodes));
    }

    // 格式判定
    public function regexp($sCodes)
    {
        //　格式
        if (!preg_match("/^((0[1-9]\,)|(10\,)){0,10}((0[1-9])|(10))$/", $sCodes)) {
            return false;
        }

        $aCode = explode(",", $sCodes);

        //　去重
        if(count($aCode) != count(array_filter(array_unique($aCode)))) return false;

        //　校验
        foreach ($aCode as $_code) {
            if (!isset(self::$filterArr[$_code])) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        return count(explode(",", $sCodes));
    }

    //　判定中奖
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $winCode    = $aOpenNumbers[0];
        $aBetCodes  = explode(',', $sBetCodes);

        if (in_array($winCode, $aBetCodes)) {
            return 1;
        }
    }
}
