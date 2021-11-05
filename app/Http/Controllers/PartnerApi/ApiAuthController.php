<?php

namespace App\Http\Controllers\PartnerApi;

use App\Lib\Help;
use App\Lib\Logic\Cache\PartnerCache;
use App\Lib\Telegram\TelegramTrait;
use App\Models\Partner\PartnerAdminGroup;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * 商户后台登录逻辑
 * 命名：
 *   1. 商户端管理用户用-$partnerAdminUser
 *   2. 总后台管理用户用-$adminUser
 * Tom 2019.09
 * Class ApiAuthController
 * @package App\Http\Controllers\PartnerApi
 */
class ApiAuthController extends ApiBaseController
{
    use Authenticatable;
    use TelegramTrait;

    // 登录
    public function login()
    {
        // 商户邮箱校验
        $email = trim(request("email"));
        if (!$email) {
            return Help::returnApiJson("对不起, 您输入的邮箱为空!", 0);
        }

        $codeOne = base64_decode(trim(request("password")));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $password = substr($final, 5, 37);
        // 商户密码检验
        if (!$password) {
            return Help::returnApiJson("对不起, 您输入的密码为空!", 0);
        }

        $isProd = isProductEnv();
        $tCode  = request('code');
        if ($isProd && !$tCode) {
            return Help::returnApiJson("请输入安全码!!!", 0);
        }

        $cacheCode = PartnerCache::getLoginCode($email);

        if (!$cacheCode || $cacheCode != $tCode) {
            return Help::returnApiJson("对不起, 验证码不正确或已过期!!!", 0);
        }
        PartnerCache::delLoginCode($email);

        // 商户登录验证
        $credentials = ['email' => $email, 'password' => $password,'partner_sign'=>$this->partnerSign];
        
        if (!$token = auth('partner_api')->attempt($credentials)) {
            return Help::returnApiJson("对不起, 用户名或密码错误!", 0);
        }

        // 业务数据更正
        $partnerAdminUser = auth('partner_api')->user();

        // 管理员状态
        if ($partnerAdminUser->status == 0) {
            return Help::returnApiJson("对不起, 管理员已停用!", 0);
        }

        $partnerAdminUser->last_login_ip    = real_ip();
        $partnerAdminUser->last_login_time  = time();
        $partnerAdminUser->save();

        $admin = PartnerAdminGroup::where('id', $partnerAdminUser->group_id)->where('acl', '!=','*')->first();
        // 返回数据
        $data = [
            'group_id'          => $admin->id ?? '',
            'name'              => $admin->name ?? '',
            'token'             => $token,
            'token_type'        => 'bearer',
            'expires_in'        => auth('partner_api')->factory()->getTTL() * 60,
            'user_id'           => $partnerAdminUser->id,
            'username'          => $partnerAdminUser->username,
        ];
        $data['system_pic_base_url'] = configure("system_pic_base_url");
        return Help::returnApiJson('登录成功', 1, $data);
    }

    /**
     * 发送二维码
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function sendCode() {
        // 用户校验
        $email = trim(request("email"));
        if (!$email) {
            return Help::returnApiJson("对不起, 您输入的账号为空!", 0);
        }

        // 密码检验
        $codeOne = base64_decode(trim(request("password")));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $password = substr($final, 5, 37);
        if (!$password) {
            return Help::returnApiJson("对不起, 您输入的密码为空!", 0);
        }

        // 登录验证
        $credentials = ['email' => $email, 'password' => $password];
        if (!$token = auth('partner_api')->attempt($credentials)) {

            return Help::returnApiJson("对不起, 用户名或密码错误!", 0);
        }

        // 业务数据更正
        $partnerAdminUser = auth('partner_api')->user();

        $emailArr   = explode("@", $email);
        $l = strlen($emailArr[0]); $x = '';
        for($i = 0; $i < $l-2; ++ $i) {
            $x .= '*';
        }

        $username   = str_replace(substr($emailArr[0],2,-1), $x, $emailArr[0]) . "@" . $emailArr[1];
        $ip         = real_ip();
        $code       = mt_rand(10000, 99999);

        PartnerCache::saveLoginCode($email, $code);

        $text = '<b>用户名 : '.$username.'</b>' . chr(10);
        $text .= '<b>I        P : '.$ip.'</b>' . chr(10);
        $text .= '<b>验证码 : '.$code.'</b>';
        $res = telegramSend("send_code", $text, $partnerAdminUser->partner_sign);

        if ($res) {
            return Help::returnApiJson("发送安全码成功, 请从相关群组获取!!", 1);
        } else {
            return Help::returnApiJson($res['msg'], 0);
        }
    }

    // 获取站点信息
    public function lang() {
        return Help::returnApiJson([]);
    }

    // 登出
    public function logout()
    {
        auth()->guard("partner_api")->logout();
        return Help::returnApiJson('登出成功!', 1);
    }

    // 守卫
    protected function guard()
    {
        return Auth::guard('partner_api');
    }

    // 用户名字段
    public function username()
    {
        return 'email';
    }
}
