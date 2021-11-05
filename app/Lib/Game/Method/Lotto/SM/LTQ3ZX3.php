<?php namespace App\Lib\Game\Method\Lotto\SM;

use App\Lib\Game\Method\Lotto\Base;

// 直选3
class LTQ3ZX3 extends Base
{

    public static $filterArr = [
        '01'    => 1,
        '02'    => 1,
        '03'    => 1,
        '04'    => 1,
        '05'    => 1,
        '06'    => 1,
        '07'    => 1,
        '08'    => 1,
        '09'    => 1,
        '10'    => 1,
        '11'    => 1
    ];
    //供测试用 生成随机投注
    public function randomCodes()
    {
        $arr=[];
        $rand=rand(1,10);
        $arr[]=implode('&',(array)array_rand(self::$filterArr,$rand));
        $rand=rand(1,10);
        $arr[]=implode('&',(array)array_rand(self::$filterArr,$rand));
        $rand=rand(1,10);
        $arr[]=implode('&',(array)array_rand(self::$filterArr,$rand));

        return implode('|',$arr);
    }

    public function fromOld($sCodes){
        return implode('|',array_map(function($v){
            return implode('&',explode(' ',$v));
        },explode('|',$sCodes)));
    }

    public function regexp($sCodes)
    {
        // 格式
        if (!preg_match("/^(((0[1-9]&)|(1[01]&)){0,10}((0[1-9])|(1[01]))\|){2}(((0[1-9]&)|(1[01]&)){0,10}((0[1-9])|(1[01])))$/", $sCodes)) {
            return false;
        }

        $filterArr = self::$filterArr;

        $aCode = explode("|", $sCodes);
        foreach ($aCode as $sCode) {
            $t = explode("&", $sCode);
            $iUniqueCount = count(array_filter(array_unique($t), function($v) use($filterArr) {
                return isset($filterArr[$v]);
            }));

            if ($iUniqueCount != count($t)) {
                return false;
            }

            if($iUniqueCount == 0) {
                return false;
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        $iNums = 0;
        $aNums = [];
        $aCode = explode("|", $sCodes);
        foreach ($aCode as $sCode) {
            $aNums[] = explode("&", $sCode);
        }

        if (count($aNums[0]) > 0 && count($aNums[1]) > 0 && count($aNums[2]) > 0) {
            for ($i = 0; $i < count($aNums[0]); $i++) {
                for ($j = 0; $j < count($aNums[1]); $j++) {
                    for ($k = 0; $k < count($aNums[2]); $k++) {
                        if ($aNums[0][$i] != $aNums[1][$j] && $aNums[0][$i] != $aNums[2][$k] && $aNums[1][$j] != $aNums[2][$k]) {
                            $iNums++;
                        }
                    }
                }
            }
        }

        return $iNums;
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
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        $aBetCodes  = explode('|', $sBetCodes);
        $aBetCodes  = $this->convertLtCodes($aBetCodes);
        $numbers    = $this->convertLtCodes($aOpenNumbers);

        $preg = "|[" . str_replace('&', '', $aBetCodes[0]) . "][" . str_replace('&', '', $aBetCodes[1]) . "][" . str_replace('&', '', $aBetCodes[2]) . "]|";

        if (preg_match($preg, implode("", $numbers))) {
            return 1;
        }
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $codeArr        = explode('|', $sCodes);
        $codeArr[0]     = $this->convertLtCodes(explode('&', $codeArr[0]));
        $codeArr[1]     = $this->convertLtCodes(explode('&', $codeArr[1]));
        $codeArr[2]     = $this->convertLtCodes(explode('&', $codeArr[2]));

        // 累加
        $tmp    = $this->convertLtCodes(["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"]);
        foreach ($codeArr[0] as $w) {
            foreach ($codeArr[1] as $q) {
                foreach ($codeArr[2] as $b) {
                    foreach ($tmp as $s) {
                        foreach ($tmp as $g) {
                            $key = $w . $q . $b . $s . $g;
                            if (isset($data[$key])) {
                                $data[$key] = bcadd($data[$key], $prizes[1], 4);
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
