<?php namespace App\Lib\Game\Method\Ssc\Q3;

use App\Lib\Game\Method\Ssc\Base;

class QZXKD extends Base
{
    public $allCount = 1000;
    public static $filterArr = array(0 => 10, 1 => 54, 2 => 96, 3 => 126, 4 => 144, 5 => 150, 6 => 144, 7 => 126, 8 => 96, 9 => 54);

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand   = rand(1, count(self::$filterArr));
        return implode('&',(array)array_rand(self::$filterArr, $rand));
    }

    public function fromOld($codes)
    {
        return implode('&', explode('|',$codes));
    }

    /**
     * 判定格式
     * @param $sCodes
     * @return bool
     */
    public function regexp($sCodes)
    {
        // 去重
        $t      = explode("&", $sCodes);
        $temp   = array_unique($t);
        $arr    = self::$filterArr;

        $temp = array_filter($temp,function($v) use ($arr) {
            return isset($arr[$v]);
        });

        if(count($temp) == 0) {
            return false;
        }

        return count($temp) == count($t);
    }

    public function count($sCodes)
    {
        // 枚举之和
        $n = 0;
        $temp = explode('&', $sCodes);
        foreach($temp as $c){
            $n += self::$filterArr[$c];
        }

        return $n;
    }

    public function bingoCode(Array $numbers)
    {
        sort($numbers);
        $val=$numbers[2]-$numbers[0];
        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $_code){
            $result[]=intval($_code == $val);
        }
        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        sort($numbers);
        $min = array_shift($numbers);
        $max = array_pop($numbers);
        $val = $max - $min;

        $aCodes = explode('&', $sCodes);

        foreach ($aCodes as $code) {
            if ($code == $val) {
                return 1;
            }
        }

        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $exists = array_flip($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    // 和值必须存在
                    $kd = $this->getKd([$w, $q, $b]);
                    if(!isset($exists[$kd])) {
                        continue;
                    }

                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            $key = $w . $q . $b . $s. $g;
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
