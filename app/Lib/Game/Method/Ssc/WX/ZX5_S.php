<?php namespace App\Lib\Game\Method\Ssc\WX;

use App\Lib\Game\Method\Ssc\Base;

/**
 * tom 2019
 * Class ZX5_S
 * @package App\Lib\Game\Method\Ssc\WX
 */
class ZX5_S extends Base
{
    public $allCount = 100000;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    public function parse64($codes)
    {
        if (strpos($codes, 'base64:') !== false) {
            $ex = explode('base64:', $codes);
            $codes = $this->_parse64($ex[1], 5);
            if (is_array($codes)) {
                $codes = implode(',', $codes);
            }
        }
        return $codes;
    }

    public function encode64($codes)
    {
        return $this->_encode64(explode(',', $codes));
    }

    // 检测号码
    public function regexp($sCodes)
    {
        // 重复号码
        $temp = explode(",", $sCodes);
        $i = count(array_filter(array_unique($temp), function ($val) {
            if (!preg_match("/^[0-9]{5}$/", $val)) {
                return false;
            }
            return true;
        }));

        if ($i != count($temp)) return false;

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        return count(explode(",", $sCodes));
    }

    //　判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str    = implode('', $numbers);
        $exists = array_flip(explode(',', $sCodes));
        return intval(isset($exists[$str]));
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode(',', $sCodes);

        foreach ($aCodes as $code) {
            if (isset($data[$code])) {
                $data[$code] = bcadd($data[$code], $prizes[1], 4);
            } else {
                $data[$code] = $prizes[1];
            }
        }

        return $data;
    }
}
