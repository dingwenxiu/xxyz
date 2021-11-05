<?php namespace App\Lib\Game\Method\Ssc\WX;

use App\Lib\Game\Method\Ssc\Base;

class ZH5 extends Base
{
    public $allCount = 500000;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $arr = [];
        $rand = rand(1, 10);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, 10);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, 10);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, 10);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, 10);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));

        return implode('|', $arr);
    }

    public function regexp($sCodes)
    {
        $regexp = '/^(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])$/';
        if (!preg_match($regexp, $sCodes)) return false;

        $filterArr = self::$filterArr;

        // 去重
        $sCodes = explode("|", $sCodes);
        foreach ($sCodes as $codes) {
            $temp = explode('&', $codes);
            if (count($temp) != count(array_filter(array_unique($temp), function ($v) use ($filterArr) {
                    return isset($filterArr[$v]);
                }))) return false;

            if (count($temp) == 0) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        //n1*n2*n3*n4*n5*5
        $cnt = 1;
        $temp = explode('|', $sCodes);
        foreach ($temp as $c) {
            $cnt *= count(explode('&', $c));
        }

        $cnt *= 5;

        return $cnt;
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

    // 判定中奖
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $aCodes = explode('|', $sBetCodes);

        if ($levelId == '1') {
            $preg = "/[" . str_replace('&', '', $aCodes[0]) . "][" . str_replace('&', '', $aCodes[1]) . "][" . str_replace('&', '', $aCodes[2]) . "][" . str_replace('&', '', $aCodes[3]) . "][" . str_replace('&', '', $aCodes[4]) . "]/";
            if (preg_match($preg, implode("", $aOpenNumbers))) {
                return 1;
            }
        } elseif ($levelId == '2') {
            $preg = "/[" . str_replace('&', '', $aCodes[1]) . "][" . str_replace('&', '', $aCodes[2]) . "][" . str_replace('&', '', $aCodes[3]) . "][" . str_replace('&', '', $aCodes[4]) . "]/";
            if (preg_match($preg, implode("", $aOpenNumbers))) {
                $times = count(explode('&', $aCodes[0]));
                return $times;
            }
        } elseif ($levelId == '3') {
            $preg = "/[" . str_replace('&', '', $aCodes[2]) . "][" . str_replace('&', '', $aCodes[3]) . "][" . str_replace('&', '', $aCodes[4]) . "]/";
            if (preg_match($preg, implode("", $aOpenNumbers))) {
                $times = count(explode('&', $aCodes[0])) * count(explode('&', $aCodes[1]));
                return $times;
            }
        } elseif ($levelId == '4') {
            $preg = "/[" . str_replace('&', '', $aCodes[3]) . "][" . str_replace('&', '', $aCodes[4]) . "]/";
            if (preg_match($preg, implode("", $aOpenNumbers))) {
                $times = count(explode('&', $aCodes[0])) * count(explode('&', $aCodes[1])) * count(explode('&', $aCodes[2]));
                return $times;
            }
        } elseif ($levelId == '5') {
            $preg = "/[" . str_replace('&', '', $aCodes[4]) . "]/";
            if (preg_match($preg, implode("", $aOpenNumbers))) {
                $times = count(explode('&', $aCodes[0])) * count(explode('&', $aCodes[1])) * count(explode('&', $aCodes[2])) * count(explode('&', $aCodes[3]));
                return $times;
            }
        }

    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $codes  = explode('|', $sCodes);
        $pcnt   = [];

        foreach($codes as $k => $code){
            $_code      = explode('&', $code);
            $codes[$k]  = $_code;
            $pcnt[$k]   = count($_code);
        }

        $times  = $pcnt[0] * $pcnt[1] * $pcnt[2] * $pcnt[3] ;
        $p5     = $times * $prizes[5];

        $times  = $pcnt[0] * $pcnt[1] * $pcnt[2];
        $p4     = $times * $prizes[4];

        $times  = $pcnt[0] * $pcnt[1];
        $p3     = $times * $prizes[3];

        $times  = $pcnt[0];
        $p2     = $times * $prizes[2];

        $times  = 1;
        $p1     = $times * $prizes[1];

        $prizes[1]  = $p1;
        $prizes[2]  = $p2;
        $prizes[3]  = $p3;
        $prizes[4]  = $p4;
        $prizes[5]  = $p5;

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($codes[0] as $w) {
            foreach ($codes[1] as $q) {
                foreach ($codes[2] as $b) {
                    foreach ($codes[3] as $s) {
                        foreach ($codes[4] as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $p1, 4);
                            } else {
                                $data[$key] = $p1;
                            }
                        }
                    }
                }
            }
        }

        foreach ($tmp as $w) {
            foreach ($codes[1] as $q) {
                foreach ($codes[2] as $b) {
                    foreach ($codes[3] as $s) {
                        foreach ($codes[4] as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $p2, 4);
                            } else {
                                $data[$key] = $p2;
                            }
                        }
                    }
                }
            }
        }

        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($codes[2] as $b) {
                    foreach ($codes[3] as $s) {
                        foreach ($codes[4] as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $p3, 4);
                            } else {
                                $data[$key] = $p3;
                            }
                        }
                    }
                }
            }
        }

        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($codes[3] as $s) {
                        foreach ($codes[4] as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $p4, 4);
                            } else {
                                $data[$key] = $p4;
                            }
                        }
                    }
                }
            }
        }

        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($tmp as $s) {
                        foreach ($codes[4] as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $p5, 4);
                            } else {
                                $data[$key] = $p5;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

}
