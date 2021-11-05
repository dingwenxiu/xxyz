<?php namespace App\Lib\Game\Method\Ssc\COZH;

use App\Lib\Game\Method\Ssc\Base;

// 大小单双
class CO_ZHDXDS extends Base
{
    public $totalCount  = 4;
    public static $dxds = array(
        'b' => '大',
        's' => '小',
        'o' => '单',
        'e' => '双',
    );

    // 格式解析
    public function codeChange($code)
    {
        return isset(self::$dxds[$code]) ? self::$dxds[$code] : $code;
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

        $number = intval($numbers[0] + $numbers[1] + $numbers[2] + $numbers[3] + $numbers[4]);

        if ($sCode == 'b') {
            return $number > 22;
        } else if ($sCode == 's') {
            return $number <= 22;
        } else if ($sCode == 'o') {
            return $number % 2 > 0;
        } else if ($sCode == 'e') {
            return $number % 2 == 0;
        }

        return 0;
    }

    // 控水处理
    public function doControl($data, $sCode, $prizes)
    {
        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            $number = $w + $q + $b + $s + $g;
                            if ($sCode == 'b' && $number > 22) {
                                $data[$w . $q . $b . $s . $g] += $prizes[1];
                            } else if ($sCode == 's' && $number <= 22) {
                                $data[$w . $q . $b . $s . $g] += $prizes[1];
                            } else if ($sCode == 'o' && $number % 2 > 0) {
                                $data[$w . $q . $b . $s . $g] += $prizes[1];
                            } else if ($sCode == 'e' && $number % 2 == 0) {
                                $data[$w . $q . $b . $s . $g] += $prizes[1];
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
}
