<?php namespace App\Lib\Game\Method\Ssc\SX;

use App\Lib\Game\Method\Ssc\Base;

class ZX4_S extends Base
{
    // 12345,12345,12345,12345,12345,12345,
    public $allCount = 10000;
    public static $filterArr = [
        0 => 1,
        1 => 1,
        2 => 1,
        3 => 1,
        4 => 1,
        5 => 1,
        6 => 1,
        7 => 1,
        8 => 1,
        9 => 1
    ];

    public function parse64($codes)
    {
        if(strpos($codes,'base64:')!==false){
            $ex=explode('base64:',$codes);
            $codes=$this->_parse64($ex[1],4);
            if(is_array($codes)){
                $codes=implode(',',$codes);
            }
        }
        return $codes;
    }

    public function encode64($codes)
    {
        return $this->_encode64(explode(',',$codes));
    }

    public function regexp($sCodes)
    {
        // 重复号码
        $temp   = explode(",", $sCodes);
        $i      = count(array_filter(array_unique($temp),function($val){
            if(!preg_match("/^[0-9]{4}$/",$val)) {
                return false;
            }
            return true;
        }));

        if($i != count($temp)) return false;

        return true;
    }

    public function count($sCodes)
    {
        return count(explode(",", $sCodes));
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str = implode('', $numbers);
        $exists = array_flip(explode(',', $sCodes));
        return intval(isset($exists[$str]));
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode(',', $sCodes);
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($aCodes as $code) {
            foreach ($tmp as $number) {
                if (isset($data[$number . $code])) {
                    $data[$number . $code] = bcadd($data[$number . $code], $prizes[1], 4);
                } else {
                    $data[$number . $code] = $prizes[1];
                }
            }
        }

        return $data;
    }
}
