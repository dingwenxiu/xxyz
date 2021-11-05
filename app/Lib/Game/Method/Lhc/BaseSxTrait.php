<?php namespace App\Lib\Game\Method\Lhc;

use App\Lib\Common\Lunar;

trait BaseSxTrait {

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

    /**
     * 生肖 格式化
     * @return array
     */
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
}
