<?php
namespace App\Http\Controllers\PartnerApi\Activity;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Activity\ActivityRule;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityPrize;

class ApiActivityCheckController extends ApiBaseController
{
    //获取活动记录
    public function getLists()
    {
        $c  = request()->all();
        $c['partner_sign']     = $this->partnerSign;
        $partnerActivityPrize  = PartnerActivityLog::getList($c);

        return Help::returnApiJson('获取成功', 1, $partnerActivityPrize);
    }

    public function getParams()
    {
        $ActivityRule = ActivityRule::all();
//        $ActivityRule = ActivityRule::whereRaw('FIND_IN_SET(?, open_partner)', [$this->partnerSign])->get();
        $type = [];
        foreach ($ActivityRule as $key => $item) {
            $type[] = [
                'name' => $item->name ?? '',
                'type' => $item->type ?? '',
            ];
        }
        $params = [
            'prize'  => config('active.prize'),
            'status' => config('active.main.log_status'),
            'check'  => config('active.main.check'),
            'obtain_type'  => config('active.main.obtain_type'),
            'type'   => $type,
        ];
        return Help::returnApiJson('获取成功', 1, $params);
    }

    // 后台审核
    public function check($id)
    {
        $c  = request()->all();
        $c['partner_sign']     = $this->partnerSign;
        $c['partner_admin_username'] = $this->partnerAdminUser->username;
        $c['partner_admin_user_id'] = $this->partnerAdminUser->id;


        $partnerActivityLog = new PartnerActivityLog();

        $checkStatus = $partnerActivityLog->checkLock($c, $id);
        if ($checkStatus) {
            return Help::returnApiJson($partnerActivityLog->errorMsg, 1, []);
        }
        return Help::returnApiJson($partnerActivityLog->errorMsg, 0, []);
    }
}
