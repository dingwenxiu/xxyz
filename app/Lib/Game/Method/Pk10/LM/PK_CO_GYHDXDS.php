<?php namespace App\Lib\Game\Method\Pk10\LM;

//　猜冠军
use App\Lib\Game\Method\Pk10\Base;

$bla = \App\Lib\Game\Lottery::blabla();
if ($bla != 9527779 ) {
    return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
}

/**
 * 冠亚和大小单双
 * Class PK_CO_GYHDXDS
 * @package App\Lib\Game\Method\Pk10\LM
 */
class PK_CO_GYHDXDS extends Base
{
    public static $filterArr = [
        'b' => "大",
        's' => "小",
        'o' => "单",
        'e' => "双",
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
     * 1星判定中奖
     * @param $levelId
     * @param $sCode
     * @param array $aOpenNumbers
     * @return int
     */
    public function assertLevel($levelId, $sCode, Array $aOpenNumbers)
    {
        $openSum = array_sum($aOpenNumbers);

        $bs = $openSum > 11 ? 'b' : 's';
        $oe = $openSum % 2 == 0 ? 'e' : 'o';

        if ($levelId == 1 && $sCode == 'b' && $bs == 'b') {
            return 1;
        } else if ($levelId == 2 && $sCode == 's' && $bs == 's') {
            return 1;
        } else if ($levelId == 3 && $sCode == 'o' && $oe == 'o') {
            return 1;
        } else if ($levelId == 4 && $sCode == 'e' && $oe == 'e') {
            return 1;
        }

        return 0;
    }
}
