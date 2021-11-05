<?php namespace App\Lib\Game\Method\Ssc\LH;

use App\Lib\Game\Method\Ssc\Base;

/**
 * tom 2019
 * 龙虎　千百
 * Class LHQB
 * @package App\Lib\Game\Method\Ssc\LH
 */
class LHQB extends Base
{
    public $allCount = 3;
    public static $filterArr = [
        1 => "龙",
        2 => "虎",
        3 => "和"
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $code = "";
        $code   .= rand(1,3);
        $code   .= "&" . rand(1,3);
        $code   .= "&" . rand(1,3);

        return $code;
    }

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$filterArr);
    }

    // 检测号码
    public function regexp($sCodes)
    {
        $aCodes = explode('&', $sCodes);

        foreach ($aCodes as $code) {
            $code = intval($code);
            if (!array_key_exists($code, self::$filterArr)) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        $temp = explode('&', $sCodes);
        $temp = array_unique($temp);
        return count($temp);
    }

    // 冷热遗漏
    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = array_keys(self::$filterArr);

        foreach($numbers as $pos => $code){
            $tmp = [];
            foreach($arr as $_code){
                $tmp[] = intval($code==$_code);
            }
            $result[$pos]=$tmp;
        }

        return $result;
    }

    // 开奖
    public function assertLevel($levelId, $sBetCode, Array $aOpenCodes)
    {
        $aBetCodes = explode('&', $sBetCode);
        $q = $aOpenCodes[0];
        $b = $aOpenCodes[1];

        $count = 0;

        // 龙
        if ($levelId == 1 && $q > $b && in_array(1, $aBetCodes)) {
            $count = 1;
        }

        // 虎
        if ($levelId == 3 && $q == $b && in_array(3, $aBetCodes)) {
            $count = 1;
        }

        // 和
        if ($levelId == 2 && $q < $b && in_array(2, $aBetCodes)) {
            $count = 1;
        }

        return $count;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $codeArr        = explode('&', $sCodes);

        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($tmp as $q) {
            foreach ($tmp as $b) {
                foreach ($tmp as $w) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            if ($q > $b) {
                                if (in_array(1, $codeArr)) {
                                    $prize = $prizes[1];
                                } else {
                                    continue;
                                }
                            } else if ($q < $b){
                                if (in_array(2, $codeArr)) {
                                    $prize = $prizes[2];
                                } else {
                                    continue;
                                }
                            } else {
                                if (in_array(3, $codeArr)) {
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
