<?php

namespace App\Http\Controllers\Api;

// 登录
use App\Lib\Help;
use App\Lib\Logic\Cache\ApiCache;
use App\Models\Player\Player;
use App\Models\Player\PlayerLog;
use App\Models\Player\PlayerIp;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Player\PlayerInviteLink;

class AuthController extends ApiBaseController
{
    use Authenticatable;

    // 登录
    public function login()
    {
        $code = trim(request("code", ''));

        if (!\Captcha::check($code)) {
            // return Help::returnApiJson("对不起, 您输入的验证码不正确!", 0);
        }

        $username = trim(request("username"));
        if (!$username) {
            return Help::returnApiJson("对不起, 您输入的用户名为空!"."1", 0);
        }
        $codeOne = base64_decode(trim(request("password")));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $password = substr($final, 5, 37);
        if (!$password) {
            return Help::returnApiJson("对不起, 您输入的密码为空!", 0);
        }

        $credentials = ['username' => $username, 'password' => $password, 'partner_sign'=>$this->partner->sign];

        if (!$token = auth('api')->attempt($credentials)) {
            return Help::returnApiJson("对不起, 用户名或密码错误!", 0);
        }

        $user = auth('api')->user();

        // 玩家是否被停用
        if ($user->status == 0) {
            return Help::returnApiJson("对不起, 用户已停用!", 0);
        }

        // 玩家是否被禁止登录
        if ($user->frozen_type == 1) {
            return Help::returnApiJson("对不起, 您已被禁止登录!", 0);
        }

        $user->last_login_ip = real_ip();
        $user->last_login_time = time();
        $user->save();

        $data = [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_at'   => now()->addMinutes(auth('api')->factory()->getTTL())->format('Y-m-d H:i:s'),
        ];

        // 清除 踢线缓存

        ApiCache::cleanKickLine($user);
        PlayerLog::saveItem($user, 'login');
        PlayerIp::saveItem($user,'login');
        
        return Help::returnApiJson('登录成功', 1, $data);
    }

    /**
     * 首页注册
     * @return JsonResponse
     * @throws \Exception
     */
    public function register()
    {
        $parentUsername = partnerConfigure($this->partner->sign, 'player_default_register_parent_username');
        $type = partnerConfigure($this->partner->sign, 'player_default_type',Player::PLAYER_TYPE_PROXY);

         // 6. 默认奖金组
        $prizeGroup = partnerConfigure($this->partner->sign,"player_open_register_default_group", 1800);

        // 如果是邀请码 注册
        $keyword = request()->input('keyword','');
        if(!empty(trim($keyword))) {
            $playInviteLink = PlayerInviteLink::where('code', $keyword)->where('partner_sign',$this->partner->sign)->first();
            
            if($playInviteLink)
            {
               $expireTime = strtotime($playInviteLink->created_at) + intval($playInviteLink->expired_at) * 86400;
               if($playInviteLink->expired_at == 0 || time() < $expireTime)
               {
                   $parentUsername = $playInviteLink->username;
                   $type           = $playInviteLink->type;
                   $prizeGroup     = $playInviteLink->prize_group;
               }
            }
        }

        $parent = Player::findByUsername($parentUsername,$this->partner->sign);

        // 如果找不到用户 用默认ID为10000的
        if (!isset($parent) || !$parent) {
            $parent = Player::find(10000);
        }
       
        if ($parent->partner_sign != $this->partner->sign) {
            return Help::returnApiJson("对不起, 无效的注册渠道!", 0);
        }

        // 1. 当前用户如果为会员 不能添加
        if ($parent->type == Player::PLAYER_TYPE_PLAYER) {
            return Help::returnApiJson("对不起, 会员不能有下级!", 0);
        }

        // 2. 检查用户名
        $username       = trim(request('username', ''));
        $resUsername    = Player::checkUsername($username,$this->partner->sign);
        if (true !== $resUsername) {
            return Help::returnApiJson("{$resUsername}", 0);
        }

        // 手机号码
        $phoneNumber    = request('phone');
		$resPhone       = Player::checkPhoneNumber($phoneNumber,$this->partner->sign);
		if (true !== $resPhone) {
			return Help::returnApiJson("{$resPhone}", 0);
		}

        // 3. 检查密码
        $codeO = base64_decode(trim(request("password", '')));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);
        $resPassword    = Player::checkPassword($password);
        if (true !== $resPassword) {
            return Help::returnApiJson("{$resPassword}!", 0);
        }

        // 4. 用户名和密码不能一样
        if ($username == $password) {
            return Help::returnApiJson("对不起, 用户名和密码不能重复!", 0);
        }

        // 5. 1分钟中注册不能超过 n 个
        $maxRegisterCountOneIp = partnerConfigure($this->partner->sign,"player_max_register_one_ip_minute", 20);
        $now        = time();
        $nowCount   = $parent->getUserRegisterCount($now - 60, $now);
        if ($nowCount > $maxRegisterCountOneIp) {
            return Help::returnApiJson("对不起, 当前注册通道过于繁忙,请稍后再试!", 0);
        }

        $res = $parent->addChild($username, $password, $type, $prizeGroup, $parent->is_tester, $phoneNumber);
        if (!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }
        return Help::returnApiJson("恭喜,注册成功!", 1);
    }

    /**
     * 验证码
     * @return mixed
     */
    public function captcha()
    {
        $res = app('captcha')->create('flat', true);
        return Help::returnApiJson("恭喜,　获取验证码数据成功!", 1, ['img' => $res['img'], 'key' => $res['key']]);
    }

    // 登出
    public function logout()
    {
        $user = auth('api')->user();
        if ($user) {
            auth('api')->logout();
        }

        return Help::returnApiJson('登出成功!', 1);
    }

    //
    protected function guard()
    {
        return Auth::guard('api');
    }

    public function username()
    {
        return 'username';
    }

    /**
     * 获取银行列表
     * @return array
     */
    public function getBankList()
    {
        $banks = config("web.banks");

        $data = [];
        foreach ($banks as $sign => $name) {
            $data[] = [
                'sign' => $sign,
                'name' => $name
            ];
        }

        return $data;
    }

    function base64EncodeImage($image)
    {
        $image_info = getimagesize($image);
        $image_data = fread(fopen($image, 'r'), filesize($image));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
}
