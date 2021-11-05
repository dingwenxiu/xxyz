<?php namespace App\Console\Commands\Test;

use App\Console\Commands\Command;
use App\Lib\Common\Lunar;
use App\Lib\Shm\ShmCache;
use App\Models\Game\LotteryJackpotPlan;

class CmdTestEncode extends Command {

    protected $signature = 'test:encode';
    protected $description = "生成投注号码!";

    static $nameOption = [
        "ZHU"   => "猪",
        "GOU"   => "狗",
        "JI"    => "鸡",
        "HOU"   => "猴",
        "YANG"  => "羊",
        "MA"    => "马",
        "SHE"   => "蛇",
        "LONG"  => "龙",
        "TU"    => "兔",
        "HU"    => "虎",
        "NIU"   => "牛",
        "SHU"   => "鼠",
    ];

    public function handle()
    {
        $projectArr     = LotteryJackpotPlan::where('id', '>', 0)->get();

        $startTime      = microtime(true);

        $data = config('game.all_code_ssc', []);
        foreach ($projectArr as $project) {
            $tmp   = json_decode($project->detail, true);
            foreach ($tmp as $key => $value) {
                $data[$key] += $value;
            }
        }

        $endTime = microtime(true);

        $this->info(($startTime - $endTime));
        info(11, $data);

//        foreach ($data as $key => $value) {
//            $this->info($key . "=======================" . $value);
//        }


//        $aCodes = explode('&', "2&2&4&7&7");
//        $aP1    = $this->getRepeatCombination($aCodes, 2);
//        var_dump($aP1);

        // $res = $this->testRedis();
    }

    public function getSxCodeFromYear() {
        $_year   = date('Y');
        $_month  = date('m');
        $_day    = date('d');

        $lunar      = new Lunar();
        $dateArr    = $lunar->convertSolarToLunar($_year, $_month, $_day);
        $year       = isset($dateArr[0]) ? $dateArr[0] : date("Y");

        $sxSortArr = [
            "2019" => [1 => "ZHU",  2 => "GOU",     3 => "JI",      4 => "HOU",     5 => "YANG",    6 => "MA",      7 => "SHE",     8 => "LONG",    9 => "TU",      10 => "HU",     11 => "NIU",    12 => "SHU"],
            "2020" => [1 => "SHU",  2 => "ZHU",     3 => "GOU",     4 => "JI",      5 => "HOU",     6 => "YANG",    7 => "MA",      8 => "SHE",     9 => "LONG",    10 => "TU",     11 => "HU",     12 => "NIU"],
            "2021" => [1 => "NIU",  2 => "SHU",     3 => "ZHU",     4 => "GOU",     5 => "JI",      6 => "HOU",     7 => "YANG",    8 => "MA",      9 => "SHE",     10 => "LONG",   11 => "TU",     12 => "HU"],
            "2022" => [1 => "HU",   2 => "NIU",     3 => "SHU",     4 => "ZHU",     5 => "GOU",     6 => "JI",      7 => "HOU",     8 => "YANG",    9 => "MA",      10 => "SHE",    11 => "LONG",   12 => "TU"],
            "2023" => [1 => "TU",   2 => "HU",      3 => "NIU",     4 => "SHU",     5 => "ZHU",     6 => "GOU",     7 => "JI",      8 => "HOU",     9 => "YANG",    10 => "MA",     11 => "SHE",    12 => "LONG"],
            "2024" => [1 => "LONG", 2 => "TU",      3 => "HU",      4 => "NIU",     5 => "SHU",     6 => "ZHU",     7 => "GOU",     8 => "JI",      9 => "HOU",     10 => "YANG",   11 => "MA",     12 => "SHE"],
            "2025" => [1 => "SHE",  2 => "LONG",    3 => "TU",      4 => "HU",      5 => "NIU",     6 => "SHU",     7 => "ZHU",     8 => "GOU",     9 => "JI",      10 => "HOU",    11 => "YANG",   12 => "MA"],
            "2026" => [1 => "MA",   2 => "SHE",     3 => "LONG",    4 => "TU",      5 => "HU",      6 => "NIU",     7 => "SHU",     8 => "ZHU",     9 => "GOU",     10 => "JI",     11 => "HOU",    12 => "YANG"],
            "2027" => [1 => "YANG", 2 => "MA",      3 => "SHE",     4 => "LONG",    5 => "TU",      6 => "HU",      7 => "NIU",     8 => "SHU",     9 => "ZHU",     10 => "GOU",    11 => "JI",     12 => "HOU"],
            "2028" => [1 => "HOU",  2 => "YANG",    3 => "MA",      4 => "SHE",     5 => "LONG",    6 => "TU",      7 => "HU",      8 => "NIU",     9 => "SHU",     10 => "ZHU",    11 => "GOU",    12 => "JI"],
            "2029" => [1 => "JI",   2 => "HOU",     3 => "YANG",    4 => "MA",      5 => "SHE",     6 => "LONG",    7 => "TU",      8 => "HU",      9 => "NIU",     10 => "SHU",    11 => "ZHU",    12 => "GOU"],
        ];

        $data = [];

        $sxCodeAllArr = config("game.sx.sx_code", []);
        if (isset($sxSortArr[$year])) {
            $sx = $sxSortArr[$year];
            foreach ($sx as $index => $_sign) {
                $data[$_sign] = $sxCodeAllArr[$index];
                $data[$_sign]['name'] = self::$nameOption[$_sign];
            }
        }

        return $data;
    }

