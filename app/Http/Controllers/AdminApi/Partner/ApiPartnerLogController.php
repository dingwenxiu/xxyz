<?php

namespace App\Http\Controllers\AdminApi\Partner;

use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerAdminBehavior;
use App\Models\Partner\PartnerAdminAccessLog;
use App\Models\Partner\PartnerAdminActionReview;
use App\Http\Controllers\AdminApi\ApiBaseController;


/**
 * version 1.0
 * Class ApiPartnerLogController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiPartnerLogController extends ApiBaseController
{
    // 商户管理员访问日志
    public function adminAccessLogList()
    {
        $c                          = request()->all();
        $data                       = PartnerAdminAccessLog::getList($c);
        foreach ($data['data'] as $item) {
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
        }

        $partnerSign                = $c['partner_sign'] ?? '';
        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions($partnerSign);
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** =================================== 行为 @ 相关 ===================================== */
    // 商户管理员行为
    public function partnerAdminBehavior()
    {
        $c                          = request()->all();
        $data                       = PartnerAdminBehavior::getList($c);
        foreach ($data['data'] as $item) {
            $item->add_time         = date("Y-m-d H:i:S", $item->add_time);
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
        }

        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions();
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** =================================== 审核 @ 相关 ===================================== */
    // 商户 动作　审核
    public function partnerAdminActionReview()
    {
        $c          = request()->all();
        $data       = PartnerAdminActionReview::getList($c);

        foreach ($data['data'] as $item) {
            $item->add_time = date("Y-m-d H:i:S", $item->add_time);
        }

        $partnerSign = $c['partner_sign'] ?? '';
        $data['partner_admin_user']     = PartnerAdminUser::getAdminUserOptions($partnerSign);
        $data['partner_options']        = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
