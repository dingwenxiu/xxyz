<?php namespace App\Lib\Game\Method\Ssc\ZX1;;

class ZX1_W extends ZX1
{

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $codes      = explode(',', $sCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        // 累加
        foreach ($codes as $w) {
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
