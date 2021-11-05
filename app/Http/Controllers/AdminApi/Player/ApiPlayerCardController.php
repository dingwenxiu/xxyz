<?php

namespace App\Http\Controllers\AdminApi\Player;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Player\PlayerCard;

class ApiPlayerCardController extends ApiBaseController
{

    // 获取银行卡列表
    public function cardList()
    {
        $c          = request()->all();
        $data       = PlayerCard::getList($c);

        $_data = [];

        $data['partner_option']     = Partner::getOptions();
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                => $item->id,
                "partner_sign"      => $item->partner_sign,
                "partner_name"      => $data['partner_option'][$item->partner_sign],
                "user_id"           => $item->user_id,
                "username"          => $item->username,
                "bank_sign"         => $item->bank_sign,
                "bank_name"         => $item->bank_name,
                "owner_name"        => $item->owner_name,
                "card_number"       => $item->card_number,
                "province_id"       => $item->province_id,
                "city_id"           => $item->city_id,
                "branch"            => $item->branch,
                "admin_id"          => $item->admin_id,
                "status"            => $item->status,
                "add_time"          => date("m-d H:i", strtotime($item->created_at)),
                "update_time"       => date("m-d H:i", strtotime($item->updated_at)),
            ];
        }

        $data['data']               = $_data;
        $data['bank_list']          = config("web.banks");

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加银行卡
    public function cardAdd($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnJson("对不起, 不存在的用户！", 0);
        }

        if ($id) {
            $model  = PlayerCard::find($id);
            if (!$model) {
                return Help::returnApiJson("对不起, 目标对象不存在！", 0);
            }
        } else {
            $model  = new PlayerCard();
        }

        $data   = request()->all();
        $res    = $model->saveItem($data, $adminUser->id);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加数据成功！", 1);
    }

    // 获取卡详情
    public function cardDetail($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['card']  = [];

        // 获取用户银行卡
        $card = PlayerCard::find($id);
        if ($card) {
            $data['card']   = $card;
        }

        $data['bank_options']       = config("web.banks");

        $province = config("web.province");

        $options = [];
        foreach ($province as $pid => $item) {
            $_tmp = [
                'value'     => $pid,
                'label'     => $item['name'],
                'children'  => []
            ];

            if ($item['city']) {
                foreach ($item['city'] as $cid => $cName) {
                    $_tmp['children'][] = [
                        'value'     => $cid,
                        'label'     => $cName,
                        'isLeaf'    => true
                    ];
                }
            }

            $options[] = $_tmp;
        }

        $data['province_options']   = $options;

        return Help::returnApiJson("恭喜, 获取银行卡详情成功！", 1, $data);
    }
}
