<?php namespace App\Lib\Game\Method\Ssc;

class BaseCoZx1 extends Base
{
    public $totalCount = 10;

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
        return true;
    }

    public function count($sCodes)
    {
        return 1;
    }

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = array_keys(self::$filterArr);

        foreach ($numbers as $pos => $code) {
            $tmp = [];
            foreach ($arr as $_code) {
                $tmp[] = intval($code == $_code);
            }
            $result[$pos] = $tmp;
        }

        return $result;
    }

    // 判断中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $openName = $numbers[0];

        if ($openName == $sCodes) {
            return 1;
        }

        return 0;
    }
}
