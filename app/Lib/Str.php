<?php namespace App\Lib;

use App\Models\Admin\AdminMenu;
use Illuminate\Support\Facades\Cache;

class Str extends \Illuminate\Support\Str {
    /**
     * 替换中间为星号
     * @param $str
     * @param $firstLength
     * @param $lastLength
     * @param string $delimiter
     * @return string
     */
    static function replaceMiddle($str, $firstLength, $lastLength, $delimiter = "***") {
        $totalLength    = mb_strlen($str, 'utf-8');

        if ($totalLength <= $firstLength + $lastLength) {
            return $str;
        }

        $firstStr       = mb_substr($str, 0, $firstLength, 'utf-8');
        $lastStr        = mb_substr($str, -$lastLength, $lastLength, 'utf-8');

        return $firstStr . $delimiter . $lastStr;
    }

    static function strToGBK($strText) {
        $encode = mb_detect_encoding($strText, array('UTF-8','GB2312','GBK'));
        if($encode == "UTF-8") {
            return @iconv('UTF-8','GB18030', $strText);
        } else {
            return $strText;
        }
    }

    static function strToUTF8($strText) {
        $encode = mb_detect_encoding($strText, array('UTF-8','GB2312','GBK'));
        if($encode != "UTF-8") {
            return @iconv($encode,'UTF-8', $strText);
        } else {
            return $strText;
        }
    }
}