<?php
// 自定义helper

// hashId
if (!function_exists('hashId')) {
    function hashId()
    {
        return new Hashids\Hashids('', 8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
    }
}

// hashID  Decode
if (!function_exists('hashId_decode')) {
    function hashId_decode($id)
    {
        $idArr = hashId()->decode($id);
        if ($idArr && isset($idArr[0])) {
            return $idArr[0];
        }

        return 0;
    }
}

// 扔进队列
if (!function_exists('telegramSend')) {
    function telegramSend($type, $msg, $partnerSign = "system")
    {
        if ($type == "send_code") {
            $queue = "login_code";
        } else {
            $queue = "notify";
        }

        jtq(new \App\Jobs\Notify\Telegram($type, ["msg" => $msg, "partner_sign" => $partnerSign]), $queue);
        return true;
    }
}

// 扔进队列
if (!function_exists('jtq')) {
    function jtq($job, $queue)
    {
        dispatch(($job)->onQueue($queue));
    }
}

if (!function_exists('moneyUnitTransferIn')) {
    function moneyUnitTransferIn($amount)
    {
        $unit = config('game.main.money_unit', 10000);
        return $unit * $amount;
    }
}

if (!function_exists('moneyUnitTransferOut')) {
    function moneyUnitTransferOut($amount)
    {
        $unit = config('game.main.money_unit', 10000);
        return $amount / $unit;
    }
}

if (!function_exists('db')) {
    function db($connection = null)
    {
        if (is_null($connection)) {
            return app('db');
        } else {
            return app('db')->connection($connection);
        }
    }
}

// 真实IP
if (!function_exists('real_ip')) {
    function real_ip()
    {
        return getRealIP();
    }
}

// 日期时间段
if (!function_exists('getDaySet')) {

    function getDaySet($startTime, $endTime)
    {
        $daySet = [];

        while ($startTime <= $endTime) {
            $daySet[] = date("Ymd", $startTime);
            $startTime += 86400;
        }

        return $daySet;
    }
}

if ( ! function_exists('isProductEnv')) {
    function isProductEnv()
    {
        if (app()->environment() == 'product') {
            return true;
        }

        return false;
    }
}

if ( ! function_exists('envName')) {
    function envName()
    {
        return env('APP_NAME', "default");
    }
}

// 获取缓存 = 总后台
if (!function_exists('configure')) {
    function configure($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('Configure');
        } else {
            return \App\Lib\Logic\Cache\ConfigureCache::getSystemConfigure($key, $default);
        }
    }
}

// 获取缓存 = 商户
if (!function_exists('partnerConfigure')) {
    function partnerConfigure($partnerSign, $key = null, $default = null)
    {
        return \App\Lib\Logic\Cache\ConfigureCache::getPartnerConfigure($partnerSign, $key, $default);
    }
}

// 小数２
if (!function_exists('number2')) {
    function number2($number)
    {
        return \App\Lib\Help::number2($number);
    }
}

// 小数4
if (!function_exists('number4')) {
    function number4($number)
    {
        return \App\Lib\Help::number4($number);
    }
}

// 获取所有的数据库链接
if (!function_exists('getAllDbConnect')) {
    function getAllDbConnect()
    {
        $config = config("database.connections");
        return array_keys($config);
    }
}

// 是否合法域名
if (!function_exists('isValidDomainName')) {
    function isValidDomainName($domainName)
    {
        return preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $domainName);
    }
}

// 获取公告
if (!function_exists('noticeImg')) {
    function noticeImg($path, $partnerSign = 'system')
    {
        $openOssStorage = configure("system_open_oss_storage", 1);
        if ($openOssStorage) {
            $partnerSign    = strtolower($partnerSign);
            $baseUrl        = configure('system_pic_base_url', "https://img.play322.com");

            return $baseUrl . "/{$partnerSign}/" . $path;
        } else {
            $partnerSign = strtolower($partnerSign);
            $baseUrl = configure('system_pic_base_url', "https://img.play322.com");
            return $baseUrl . "/upload/{$partnerSign}/" . $path;
        }

    }
}

// 获取活动
if (!function_exists('activityImg')) {
    function activityImg($path)
    {
        $openOssStorage = configure("system_open_oss_storage", 1);
        if ($openOssStorage) {
            $baseUrl        = configure('system_pic_base_url', "https://img.play322.com");

            return $baseUrl . "/" . $path;
        } else {
            $baseUrl = configure('system_pic_base_url', "https://img.play322.com");
            return $baseUrl . "/upload/" . $path;
        }

    }
}

// 获取VIP标
if (!function_exists('vipIcon')) {
    function vipIcon($path)
    {
        $openOssStorage = configure("system_open_oss_storage", 1);
        if ($openOssStorage) {
            $baseUrl        = configure('system_pic_base_url', "https://img.play322.com");

            return $baseUrl . "/" . $path;
        } else {
            $baseUrl = configure('system_pic_base_url', "https://img.play322.com");
            return $baseUrl . "/upload/" . $path;
        }
    }
}

