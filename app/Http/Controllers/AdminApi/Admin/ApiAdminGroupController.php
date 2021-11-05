<?php namespace App\Http\Controllers\AdminApi\Admin;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Admin\AdminGroup;
use App\Models\Admin\AdminMenu;


/**
 * version 1.0
 * 后台用户管理
 * Class ApiAdminController
 * @package App\Http\Controllers\AdminApi\Admin
 */
class ApiAdminGroupController extends ApiBaseController
{

    /**
     * 修改管理员 组列表
     * @return mixed
     */
    public function adminGroupList()
    {
        $adminUser  = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        // 获取数据
        $_data          = AdminGroup::getAdminGroupList($adminUser);
        $data['data']   = $_data;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 用户组查看权限
     * @param $id
     * @return mixed
     */
    public function adminGroupDetail($id)
    {
        return $this->groupAclDetail($id);
    }

	/**
	 * 修改/添加管理员 组列表
	 * @param $id
	 * @return mixed
	 */
    public function adminGroupAdd($id)
    {
        $adminUser  = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        if ($id) {
            $group   = AdminGroup::find($id);
            if (!$group) {
                return Help::returnApiJson('无效的组Id!', 0,  []);
            }

            // 是否可以修改
            $parentGroup = $adminUser->group();
            if (!$group->isChildGroup($parentGroup->id)) {
                return Help::returnApiJson('对不起，您无权操作!', 0,  []);
            }

        } else {
            $parentGroup = $adminUser->group();
            $group = new AdminGroup();
        }

        $groupName   = Request("group_name");
        if (!$groupName) {
            return Help::returnApiJson('无效的上级组Id!', 0,  []);
        }

        if ($group && $group->id > 0) {
            $group->name    = $groupName;
            $group->save();

			// 添加telegram提现推送消息
			$fromConfig = config("admin.main.admin_behavior_type");
			$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[10]} 功能, 被修改组："  .  $group->name . '</b>';
			telegramSend('send_admin_behavior',$text);
            return Help::returnApiJson('修改管理组成功!', 1);
        }

        $group->name    = $groupName;
        $group->pid     = $parentGroup ? $parentGroup->id : 0;
        $group->rid     = 0;

        $group->save();
        $group->rid     = $parentGroup ? $parentGroup->rid . '|' . $group->id : $group->id;
        $group->save();
		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[9]} 功能, 新添加组："  .  $group->name . '</b>';
		telegramSend('send_admin_behavior',$text);

        return Help::returnApiJson('添加管理组成功!', 1);
    }

    public function adminGroupDel($id) {
		$adminUser  = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 不存在的用户！", 0);
		}
        $model   = AdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("无效的管理组!", 0);
        }
        // 删除
        $model->delete();

        // 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[8]} 功能, 被删除组："  .  $model->name . '</b>';
		telegramSend('send_admin_behavior',$text);
        return Help::returnApiJson("删除数据成功!", 1);
    }

    /**
     * 修改管理员 组列表
     * @return mixed
     */
    public function adminGroupStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $group = AdminGroup::find($id);
        if (!$group) {
            return Help::returnApiJson("对不起, 无效的管理组！", 0);
        }

        $group->status = $group->status ? 0 : 1;
        $group->save();

        return Help::returnApiJson("恭喜, 修改管理组状态成功！", 1);
    }

    /**
     * 修改管理员 组列表
     * @return mixed
     */
    public function groupAclEdit($groupId) {
        $group = AdminGroup::find($groupId);
        if (!$group) {
            return Help::returnApiJson("无效的管理组!", 1);
        }

        if ($group->pid) {
            $parentGroup = AdminGroup::find($group->pid);
        } else {
            $parentGroup = AdminGroup::find(1);
        }

        $parentAcl      = AdminMenu::getAclIds($parentGroup);;

        // 获取选项
        $action     = request('action', 'process');
        if ($action == 'option') {
            $data['canUseIds']      = AdminMenu::getAclMenus($parentAcl);
            $data['currentIds']     = AdminMenu::getAclIds($group);
            return Help::returnApiJson("恭喜, 获取数据成功！", 1, $data);
        }

        // 保存
        $aclIds = request("acl_id", []);

        $menus  = AdminMenu::whereIn("id", $aclIds)->where("status", 1)->get();

        $acl    = [];
        foreach ($menus as $m) {
            if (!in_array($m->id, $parentAcl)) {
                continue;
            }
            $acl[] = $m->id;
        }

        $group->acl = serialize($acl);
        $group->save();

        return Help::returnApiJson("恭喜, 修改管理组状态成功！", 1);
    }

    /**
     * 获取权限详情
     * @param  $id
     * @return mixed
     */
    public function groupAclDetail($id) {
        $adminUser  = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        $group = AdminGroup::find($id);
        if (!$group) {
            return Help::returnApiJson("无效的管理组!", 1);
        }

        $ids        = AdminMenu::getAclIds($group);
        $allMenus   = AdminMenu::getAclMenus($ids);

        return Help::returnApiJson("恭喜, 获取管理组权限成功！", 1, $allMenus);

    }

    /**
     * 查看管理员-管理组权限
     * @param $id
     * @return mixed
     */
    public function adminGroupsAcl($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $model = AdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $data = $model->getAcl();

        return Help::returnApiJson("恭喜, 获取权限！", 1, $data);
    }

    /**
     * 设置管理组权限
     * @param $id
     * @return mixed
     */
    public function adminGroupsSetAcl($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        $admin = AdminGroup::where('id', $adminUser->group_id)->first();
        if ($admin->acl != '*') {
            return Help::returnApiJson("对不起, 无权限操作！", 0);
        }

        // 获取组
        $model = AdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $ids = request("menu_ids");
        dd($ids);
        if (!$ids || !is_array($ids)) {
            return Help::returnApiJson("对不起, 无效的菜单ID传入！", 0);
        }

        $res = $model->setAcl($ids, $adminUser);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[12]} 功能, 被编辑权限组："  .  $model->name . '</b>';
		telegramSend('send_admin_behavior',$text);
        return Help::returnApiJson("恭喜, 设置权限成功！", 1);
    }
}
