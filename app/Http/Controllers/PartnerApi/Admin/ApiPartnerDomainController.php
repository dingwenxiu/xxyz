<?php

namespace App\Http\Controllers\PartnerApi\Admin;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerDomain;


/**
 * 商户域名配置
 * Class ApiPartnerDomainController
 * @package App\Http\Controllers\PartnerApi\Partner
 */
class ApiPartnerDomainController extends ApiBaseController
{
    // 获取商户域名列表
    public function partnerDomainList()
    {
        $c          = request()->all();
        $c['partner_sign'] = $this->partnerSign;

        $data       = PartnerDomain::getList($c);

        $data['partner_options']   = Partner::getOptions($c['partner_sign']);
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

}
