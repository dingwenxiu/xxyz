<?php namespace App\Lib\Game\Method;

use App\Lib\Game\Lottery;

abstract class Base
{
    public $id;
    public $pos = '';

    public $pattern;
    public $config;

    public $lottery;

    public $lock = true;

    public static $_abc = array(
        '01' => 'a',
        '02' => 'b',
        '03' => 'c',
        '04' => 'd',
        '05' => 'e',
        '06' => 'f',
        '07' => 'g',
        '08' => 'h',
        '09' => 'i',
        '10' => 'j',
        '11' => 'k',
    );

    // 构造函数
    public function __construct($id, $pattern, $config)
    {
        $bla = $this->blabla();
        if ($bla != 9527779 ) {
            return ["1" => "做一个有素质的菜弄", "2" => "指纹已经通知到站长"];
        }

        $this->id       = $id;
        $this->pattern  = $pattern;

        // 补全
        $config['id']       = $id;
        $config['pattern']  = $pattern;
        $this->config       = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function toArray()
    {
        return $this->config;
    }

    // 魔术方法
    public function __get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    // 解析加密数据
    public function _parse64($str, $_n = 5)
    {
        $temp = [];
        try {
            $str2 = gzdecode(base64_decode($str));
        } catch (\Exception $e) {
            $str2 = '';
        }

        if (!$str2) return $str;

        if (preg_match_all('/[smn]\d+/', $str2, $matchs)) {
            if (!empty($matchs[0])) {
                foreach ($matchs[0] as $v) {
                    if ($v[0] == 's') {
                        $temp[] = $v;
                    } elseif ($v[0] == 'm') {
                        $temp[] = $v;
                    } elseif ($v[0] == 'n') {
                        $temp[] = $v;
                    }
                }
            }
        }

        $codes = [];
        $start = '';

        foreach ($temp as $v) {
            if ($v[0] == 's') {
                list($_s, $n) = explode('s', $v);
                $codes[] = $this->_psInt($n, $_n);
                $start = $n;
            } elseif ($v[0] == 'm') {
                list($_s, $n) = explode('m', $v);
                for ($i = 0; $i < $n; $i++) {
                    $start++;
                    $codes[] = $this->_psInt($start, $_n);
                }
            } elseif ($v[0] == 'n') {
                list($_s, $n) = explode('n', $v);
                $start += $n;
                $codes[] = $this->_psInt($start, $_n);
            }
        }

        return $codes;
    }

    public function _psInt($i, $n = 5)
    {
        if ($n == 5) {
            if ($i < 10) {
                return '0000' . $i;
            } elseif ($i < 100) {
                return '000' . $i;
            } elseif ($i < 1000) {
                return '00' . $i;
            } elseif ($i < 10000) {
                return '0' . $i;
            }

            return $i;
        } elseif ($n == 4) {
            if ($i < 10) {
                return '000' . $i;
            } elseif ($i < 100) {
                return '00' . $i;
            } elseif ($i < 1000) {
                return '0' . $i;
            }

            return $i;
        } elseif ($n == 3) {
            if ($i < 10) {
                return '00' . $i;
            } elseif ($i < 100) {
                return '0' . $i;
            }

            return $i;
        } else {
            return $i;
        }
    }

    public function parse64($codes)
    {
        return $codes;
    }

    public function encode64($codes)
    {
        return $codes;
    }

    public function getLockPrizes($betGroup, $times, $price, $mode) {
        $levels = $this->levels;
        $data = [];
        foreach ($levels as $level => $_config) {
            $bonus = $betGroup * $_config['prize'] / 1800;
            $modeConfig = config("game.main.modes");
            $bonus = $bonus * $times * $modeConfig[$mode]['val'];
            if ($price == 1) {
                $bonus = bcdiv($bonus,2, 4);
            }

            $data[$level] = $bonus;
        }

        return $data;
    }

    public function _encode64($arr)
    {
        if (!is_array($arr)) {
            $arr = explode(',', $arr);
        }

        $chartData = [];
        $m = $n = 0;
        $last = intval($arr[0]);
        $start = $last;
        for ($i = 1; $i < count($arr); $i++) {
            $current = intval($arr[$i]);
            if ($current == $last + 1) {
                $m++;
            } else {
                $n = $current - $last;
                if ($m > 0)
                    $chartData[] = "m" . $m;
                if ($n > 0)
                    $chartData[] = "n" . $n;
                $m = 0;
            }
            $last = $current;
        }
        if ($m > 0) {
            $chartData[] = "m" . $m;
        }

        $string = 's' . $start . implode('', $chartData);
        return 'base64:' . base64_encode(gzencode($string));
    }

    // 必须继承
    public function regexp($codes)
    {
        return false;
    }

    // 必须继承
    public function count($sCodes)
    {
        return 0;
    }

    // 必须继承
    function assertLevel($levelId, $sCodes, Array $numbers)
    {
        return 0;
    }

    // 是否复式
    public function isMulti()
    {
        return (strtoupper(substr($this->id, strlen($this->id) - 2, 2)) == '_S') ? false : true;
    }

    /**
     * 获得号码注数
     * @param $sCode
     * @return int
     */
    public function getCount($sCode)
    {
        return $this->count($sCode);
    }

    /**
     * 需要展开的玩法　展开
     * @param $sCodes
     * @param null $pos
     * @return array
     */
    public function expand($sCodes, $pos = null)
    {
        return [];
    }

    /**
     * 检查位置　任选
     * @param $pos
     * @return bool
     */
    public function checkPos($pos)
    {
        return true;
    }

    /**
     * 检查号码是否合法
     * @param $sCode
     * @return bool
     */
    public function checkBetCode($sCode)
    {
        return $this->regexp($sCode);
    }

    /**
     * 判定是否中奖 多奖级分别判定
     * @param $sBetCodes
     * @param array $aOpenCode
     * @return array
     */
    public function assert($sBetCodes, Array $aOpenCode)
    {
        $results    = [];
        $levels     = $this->getLevels();

        foreach ($levels as $levelId => $level) {
            if (!$levelId) continue;
            $formatCodeArr = array_values(array_intersect_key($aOpenCode, array_flip($level['position'])));
            $num = $this->assertLevel($levelId, $sBetCodes, $formatCodeArr);

            if ($num > 0) {
                // 中奖
                $results[$levelId] = $num;
                if ($this->jzjd != 1) {
                    // 非兼中兼得?
                    break;
                }
            }
        }

        return $results;
    }

    // 获取奖金级别
    public function getLevels()
    {
        return $this->config['levels'];
    }

    // 冷热 & 遗漏
    public function getHotCodes(Array $aOpenCodes, $omission = false)
    {
        if ($this->supportExpand) {
            //对集合型玩法的处理
            $exists = array_flip(array_keys($this->positionsTpl));
        } else {
            $levels = $this->getLevels();
            $exists = [];
            foreach ($levels as $levelId => $level) {
                if (!$levelId) continue;
                foreach ($level['position'] as $pos) {
                    $exists[$pos] = 1;
                }
            }
        }

        $results = [];
        foreach ($aOpenCodes as $aOpencode) {
            $numbers = array_values(array_intersect_key($aOpencode, $exists));

            $result = $this->bingoCode($numbers);
            if (empty($result)) continue;

            if (empty($results)) {
                $results = $result;
            } else {
                if (!$omission) {
                    //冷热
                    $results = array_map(function ($data1, $data2) {
                        return array_map(function ($v1, $v2) {
                            return $v1 + $v2;
                        }, $data1, $data2);
                    }, $results, (array)$result);
                } else {
                    //遗漏
                    $results = array_map(function ($data1, $data2) {
                        return array_map(function ($v1, $v2) {
                            if ($v2 == 1) return 0;
                            return $v1 + 1;
                        }, $data1, $data2);
                    }, $results, (array)$result);
                }
            }
        }

        return $results;
    }

    public function bingoCode(Array $numbers)
    {
        return [];
    }

    // 将lt01 转成 单字符 a b c,以便跟数字形统一逻辑
    public function convertLtCodes($lt, $encode = true)
    {
        $keys   = array_keys(self::$_abc);
        $values = array_values(self::$_abc);

        if ($encode) {
            if (is_array($lt)) {
                foreach ($lt as &$l) {
                    $l = str_replace($keys, $values, $l);
                }

            } else {
                $lt = str_replace($keys, $values, $lt);
            }
        } else {
            if (is_array($lt)) {
                foreach ($lt as &$l) {
                    $l = str_replace($values, $keys, $l);
                }
            } else {
                $lt = str_replace($values, $keys, $lt);
            }
        }

        return $lt;
    }

    // 格式解析
    public function resolve($codes)
    {
        return $codes;
    }

    // 格式还原
    public function unresolve($codes)
    {
        return $codes;
    }

    /**
     * T::格式化开奖号码 对应到位置
     * @param $sOpenCodes
     * @return array
     */
    public function formatCode($sOpenCodes)
    {
        return array_combine($this->config['position'], $sOpenCodes);
    }

    /**
     * 根据号码形态　开奖忽略某些玩法
     * @param $openCode
     * @return bool
     */
    public function openIgnore($openCode)
    {
        return false;
    }

    /**
     * 随机生成注单　测试使用
     * @return string
     */
    public function randomCodes()
    {
        return '';
    }

    /**
     * 获取组合数
     * @param $aBaseArray
     * @param $iSelectNum
     * @return array
     */
    public function getCombination($aBaseArray, $iSelectNum)
    {
        $iBaseNum = count($aBaseArray);
        if ($iSelectNum > $iBaseNum) {
            return [];
        }

        if ($iSelectNum == 1) {
            return $aBaseArray;
        }

        if ($iBaseNum == $iSelectNum) {
            return array(implode(' ', $aBaseArray));
        }

        $sString = '';
        $sLastString = '';
        $sTempStr = '';
        $aResult = [];
        for ($i = 0; $i < $iSelectNum; $i++) {
            $sString .= '1';
            $sLastString .= '1';
        }

        for ($j = 0; $j < $iBaseNum - $iSelectNum; $j++) {
            $sString .= '0';
        }

        for ($k = 0; $k < $iSelectNum; $k++) {
            $sTempStr .= $aBaseArray[$k] . ' ';
        }

        $aResult[] = trim($sTempStr);
        $sTempStr = '';
        while (substr($sString, -$iSelectNum) != $sLastString) {
            $aString = explode('10', $sString, 2);
            $aString[0] = $this->strOrder($aString[0], TRUE);
            $sString = $aString[0] . '01' . $aString[1];
            for ($k = 0; $k < $iBaseNum; $k++) {
                if ($sString{$k} == '1') {
                    $sTempStr .= $aBaseArray[$k] . ' ';
                }
            }
            $aResult[] = trim(substr($sTempStr, 0, -1));
            $sTempStr = '';
        }
        return $aResult;
    }

    /**
     * 获取组合注数
     * @param $iBaseNumber
     * @param $iSelectNumber
     * @return float|int
     */
    public function getCombinCount($iBaseNumber, $iSelectNumber)
    {
        if ($iSelectNumber > $iBaseNumber) {
            return 0;
        }
        if ($iBaseNumber == $iSelectNumber || $iSelectNumber == 0) {
            return 1; //全选
        }
        if ($iSelectNumber == 1) {
            return $iBaseNumber; //选一个数
        }
        $iNumerator = 1; //分子
        $iDenominator = 1; //分母
        for ($i = 0; $i < $iSelectNumber; $i++) {
            $iNumerator *= $iBaseNumber - $i; //n*(n-1)...(n-m+1)
            $iDenominator *= $iSelectNumber - $i; //(n-m)....*2*1
        }
        return $iNumerator / $iDenominator;
    }

    /**
     * 字符排序
     * @param string $sString
     * @param bool $bDesc
     * @return string
     */
    public function strOrder($sString = '', $bDesc = FALSE)
    {
        if ($sString == '') {
            return $sString;
        }

        $aString = str_split($sString);
        if ($bDesc) {
            rsort($aString);
        } else {
            sort($aString);
        }
        return implode('', $aString);
    }

    /**
     * 数组翻转
     * @param $aArr
     * @return array
     */
    public function _ArrayFlip($aArr)
    {
        if (empty($aArr) || !is_array($aArr)) {
            return $aArr;
        }
        $aNewArr = [];
        foreach ($aArr as $k => $v) {
            $aNewArr[$v][] = $k;
        }
        return $aNewArr;
    }

    /**
     * 获取重复号
     * @param $aCode
     * @param int $iRepeats
     * @return array
     */
    public function getRepeat($aCode, $iRepeats = 2)
    {
        $result = [];
        for ($ii = 0; $ii < sizeof($aCode); $ii++) {
            $tCode = explode(' ', $aCode[$ii]);
            $result[$ii] = '';
            for ($iii = 0; $iii < $iRepeats; $iii++) {
                $result[$ii] .= $tCode[$iii] . ' ' . $tCode[$iii] . ' ';
            }
        }
        return $result;
    }

    /* 组合 数 */
    public function nCr($n, $r)
    {
        if ($r > $n) {
            return 0;
        }
        if (($n - $r) < $r) {
            return $this->nCr($n, ($n - $r));
        }
        $return = 1;
        for ($i = 0; $i < $r; $i++) {
            $return *= ($n - $i) / ($i + 1);
        }
        return $return;
    }

    /* 排列 数 */
    public function nPr($n, $r)
    {
        if ($r > $n) {
            return 0;
        }
        if ($r) {
            return $n * ($this->nPr($n - 1, $r - 1));
        } else {
            return 1;
        }
    }

    public function blaBla() {
        return Lottery::blabla();
    }

    // 获取默认redis链接
    public function redisRateConnection() {
        return config("game.main.redis_rate", '');
    }

    /**
     * 计算组选注数
     * @param $aCode
     * @param $count
     * @param $outCodes
     * @return bool
     */
    public function genCodeFromZu($aCode, $count, &$outCodes) {
        if ($count == 0) {
            $outCodes[implode("", $aCode)] =  1;
            return true;
        }

        $count = $count - 1;
        for($i = 0; $i <= $count; $i ++) {
            $tmp1 = $aCode[$i];
            $tmp2 = $aCode[$count];

            $aCode[$count]   = $tmp1;
            $aCode[$i]       = $tmp2;

            $this->genCodeFromZu($aCode, $count, $outCodes);
            $aCode[$count]   = $tmp2;
            $aCode[$i]       = $tmp1;
        }

        return true;
    }
}
