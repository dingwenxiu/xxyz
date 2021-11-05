<?php namespace App\Lib\Game\Method\Ssc\DXDS;

use App\Lib\Game\Method\Ssc\Base;

// 后2大小单双
class H2DXDS extends Base
{
    //大小单双|大小单双
    //b&s&a&d|b&s&a&d
    public $allCount = 16;
    public static $dxds = array(
        'b' => '大',
        's' => '小',
        'o' => '单',
        'e' => '双',
    );


    static $codeArr = [
        'b' => [5, 6, 7, 8, 9],
        's' => [0, 1, 2, 3, 4],
        'o' => [1, 3, 5, 7, 9],
        'e' => [0, 2, 4, 6, 8],
    ];

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$dxds);
    }

    public function regexp($sCodes)
    {
        $aCodes = explode("|", $sCodes);

        // 必须两行
        if (count($aCodes) != 2) {
            return false;
        }

        // 逐行校验
        foreach($aCodes as $codes) {
            $_rowCodeArr    = explode('&', $codes);
            $rowCodeArr     = array_unique($_rowCodeArr);

            // 不能有重复
            if (count($_rowCodeArr) != count($rowCodeArr)) {
                return false;
            }

            // 最少1个　最多4个
            if (count($rowCodeArr) < 1 || count($rowCodeArr) > 4) {
                return false;
            }

            foreach ($rowCodeArr as $code) {
                // 必须合法的字母
                if (!isset(self::$dxds[$code])) {
                    return false;
                }
            }
        }

        return true;
    }

    public function count($sCodes)
    {
        $temp   = explode("|", $sCodes);
        $n1     = count(array_unique(explode("&",$temp[0])));
        $n2     = count(array_unique(explode("&",$temp[1])));

        return $n1 * $n2;
    }

    public function bingoCode(Array $numbers)
    {
        $b  = array_flip([5,6,7,8,9]);
        $s  = array_flip([0,1,2,3,4]);
        $a  = array_flip([1,3,5,7,9]);
        $d  = array_flip([0,2,4,6,8]);
        $result=[];
        foreach($numbers as $k => $v){
            $tmp=[];
            foreach([$b,$s,$a,$d] as $arr){
                $tmp[]=intval(isset($arr[$v]));
            }
            $result[$k]=$tmp;
        }

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        //多注
        $aCodes = explode("|", $sCodes);
        $aTemp1 = explode("&", $aCodes[0]);
        $aTemp2 = explode("&", $aCodes[1]);

        $bs1 = $numbers[0] > 4 ? 'b' : 's';
        $bs2 = $numbers[1] > 4 ? 'b' : 's';
        $oe1 = $numbers[0] % 2 == 0 ? 'e' : 'o';
        $oe2 = $numbers[1] % 2 == 0 ? 'e' : 'o';

        $arr = array(array($bs1, $oe1), array($bs2, $oe2));

        $i      = 0;
        $temp   = [];
        foreach ($aTemp1 as $v1) {
            foreach ($aTemp2 as $v2) {
                if(isset($temp[$v1.'-'.$v2])) continue;
                if (in_array($v1, $arr[0]) && in_array($v2, $arr[1])) {
                    $temp[$v1.'-'.$v2]=1;
                    $i++;
                }
            }
        }

        return $i;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $canUseCodeArr = [];
        $aCodes = explode('|', $sCodes);
        foreach ($aCodes as $index =>  $rowStrCode) {
            $rowArrCode = explode('&', $rowStrCode);
            foreach ($rowArrCode as $code) {
                $_codeArr = self::$codeArr[$code];
                if (isset($canUseCodeArr[$index])) {
                    $canUseCodeArr[$index] = array_merge($canUseCodeArr[$index], $_codeArr);
                } else {
                    $canUseCodeArr[$index] = $_codeArr;
                }

            }
        }

        // 累加
        $tmp    = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        foreach ($tmp as $w) {
            foreach ($tmp as $q) {
                foreach ($tmp as $b) {
                    foreach ($canUseCodeArr[0] as $s) {
                        foreach ($canUseCodeArr[1] as $g) {
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
