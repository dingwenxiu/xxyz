<?php namespace App\Lib\Game\Method\K3\HZ;

use App\Lib\Game\Method\K3\Base;

// 快三和值
class KSHZ extends Base
{
    public static $filterArr = [
        '3' => 1,
        '4' => 1,
        '5' => 1,
        '6' => 1,
        '7' => 1,
        '8' => 1,
        '9' => 1,
        '10' => 1,
        '11' => 1,
        '12' => 1,
        '13' => 1,
        '14' => 1,
        '15' => 1,
        '16' => 1,
        '17' => 1,
        '18' => 1
    ];

    public static $ls = array(
        "1" => array(3,  18),
        "2" => array(4,  17),
        "3" => array(5,  16),
        "4" => array(6,  15),
        "5" => array(7,  14),
        "6" => array(8,  13),
        "7" => array(9,  12),
        "8" => array(10, 11),
    );

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand = rand(1,count(self::$filterArr));
        return implode('|',(array)array_rand(self::$filterArr,$rand));
    }

    /**
     * 转成数组形式
     * @param $codes
     * @return array
     */
    public function transferCodeToArray($codes)
    {
        return explode('|', $codes);
    }

    // 格式
    public function regexp($sCodes)
    {
        // 去重
        $aCodes     = explode("|", $sCodes);
        $temp       = array_unique($aCodes);
        $arr        = self::$filterArr;

        $temp = array_filter($temp, function($v) use ($arr) {
            return isset($arr[$v]);
        });

        if(count($temp) == 0) {
            return false;
        }

        return count($temp) == count($aCodes);
    }

    // 计算注数
    public function count($sCodes)
    {
        // 枚举之和
        $temp   = explode('|', $sCodes);
        return count($temp);
    }

    public function bingoCode(Array $numbers)
    {
        $val=array_sum($numbers);

        $arr=array_keys(self::$filterArr);

        $result=[];
        foreach($arr as $code){
            $result[]=intval($code==$val);
        }
        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        // 和值：投注号码与当期开奖号码的三个号码的和值相符，即中奖。
        $sum = array_sum($aOpenNumbers);

        $aBetCodes = explode('|', $sBetCodes);

        $l = self::$ls[$levelId];
        if(in_array($sum, $l)){
            foreach ($aBetCodes as $code) {
                if ($code == $sum) {
                    return 1;
                }
            }
        }
        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes     = array_flip(explode('|', $sCodes));

        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    $sum = $a + $b + $c;
                    if (isset($aCodes[$sum])) {
                        $codeStr = $a . $b . $c;
                        if (isset($data[$codeStr])) {
                            $data[$codeStr] = bcadd($data[$codeStr], $prizes[1], 4);
                        } else {
                            $data[$codeStr] = $prizes[1];
                        }
                    }
                }
            }
        }

        return $data;
    }
}
