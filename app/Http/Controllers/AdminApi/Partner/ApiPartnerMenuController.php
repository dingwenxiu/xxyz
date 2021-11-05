<?php

namespace App\Http\Controllers\AdminApi\Partner;

use App\Lib\Help;
use App\Models\Admin\AdminMenu;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerMenu;
use App\Models\Partner\PartnerMenuConfig;
use App\Http\Controllers\AdminApi\ApiBaseController;

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
        if(isset($c['pid'])&&$c['pid']){

            $c['pid'] = $c['pid'] > 0 ? $c['pid'] : 0;
        }else{
            $c['pid'] = 0;
        }
        $data = PartnerMenu::partnerGetMenu($c);
        foreach ($data['data'] as $item) {
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
        }

        //撈取未榜定的菜單
        $addList = PartnerMenu::getNoneCombineList($c);
        $data['add_list'] = $addList;
        $data['menu_rid_list'] = PartnerMenuConfig::getMenuRidArr($c['pid']);
        $data['partner_option']     = Partner::getOptions();
        $data['menu_type_options']  = PartnerMenuConfig::$typeOptions;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 修改菜单状态
    public function partnerMenuStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取状态
        $model = PartnerMenu::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的菜单id！", 0);
        }

        $model->status  = $model->status ? 0 : 1;
        $model->update_admin_id = $adminUser->id;
        $model->save();

        //更改子菜单状态
        $ids = PartnerMenuConfig::where('rid','like','%'.$model->menu_id.'|%')->pluck('id')->toArray();
        PartnerMenu::whereIn('menu_id',$ids)->where('partner_sign',$model->partner_sign)->update(['status'=>$model->status]);

        //更改父菜单状态
        if($model->status==1)
        {
            $currPartnerMenuConfig = PartnerMenuConfig::where('id',$model->menu_id)->first();
            $parentIds = explode('|',$currPartnerMenuConfig->rid);
            PartnerMenu::whereIn('menu_id',$parentIds)->where('partner_sign',$model->partner_sign)->update(['status'=>$model->status]);
        }

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 上出菜单绑定
    public function partnerMenuDel($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $model = PartnerMenu::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的菜单id！", 0);
        }

        // 状态
        if ($model->status == 1) {
            return Help::returnApiJson("对不起, 您需要先禁用菜单！", 0);
        }

        $model->delete();

        return Help::returnApiJson("恭喜, 删除数据成功！", 1);
    }

    // 添加 菜单
    public function partnerMenuAdd($id = 0)
    {
        $params       = request()->all();
        $params['id'] = $id;
        $adminUser    = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id) {
            $model = PartnerMenu::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerMenu();
        }

        $res    = $model->saveItems($params,$id,$adminUser->id);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑" : "添加";
        return Help::returnApiJson("恭喜, {$msg}菜单成功！", 1);
    }



    /** =================================== 预设菜单 @ 相关 ===================================== */
    // 获取 菜单 列表
    public function partnerMenuConfigList()
    {

        $c          = request()->all();
        $data       = PartnerMenuConfig::getMenuList($c);

        $partnerOption = Partner::getOptions();
        $data['partner_option']         = $partnerOption;
        $data['menu_type_option']       = PartnerMenuConfig::$typeOptions;

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加 菜单 列表
    public function partnerMenuConfigAdd($id=0)  //上面有傳代表修改
    {
        $params = request()->all();

        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id && $id != 0) {

            $model = PartnerMenuConfig::find((int)$id);

            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerMenuConfig();
        }

        $pid = request('pid');

        if ($pid) {
            $parent = PartnerMenuConfig::find($pid);
            if (!$parent && $pid != 0) {
                return Help::returnApiJson('无效的上级ID!', 0);
            }
        } else {
            $parent = new PartnerMenuConfig();
        }


        $res    = $model->saveItem($params, $parent, $adminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加预设菜单成功！", 1);
    }

    // 修改 预设　菜单状态
    public function partnerMenuConfigStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

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

    public function partnerBindMenuConfig($id = 0){
        $adminUser = auth()->guard('admin_api')->user();

        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }


        $c = request()->all();
        if(!isset($c['partner_sign'])||!$c['partner_sign']){

            return Help::returnApiJson("partner_sign 商戶 必填！", 0);

        }
        $partner = PartnerAdminUser::getPatnerUserAdminBySign($c['partner_sign']);

        if(!$partner){

            return Help::returnApiJson("商戶不存在！", 0);
        }
        if(!isset($c['ids'])|| !$c['ids']){
            return Help::returnApiJson("ids 沒填寫！", 0);
        }
        if(!is_array($c['ids'])){

            return Help::returnApiJson("ids 必須為array格式 ！", 0);
        }

            $res = PartnerMenu::partnerBindMenuConfig($c['ids'],$partner);
        if( $res['res']) {

            return Help::returnApiJson("恭喜, 修改状态成功！", 1);
        }else{

            return Help::returnApiJson("新增失敗！", 0,$res);
        }
    }
    public function  partnerMenuConfigDel($id){
        $menu = PartnerMenuConfig::find($id);
        if(!$menu){

            return Help::returnApiJson("無效的菜單id！", 0);
        }
        $res = PartnerMenuConfig::partnerMenuConfigDel($menu);

        if($res['res'] == 1){

            return Help::returnApiJson("刪除成功", 1);
        }

        return Help::returnApiJson("刪除失敗", 0,$res);
    }

}
