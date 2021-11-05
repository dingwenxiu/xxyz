<?php

namespace App\Http\Controllers\Api;


use App\Lib\Help;
use App\Models\Partner\HelpCenter;

class ApiSiteController extends ApiBaseController
{
    // 获取帮助中心内容
    public function helpMenuList()
    {
        $c                  = request()->all();
        $c['partner_sign']  = $this->partner->sign;
        $data               = HelpCenter::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
