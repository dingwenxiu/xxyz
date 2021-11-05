<?php namespace App\Lib\Game\Method\Lhc;

trait BaseTm {
    public $all_count = 49;

    public static $filterArr = array(
        "01"  => 1,  "02"  => 1,  "03"  => 1,  "04" => 1, "05"  => 1, "06" => 1, "07" => 1, "08" => 1, "09" => 1, "10"  => 1,
        "11"  => 1,  "12"  => 1,  "13"  => 1,  "14" => 1, "15"  => 1, "16" => 1, "17" => 1, "18" => 1, "19" => 1, "20"  => 1,
        "21"  => 1,  "22"  => 1,  "23"  => 1,  "24" => 1, "25"  => 1, "26" => 1, "27" => 1, "28" => 1, "29" => 1, "30"  => 1,
        "31"  => 1,  "32"  => 1,  "33"  => 1,  "34" => 1, "35"  => 1, "36" => 1, "37" => 1, "38" => 1, "39" => 1, "40"  => 1,
        "41"  => 1,  "42"  => 1,  "43"  => 1,  "44" => 1, "45"  => 1, "46" => 1, "47" => 1, "48" => 1, "49" => 1,
    );

    public function parse64($codes)
    {
        return true;
    }

    public function encode64($codes)
    {
        return $this->_encode64(explode(',', $codes));
    }

    /**
     * 号码是否合法
     * 拆单后的特码只有一个号码
     * @param $sCode
     * @return bool
     */
    public function regexp($sCode)
    {
        if (!isset(self::$filterArr[$sCode])) {
            return false;
        }

        return true;
    }

    /**
     * 计算注数
     * @param $sCode
     * @return int
     */
    public function count($sCode)
    {
        return 1;
    }

    /**
     *
     * @param int $levelId 奖级
     * @param string $sBetCode 投注号码
     * @param array $openCodes 开奖号码
     * @return int
     */
    public function assertLevel($levelId, $sBetCode, Array $openCodes)
    {
        $exists = array_flip($openCodes);

        if(isset($exists[$sBetCode])) {
            return 1;
        }

        return 0;
    }
}
