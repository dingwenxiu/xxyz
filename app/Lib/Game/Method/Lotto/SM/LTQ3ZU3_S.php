<?php namespace App\Lib\Game\Method\Lotto\SM;

use App\Lib\Game\Method\Lotto\Base;

class LTQ3ZU3_S extends Base
{

    public static $filterArr = [
        '01'    => 1,
        '02'    => 1,
        '03'    => 1,
        '04'    => 1,
        '05'    => 1,
        '06'    => 1,
        '07'    => 1,
        '08'    => 1,
        '09'    => 1,
        '10'    => 1,
        '11'    => 1
    ];

    public function regexp($sCodes)
    {

        $aCode = explode(",", $sCodes);

        // 去重
        if(count($aCode) != count(array_filter(array_unique($aCode)))) return false;

        // 校验
        foreach ($aCode as $sTmpCode) {
            $aTmpCode = explode(" ", $sTmpCode);
            if (count($aTmpCode) != 3) {
                return false;
            }

            // 单个号不能有重复
            if (count($aTmpCode) != count(array_filter(array_unique($aTmpCode)))) {
                return false;
            }

            // 每个号不能存在其他号码
            foreach ($aTmpCode as $c) {
                if (!isset(self::$filterArr[$c])) {
                    return false;
                }
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        return count(explode(",", $sCodes));
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $aCodes = explode(',', str_replace(' ','',$this->convertLtCodes($sCodes)));
        $str = $this->strOrder(implode('', $this->convertLtCodes($numbers)));

        foreach ($aCodes as $code) {
            if ($this->strOrder($code) === $str) {
                return 1;
            }
        }
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode(',', $sCodes);
        $tmp    = $this->convertLtCodes(["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"]);

        foreach ($aCodes as $code) {
            foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    $code   = $this->convertLtCodes($code);
                    $key    = $code . $s . $g;
                    if (isset($data[$key])) {
                        $data[$key]  = bcadd($data[$key], $prizes[1], 4);
                    } else {
                        $data[$key] = $prizes[1];
                    }
                }
            }
        }

        return $data;
    }
}
