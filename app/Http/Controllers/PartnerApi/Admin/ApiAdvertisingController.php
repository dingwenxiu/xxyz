<?php

namespace App\Http\Controllers\PartnerApi\Admin;


use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdvertising;
use App\Models\Partner\PartnerModule;

/**
 * version 1.0
 * Class ApiPartnerController
 *
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiAdvertisingController extends ApiBaseController
{
    public function getAdvertising()
    {
        $c = request()->all();
        $partnerSign = $this->partnerSign;
        $PartnerAdvertising = new PartnerAdvertising();
        $data = $PartnerAdvertising->getList($c, $partnerSign);
        return Help::returnApiJson('', 1, $data['data']);
    }

    public function delAdvertising()
    {
        $c = request()->all();
        $id = $c['id'] ?? '';
        if ($id === '') {
            return Help::returnApiJson('参数错误', 0);
        }
        PartnerAdvertising::where('id', $id)->delete();
        return Help::returnApiJson('删除成功', 1);
    }


    public function saveAdvertising()
    {
        $c = request()->all();
        $id = $c['id'] ?? '';
        $partnerSign = $this->partnerSign;
        if ($id === '') {
            return Help::returnApiJson('参数错误', 0);
        }
        $PartnerAdvertising = new PartnerAdvertising();
        if ($PartnerAdvertising->saveItem($c, $partnerSign, $id)) {
            return Help::returnApiJson('修改成功', 1);
        }
        return Help::returnApiJson($PartnerAdvertising->errMsg, 0);
    }

    public function getType()
    {
        $type = config('partner.main.advertising.type');
        $partnerSign = $this->partnerSign;
        $partner = Partner::where(['sign' => $partnerSign])->first();
        $PartnerModule = PartnerModule::where([
            'status' => 1,
            'partner_sign' => $partnerSign,
            'template_sign' => $partner->template_sign,
        ])->get();

        foreach ($type as &$typeItem) {
            if ($typeItem['key'] == 'home') {
                foreach ($PartnerModule as $partnerItem) {
                    $typeItem['module'][] = [
                        'name' => $partnerItem->name,
                        'key'  => $partnerItem->sign,
                        'pid'  => $partnerItem->id,
                        'game_id' => $partnerItem,
                    ];
                }
            }
        }

        return Help::returnApiJson('', 1, $type);
    }



}
