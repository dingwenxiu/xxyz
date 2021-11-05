<?php namespace App\Lib\Game\Method\Ssc;

// 娱乐成
class BaseCoBsoe extends Base
{
    public $totalCount  = 2;
    public static $dxds = array(
        'b' => '大',
        's' => '小',
        'o' => '单',
        'e' => '双',
    );

    // 格式解析
    public function resolve($codes)
    {
        return strtr($codes, array_flip(self::$dxds));
    }

    // 还原格式
    public function unresolve($codes)
    {
        return strtr($codes, self::$dxds);
    }

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$dxds);
    }

    // 投注格式是否正确
    public function regexp($sCode)
    {
        if (!array_key_exists($sCode, self::$dxds)) {
            return false;
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        return 1;
    }

    public function bingoCode(Array $numbers)
    {
        return [];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCode, Array $numbers)
    {

        $number = intval($numbers[0]);
        $i = 0;
        if ($sCode == 'b') {
            $i = $number > 4;
        } else if ($sCode == 's') {
            $i = $number <= 4;
        } else if ($sCode == 'o') {
            $i = $number % 2 > 0;
        } else if ($sCode == 'e') {
            $i = $number % 2 == 0;
        }

        return intval($i);
    }
}
