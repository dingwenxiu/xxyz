<?php namespace App\Lib\Game\Method\Ssc\COD1Q;

use App\Lib\Game\Method\Ssc\BaseCoBsoe;
class CO_ZX_W_DXDS extends BaseCoBsoe
{
    static $codeArr = [
        'b' => [5, 6, 7, 8, 9],
        's' => [0, 1, 2, 3, 4],
        'o' => [1, 3, 5, 7, 9],
        'e' => [0, 2, 4, 6, 8],
    ];

    // 控水处理
    public function doControl($data, $sCode, $prizes)
    {
        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $codeArr = self::$codeArr[$sCode];
        foreach ($codeArr as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            $data[$w . $q . $b . $s . $g] += $prizes[1];
                        }
                    }
                }
            }
        }
        return $data;
    }
}
