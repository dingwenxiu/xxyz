<?php namespace App\Lib\Game\Method\K3\SBTH;

use App\Lib\Game\Method\K3\Base;

// 三不同号
class SBTH extends Base
{
    public static $filterArr = [
        '123', '124', '125', '126', '134', '135', '136', '145', '146', '156',
        '234', '235', '236', '245', '246', "256", "345", "346",  "356", "456"
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $cnt    = count(self::$filterArr);
        $rand   = rand(3,$cnt);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    // 检查格式
    public function regexp($sCodes)
    {
        $aCode = explode('|', $sCodes);

        if(count($aCode) != count(array_unique($aCode))) {
            return false;
        }

        foreach ($aCode as $code) {
            // 是数字
            if (preg_match('/^\d{3}$/', $code) !== 1) {
                return false;
            }

            if (!in_array($code, self::$filterArr)) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        $temp   = explode('|',$sCodes);
        $n      = count($temp);
        return $n;
    }

    public function bingoCode(Array $numbers)
    {
        $counts=array_count_values($numbers);
        if(count($counts)!=3) return [array_fill(0,count(self::$filterArr),0)];

        $arr=array_keys(self::$filterArr);

        $result=[];
        foreach($arr as $code){
            $result[]=intval(isset($counts[$code]));
        }
        return [$result];
    }


    // 判定中奖
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $strOpenNumber  = $aOpenNumbers[0]. $aOpenNumbers[1] . $aOpenNumbers[2];
        $aBetCode       = explode('|', $sBetCodes);

        if (in_array($strOpenNumber, self::$filterArr) && in_array($strOpenNumber, $aBetCode)) {
            return 1;
        }

        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('|', $sCodes);
        foreach ($aCodes as $code) {
            if (isset($data[$code])) {
                $data[$code] = bcadd($data[$code], $prizes[1], 4);
            } else {
                $data[$code] = $prizes[1];
            }
        }

        return $data;
    }
}
