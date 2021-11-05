<?php
namespace App\Http\Controllers\AdminApi\Partner;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Admin\AdminReviewFlow;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminUser;

/**
 * version 1.0
 * Class ApiPartnerAdminController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiPartnerAdminPermissionController extends ApiBaseController
{
    /** =================================== 商户管理员 @ 相关 ===================================== */
    /**
     * 获取商户管理员列表
     * @return mixed
     */
    public function partnerReviewPermissionsList()
    {
        $c = request()->all();
        $data = AdminReviewFlow::partnerReviewPermissionsList($c);
        $data['partner_options'] = Partner::getOptions();
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }
    //添加權限
    public function bindPermissions ()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }
        $c = request()->all();
        $config =  config("admin.main.partner_review_type");
        if (!isset($c['type'])||!isset($config[$c['type']])){

            return Help::returnApiJson("無對應的類型！", 0);
        }
        //判斷是否有此type
        $permission = AdminReviewFlow::findByType($c['type']);
        //沒有就初始化

        if(!isset($c['ids'])||!is_array($c['ids'])){

            return Help::returnApiJson("無效的id！", 0);
        }
        if(!$permission){
            $defaultRes = AdminReviewFlow::setDefault($config,$c);
            if($defaultRes['res'] != 1){

                return Help::returnApiJson("添加失敗！", 0,$defaultRes);
            }
        }else{

            $res = AdminReviewFlow::bindPermissions($permission,$c,$config);
            if($res['res'] != 1){

                return Help::returnApiJson("添加失敗！", 0,$res);
            }
        }

        return Help::returnApiJson("恭喜, 添加超级管理员成功！", 1);
    }
    public function editPermissions($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id) {
            $model = PartnerAdminUser::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerAdminUser();
        }

        $params = request()->all();

        $res = $model->saveItem($params, $adminUser);
        if (!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加超级管理员成功！", 1);
    }
    public function deletePermissions($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id) {
            $model = PartnerAdminUser::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerAdminUser();
        }

        $params = request()->all();

        $res = $model->saveItem($params, $adminUser);
        if (!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加超级管理员成功！", 1);
    }





}
