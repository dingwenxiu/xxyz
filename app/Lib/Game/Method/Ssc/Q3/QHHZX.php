<?php namespace App\Lib\Game\Method\Ssc\Q3;
use App\Lib\Game\Method\Ssc\Base;

// 前三 混合组选
class QHHZX extends Base
{
    // 123, 531, 534, 534, 123
    public $allCount = 270;
    public static $filterArr = array(
        0 => 1,
        1 => 1,
        2 => 1,
        3 => 1,
        4 => 1,
        5 => 1,
        6 => 1,
        7 => 1,
        8 => 1,
        9 => 1
    );

    public function parse64($codes)
    {
        if(strpos($codes,'base64:') !== false) {
            $ex = explode('base64:', $codes);
            $codes = $this->_parse64($ex[1],3);
            if(is_array($codes)) {
                $codes = implode(',',$codes);
            }
        }
        return $codes;
    }

    public function encode64($codes)
    {
        return $this->_encode64(explode(',', $codes));
    }

    // 检测号码是否合法
    public function regexp($sCodes)
    {
        // 校验
        $regexp = '/^(([0-9]{3}\,)*[0-9]{3})$/';
        if( !preg_match($regexp, $sCodes) ) return false;

        $temp   = explode(",", $sCodes);
        $iNums  = count(array_filter(array_unique($temp)));

        if($iNums != count($temp)) return false;

        // 排除豹子号
        foreach($temp as $c) {
            if($c[0] == $c[1] && $c[1]==$c[2]){
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        return count(array_unique(explode(",", $sCodes)));
    }

    /**
     * 检查本降级是否中奖
     * @param $levelId
     * @param $sCodes
     * @param array $numbers
     * @return int
     */
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str = $this->strOrder(implode('', $numbers));

        $aCode = explode(',', $sCodes);

        if ($levelId == '1') {
            $flip = array_filter(array_count_values($numbers), function ($v) {
                return $v == 2;
            });

            // 组三
            if (count($flip) == 1) {
                foreach ($aCode as $code) {
                    if ($str === $this->strOrder($code)) {
                        return 1;
                    }
                }
            }
        } elseif ($levelId == '2') {
            $flip = array_filter(array_count_values($numbers), function ($v) {
                return $v >= 2;
            });

            // 组六
            if (count($flip) == 0) {
                foreach ($aCode as $code) {
                    if ($str === $this->strOrder($code)) {
                        return 1;
                    }
                }
            }
        }
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes     = explode(',', $sCodes);
        $z3 = $z6   =[];
        foreach($aCodes as $v1) {
            $v1 = str_split($v1);
            // 豹子号
            if($v1[0] == $v1[1] && $v1[1] == $v1[2]) continue;

            if($v1[0] == $v1[1] || $v1[1] == $v1[2] || $v1[0] == $v1[2]) {
                // 组三
                if($v1[0] == $v1[1]) {
                    $n1 = $v1[0];
                    $n2 = $v1[2];
                } elseif($v1[1] == $v1[2]) {
                    $n1 = $v1[1];
                    $n2 = $v1[0];
                } else {
                    $n1 = $v1[0];
                    $n2 = $v1[1];
                }

                $z3[$n1.$n1.$n2]=1;
                $z3[$n1.$n2.$n1]=1;
                $z3[$n2.$n1.$n1]=1;
            } else {
                $z6[$v1[0].$v1[1].$v1[2]] = 1;
                $z6[$v1[0].$v1[2].$v1[1]] = 1;
                $z6[$v1[1].$v1[0].$v1[2]] = 1;
                $z6[$v1[1].$v1[2].$v1[0]] = 1;
                $z6[$v1[2].$v1[0].$v1[1]] = 1;
                $z6[$v1[2].$v1[1].$v1[0]] = 1;
            }
        }

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($z3 as $code => $val) {
            foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    $key = $code.$s.$g;
                    if (isset($data[$key])) {
                        $data[$key]  = bcadd($data[$key], $prizes[1], 4);
                    } else {
                        $data[$key] = $prizes[1];
                    }
                }
            }
        }

        foreach ($z6 as $code => $val) {
            foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    $key = $code.$s.$g;
                    if (isset($data[$key])) {
                        $data[$key]  = bcadd($data[$key], $prizes[2], 4);
                    } else {
                        $data[$key] = $prizes[2];
                    }
                }
            }
        }

        return $data;
    }

}
