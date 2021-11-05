<?php namespace App\Lib\Game\Method\Ssc\Q3;

use App\Lib\Game\Method\Ssc\Base;

//组选三

class QZU3 extends Base
{
    //1&2&3&4&5&6
    public $allCount = 90;
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

    public function regexp($sCodes)
    {
        // 去重
        $_codeArr       = explode("&", $sCodes);
        $codeArr        = array_unique($_codeArr);

        // 存在重复
        if (count($_codeArr) != count($codeArr)) {
            return false;
        }

        // 长度
        if (count($codeArr) < 2 || count($codeArr) > 10 ) {
            return false;
        }

        // 数字
        foreach ($codeArr as $code) {
            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        $n = count(array_unique(explode("&", $sCodes)));
        return $this->getCombinCount($n,2) * 2;
    }

    public function bingoCode(Array $numbers)
    {
        // 有对子
        if(count(array_count_values($numbers))!=2) return [array_fill(0, count(self::$filterArr),0)];

        $exists = array_flip($numbers);
        $arr    = array_keys(self::$filterArr);
        $result = [];
        foreach($arr as $pos => $_code) {
            $result[$pos] = intval(isset($exists[$_code]));
        }

        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $flip = array_filter(array_count_values($numbers), function ($v) {
            return $v == 2;
        });

        if (count($flip) == 1) {
            $preg = "|[" . str_replace('&', '', $sCodes) . "]{3}|";
            if (preg_match($preg, implode("", $numbers))) {
                return 1;
            }
        }

    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $allCodeArr = $this->unpPackZu3($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($allCodeArr as $_code => $val) {
            foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    if (isset($data[$_code.$s.$g])) {
                        $data[$_code.$s.$g] = bcadd($data[$_code.$s.$g], $prizes[1], 4);
                    } else {
                        $data[$_code.$s.$g] = $prizes[1];
                    }
                }
            }
        }

        return $data;
    }
}
