<?php namespace App\Lib\Game\Method\K3\DXDS;

use App\Lib\Game\Method\K3\Base;

// 和值大小单双
class KSHZDXDS extends Base
{
    // 大小单双
    public $allCount = 4;

    public static $dxds = array(
        'b' => '大',
        's' => '小',
        'o' => '单',
        'e' => '双',
    );

    // 供测试用 生成随机投注
    public function randomCodes() {
        $rand = rand(1, count(self::$dxds));
        return implode('&', (array)array_rand(array_flip(self::$dxds), $rand));
    }

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$dxds);
    }

    // 投注格式判定
    public function regexp($sCodes)
    {
        $regexp = '/^([bsoe]\|){0,3}[bsoe]$/';

        if(!preg_match($regexp, $sCodes)) return false;

        $filterArr = self::$dxds;

        $temp = explode('|', $sCodes);
        if(count($temp) != count(array_filter(array_unique($temp),function($v) use($filterArr) {
                return isset($filterArr[$v]);
        }))) return false;

        if(count($temp) == 0){
            return false;
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        $count = count(explode("|", $sCodes));
        return $count;
    }

    public function bingoCode(Array $numbers)
    {
        $b=array_flip([11,12,13,14,15,16,17,18]);
        $s=array_flip([3, 4, 5, 6, 7, 8, 9, 10]);
        $a=array_flip([3,5,7,9,11,13,15,17]);
        $d=array_flip([4,6,8,10,12,14,16,18]);
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

    /**
     * 判定 中奖注单
     * 3 - 10 小 11 - 28 大
     * @param $levelId
     * @param $sBetCodes
     * @param array $aOpenNumbers
     * @return int
     */
    public function assertLevel($levelId, $sBetCodes, Array $aOpenNumbers)
    {
        // 投注内容
        $aCodes = explode("|", $sBetCodes);

        // 开奖内容
        $number = array_sum($aOpenNumbers);

        $bs     = $number > 10 ? 'b' : 's';
        $ad     = $number % 2 == 0 ? 'e' : 'o';

        $arr    = array($bs, $ad);

        $i      = 0;
        $temp   = [];
        foreach ($aCodes as $v1) {
            if(isset($temp[$v1])) continue;
            if (in_array($v1, $arr)) {
                $temp[$v1] = 1;
                $i++;
            }
        }

        return $i;
    }

    // 控水处理
    public function doControl($data, $sCodes, $prizes)
    {
        $aCodes     = array_flip(explode('|', $sCodes));

        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    $number = $a + $b + $c;
                    $bs     = $number > 10 ? 'b' : 's';
                    $oe     = $number % 2 == 0 ? 'e' : 'o';

                    if (isset($aCodes[$bs])) {
                        $codeStr = $a . $b . $c;
                        if (isset($data[$codeStr])) {
                            $data[$codeStr] = bcadd($data[$codeStr], $prizes[1], 4);
                        } else {
                            $data[$codeStr] = $prizes[1];
                        }
                    }

                    if (isset($aCodes[$oe])) {
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
