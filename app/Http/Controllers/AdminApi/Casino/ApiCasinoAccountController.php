<?php

namespace App\Http\Controllers\AdminApi\Casino;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Admin\AdminUser;
use App\Models\Casino\CasinoPlatform;

class ApiCasinoAccountController extends ApiBaseController
{
    // 获取平台列表
    public function platformList()
    {
        $c          = request()->all();
        $data       = CasinoPlatform::getList($c);

        $data['admin_user']     = AdminUser::getAdminUserOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 平台拉取
    public function platformFetch()
    {
        $c          = request()->all();
        $data       = CasinoPlatform::getList($c);

        $data['admin_user']     = AdminUser::getAdminUserOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 平台状态
    public function platformStatus($id)
    {
        $c          = request()->all();
        $data       = CasinoPlatform::getList($c);

        $data['admin_user']     = AdminUser::getAdminUserOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
