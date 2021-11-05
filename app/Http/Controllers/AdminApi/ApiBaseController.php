<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\Admin\AdminAccessLog;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
    	$routeName     = request()->route()->getName();
        $adminUser     = auth("admin_api")->user();
        AdminAccessLog::saveItem($adminUser);

        if ($adminUser) {
           AdminAccessLog::saveItem($adminUser);
        } elseif($routeName != "sendCode" && $routeName != "login" && $routeName != "encode"){
            return response()->json(['msg' => '对不起, 用户未登录!'],401);
        }

    }
}
