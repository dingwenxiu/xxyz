<?php namespace App\Lib\Game\Method\Klsf\EX;

use App\Lib\Game\Method\Klsf\Base;

// 快乐10分 前2组选2
class KLSF_Q_ZU2 extends Base
{

    public static $filterArr = [
        "01" => 1,
        "02" => 1,
        "03" => 1,
        "04" => 1,
        "05" => 1,
        "06" => 1,
        "07" => 1,
        "08" => 1,
        "09" => 1,
        "10" => 1,
        "11" => 1,
        "12" => 1,
        "13" => 1,
        "14" => 1,
        "15" => 1,
        "16" => 1,
        "17" => 1,
        "18" => 1,
        "19" => 1,
        "20" => 1,
    ];

    public function regexp($sCodes)
    {
        // 格式
        $_aCodeArr  = explode("&", $sCodes);
        $aCodeArr   = array_unique($_aCodeArr, SORT_STRING);

        // 不能重复
        if (count($_aCodeArr) != count($aCodeArr)) {
            return false;
        }

        // 长度
        if (count($aCodeArr) < 2 || count($aCodeArr) > 20) {
            return false;
        }

        // 号码检测
        foreach ($aCodeArr as $code) {
            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        // C(n,3)
        $n = count(explode("&", $sCodes));

        return $this->getCombinCount($n,2);
    }

    public function bingoCode(Array $numbers)
    {
        $exists = array_flip($numbers);
        $arr    = array_keys(self::$filterArr);
        $result = [];

        foreach($arr as $pos => $_code) {
            $result[] = intval(isset($exists[$_code]));
        }

        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $aOpenNumbers)
    {
        // 组装number
        $winNumber = [
            0 => [$aOpenNumbers[0] , $aOpenNumbers[1]],
            1 => [$aOpenNumbers[1] , $aOpenNumbers[2]],
            2 => [$aOpenNumbers[2] , $aOpenNumbers[3]],
            3 => [$aOpenNumbers[3] , $aOpenNumbers[4]],
            4 => [$aOpenNumbers[4] , $aOpenNumbers[5]],
            5 => [$aOpenNumbers[5] , $aOpenNumbers[6]],
            6 => [$aOpenNumbers[6] , $aOpenNumbers[7]],
        ];

        info("zu2------", $winNumber);

        $aCodes     = explode('&', $sCodes);

        $count = 0;
        foreach ($winNumber as $code) {
            $sameCode = array_intersect($code, $aCodes);
            if (count($sameCode) == 2) {
                $count ++;
            }
        }


        return $count;
    }

}
