<?php namespace App\Lib\Game\Method\K3\EBTH;

use App\Lib\Game\Method\K3\Base;

// 二不同号
class EBTH extends Base
{
    public static $filterArr = [
        "12" => 1, "13" => 1, "14" => 1, "15" => 1, "16" => 1,
        "23" => 1, "24" => 1, "25" => 1, "26" => 1, "34" => 1,
        "35" => 1, "36" => 1, "45" => 1, "46" => 1, "56" => 1
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {
        $cnt    = count(self::$filterArr);
        $rand   = rand(2,$cnt);
        return implode('&',(array)array_rand(self::$filterArr,$rand));
    }

    // 判定号码格式是否正确
    public function regexp($sCodes)
    {
        $aCode = explode('|', $sCodes);

        if(count($aCode) != count(array_unique($aCode))) {
            return false;
        }

        foreach ($aCode as $code) {
            // 是数字
            if (preg_match('/^\d{2}$/', $code) !== 1) {
                return false;
            }

            if (!isset(self::$filterArr[$code])) {
                return false;
            }
        }

        return true;
    }

    // 计奖
    public function count($sCodes)
    {
        $aCodes = explode('|', $sCodes);
        return count(array_unique($aCodes));
    }

    public function bingoCode(Array $numbers)
    {
        $result=[];
        $arr=array_keys(self::$filterArr);
        foreach($numbers as $pos=>$code){
            $tmp=[];
            foreach($arr as $_code){
                $tmp[]=intval($code==$_code);
            }
            $result[$pos]=$tmp;
        }

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $aOpenNumbers)
    {
        // 排除豹子号
        if ($aOpenNumbers[0] == $aOpenNumbers[1] && $aOpenNumbers[1] == $aOpenNumbers[2]) {
            return 0;
        }

        sort($aOpenNumbers);

        $canWinCodeArr  = [
            $aOpenNumbers[0].$aOpenNumbers[1],
            $aOpenNumbers[0].$aOpenNumbers[2],
            $aOpenNumbers[1].$aOpenNumbers[2],
        ];

        $canWinCodeArr = array_unique($canWinCodeArr);

        $aCodes = explode('|', $sCodes);

        $count = 0;
        foreach ($aCodes as $code) {
            if (in_array($code, $canWinCodeArr)) {
                $count += 1;
            }
        }

        return $count;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes     = array_flip(explode('|', $sCodes));

        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    // 豹子号去除
                    if ($a == $b && $b == $c) {
                        continue;
                    }

                    // 三不同号去除
                    if ($a != $b && $b != $c) {
                        continue;
                    }

                    $_code = [$a, $b, $c];
                    $_code = array_unique($_code);
                    sort($_code);
                    $_strCode = $_code[0] . $_code[1];

                    if (isset($aCodes[$_strCode])) {
                        $codeStr = $a . $b . $c;
                        if (isset($data[$codeStr])) {
                            $data[$codeStr] = bcadd($data[$codeStr], $prizes[1], 4);
                        } else {
                            $data[$codeStr] = $prizes[1];
                        }
                    }
                }
            }
        }

        return $data;
    }
}
