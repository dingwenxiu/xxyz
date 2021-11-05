<?php namespace App\Lib\Game\Method\Ssc\EX;

use App\Lib\Game\Method\Ssc\Base;

class QZX2_S extends Base
{
    // 12,23,43,23,12,12,
    public $allCount = 100;
    public static $filterArr = array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1);

    public function regexp($sCodes)
    {
        // 重复号码
        $temp = explode(",", $sCodes);
        $i    = count(array_filter(array_unique($temp)));

        if($i != count($temp)) {
            return false;
        }

        foreach ($temp as $oneCode) {
            $oneCodeArr = str_split($oneCode);

            // 长度
            if (count($oneCodeArr) != 2) {
                return false;
            }

            // 每个号码
            foreach ($oneCodeArr as $code) {
                if (!isset(self::$filterArr[$code])) {
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
        $str = implode('', $numbers);
        $exists = array_flip(explode(',', $sCodes));
        return intval(isset($exists[$str]));
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode(',', $sCodes);
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($aCodes as $code) {
            foreach ($tmp as $b) {
                foreach ($tmp as $s) {
                    foreach ($tmp as $g) {
                        $key = $code . $b . $s . $g;
                        if (isset($data[$key])) {
                            $data[$key] += $prizes[1];
                        } else {
                            $data[$key] = $prizes[1];
                        }
                    }
                }
            }
        }

        return $data;
    }
}
