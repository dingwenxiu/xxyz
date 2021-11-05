<?php namespace App\Lib\Game\Method\Ssc\COLH;

use App\Lib\Game\Method\Ssc\BaseCoLh;

class CO_LHWQ extends BaseCoLh
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
                        foreach ($tmp as $g) {
                            if ($w > $q) {
                                if ($sCode == 1) {
                                    $prize = $prizes[1];
                                } else {
                                    continue;
                                }
                            } else if ($w < $q){
                                if ($sCode == 2) {
                                    $prize = $prizes[2];
                                } else {
                                    continue;
                                }
                            } else {
                                if ($sCode == 3) {
                                    $prize = $prizes[3];
                                } else {
                                    continue;
                                }
                            }

                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] += $prize;
                            } else {
                                $data[$key] = $prize;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}
