<?php namespace App\Lib\Game\Method\K3\DTYS;

use App\Lib\Game\Method\K3\Base;

// 单挑一筛
class DTYS extends Base
{
    public $allCount = 6;
    public static $filterArr = [1, 2, 3, 4, 5, 6];

    // 供测试用 生成随机投注
    public function randomCodes() {
        $rand = rand(1, 6);
        return $rand;
    }

    // 格式校验
    public function regexp($sCodes)
    {
        $aCode = explode('|', $sCodes);

        if(count($aCode) != count(array_unique($aCode))) {
            return false;
        }

        foreach ($aCode as $code) {
            // 是数字
            if (preg_match('/^\d$/', $code) !== 1) {
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

    // 宾果
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

    /**
     * 判定 中奖注单
     * @param $levelId
     * @param $sCodes
     * @param array $aOpenCodes
     * @return int
     */
    public function assertLevel($levelId, $sCodes, Array $aOpenCodes)
    {
        $_aOpenCodes = array_flip($aOpenCodes);

        // 投注内容
        $aCodes = explode("|", $sCodes);
        $i      = 0;
        $temp   = [];
        foreach ($aCodes as $code) {
            if(isset($temp[$code])) continue;
            if (isset($_aOpenCodes[$code])) {
                $temp[$code]=1;
                $i++;
            }
        }

        return $i;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes     = array_flip(explode('|', $sCodes));

        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    if (isset($aCodes[$a]) || isset($aCodes[$b]) || isset($aCodes[$c])) {
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
