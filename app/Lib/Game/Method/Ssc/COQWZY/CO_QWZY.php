<?php namespace App\Lib\Game\Method\Ssc\COQWZY;

use App\Lib\Game\Method\Ssc\Base;

class CO_QWZY extends Base
{
    public $totalCount = 100000;
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
        $regexp = '/^[0-9]$/';
        if (!preg_match($regexp, $sCodes)) return false;

        $filterArr = self::$filterArr;
        return isset($filterArr[$sCodes]);
    }

    public function count($sCodes)
    {
        return 1;
    }

    public function bingoCode(Array $numbers)
    {
        return [];
    }

    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $exists = array_flip($numbers);
        if (isset($exists[$sCodes])) {
            return 1;
        }
        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $aCodes = $this->getCombination($aCodes, 2);

        $exists = array_flip($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            if(!isset($exists[$q]) && !isset($exists[$b]) && !isset($exists[$s]) && !isset($exists[$g]) && !isset($exists[$w])) {
                                continue;
                            }

                            $key = $w . $q . $b . $s. $g;
                            if (isset($data[$key])) {
                                $data[$key] += $prizes[1];
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
