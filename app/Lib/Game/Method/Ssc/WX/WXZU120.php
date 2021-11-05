<?php namespace App\Lib\Game\Method\Ssc\WX;
use App\Lib\Game\Method\Ssc\Base;

class WXZU120 extends Base
{
    // 0&1&2&3&4&5&6&7&8&9
    public $allCount = 252;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $rand = rand(5,10);
        return implode('&', (array)array_rand(self::$filterArr,$rand));
    }

    public function regexp($sCodes) {
        $aCodeArr = explode("&", $sCodes);
        $aCodeArr = array_unique($aCodeArr);

        // 长度
        if (count($aCodeArr) < 5 || count($aCodeArr) > 10) {
            return false;
        }

        // 号码
        foreach($aCodeArr as $code) {
            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        // C(n,5)
        return $this->getCombinCount(count(explode('&',$sCodes)),5);
    }

    public function bingoCode(Array $numbers)
    {
        $exists=array_flip($numbers);
        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $pos=>$_code){
            $result[$pos]=intval(isset($exists[$_code]));
        }

        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str = $this->strOrder(implode('', $numbers));
        $aCodes = explode('&', $sCodes);

        $aP1 = $this->getCombination($aCodes, 5);

        foreach ($aP1 as $v1) {
            if ($str === $this->strOrder(str_replace(' ', '', $v1)) ) {
                return 1;
            }
        }
    }

    /**
     * @param $data
     * @param $sCodes
     * @param $prizes
     * @return mixed
     */
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $aP1    = $this->getCombination($aCodes, 5);

        $tmp    = [];
        foreach ($aP1 as $v1) {
            $arr = explode(' ', $v1);
            sort($arr);
            $tmp[] = $arr;
        }

        $aCodes = [];
        foreach ($tmp as $_aCode) {
            $this->genCodeFromZu($_aCode, 5, $aCodes);
        }

        foreach ($aCodes as $code => $value) {
            if (isset($data[$code])) {
                $data[$code] = bcadd($data[$code], $prizes[1], 4);
            } else {
                $data[$code] = $prizes[1];
            }

        }

        return $data;
    }
}