// 获取彩种图标
if (!function_exists('lotteryIcon')) {
    function lotteryIcon($path)
    {
        $openOssStorage = configure("system_open_oss_storage", 1);
        if ($openOssStorage) {
            $baseUrl        = configure('system_pic_base_url', "https://img.play322.com");

            return $baseUrl . "/" . $path;
        } else {
            $baseUrl = configure('system_pic_base_url', "https://img.play322.com");
            return $baseUrl . "/upload/" . $path;
        }
    }
}

// 获取玩家图标
if (!function_exists('playerIcon')) {
    function playerIcon($path)
    {
        $openOssStorage = configure("system_open_oss_storage", 1);
        if ($openOssStorage) {
            $baseUrl        = configure('system_pic_base_url', "https://img.play322.com");

            return $baseUrl . "/" . $path;
        } else {
            $baseUrl = configure('system_pic_base_url', "https://img.play322.com");
            return $baseUrl . "/upload/" . $path;
        }

    }
}

// 获取logo
if (!function_exists('getLogo')) {
    function getLogo($partnerSign = 'system')
    {
        $openOssStorage = configure("system_open_oss_storage", 1);
        if ($openOssStorage) {
            $partnerSign    = strtolower($partnerSign);
            $baseUrl        = configure('system_pic_base_url', "https://img.play322.com");

            return $baseUrl . "/{$partnerSign}/" . "logo.png";
        } else {
            $partnerSign = strtolower($partnerSign);
            $baseUrl = configure('system_pic_base_url', "https://img.play322.com");
            return $baseUrl . "/upload/{$partnerSign}/logo.png";
        }
    }
}

function isDatetime($date)
{
    $patten = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/";
    if (preg_match($patten, $date)) {
        return true;
    }
    return false;
}

function isDateDay($date)
{
    $patten = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])$/";
    if (preg_match($patten, $date)) {
        return true;
    }
    return false;
}

function substrString($str, $length, $dot = false)
{
    $resStr = mb_substr($str, 0, $length, 'utf-8');

    if ($dot && $resStr != $str) {
        $resStr .= "...";
    }
    return $resStr;
}

function send_warning_mail($title, $data)
{
    if (app()->environment() != 'product') {
        $title = "(测试)" . $title;
    }

    $emails = [];

    // 邮件阀值
    $mailData = array(
        'emails' => $emails,
        'subject' => $title,
        'data' => $data,
        'tpl' => 'emails.draw',
    );

    if (!empty($emails)) {
        dispatch((new \App\Jobs\Mail($mailData))->onQueue('mail'));
    }
}

//秒转成 年-天-小时-分-秒
function second2Time($time)
{
    $value = array(
        "years" => 0, "days" => 0, "hours" => 0,
        "minutes" => 0, "seconds" => 0,
    );
    if ($time >= 31556926) {
        $value["years"] = floor($time / 31556926);
        $time = ($time % 31556926);
    }
    if ($time >= 86400) {
        $value["days"] = floor($time / 86400);
        $time = ($time % 86400);
    }
    if ($time >= 3600) {
        $value["hours"] = floor($time / 3600);
        $time = ($time % 3600);
    }
    if ($time >= 60) {
        $value["minutes"] = floor($time / 60);
        $time = ($time % 60);
    }
    $value["seconds"] = floor($time);

    $str = '';
    if ($value['years']) {
        $str .= $value["years"] . "年";
    }
    if ($value['days']) {
        $str .= $value["days"] . "天";
    }
    if ($value['hours']) {
        $str .= $value["hours"] . "小时";
    }

    $str .= $value["minutes"] . "分" . $value["seconds"] . "秒";

    return $str;
}

function getIpDesc($ip = "", $all = false)
{
    if ($ip == "") {
        $ip = getRealIP();
    }

    if ($ip == '127.0.*.*' || $ip == '127.0.0.*' || $ip == '127.0.0.1') {
        return "本机地址";
    }

    //依赖ip库
    $res = \Ip::find($ip);

    if ($res == "N/A") {
        return "未知地区";
    }

    if ($res[0] = $res[1]) {
        $return = $res[0];
    } else {
        $return = $res[0] . " " . $res[1];
    }

    if ($all && isset($res[2])) {
        $return .= " " . $res[2];
    }

    if ($all && isset($res[3])) {
        $return .= " " . $res[3];
    }

    return $return;
}

function getIpAddress($ip) {
    $location   = \Torann\GeoIP\Facades\GeoIP::getLocation($ip);

    $address = "";
    if (isset($location['country'])) {
        $address .= $location['country'];
    }

    if (isset($location['city'])) {
        $address .= " , " . $location['city'];
    }

    return $address ? $address : "";
}

