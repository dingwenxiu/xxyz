<?php namespace App\Lib\Game\Method\Ssc\DWD;

use App\Lib\Game\Method\Ssc\BaseDWD;
use App\Lib\Game\Method\Ssc\Base;

class DWD_Q extends Base
{
    use BaseDWD;

    public $allCount = 10;

    public static $filterArr = array(
        0 => 1,
        1 => 1,
        2 => 1,
        3 => 1,
        4 => 1,
        5 => 1,
        6 => 1,
        7 => 1,
        8 => 1,
        9 => 1
    );

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand   = rand(1,10);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($codes)
    {
        return implode('&',explode('|',$codes));
    }

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = array_keys(self::$filterArr);

        foreach($numbers as $pos => $code) {
            $tmp = [];
            foreach($arr as $_code) {
                $tmp[] = intval($code == $_code);
            }
            $result[$pos] = $tmp;
        }

        return $result;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $codes      = explode('&', $sCodes);

        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($codes as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $prizes[1], 4);
                            } else {
                                $data[$key] = $prizes[1];
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}
