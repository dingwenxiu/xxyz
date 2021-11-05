<?php

namespace App\Http\Controllers\AdminApi;

use App\Lib\Help;
use App\Models\Admin\AdminMenu;
use App\Models\Partner\Partner;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

// 后台登录登出逻辑
class ApiAuthController extends ApiBaseController
{

    use Authenticatable;

    // 登录
    public function login()
    {
        // 用户校验
        $email = trim(request("email"));
        if (!$email) {
            return Help::returnApiJson("对不起, 您输入的邮箱为空!", 0);
        }

        // 密码检验
        $password = trim(request("password"));
        if (!$password) {
            return Help::returnApiJson("对不起, 您输入的密码为空!", 0);
        }

        // 登录验证
        $credentials = ['email' => $email, 'password' => $password];
        if (!$token = auth('admin_api')->attempt($credentials)) {

            return Help::returnApiJson("对不起, 用户名或密码错误!", 0);
        }

        $adminUser = auth('admin_api')->user();
        $adminUser->last_login_ip    = real_ip();
        $adminUser->last_login_time  = time();
        $adminUser->save();

        // 返回数据
        $data = [
            'token'                 => $token,
            'token_type'            => 'bearer',
            'expires_in'            => auth('admin_api')->factory()->getTTL() * 60,
            'user_id'               => $adminUser->id,
            'username'              => $adminUser->username,
            'default_partner_sign'  => Partner::getDefaultPartnerSign(),
        ];
        $data['system_pic_base_url'] = configure("system_pic_base_url");
        return Help::returnApiJson('登录成功', 1, $data);
    }

    // 获取站点信息
    public function lang() {
        return Help::returnApiJson([]);
    }

    // 登出
    public function logout()
    {
        auth()->logout();
        return Help::returnApiJson('登出成功!', 1);
    }

    // 守卫
    protected function guard()
    {
        return Auth::guard('admin_api');
    }

    // 用户名字段
    public function username()
    {
        return 'email';
    }
}
