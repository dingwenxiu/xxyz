<?php namespace App\Lib;

/**
 * 验证归类
 * Class Check
 * @package App\Lib
 */
class Check {

    /**
     * 检测地址
     * @param $address
     * @return bool|string
     */
    static function checkAddress($address) {
        if (mb_strlen($address) > 64 || mb_strlen($address) < 2) {
            return "对不起, 地址为2 ~ 64 长度!";
        }

        return true;
    }

    /**
     * 检测邮件
     * @param $email
     * @return bool|string
     */
    static function checkMail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "对不起, 邮箱不合法";
        }

        return true;
    }

    /**
     * 检测域名
     * @param $domain
     * @return bool|string
     */
    static function checkDomain($domain)
    {
        if ( !preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \.)*[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?\.[a-z]{2,6} $/ix', $domain)) {
            return "对不起, 不合法的域名";
        }
        return true;
    }

    /**
     * 手机号
     * @param $mobile
     * @return bool|string
     */
    static function checkMobile($mobile)
    {
        if (!preg_match("/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\d{8}$/", $mobile)) {
            return "对不起, 不合法的手机号";
        }
        return true;
    }

    /**
     * 检测用户名
     * @param $realName
     * @return bool|string
     */
    static function checkRealName($realName)
    {
        if (mb_strlen($realName) > 64 || mb_strlen($realName) < 2) {
            return "对不起, 姓名为2 ~ 64 长度!";
        }
        return true;
    }

    /**
     * 检测邮政编码
     * @param $code
     * @return bool|string
     */
    static function checkZipCode($code)
    {
        $code = preg_replace("/[\. -]/", "", $code);
        if(!preg_match("/^\d{6}$/", $code))
        {
            return "对不起, 无效的邮政编码!";
        }
        return true;
    }

}
