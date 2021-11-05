<?php namespace App\Lib\Game\Method\Lhc\SX;

use App\Lib\Game\Method\Lhc\Base;
use App\Lib\Game\Method\Lhc\BaseSxTrait;

// 一肖
class LHC_YX extends Base
{
    use BaseSxTrait;

    public $allCount = 12;

    // 格式解析
    public function codeChange($code)
    {
        $config = self::getSxCodeFromYear();

        return isset($config[$code]) ? $config[$code]["name"] : "未知";
    }

    // 格式判定
    public function regexp($sCode)
    {
        $config = self::getSxCodeFromYear();
        if (!isset($config[$sCode])) {
            return false;
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        return 1;
    }

    /**
     * 判定奖金 任意一肖 都是中
     * @param $levelId
     * @param $sCode
     * @param array $aOpenNumber
     * @return int
     */
    public function assertLevel($levelId, $sCode, Array $aOpenNumber)
    {
        $config = self::getSxCodeFromYear();

        if (isset($config[$sCode])) {
            $codeConfig = $config[$sCode];
        } else {
            return 0;
        }

        // 奖级不匹配
        if ($codeConfig['level'] != $levelId) {
            return 0;
        }

        // 生肖对应号码
        $codeArr = $codeConfig["code"];

        foreach ($aOpenNumber as $_code) {
            if (isset($codeArr[$_code])) {
                return 1;
            }
        }

        return 0;
    }

}
