<?php namespace App\Lib\Game\Method\Ssc\SX;

use App\Lib\Game\Method\Ssc\Base;

class SXZU6 extends Base
{
    //0&1&2&3&4&5&6&7&8&9
    public $allCount = 45;
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

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $rand = rand(2,10);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    public function fromOld($codes)
    {
        return implode('&',explode('|',$codes));
    }


    public function regexp($sCodes)
    {
        $aCodeArr = explode("&", $sCodes);
        $aCodeArr = array_unique($aCodeArr);

        // 长度
        if (count($aCodeArr) < 2 || count($aCodeArr) > 10) {
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
        // C(n,2)
        return $this->getCombinCount(count(array_unique(explode('&', $sCodes))),2);
    }

    public function bingoCode(Array $numbers)
    {
        $counts=array_count_values($numbers);

        $arr= array_keys(self::$filterArr);
        $result=[];
        foreach($arr as $pos=>$_code){
            $result[$pos]=intval(isset($counts[$_code]) && $counts[$_code]>=2);
        }

        return [$result];
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str = $this->strOrder(implode('', $numbers));

        $aCodes = explode('&', $sCodes);

        $aP1 = $this->getCombination($aCodes, 2);
        $aP1 = $this->getRepeat($aP1, 2);

        foreach ($aP1 as $v1) {
            if ($str == $this->strOrder(str_replace(' ', '', $v1)) ) {
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
        $aP1    = $this->getCombination($aCodes, 2);

        $tmp    = [];
        foreach ($aP1 as $v1) {
            $arr = explode(' ', $v1);
            $arr = [$arr[0],$arr[0],$arr[1],$arr[1]];
            sort($arr);
            $tmp[] = $arr;
        }

        $aCodes = [];
        foreach ($tmp as $_aCode) {
            $this->genCodeFromZu($_aCode, 4, $aCodes);
        }

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $w) {
            foreach ($aCodes as $code => $value) {
                $key = $w . $code;
                if (isset($data[$key])) {
                    $data[$key] = bcadd($data[$key], $prizes[1], 4);
                } else {
                    $data[$key] = $prizes[1];
                }
            }
        }

        return $data;
    }
}
