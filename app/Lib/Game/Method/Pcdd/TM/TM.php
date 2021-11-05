<?php namespace App\Lib\Game\Method\Pcdd\TM;

use App\Lib\Game\Method\Pcdd\Base;

// 特码
class TM extends Base
{
    public $totalCount          = 1000;
    public static $filterArr    = [
        0  =>   1, 1  =>    1,  2   =>  1,  3   =>  1,  4   =>  1,  5   =>  1,  6   =>  1,  7  =>   1,  8  =>  1,  9   =>  1,  10  => 1,
        11 =>   1, 12 =>    1, 13   =>  1,  14  =>  1, 15   =>  1, 16   =>  1, 17   =>  1,  18 =>   1, 19  =>  1, 20   =>  1,
        21 =>   1, 22 =>    1, 23   =>  1, 24   =>  1, 25   =>  1, 26   =>  1, 27   =>  1
    ];

    // 展开
    public function expand($sCode, $pos = null)
    {
        $methodId = "T" . $sCode;
        $result[]   = array(
            'method_sign' => $methodId,
            'codes'     => $sCode,
            'count'     => 1,
        );
        return $result;
    }

    // 是否复式
    public function isMulti()
    {
        return true;
    }

    /**
     * 转成数组形式
     * @param $code
     * @return array
     */
    public function transferCodeToArray($code)
    {
        return [$code];
    }


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

    // 判定中奖
    public function assertLevel($levelId, $sCode, Array $aOpenNumbers)
    {
        $count = $aOpenNumbers[0] + $aOpenNumbers[1] +  $aOpenNumbers[2];

        if ($sCode == $count) {
            if ($sCode == ($levelId - 1)) {
                return 1;
            }
        }

        return 0;
    }
}
