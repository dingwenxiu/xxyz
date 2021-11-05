<?php namespace App\Lib\Game\Method\K3\ZH;

use App\Lib\Game\Method\K3\Base;

// 快三 和值
class KS_CO_HZ extends Base
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
        "1" => array(3 => 1,  18 => 1),
        "2" => array(4 => 1,  17 => 1),
        "3" => array(5 => 1,  16 => 1),
        "4" => array(6 => 1,  15 => 1),
        "5" => array(7 => 1,  14 => 1),
        "6" => array(8 => 1,  13 => 1),
        "7" => array(9 => 1,  12 => 1),
        "8" => array(10 => 1, 11 => 1),
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
    public function regexp($code)
    {
        return isset(self::$filterArr[$code]);
    }

    // 计算注数
    public function count($sCodes)
    {
        return 1;
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
    public function assertLevel($levelId, $sBetCode, Array $aOpenNumbers)
    {
        // 和值：投注号码与当期开奖号码的三个号码的和值相符，即中奖。
        $sum = array_sum($aOpenNumbers);

        $allowedCodeArr = self::$ls[$levelId];
        if(isset($allowedCodeArr[$sum])) {
            if ($sBetCode == $sum) {
                return 1;
            }
        }
        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $betCode = $sCodes;

        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    $sum = $a + $b + $c;
                    if ($betCode == $sum) {
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
