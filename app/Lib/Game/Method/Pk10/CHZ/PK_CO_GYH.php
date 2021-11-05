<?php namespace App\Lib\Game\Method\Pk10\CHZ;

//　猜冠军
use App\Lib\Game\Method\Pk10\Base;

/**
 * 冠亚和
 * Class PK_CO_GYH
 * @package App\Lib\Game\Method\Pk10\CHZ
 */
class PK_CO_GYH extends Base
{
    public static $filterArr = [
        '3'     => 1,
        '4'     => 1,
        '5'     => 1,
        '6'     => 1,
        '7'     => 1,
        '8'     => 1,
        '9'     => 1,
        '10'    => 1,
        '11'    => 1,
        '12'    => 1,
        '13'    => 1,
        '14'    => 1,
        '15'    => 1,
        '16'    => 1,
        '17'    => 1,
        '18'    => 1,
        '19'    => 1,
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
     * 和值
     * @param $levelId
     * @param $sCode
     * @param array $aOpenNumbers
     * @return int
     */
    public function assertLevel($levelId, $sCode, Array $aOpenNumbers)
    {
        $openSum = array_sum($aOpenNumbers);
        if ($levelId == $openSum && $sCode == $openSum) {
            return 1;
        }

        return 0;
    }
}