function getRealIP()
{
    static $realip = NULL;
    if ($realip !== NULL) {
        return $realip;
    }

    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr AS $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}

// 替换IP 后1位保留星号
function ipHide($ip)
{
    return preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/is', "$1.$2.$3.*", $ip);
}

// 替换用户名***
function usernameHide($username)
{
    $prv = mb_substr($username, 0, 1);

    $last = mb_substr($username, mb_strlen($username) - 1, 1);

    return $prv . '***' . $last;
}

// 中文日期 星期几
function cnWeeks()
{
    $arr = array('天', '一', '二', '三', '四', '五', '六');
    return $arr[date('w')];
}

// 生成客服地址
function genCustomUrl($user)
{

    // 客服
    $liveConfig = config('web.live800');
    $key = $liveConfig['key'];
    $params = $liveConfig['params'];

    $l = count(explode('|', $user->raleid)) - 1;

    $levels = [
        1 => '直属',
        2 => '总代',
    ];

    $directors = config('game.director');
    if (isset($directors[$user->topid])) {
        $levels = [
            1 => '主管',
            2 => '主管-直属',
            3 => '主管-总代',
        ];
    }

    $role = '代理';
    if (isset($levels[$l])) {
        $role = $levels[$l];
    } else {
        if ($user->type == \App\Models\User::PLAYER_TYPE_MEMBER) {
            $role = '用户';
        }
    }

    $role .= '-' . $user->point;

    $hashParams = [];
    $hashParams['userId'] = $user->id;
    $hashParams['name'] = $user->username . '(' . $role . ')';
    $hashParams['memo'] = $user->username . '(' . $user->nickname . ')';
    $hashParams['timestamp'] = time() * 1000;
    $hashParams['hashCode'] = md5(urlencode($hashParams['userId'] . $hashParams['name'] . $hashParams['memo'] . $hashParams['timestamp'] . $key));
    $paramStr = "userId=" . $hashParams['userId'] . "&name=" . $hashParams['name'] . "&memo=" . $hashParams['memo'] . "&timestamp=" . $hashParams['timestamp'] . "&hashCode=" . $hashParams['hashCode'];
    $params['info'] = urlencode($paramStr);

    $url = $liveConfig['url'] . "?jid={$params['jid']}&s=1&companyID={$params['companyID']}&configID={$params['configID']}&codeType={$params['codeType']}&info={$params['info']}";
    return $url;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

if (!function_exists('casino_request')) {
    /**
     * api请求
     * @param string $method GET POST.
     * @param string $call_url Url.
     * @param array $params 请求参数.
     * @param string $header 请求头.
     * @param boolean $cuestomerquest 请求类型.
     * @param integer $https Https 1.
     * @param integer $locaIp 请求ip.
     * @return string
     */
    function casino_request(string $method, string $call_url, array $params, string $header, bool $cuestomerquest, int $https, int $locaIp)
    {
        $ch_hook = curl_init();
        curl_setopt($ch_hook, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_hook, CURLOPT_URL, $call_url);
        if ($https) {
            curl_setopt($ch_hook, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_hook, CURLOPT_SSL_VERIFYHOST, false);
        }
        if ($locaIp) {
            curl_setopt($ch_hook, CURLOPT_INTERFACE, config('game.pub.AddressIp'));
        }
        curl_setopt($ch_hook, CURLOPT_HEADER, false);
        curl_setopt($ch_hook, CURLINFO_HEADER_OUT, false);

        if (!empty($header)) {
            curl_setopt($ch_hook, CURLOPT_HTTPHEADER, $header);
        }
        if ($cuestomerquest) {
            curl_setopt($ch_hook, CURLOPT_CUSTOMREQUEST, $method);
        }
        $output = '';
        switch ($method) {
            case 'POST':
                curl_setopt($ch_hook, CURLOPT_POST, true);
                if (is_array($params)) {
                    curl_setopt($ch_hook, CURLOPT_POSTFIELDS, http_build_query($params));
                } else {
                    curl_setopt($ch_hook, CURLOPT_POSTFIELDS, $params);
                }
                $output = curl_exec($ch_hook);
                //                Log::channel('casino-success')->info(json_encode($output));
                if (curl_errno($ch_hook)) {
                    //                    Log::channel('casino-err')->info(json_encode(curl_error($ch)));
                    curl_close($ch_hook);
                    return false;
                }
                return $output;
                break;
            case 'GET':
                $output = curl_exec($ch_hook);
                //                Log::channel('casino-success')->info(json_encode($output));
                if (curl_errno($ch_hook)) {
                    //                    Log::channel('casino-err')->info(json_encode(curl_error($ch)));
                    curl_close($ch_hook);
                    return false;
                }
                return $output;
                break;
        }
    }
}
