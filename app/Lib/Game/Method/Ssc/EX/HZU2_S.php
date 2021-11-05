<?php namespace App\Lib\Game\Method\Ssc\EX;

use App\Lib\Game\Method\Ssc\Base;

/**
 * 后2组选
 * Class HZU2_S
 * @package App\Lib\Game\Method\Ssc\EX
 */
class HZU2_S extends Base
{
    // 12,21,31
    public $allCount = 45;
    public static $dzArr = array(
        '00'    => 1,
        '11'    => 1,
        '22'    => 1,
        '33'    => 1,
        '44'    => 1,
        '55'    => 1,
        '66'    => 1,
        '77'    => 1,
        '88'    => 1,
        '99'    => 1
    );

    public static $filterArr = array(
        0   => 1,
        1   => 1,
        2   => 1,
        3   => 1,
        4   => 1,
        5   => 1,
        6   => 1,
        7   => 1,
        8   => 1,
        9   => 1
    );

    public function regexp($sCodes)
    {
        //格式
        if (!preg_match("/^(([0-9]{2}\,)*[0-9]{2})$/", $sCodes)) {
            return false;
        }

        // 重复号码
        $temp   = explode(",", $sCodes);
        $i      = count(array_filter(array_unique($temp)));
        if($i != count($temp)) {
            return false;
        }

        // 对子号
        $exists = [];
        $dzArr  = self::$dzArr;
        foreach($temp as $c) {
            // 对子号
            if(isset($dzArr[$c])) {
                return false;
            }

            // 强制校验每个一个号码
            $codeArr = str_split($c);
            foreach ($codeArr as $__code) {
                if (!isset(self::$filterArr[$__code])) {
                    return false;
                }
            }

            // 组选不能重复号码
            $vv = $this->strOrder($c);
            if(isset($exists[$vv])) {
                return false;
            }
            $exists[$vv] = 1;
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
        $str1 = $this->strOrder(implode('', $numbers));
        $str2 = $this->strOrder(implode('', $numbers),true);

        $aCodes = explode(',', $sCodes);

        if ($numbers[0] != $numbers[1]) {
            foreach ($aCodes as $code) {
                if ($code === $str1 || $code === $str2) {
                    return 1;
                }
            }
        }
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode(',', $sCodes);
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($aCodes as $code) {

                    $key = $w . $q . $code;
                    if (isset($data[$key])) {
                        $data[$key] += $prizes[1];
                    } else {
                        $data[$key] = $prizes[1];
                    }
                }
            }
        }

        return $data;
    }
}
