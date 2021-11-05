<?php namespace App\Lib\Game\Method\Ssc\Q3;

use App\Lib\Game\Method\Ssc\Base;

// 前组合3
class QZH3 extends Base
{
    // 0&1&2&3&4&5&6&7&8&9|0&1&2&3&4&5&6&7&8&9|0&1&2&3&4&5&6&7&8&9
    public $allCount = 3000;
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

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $arr    = [];
        $rand   = rand(1, 10);
        $arr[]  = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand   = rand(1, 10);
        $arr[]  = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand   = rand(1, 10);
        $arr[]  = implode('&', (array)array_rand(self::$filterArr, $rand));

        return implode('|', $arr);
    }

    public function regexp($sCodes)
    {
        $regexp = '/^(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])\|(([0-9]&){0,9}[0-9])$/';
        if( !preg_match($regexp, $sCodes) ) return false;

        $filterArr = self::$filterArr;

        // 去重
        $sCodes = explode("|", $sCodes);
        foreach($sCodes as $codes) {
            $temp = explode('&', $codes);
            if(count($temp) != count(array_filter(array_unique($temp), function($v) use($filterArr) {
                    return isset($filterArr[$v]);
                }))) return false;

            if(count($temp) == 0) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        //n1*n2*n3*3
        $cnt    = 1;
        $temp   = explode('|', $sCodes);
        foreach($temp as $c) {
            $cnt *= count(explode('&',$c));
        }

        $cnt *= 3;

        return $cnt;
    }

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = array_keys(self::$filterArr);

        foreach($numbers as $pos => $code) {
            $tmp = [];
            foreach($arr as $_code) {
                $tmp[] = intval($code == $_code);
            }
            $result[$pos] = $tmp;
        }

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {

        $aCodes = explode('|', $sCodes);

        if ($levelId == '1') {
            $preg = "|[" . str_replace('&', '', $aCodes[0]) . "][" . str_replace('&', '', $aCodes[1]) . "][" . str_replace('&', '', $aCodes[2]) . "]|";
            if (preg_match($preg, implode("", $numbers))) {
                return 1;
            }
        } elseif ($levelId == '2') {
            $preg = "|[" . str_replace('&', '', $aCodes[1]) . "][" . str_replace('&', '', $aCodes[2]) . "]|";
            if (preg_match($preg, implode("", $numbers))) {
                $times = count(explode('&',$aCodes[0]));
                return $times;
            }
        } elseif ($levelId == '3') {
            $preg = "|[" . str_replace('&', '', $aCodes[2]) . "]|";
            if (preg_match($preg, implode("", $numbers))) {
                $times = count(explode('&',$aCodes[0])) * count(explode('&',$aCodes[1]));
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

        $times  = $pcnt[0] * $pcnt[1];
        $p3     = $times * $prizes[3];

        $times  = $pcnt[0];
        $p2     = $times * $prizes[2];

        $times  = 1;
        $p1     = $times * $prizes[1];

        $prizes[1]  = $p1;
        $prizes[2]  = $p2;
        $prizes[3]  = $p3;

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        // 一等奖
        foreach ($codes[0] as $w) {
            foreach ($codes[1] as $q) {
                foreach ($codes[2] as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
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

        // 二等奖
        foreach ($tmp as $w) {
            foreach ($codes[1] as $q) {
                foreach ($codes[2] as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
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

        // 三等奖
        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($codes[2] as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
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

        return $data;
    }
}
