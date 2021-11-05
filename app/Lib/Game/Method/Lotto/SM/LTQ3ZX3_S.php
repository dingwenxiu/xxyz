<?php namespace App\Lib\Game\Method\Lotto\SM;

use App\Lib\Game\Method\Lotto\Base;


class LTQ3ZX3_S extends Base
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

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand=3;
        return implode(' ',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($codes)
    {
        //$sCodes
        return implode(',',explode('|',$codes));
    }

    public function regexp($sCodes)
    {
        $aCode = explode(",", $sCodes);

        // 去重
        if(count($aCode) != count(array_filter(array_unique($aCode)))) return false;

        // 校验
        foreach ($aCode as $sTmpCode) {
            if (!preg_match("/^((0[1-9]\s)|(1[01]\s)){2}((0[1-9])|(1[01]))$/", $sTmpCode)) {
                return false;
            }

            $aTmpCode = explode(" ", $sTmpCode);
            if (count($aTmpCode) != 3) {
                return false;
            }
            if (count($aTmpCode) != count(array_filter(array_unique($aTmpCode)))) {
                return false;
            }
            foreach ($aTmpCode as $c) {
                if (!isset(self::$filterArr[$c])) {
                    return false;
                }
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        return count(explode(",",$sCodes));
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str = implode(' ', $numbers);
        $aCodes = explode(',', $sCodes);

        foreach ($aCodes as $code) {
            if ($code === $str) {
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
                        $data[$key] = bcadd($data[$key], $prizes[1], 4);
                    } else {
                        $data[$key] = $prizes[1];
                    }
                }
            }
        }

        return $data;
    }
}
