<?php namespace App\Lib\Game\Method\K3\ZH;

use App\Lib\Game\Method\K3\Base;

// 和值大小单双
class KS_CO_HZDXDS extends Base
{
    // 大小单双
    public $allCount = 4;

    public static $dxds = array(
        'b' => '大',
        's' => '小',
        'o' => '单',
        'e' => '双',
    );

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$dxds);
    }

    // 投注格式判定
    public function regexp($sCode) {
        $filterArr = self::$dxds;
        return isset($filterArr[$sCode]);
    }

    // 计算注数
    public function count($sCodes)
    {
        return 1;
    }

    /**
     * 判定 中奖注单
     * 3 - 10 小 11 - 28 大
     * @param $levelId
     * @param $sBetCode
     * @param array $aOpenNumbers
     * @return int
     */
    public function assertLevel($levelId, $sBetCode, Array $aOpenNumbers)
    {
        // 开奖内容
        $number = array_sum($aOpenNumbers);

        $bs     = $number > 10 ? 'b' : 's';
        $ad     = $number % 2 == 0 ? 'e' : 'o';

        $arr    = array($bs, $ad);
        if (in_array($sBetCode, $arr)) {
            return 1;
        }

        return 0;
    }

    // 控水处理
    public function doControl($data, $sCode, $prizes)
    {
        $tmp        = [1, 2, 3, 4, 5, 6];
        foreach ($tmp as $a) {
            foreach ($tmp as $b) {
                foreach ($tmp as $c) {
                    $number = $a + $b + $c;
                    $bs     = $number > 10 ? 'b' : 's';
                    $oe     = $number % 2 == 0 ? 'e' : 'o';

                    if ($sCode == $bs) {
                        $codeStr = $a . $b . $c;
                        if (isset($data[$codeStr])) {
                            $data[$codeStr] = bcadd($data[$codeStr], $prizes[1], 4);
                        } else {
                            $data[$codeStr] = $prizes[1];
                        }
                    }

                    if ($sCode == $oe) {
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
