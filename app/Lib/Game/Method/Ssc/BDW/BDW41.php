<?php namespace App\Lib\Game\Method\Ssc\BDW;

use App\Lib\Game\Method\Ssc\Base;

/**
 * 4星1码不定位
 * Class BDW41
 * Tom 2019 08 整理
 * @package App\Lib\Game\Method\Ssc\BDW
 */
class BDW41 extends Base
{
    public $allCount =10;

    public static $filterArr = [0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1];

    // 生成N注随机单子
    public function randomCodes()
    {
        $rand = rand(1, 10);
        return implode('&', (array)array_rand(self::$filterArr, $rand));
    }

    // 老号码转换
    public function fromOld($codes)
    {
        return implode('&', explode('|', $codes));
    }

    // 投注格式判定
    public function regexp($sCodes)
    {
        if (!preg_match("/^(([0-9]&){0,9}[0-9])$/", $sCodes)) {
            return false;
        }

        $filterArr = self::$filterArr;

        $iNums = count(array_filter(array_unique(explode("&", $sCodes)),function($v) use ($filterArr) {
            return isset($filterArr[$v]);
        }));

        if($iNums == 0) {
            return false;
        }

        return $iNums == count(explode("&", $sCodes));
    }

    // 计算注数
    public function count($sCodes)
    {
        // C(n,1)
        $iNums = count(array_unique(explode("&", $sCodes)));
        return $this->getCombinCount($iNums,1);
    }

    // 冷热 & 遗漏
    public function bingoCode(Array $numbers)
    {
        $numbers    = array_flip($numbers);
        $result     = [];
        $arr        = array_keys(self::$filterArr);
        foreach($arr as $v) {
            $result[] = intval(isset($numbers[$v]));
        }
        return [$result];
    }

    // 判定中奖
    public function assertLevel($levelId, $sBetCodes, Array $aOpenCodes)
    {
        $temp = array();
        $nums = array_count_values ($aOpenCodes);

        $aCodes = explode("&", $sBetCodes);
        $i = 0;
        foreach ($aCodes as $code) {
            if(isset($temp[$code])) continue;
            $temp[$code] = 1;
            if (isset($nums[$code]) && $nums[$code] >= 1 ) {
                $i ++;
            }
        }

        return $i;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes = explode('&', $sCodes);
        $aCodes = $this->getCombination($aCodes, 2);

        $exists = array_flip($aCodes);

        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        foreach ($tmp as $q) {
            foreach ($tmp as $b) {
                foreach ($tmp as $s) {
                    foreach ($tmp as $g) {
                        if(!isset($exists[$q]) && !isset($exists[$b]) && !isset($exists[$s]) && !isset($exists[$g])) {
                            continue;
                        }

                        foreach ($tmp as $w) {
                            $key = $w . $q . $b . $s. $g;
                            if (isset($data[$key])) {
                                $data[$key] += $prizes[1];
                            } else {
                                $data[$key] = $prizes[1];
                            }

                        }
                    }

                }
            }
        }

        return $data;
    }
}
