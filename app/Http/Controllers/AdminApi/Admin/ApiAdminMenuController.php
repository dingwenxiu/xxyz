<?php namespace App\Http\Controllers\AdminApi\Admin;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Admin\AdminGroup;
use App\Models\Admin\AdminMenu;

/**
 * version 1.0
 * 后台 - Menu - 管理
 * Class ApiAdminController
 * @package App\Http\Controllers\AdminApi\Admin
 */
class ApiAdminMenuController extends ApiBaseController
{

    /**
     * 获取菜单里表
     * @return mixed
     */
    public function adminMenuList()
    {
        $adminUser  = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        // 获取数据
        $c      = request()->all();
        $data   = AdminMenu::getMenuList($c);
        $data['related'] = [];


        if (isset($c['pid']) && $c['pid']) {
            $data['related'] = AdminMenu::getMenuRelated($c['pid']);
        }

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 获取菜单详情
     * @param  $id
     * @return mixed
     */
    public function adminMenuDetail($id = 0)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['group_options']  = AdminGroup::getGroupOptions($adminUser->group_id);;

        // 获取用户
        $menu = AdminMenu::find($id);
        if ($menu) {
            $data['menu']   = $menu;
        }

        return Help::returnApiJson("恭喜, 获取菜单详情成功！", 1, $data);
    }

    /**
     * 添加管理菜单
     * @param int $id
     * @return mixed
     */
    public function adminMenuAdd($id = 0)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id && $id >0) {
            $model = AdminMenu::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new AdminMenu();
        }

        $pid        = request('pid', 0);

        $parentMenu =  AdminMenu::find($pid);

        $params = request()->all();

        $res    = $model->saveItem($params, $parentMenu, $adminUser->id);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加菜单成功！", 1);
    }
}
