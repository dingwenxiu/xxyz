<?php
namespace App\Lib;

use App\Jobs\User\Behavior;

class Help
{

    /**
     * Api返回
     * @param $msg
     * @param int $status
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    static function returnApiJson($msg, $status = 0, $data = [], $code = 200) {

        $data = [
            'success'       => $status ? true :false,
            'msg'           => $msg,
            'data'          => $data,
            'code'          => $code
        ];

        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE);

    }

    /**
     * 2位小数
     * @param $number
     * @return string
     */
    static function number2($number) {
        return bcdiv($number, 100, 2);
    }

    /**
     * 4位小数
     * @param $number
     * @return string
     */
    static function number4($number) {
        return bcdiv($number, 10000, 4);
    }

    /**
     * 字符串截取函数
     * @param $str
     * @param $length
     * @param bool $dot
     * @param bool $isFront
     * @return bool|string
     */
    static function cutStr($str, $length, $dot = false, $isFront = false) {
        $resStr =  mb_substr($str, 0, $length, 'utf-8');
        if ($dot && $resStr !=  $str) {
            if ($isFront) {
                $resStr  = "****" . $resStr;
            } else {
                $resStr  .= "****";
            }
        }
        return $resStr;
    }

    static function cutFrontStr($str, $length, $dot = true) {
        $resStr =  mb_substr($str, -$length, $length, 'utf-8');
        if ($dot && $resStr !=  $str) {
            $resStr  = "**** " . $resStr;
        }
        return $resStr;
    }

    /**
     * 随机概率
     * @param $maxProbability
     * @return true;
     */
    static function randProbability($maxProbability) {
        $number = mt_rand(1, 10000);
        if ($number <= $maxProbability * 100) {
            return $number;
        }

        return false;
    }

    /**
     * 保存用户行为
     * @param $type
     * @param $userId
     * @param $data
     */
    static function savePlayerBehavior($type, $userId, $data) {
        // 行为
        dispatch( (new Behavior($type, [
            'ip'                    => real_ip(),
            'user_id'               => $userId,
            'data'                  => $data,
            'agent'                 => \Browser::userAgent(),
            'device_type'           => \Browser::deviceFamily() . "|" . \Browser::deviceModel(),
            'platform_type'         => \Browser::platformName() . "|" . \Browser::platformVersion(),
            'browser_type'          => \Browser::browserName()  . "|" . \Browser::browserVersion(),
        ]))->onQueue('behavior') );
    }
}
