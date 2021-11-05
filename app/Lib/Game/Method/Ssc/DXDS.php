<?php namespace App\Lib\Game\Method\Ssc;

// 大小单双
class DXDS extends Base
{
    public $totalCount  = 16;
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

        if ($sCode == 'b') {
            return $number > 4;
        } else if ($sCode == 's') {
            return $number <= 4;
        } else if ($sCode == 'o') {
            return $number % 2 > 0;
        } else if ($sCode == 'e') {
            return $number % 2 == 0;
        }

        return 0;
    }
}
