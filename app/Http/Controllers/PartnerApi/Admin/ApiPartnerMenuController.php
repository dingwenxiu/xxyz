<?php

namespace App\Http\Controllers\PartnerApi\Admin;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerMenu;
use App\Models\Partner\PartnerMenuConfig;
use Illuminate\Support\Facades\DB;

/**
 * version 1.0
 * Class ApiPartnerMenuController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiPartnerMenuController extends ApiBaseController
{
    /** =================================== 菜单 @ 相关 ===================================== */
    // 获取商户菜单列表
    public function partnerMenuList()
    {
        $c          = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $data       = PartnerMenu::getMenuList($c);

        $data['partner_options']    = Partner::getOptions();
        $data['menu_type_options']  = PartnerMenuConfig::$typeOptions;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 修改菜单状态1
    public function partnerMenuStatus($menuId)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取菜单状态
        $model = PartnerMenu::where('menu_id',$menuId)->where('partner_sign',$this->partnerSign)->first();
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的菜单id！", 0);
        }

        $model->status          = $model->status ? 0 : 1;
        $model->update_admin_id = $adminUser->id;
        $model->save();

        // 子菜单状态
        $ids = PartnerMenuConfig::where('rid','like','%'.$model->menu_id.'|%')->pluck('id')->toArray();
        PartnerMenu::whereIn('menu_id',$ids)->where('partner_sign',$this->partnerSign)->update(['status'=>$model->status]);
        $parentIds ='';
        // 父菜单状态
        if($model->status == 1)
        {
            $currPartnerMenuConfig = PartnerMenuConfig::where('id',$model->menu_id)->first();
            $parentIds = explode('|',$currPartnerMenuConfig->rid);
            PartnerMenu::whereIn('id',$parentIds)->where('partner_sign',$this->partnerSign)->update(['status'=>$model->status]);
        }

        return Help::returnApiJson("恭喜, 修改状态成功！", 1,[$ids,$parentIds]);
    }

    // 删除菜单绑定
    public function partnerMenuDel($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取用户
        $model = PartnerMenu::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        // 状态
        if ($model->status == 1) {
            return Help::returnApiJson("对不起, 您需要先禁用菜单！", 0);
        }

        $model->delete();

        return Help::returnApiJson("恭喜, 删除数据成功！", 1);
    }

    /** =================================== 预设菜单 @ 相关 ===================================== */
    // 获取 菜单 列表
    public function partnerMenuConfigList()
    {
        $c                          = request()->all();
        $data                       = PartnerMenu::getPartnerMenuList([],$this->partnerSign);
        $data['menu_type_options']  = PartnerMenuConfig::$typeOptions;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加 菜单 列表
    /*
    public function partnerMenuConfigAdd($id)
    {
        $adminUser = $this->partnerAdminUser;

        if ($id) {
            $model = PartnerMenuConfig::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerMenuConfig();
        }
        dd('fdsfsf');
        $pid = request('pid');

        if ($pid) {
            $parent = PartnerMenuConfig::find($pid);
            if (!$parent) {
                return Help::returnApiJson('无效的上级ID!', 0);
            }
        } else {
            $parent = new PartnerMenuConfig();
        }

        $params = request()->all();

        $res    = $model->saveItem($params, $parent, $adminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加预设菜单成功！", 1);
    }
    */

    // 修改 预设　菜单状态
    public function partnerMenuConfigStatus($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取菜单
        $model = PartnerMenuConfig::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的MenuId！", 0);
        }

        $model->status          = $model->status ? 0 : 1;
        $model->update_admin_id = $adminUser->id;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }
}
