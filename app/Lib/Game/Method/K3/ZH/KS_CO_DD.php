<?php namespace App\Lib\Game\Method\K3\ZH;

use App\Lib\Game\Method\K3\Base;

// 单挑一筛
class KS_CO_DD extends Base
{
    public $allCount = 6;
    public static $filterArr = [1, 2, 3, 4, 5, 6];

    // 供测试用 生成随机投注
    public function randomCodes() {
        $rand = rand(1, 6);
        return $rand;
    }

    // 格式校验
    public function regexp($sCode)
    {
        // 是数字
        if (preg_match('/^\d$/', $sCode) !== 1) {
            return false;
        }

        if (!in_array($sCode, self::$filterArr)) {
            return false;
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        return 1;
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
     * @param $sCode
     * @param array $aOpenCodes
     * @return int
     */
    public function assertLevel($levelId, $sCode, Array $aOpenCodes)
    {
        $_aOpenCodes = array_flip($aOpenCodes);
        if (isset($_aOpenCodes[$sCode])) {
            return 1;
        }

        return 0;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $betCode    = $sCodes;
        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    $codeArr = [$a , $b , $c];
                    if (in_array($betCode, $codeArr)) {
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
