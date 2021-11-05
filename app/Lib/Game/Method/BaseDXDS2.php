<?php namespace App\Services\Game\Method;

// 2位大小单双 基类
trait BaseDXDS2
{
    // 大小单双|大小单双
    // b&s&a&d|b&s&a&d
    public $all_count = 16;
    public static $dxds = array(
        'b' => '大',
        's' => '小',
        'a' => '单',
        'd' => '双',
    );

    //　供测试用 生成随机投注
    public function randomCodes()
    {
        $line = array();
        $rand = rand(1, count(self::$dxds));
        $line[] = implode('&', (array)array_rand(array_flip(self::$dxds), $rand));
        $rand = rand(1, count(self::$dxds));
        $line[] = implode('&', (array)array_rand(array_flip(self::$dxds), $rand));

        return implode('|', $line);
    }

    public function fromOld($codes)
    {
        //　0123|0123
        $codes  = str_replace(array('0', '1', '2', '3'), array('b', 's', 'a', 'd'), $codes);
        $ex     = explode('|', $codes);
        $ex[0]  = implode('&', str_split($ex[0]));
        $ex[1]  = implode('&', str_split($ex[1]));
        return implode('|', $ex);
    }

    // 格式解析
    public function resolve($codes)
    {
        return strtr($codes, array_flip(self::$dxds));
    }

    // 还原格式
    public function unresolve($codes)
    {
        return strtr($codes, self::$dxds);
    }

    public function regexp($sCodes)
    {
        $regexp = '/^([bsad]&){0,3}[bsad]\|([bsad]&){0,3}[bsad]$/';

        if (!preg_match($regexp, $sCodes)) return false;

        $filterArr = self::$dxds;

        $sCodes = explode("|", $sCodes);
        foreach ($sCodes as $codes) {
            $temp = explode('&', $codes);
            if (count($temp) != count(array_filter(array_unique($temp), function ($v) use ($filterArr) {
                    return isset($filterArr[$v]);
                }))) return false;

            if (count($temp) == 0) {
                return false;
            }
        }

        return true;
    }

    // 计算注数
    public function count($sCodes)
    {
        // n1*n2
        $temp = explode("|", $sCodes);
        $n1 = count(explode("&", $temp[0]));
        $n2 = count(explode("&", $temp[1]));

        return $n1 * $n2;
    }

    // 冷热遗漏
    public function bingoCode(Array $numbers)
    {
        $b = array_flip([5, 6, 7, 8, 9]);
        $s = array_flip([0, 1, 2, 3, 4]);
        $a = array_flip([1, 3, 5, 7, 9]);
        $d = array_flip([0, 2, 4, 6, 8]);
        $result = [];
        foreach ($numbers as $k => $v) {
            $tmp = [];
            foreach ([$b, $s, $a, $d] as $arr) {
                $tmp[] = intval(isset($arr[$v]));
            }
            $result[$k] = $tmp;
        }

        return $result;
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $numbers)
    {
        // 多注
        $aCodes = explode("|", $sCodes);
        $aTemp1 = explode("&", $aCodes[0]);
        $aTemp2 = explode("&", $aCodes[1]);

        $bs1 = $numbers[0] > 4 ? 'b' : 's';
        $bs2 = $numbers[1] > 4 ? 'b' : 's';
        $ad1 = $numbers[0] % 2 == 0 ? 'd' : 'a';
        $ad2 = $numbers[1] % 2 == 0 ? 'd' : 'a';

        $arr = array(array($bs1, $ad1), array($bs2, $ad2));

        $i = 0;
        $temp = [];
        foreach ($aTemp1 as $v1) {
            foreach ($aTemp2 as $v2) {
                if (isset($temp[$v1 . '-' . $v2])) continue;
                if (in_array($v1, $arr[0]) && in_array($v2, $arr[1])) {
                    $temp[$v1 . '-' . $v2] = 1;
                    $i++;
                }
            }
        }

        return $i;
    }

}
