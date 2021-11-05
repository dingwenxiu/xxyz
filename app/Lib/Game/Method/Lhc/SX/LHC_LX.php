<?php namespace App\Lib\Game\Method\Lhc\SX;

use App\Lib\Game\Method\Lhc\Base;
use App\Lib\Game\Method\Lhc\BaseSxTrait;

// 六肖
class LHC_LX extends Base
{
    use BaseSxTrait;

    public $allCount = 12;

    // 格式解析
    public function codeChange($sCodes)
    {
        $config = self::getSxCodeFromYear();

        $codeArrBefore  = explode('&', $sCodes);
        $codeArr        = array_unique($codeArrBefore);

        $str    = "";
        $midStr = "";
        foreach ($codeArr as $code) {
            $name = isset($config[$code]) ? $config[$code]["name"] : "未知";
            $str .= $midStr . $name;
            $midStr = "&";
        }

        return $str;
    }

    // 格式判定
    public function regexp($sCodes)
    {
        $codeConfig     = self::getSxCodeFromYear();

        $codeArrBefore  = explode('&', $sCodes);
        $codeArr        = array_unique($codeArrBefore);

        // 不能有重复
        if (count($codeArrBefore) != count($codeArr)) {
            return false;
        }

        // 号码是否存在
        foreach ($codeArr as $sCode) {
            if (!isset($codeConfig[$sCode])) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        $aCode          = explode("&", $sCodes);
        $aCodeUnique    = array_unique($aCode);
        return $this->getCombinCount(count($aCodeUnique),6);
    }

    /**
     * 判定奖金 任意一肖 都是中
     * @param $levelId
     * @param $sCodes
     * @param array $aOpenNumber
     * @return int
     */
    public function assertLevel($levelId, $sCodes, Array $aOpenNumber)
    {
        $config = self::getSxCodeFromYear();

        $aBetCodeArr = array_unique(explode('&', $sCodes));

        foreach ($aBetCodeArr as $sCode) {
            if (isset($config[$sCode])) {
                $codeConfig = $config[$sCode];
            } else {
                return 0;
            }

            // 生肖对应号码
            $codeArr    = $codeConfig["code"];
            $sOpenCode = $aOpenNumber[0];

            if (isset($codeArr[$sOpenCode])) {
                return 1;
            }
        }

        return 0;
    }
}
