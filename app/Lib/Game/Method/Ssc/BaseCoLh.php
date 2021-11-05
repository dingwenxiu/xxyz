<?php namespace App\Lib\Game\Method\Ssc;

class BaseCoLh extends Base
{
    // 龙虎和
    public $allCount =3;
    public static $filterArr = array(
        1 => "龙",
        2 => "虎",
        3 => "和"
    );

    // 判断奖金
    public function regexp($sCodes)
    {
        if(!isset(self::$filterArr[$sCodes])) {
            return false;
        }
        return true;
    }

    public function count($sCodes)
    {
        return 1;
    }

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$filterArr);
    }

    public function bingoCode(Array $numbers)
    {
        $result = [];
        $arr    = array_keys(self::$filterArr);

        foreach($numbers as $pos=>$code){
            $tmp    = [];
            foreach($arr as $_code){
                $tmp[] = intval($code == $_code);
            }
            $result[$pos]=$tmp;
        }

        return $result;
    }

    /**
     * 判定奖金
     * @param $levelId
     * @param $sCodes
     * @param array $openNumbers
     * @return int
     */
    public function assertLevel($levelId, $sCodes, Array $openNumbers)
    {
        $numbers = array_values($openNumbers);
        $num1 = $numbers[0];
        $num2 = $numbers[1];

        $count = 0;
        if ($num1 > $num2 && $sCodes == 1) {
            $count = 1;
        }

        if ($num1 == $num2 && 3 == $sCodes) {
            $count = 1;
        }

        if ($num1 < $num2 && 2 == $sCodes) {
            $count = 1;
        }

        return $count;
    }
}
