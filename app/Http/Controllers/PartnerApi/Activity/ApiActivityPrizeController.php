<?php
namespace App\Http\Controllers\PartnerApi\Activity;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\PartnerActivityPrize;

class ApiActivityPrizeController extends ApiBaseController
{
    //获取活动列表
    public function getLists()
    {
        $c  = request()->all();
        $c['partner_sign']     = $this->partnerSign;
        $c['status']           = 1;
        $partnerActivityPrize  = PartnerActivityPrize::getList($c);

        return Help::returnApiJson('获取成功', 1, $partnerActivityPrize);
    }


    public function set($id)
    {

        $c                     = request()->all();
        $c['partner_sign']     = $this->partnerSign;
        $partnerActivityPrize = new PartnerActivityPrize();
        if ($partnerActivityPrize->saveItem($c, $id)) {
            return Help::returnApiJson($partnerActivityPrize->errorMsg, 1, []);
        }
        return Help::returnApiJson($partnerActivityPrize->errorMsg, 0, []);
    }

    public function del($id)
    {
        $c                     = request()->all();
        $saveId = [1, 2, 3];
        if (in_array($id, $saveId)) {
            return Help::returnApiJson('此值不能被修改', 0, []);
        }
        PartnerActivityPrize::where('id', $id)->delete();
        return Help::returnApiJson('删除成功', 1, []);
    }

}
