<?php namespace App\Lib\Game\Method\Klsf;

// 定位胆
trait BaseDWD {
    static public $codeFilter = [
        "01" => 1,
        "02" => 1,
        "03" => 1,
        "04" => 1,
        "05" => 1,
        "06" => 1,
        "07" => 1,
        "08" => 1,
        "09" => 1,
        "10" => 1,
        "11" => 1,
        "12" => 1,
        "13" => 1,
        "14" => 1,
        "15" => 1,
        "16" => 1,
        "17" => 1,
        "18" => 1,
        "19" => 1,
        "20" => 1,
    ];

    // 格式判断
    public function regexp($sCodes)
    {
        // 去重
        $_aCodeArr  = explode('&', $sCodes);
        $aCodeArr   = array_unique($_aCodeArr, SORT_STRING);

        if (count($_aCodeArr) != count($aCodeArr)) {
            return false;
        }

        foreach ($aCodeArr as $code) {
            if (!isset(self::$codeFilter[$code])) {
                return false;
            }
        }

        return true;
    }

    // 注数计算
    public function count($sCodes)
    {
        $count =  count(array_unique(explode('&', $sCodes), SORT_STRING));
        info($sCodes);
        info($count);
        return $count;
    }

    // 判断奖金
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $codes  = explode('&', $sBetCodes);
        $exists = array_flip($aOpenNumbers);
        foreach($codes as $c){
            if(isset($exists[$c])) {
                return 1;
            }
        }
        return 0;
    }

}
