<?php namespace App\Lib\Game\Method\Klsf\SX;

use App\Lib\Game\Method\Klsf\Base;

// 快乐10分 前3组选3
class KLSF_Q_ZU3 extends Base
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
        if (count($aCodeArr) < 3 || count($aCodeArr) > 20) {
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

        return $this->getCombinCount($n,3);
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
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $aCodes     = $this->convertKlsfCodes($sCodes);
        $numbers    = $this->convertKlsfCodes($numbers);

        if ($numbers[0] != $numbers[1] && $numbers[1] != $numbers[2]  && $numbers[0] != $numbers[2]) {
            $preg = "|[" . str_replace('&', '', $aCodes) . "]{3}|";
            if (preg_match($preg, implode("", $numbers))) {
                return 1;
            }
        }
    }

}
