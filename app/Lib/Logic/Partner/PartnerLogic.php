<?php
namespace App\Lib\Logic\Partner;

/**
 * 商户逻辑　
 * 2020 整理
 * Class PartnerLogic
 * @package App\Lib\Lottery\JackpotLogic
 */
class PartnerLogic
{
    static $key = "1129ui7uer";


    static function getPartnerKey($key) {
        return self::rc4(base64_decode($key));
    }

    static function genPartnerKey($sign) {
        return base64_encode(self::rc4(md5($sign . "|" . self::$key)));
    }

    /**
     * Rc4加密解密
     * @param $str
     * @return string
     */
    static function rc4($str)
    {
        $pwd = md5("partner9987UIF7SFJ#DFK");

        $key[]       = "";
        $box[]       = "";
        $pwd_length  = strlen($pwd);
        $data_length = strlen($str);
        $cipher      = '';

        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $key[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($str[$i]) ^ $k);
        }

        return $cipher;
    }
}
