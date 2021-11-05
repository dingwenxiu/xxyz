<?php namespace App\Lib\Game\Method\Ssc\WX;

use App\Lib\Game\Method\Ssc\Base;

/**
 * tom 2019
 * 直选5星
 * Class ZX5
 * @package App\Lib\Game\Method\Ssc\WX
 */
class ZX5 extends Base
{
    public $allCount = 100000;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $arr = [];
        $cnt = count(self::$filterArr);
        $rand = rand(1, $cnt);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, $cnt);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, $cnt);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, $cnt);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand = rand(1, $cnt);
        $arr[] = implode('&', (array)array_rand(self::$filterArr, $rand));

        return implode('|', $arr);
    }

    // 号码检测
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
        // n1*n2*n3*n4*n5
        $cnt = 1;
        $temp = explode('|', $sCodes);

        foreach ($temp as $c) {
            $cnt *= count(explode('&', $c));
        }

        return $cnt;
    }

    // 冷热遗漏
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

    // 计算奖金
    public function assertLevel($levelId, $sBetCodes, Array $aOpenCodes)
    {
        $aCodes = explode('|', $sBetCodes);
        $preg   = "/[" . str_replace('&', '', $aCodes[0]) . "][" . str_replace('&', '', $aCodes[1]) . "][" . str_replace('&', '', $aCodes[2]) . "][" . str_replace('&', '', $aCodes[3]) . "][" . str_replace('&', '', $aCodes[4]) . "]/";

        $sOpenCodes = implode("", $aOpenCodes);

        if (preg_match($preg, $sOpenCodes)) {
            return 1;
        }

        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $codes      = explode('|', $sCodes);
        $codeArr = [];
        foreach ($codes as $index =>  $rowCodeStr) {
            $codeArr[$index] = explode("&", $rowCodeStr);
        }

        // 累加
        foreach ($codeArr[0] as $w) {
            foreach ($codeArr[1] as $q) {
                foreach ($codeArr[2] as $b) {
                    foreach ($codeArr[3] as $s) {
                        foreach ($codeArr[4] as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $prizes[1], 4);
                            } else {
                                $data[$key] = $prizes[1];
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

}
