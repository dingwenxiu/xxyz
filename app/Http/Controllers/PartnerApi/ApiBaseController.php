<?php

namespace App\Http\Controllers\PartnerApi;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Partner\PartnerAdminAccessLog;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerDomain;

class ApiBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $partnerSign      = null;
    protected $partnerAdminUser = null;
    protected $partner          = null;

    public function __construct()
    {
        $routeName     = request()->route()->getName();
        $url           = request()->header('Host');
        $url           = str_replace("http://", "", trim($url));
        $this->domain  = $url;
        $partner       = Partner::findPartnerByDomain($this->domain, PartnerDomain::DOMAIN_TYPE_PARTNER);
        $this->partner = $partner;

        if ($partner) {
            $this->partnerSign = $partner->sign;
            $this->partnerAdminUser = auth("partner_api")->user();
            if ($this->partnerAdminUser) {
                PartnerAdminAccessLog::saveItem($this->partnerAdminUser);
            } elseif($routeName != "sendCode" && $routeName != "login"){
                return response()->json(['msg' => '对不起, 用户未登录!'],401);
            }
        } else {
            return response()->json(['msg' => '对不起，此域名不存在！'],401);
        }
    }
}
