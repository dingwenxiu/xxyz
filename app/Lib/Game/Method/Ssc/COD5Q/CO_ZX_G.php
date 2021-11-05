<?php namespace App\Lib\Game\Method\Ssc\COD5Q;

use App\Lib\Game\Method\Ssc\BaseCoZx1;

class CO_ZX_G extends BaseCoZx1
{
    // 控水处理
    public function doControl($data, $sCode, $prizes)
    {
        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        $data[$w . $q . $b . $s . $sCode] += $prizes[1];
                    }
                }
            }
        }
        return $data;
    }
}
