<?php

namespace App\Http\Controllers\PartnerApi\Template;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Template\TemplateColor;

/**
 * 模板配置
 * Class ApiTemplateController
 *
 * @package App\Http\Controllers\PartnerApi\System
 */
class ApiTemplateColorController extends ApiBaseController
{
    // 1. 获取模板颜色
    public function getTemplateColor()
    {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $data = TemplateColor::getList($c);

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

}
