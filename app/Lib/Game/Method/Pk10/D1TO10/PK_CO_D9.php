<?php namespace App\Lib\Game\Method\Pk10\D1TO10;

//　猜冠军
use App\Lib\Game\Method\Pk10\Base;

$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

/**
 * 猜第9
 * Class PK_CO_D9
 * @package App\Lib\Game\Method\Pk10\D1TO10
 */
class PK_CO_D9 extends Base
{
    public static $filterArr = [
        '01' => 1,
        '02' => 1,
        '03' => 1,
        '04' => 1,
        '05' => 1,
        '06' => 1,
        '07' => 1,
        '08' => 1,
        '09' => 1,
        '10' => 1
    ];

    public function regexp($sCode)
    {
        if (!isset(self::$filterArr[$sCode])) {
            return false;
        }

        return true;
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
        if ($aOpenNumbers[0] == $sCode) {
            return 1;
        }

        return 0;
    }
}
