<?php

namespace App\Http\Controllers\PartnerApi\Template;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Admin\AdminUser;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerModule;
use App\Models\Template\Template;

/**
 * 模板配置
 * Class ApiTemplateController
 *
 * @package App\Http\Controllers\PartnerApi\System
 */
class ApiTemplateController extends ApiBaseController
{
    // 1. 商户配置模板
    public function getTemplate()
    {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $c['status']  = 1;
        // 1. 显示总后台 分配的 模板
        $data = Template::getList($c);

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    // 2. 平台配置
    public function setTemplate()
    {
        $c            = request()->all();
        $templateSign = $c['template_sign'];


        PartnerAdminUser::where('partner_sign', $this->partnerSign)->update(['template_sign' => $templateSign]);

        return Help ::returnApiJson('设置成功!', 1, []);

    }


}