    public function testDB() {
        $this->info(microtime(TRUE));
        $aCode = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20"];

        $total = 0;
        foreach ($aCode as $a1) {
            foreach ($aCode as $a2) {
                foreach ($aCode as $a3) {
                    foreach ($aCode as $a4) {
                        foreach ($aCode as $a5) {
                            foreach ($aCode as $a6) {
                                foreach ($aCode as $a7) {
                                    foreach ($aCode as $a8) {
                                        if ($a1 == $a2 || $a2 == $a3 || $a3 == $a4 || $a4 == $a5 || $a5 == $a6 || $a6 == $a7 || $a7 == $a8) {
                                            continue;
                                        }

                                        $total ++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->info($total);

        $this->info(microtime(TRUE));
    }

    public function testRedis() {
        $this->info(microtime(TRUE));

        $plan = 1270;
        $n = 0;
        for ($i = 1; $i <= 100; $i++) {
            $startTime = microtime(TRUE);
            $data  = ShmCache::getCache($plan);
            if (!$data) {
                $data = [];
            }

            for ($a = 0; $a <= 9; $a++) {
                for ($b = 0; $b <= 9; $b++) {
                    for ($c = 0; $c <= 9; $c++) {
                        for ($d = 0; $d <= 9; $d++) {
                            for ($e = 0; $e <= 9; $e++) {

                                $_key = $a . $b . $c . $d . $e;
                                if (isset($data[$_key])) {
                                    $data[$_key] += 1;
                                } else {
                                    $data[$_key] = 1;
                                }
                            }
                        }
                    }
                }
            }



            ShmCache::saveCache($plan, $data);
            $endTime = microtime(TRUE);

            $time = $startTime - $endTime;

            $this->info($time);
            $n ++;
        }

        $data  = ShmCache::getCache($plan);

        $this->info("\n\r");
        $this->info($data['11111']);
        $this->info(microtime(TRUE));
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


    public function getRepeatCombination($codeArr, $count = 2)
    {
        $data       = [];
        $codeArr    = $this->getCombination($codeArr, $count);

        foreach ($codeArr as $_code) {
            $_codeArr = explode(' ', $_code);
            if (count(array_unique($_codeArr)) == 1) {
                $data[] = implode('', $_codeArr);
            }
        }

        return $data;
    }
}
