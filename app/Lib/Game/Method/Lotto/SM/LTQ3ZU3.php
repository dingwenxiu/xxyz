<?php namespace App\Lib\Game\Method\Lotto\SM;

use App\Lib\Game\Method\Lotto\Base;

// 乐透前3组选3
class LTQ3ZU3 extends Base
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
        // 去重
        $_codeArr       = explode("&", $sCodes);
        $codeArr        = array_unique($_codeArr);

        // 存在重复
        if (count($_codeArr) != count($codeArr)) {
            return false;
        }

        // 长度
        if (count($codeArr) < 3 || count($codeArr) > 11 ) {
            return false;
        }

        // 数字
        foreach ($codeArr as $code) {
            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        // C(n,3)
        $n = count(explode("&", $sCodes));

        return $this->getCombinCount($n,3);
    }

    public function bingoCode(Array $numbers)
    {
        $exists=array_flip($numbers);
        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $pos=>$_code){
            $result[]=intval(isset($exists[$_code]));
        }

        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $aCodes     = $this->convertLtCodes($sCodes);
        $numbers    = $this->convertLtCodes($numbers);

        if ($numbers[0] != $numbers[1] && $numbers[1] != $numbers[2]  && $numbers[0] != $numbers[2]) {
            $preg = "|[" . str_replace('&', '', $aCodes) . "]{3}|";
            if (preg_match($preg, implode("", $numbers))) {
                return 1;
            }
        }
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes         = explode('&', $sCodes);
        $allCodeArr     = $this->unpPackZu3($aCodes);

        $tmp    = $this->convertLtCodes(["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"]);

        foreach ($allCodeArr as $_code => $val) {
            foreach ($tmp as $s) {
                foreach ($tmp as $g) {
                    $_code = $this->convertLtCodes($_code);
                    if (isset($data[$_code.$s.$g])) {
                        $data[$_code.$s.$g] = bcadd($data[$_code.$s.$g], $prizes[1], 4);
                    } else {
                        $data[$_code.$s.$g] = $prizes[1];
                    }
                }
            }
        }

        return $data;
    }
}
