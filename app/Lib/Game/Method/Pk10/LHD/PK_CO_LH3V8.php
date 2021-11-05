<?php namespace App\Lib\Game\Method\Pk10\LHD;

//　猜冠军
use App\Lib\Game\Method\Pk10\Base;

$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

/**
 * LH3V8
 * Class PK_CO_LH3V8
 * @package App\Lib\Game\Method\Pk10\LHD
 */
class PK_CO_LH3V8 extends Base
{
    public static $filterArr = [
        '1'     => "龙",
        '2'     => "虎",
    ];

    public function regexp($sCode)
    {
        if (!isset(self::$filterArr[$sCode])) {
            return false;
        }

        return true;
    }

    // 格式解析
    public function codeChange($code)
    {
        return isset(self::$filterArr[$code]) ? self::$filterArr[$code] : $code;
    }

    public function count($sCodes)
    {
        return 1;
    }

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr = array_keys(self::$filterArr);

        foreach ($numbers as $pos => $code) {
            $tmp = [];
            foreach ($arr as $_code) {
                $tmp[] = intval($code == $_code);
            }
            $result[$pos] = $tmp;
        }

        return $result;
    }

    /**
     * 一样才中奖
     * @param $levelId
     * @param $sCode
     * @param array $aOpenNumbers
     * @return int
     */
    public function assertLevel($levelId, $sCode, Array $aOpenNumbers)
    {
        if ($aOpenNumbers[0] > $aOpenNumbers[1] && $sCode == 1) {
            return 1;
        }

        if ($aOpenNumbers[0] < $aOpenNumbers[1] && $sCode == 2) {
            return 1;
        }

        return 0;
    }
}
