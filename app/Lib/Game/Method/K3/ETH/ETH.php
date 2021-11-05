<?php namespace App\Lib\Game\Method\K3\ETH;

use App\Lib\Game\Method\K3\Base;

// 二同号
class ETH extends Base
{
    public static $filterArr = [
        "112", "113", "114", "115", "116", "122", "223", "224", "225", "226", "133", "233", "334", "335", "336",
        "144", "244", "344", "445", "446", "155", "255", "355", "455", "556", "166", "266", "366", "466", "566"
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $cnt    = count(self::$filterArr);
        $rand   = rand(1, $cnt-1);

        $temp   = (array)array_rand(self::$filterArr, $rand);
        return implode('&', $temp);
    }

    // 判定格式
    public function regexp($sCodes)
    {
        $aCode = explode('|', $sCodes);

        if(count($aCode) != count(array_unique($aCode))) {
            return false;
        }

        foreach ($aCode as $code) {
            // 是数字
            if (preg_match('/^\d{3}$/', $code) !== 1) {
                return false;
            }

            if (!in_array($code, self::$filterArr)) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        $aCodes = explode('|', $sCodes);
        return count(array_unique($aCodes));
    }

    public function bingoCode(Array $numbers)
    {
        // 必须有相同号
        $counts = array_count_values($numbers);

        $tmp    = array_fill(0,count(self::$filterArr),0);
        if(count($counts) != 2) return [$tmp,$tmp];

        $arr = array_keys(self::$filterArr);

        $result = [];
        // 同号
        $t = [];
        foreach($arr as $code){
            $t[]    = intval(isset($counts[$code]) && $counts[$code] == 2);
        }

        $result[]   = $t;
        // 不同号
        $bt = [];
        foreach($arr as $code) {
            $bt[] = intval(isset($counts[$code]) && $counts[$code] == 1);
        }
        $result[] = $bt;

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $aOpenNumbers)
    {
        $aCodes = explode('|', $sCodes);
        foreach ($aCodes as $code) {
            $code = $this->strOrder($code);
            if (
                ($aOpenNumbers[0] == $aOpenNumbers[1] && $aOpenNumbers[0] != $aOpenNumbers[2]) ||
                ($aOpenNumbers[0] == $aOpenNumbers[2] && $aOpenNumbers[0] != $aOpenNumbers[1]) ||
                ($aOpenNumbers[1] == $aOpenNumbers[2] && $aOpenNumbers[1] != $aOpenNumbers[0])
            ) {
                $openStr = $this->strOrder(implode('', $aOpenNumbers));
                if ($openStr == $code) {
                    return 1;
                }
            }
        }
        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes     = explode('|', $sCodes);

        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    // 豹子号去除
                    if ($a == $b && $b == $c) {
                        continue;
                    }

                    // 三同号去除
                    if ($a != $b && $b != $c) {
                        continue;
                    }

                    $_code = $this->strOrder($a . $b . $c);

                    if (isset($aCodes[$_code])) {
                        $codeStr = $a . $b . $c;
                        if (isset($data[$codeStr])) {
                            $data[$codeStr] = bcadd($data[$codeStr], $prizes[1], 4);
                        } else {
                            $data[$codeStr] = $prizes[1];
                        }
                    }
                }
            }
        }

        return $data;
    }
}
