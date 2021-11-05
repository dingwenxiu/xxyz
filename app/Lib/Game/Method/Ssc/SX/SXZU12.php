<?php namespace App\Lib\Game\Method\Ssc\SX;

use App\Lib\Game\Method\Ssc\Base;

class SXZU12 extends Base
{
    public $allCount = 360;
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
        if (count($_twoLineCodeArr) != count($twoLineCodeArr) || count($twoLineCodeArr) < 2 || count($twoLineCodeArr) > 10) {
            return false;
        }

        // 如果都是一个号码　不合法
        if (count($oneLineCodeArr) <= 2 && count($twoLineCodeArr) == 2) {
            $arrDiff = array_diff($oneLineCodeArr, $twoLineCodeArr);

            // 如果不存在差值　则判定错误
            if (count($arrDiff) <= 0) {
                return false;
            }
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
        // 1&2&3&4&5&6&9|0&2&3&4&5&6&7&8&9
        // m表示上一排数量
        // n表示下一排数量
        // h表示重复的数量
        // C(m,1)*C(n,2)-C(h,1)*C(n-1,1)

        $temp   = explode('|', $sCodes);
        $t1     = array_unique(array_unique(explode('&', $temp[0])));
        $t2     = array_unique(array_unique(explode('&', $temp[1])));
        $m      = count($t1);
        $n      = count($t2);
        $t3     = array_intersect_key(array_flip($t1), array_flip($t2));

        $h      = count($t3);

        return $this->getCombinCount($m,1) * $this->getCombinCount($n,2) - $this->getCombinCount($h,1)*$this->getCombinCount($n-1,1);
    }

    //判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        $str = $this->strOrder(implode('', $numbers));

        $aCodes = explode('|', $sCodes);

        $aP1 = $this->getCombination(explode('&', $aCodes[0]), 1);
        $aP2 = $this->getCombination(explode('&', $aCodes[1]), 2);
        foreach ($aP2 as $v2) {
            $v2 = str_replace(' ','',$v2);
            $vs= str_split($v2);
            foreach ($aP1 as $v1) {
                if (in_array($v1, $vs)) continue;
                if ($str === $this->strOrder(str_repeat($v1, 2) . str_repeat($v2, 1))) {
                    return 1;
                }
            }
        }
        return 0;
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
        $tmp=[];
        $aP1 = $this->getCombination(explode('&', $aCodes[0]), 1);
        $aP2 = $this->getCombination(explode('&', $aCodes[1]), 2);


        foreach ($aP2 as $v2) {
            $vs = explode(' ', $v2);
            foreach ($aP1 as $v1) {
                if($v1 == $vs[0] || $v1 == $vs[1]) continue;

                $arr = [$v1, $v1, $vs[0], $vs[1]];
                sort($arr);
                $tmp[] = $arr;
            }
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
