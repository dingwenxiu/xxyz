<?php namespace App\Lib\Game\Method\Ssc\WX;
use App\Lib\Game\Method\Ssc\Base;

class WXZU10 extends Base
{
    // 0&1&2&3&4&5&6&7&8&9|0&1&2&3&4&5&6&7&8&9
    public $allCount = 90;
    public static $filterArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1);

    //供测试用 生成随机投注
    public function randomCodes()
    {
        $arr = array();

        $rand   = rand(2, count(self::$filterArr));
        $arr[]  = implode('&', (array)array_rand(self::$filterArr, $rand));
        $rand   = rand(1, count(self::$filterArr));
        $arr[]  = implode('&', (array)array_rand(self::$filterArr, $rand));

        return implode('|',$arr);
    }

    public function regexp($sCodes)
    {
        // 去重
        $aCodeArr = explode("|", $sCodes);
        if (count($aCodeArr) != 2) {
            return false;
        }

        $_oneLineCodeArr = explode('&', $aCodeArr[0]);
        $oneLineCodeArr  = array_unique($_oneLineCodeArr);
        if (count($_oneLineCodeArr) != count($oneLineCodeArr) || count($oneLineCodeArr) < 1 || count($oneLineCodeArr) > 10) {
            return false;
        }

        $_twoLineCodeArr = explode('&', $aCodeArr[1]);
        $twoLineCodeArr  = array_unique($_twoLineCodeArr);
        if (count($_twoLineCodeArr) != count($twoLineCodeArr) || count($twoLineCodeArr) < 1 || count($twoLineCodeArr) > 10) {
            return false;
        }

        foreach($oneLineCodeArr as $_code) {
            if (!isset(self::$filterArr[$_code])) {
                return false;
            }
        }

        foreach($twoLineCodeArr as $_code) {
            if (!isset(self::$filterArr[$_code])) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        //m表示上一排数量
        //n表示下一排数量
        //h表示重复的数量
        //C(m,1)*C(n,1)-C(h,1)

        $temp   = explode('|',$sCodes);
        $t1     = array_unique(explode('&',$temp[0]));
        $t2     = array_unique(explode('&',$temp[1]));
        $m = count($t1);
        $n = count($t2);
        $t = array_intersect_key(array_flip($t1), array_flip($t2));
        $h = count($t);

        return $this->getCombinCount($m,1) * $this->getCombinCount($n,1) - $this->getCombinCount($h,1);
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str = $this->strOrder(implode('', $numbers));

        $aCodes = explode('|', $sCodes);

        $aP1 = $this->getCombination(explode('&', $aCodes[0]), 1);
        $aP2 = $this->getCombination(explode('&', $aCodes[1]), 1);
        foreach ($aP1 as $v1) {
            foreach ($aP2 as $v2) {
                if ($v1 == $v2) continue;
                if ($str === $this->strOrder(str_repeat($v1, 3) . str_repeat($v2, 2))) {
                    return 1;
                }
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
        $aCodes = explode('|', $sCodes);
        $tmp    = [];
        $aP1    = $this->getCombination(explode('&', $aCodes[0]), 1);
        $aP2    = $this->getCombination(explode('&', $aCodes[1]), 1);

        foreach ($aP1 as $v1) {
            foreach ($aP2 as $v2) {
                if ($v1 == $v2) continue;
                $arr = [$v1, $v1, $v1, $v2, $v2];
                sort($arr);
                $tmp[] = $arr;
            }
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
