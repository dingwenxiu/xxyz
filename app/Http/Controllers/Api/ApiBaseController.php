<?php

namespace App\Http\Controllers\Api;

use App\Lib\Logic\Cache\ApiCache;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerDomain;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $partner  = "";
    protected $domain   = "";

    public function __construct()
    {
        $url          = request()->header('Host');
        $url          = str_replace("http://", "", trim($url));
        $this->domain = $url;
        $partner      = Partner::findPartnerByDomain($this->domain, PartnerDomain::DOMAIN_TYPE_FRONTEND);

        if (!$partner) {
            return response()->json(['msg' => '对不起, 无效的域名!'],401);
        }
        
        ApiCache::login();
        $this->partner = $partner;
    }
}
