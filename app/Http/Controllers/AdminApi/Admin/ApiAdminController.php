<?php namespace App\Http\Controllers\AdminApi\Admin;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Lib\Str;
use App\Models\Admin\AdminAccessLog;
use App\Models\Admin\AdminActionReview;
use App\Models\Admin\AdminBehavior;
use App\Models\Admin\AdminGroup;
use App\Models\Admin\AdminMenu;
use App\Models\Admin\AdminUser;
use App\Models\Partner\Partner;
use Illuminate\Support\Facades\Hash;


/**
 * Tom 2019 整理
 * 后台用户管理
 * Class ApiAdminController
 * @package App\Http\Controllers\AdminApi\Admin
 */
class ApiAdminController extends ApiBaseController
{

    /**
     * 获取菜单
     * @return mixed
     */
    public function menu()
    {
        // 获取权限数据
        $routes         = AdminMenu::getApiAllRoute();
        $data['menu']   = $routes;

        return Help::returnApiJson('获取菜单成功', 1, $data);
    }

    /**
     * 获取后台访问日志列表
     * @return mixed
     */
    public function adminLogList()
    {
        $c          = request()->all();
        $data       = AdminAccessLog::getList($c);

        $data['admin_user']     = AdminUser::getAdminUserOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 获取用户行为
     * @return mixed
     */
    public function adminBehaviorList()
    {
        $c          = request()->all();
        $data       = AdminBehavior::getList($c);

        foreach ($data['data'] as $item) {
            $item->add_time = date("Y-m-d H:i:S", $item->add_time);
        }
        
        $data['type'] = config('admin.main.admin_behavior_type_log');
        $data['admin_user']     = AdminUser::getAdminUserOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** =================================== 管理员 @ 相关 ===================================== */

    /**
     * 获取管理用户
     * @return mixed
     */
    public function adminUserList()
    {
        $c          = request()->all();
        $adminUser  = auth()->guard('admin_api')->user();
        $group      = $adminUser->group();

        // 获取数据
        $data       = AdminUser::getAdminUserList($c, $group);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                => $item->id,
                "username"          => $item->username,
                "email"             => $item->email,
                "group_id"          => $item->group_id,
                "group_name"        => $item->group_name,
                "register_time"     => $item->created_at->toDateTimeString(),
                "register_ip"       => $item->register_ip,
                "last_login_ip"     => $item->last_login_ip,
                "last_login_time"   => $item->last_login_time ? date("Y-m-d H:i:s", $item->last_login_time) : "---",
                "admin_id"          => $item->admin_id,
                "status"            => $item->status,
            ];
        }

        $data['data'] = $_data;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 添加管理员
     * @return mixed
     */
    public function adminUserAdd()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 是否存在下级组
        $groupList = AdminGroup::getAdminGroupList($adminUser);
        if (!$groupList) {
            return Help::returnApiJson("对不起, 您不能添加用户！", 0);
        }

        // 获取选项
        $action     = request('action', '');
        if ($action == 'option') {
            $data = [
                'group_options' => $groupList,
            ];
            return Help::returnApiJson("恭喜, 获取选项成功！", 1, $data);
        }

        $c = request()->all();
        if (isset($c['id']) && $c['id']) {
        	//编辑
			$name = AdminUser::where('username', $c['username'])->where('id', '!=', $c['id'])->first();
			if ($name) {
				return Help::returnApiJson('对不起,用户名重复',0);
			}
			$email = AdminUser::where('email', $c['email'])->where('id', '!=', $c['id'])->first();
			if ($email) {
				return Help::returnApiJson('对不起,邮箱重复',0);
			}

		} else {
        	if (!isset($c['password']) || !isset($c['fund_password'])) {
        		return Help::returnApiJson('对不起,密码或者资金密码不能为空',0);
			}
        	// 添加
			$res = AdminUser::where('username', $c['username'])->orWhere('email', $c['email'])->first();
			if ($res) {
				return Help::returnApiJson('对不起,用户名重复或者邮箱重复',0);
			}

		}

        // 保存
        $user = new AdminUser();

        $res = $user->saveItem($c);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 操作成功！", 1);
    }

    /**
     * 管理员删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
	public function  adminUserDel ($id) {
		$adminUser = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}

		if (!$id) {
			return Help::returnApiJson('对不起,管理员ID不能为空',0);
		}
		$admin = AdminUser::where('id', $id)->where('add_admin_id','!=', 0)->first();
		if (!$admin) {
			return Help::returnApiJson('对不起,系统默认管理员不能删除',0);
		}

		$res = AdminUser::where('id', $id)->delete();

		if ($res != true) {
			return Help::returnApiJson('对不起,删除失败',0);
		}

		return Help::returnApiJson('恭喜,管理员删除成功',1);
	}


    /**
     * 获取管理员详情
     * @return mixed
     */
    public function adminUserDetail($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 获取用户
        $user = AdminUser::find($id);
        if (!$user) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $data['user']           = $user;
        $data['group_options']  = AdminGroup::getGroupOptions($user->group_id);;
        return Help::returnApiJson("恭喜, 获取用户详情成功！", 1, $data);
    }

    /**
     * 修改管理员状态
     * @param $id
     * @return mixed
     */
    public function adminUserStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $user = AdminUser::find($id);
        if (!$user) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $user->status = $user->status ? 0 : 1;
        $user->save();

        // 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[13]} 功能, 被操作管理员："  .  $user->username . '</b>';
		telegramSend('send_admin_behavior',$text);
        return Help::returnApiJson("恭喜, 修改用户状态成功！", 1);
    }

    /**
     * 修改管理员密码
     * @param $id
     * @return mixed
     */
    public function adminUserPassword($id) {
        // Login User
        $loginUser = auth()->guard('admin_api')->user();
        if (!$loginUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        $adminUser      = AdminUser::find($id);
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 检测操作者资金密码
        $fundPassword = trim(request("admin_password"));

        if (!$fundPassword || !Hash::check($fundPassword, $loginUser->fund_password)) {
            return Help::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        // 模型
        $type   = request("type");

        if (1 == $type) {

            $password   = request("password");
            $res        = AdminUser::checkPassword($password);
            if ($res !== true) {
                return Help::returnApiJson($res, 0);
            }

            $confirmPassword   = request("confirm_password");
            if ($confirmPassword != $password) {
                return Help::returnApiJson("对不起, 密码输入不一致!", 0);
            }

            $adminUser->password = Hash::make($password);
            $adminUser->save();

            AdminBehavior::saveItem($adminUser, "change_login_password", ['password' => Str::replaceMiddle($password, 3, 3)]);

            return Help::returnApiJson("恭喜, 修改登录密码成功!", 1);
        } else {
            $password   = request("fund_password");
            $res        = AdminUser::checkFundPassword($password);
            if ($res !== true) {
                return Help::returnApiJson($res, 0);
            }

            $confirmPassword   = request("confirm_fund_password", '');
            if ($confirmPassword != $password) {
                return Help::returnApiJson("对不起, 密码输入不一致!", 0);
            }

            $adminUser->fund_password = Hash::make($password);
            $adminUser->save();
            AdminBehavior::saveItem($adminUser, "change_fund_password", ['password' => Str::replaceMiddle($password, 3, 3)]);
            return Help::returnApiJson("恭喜, 修改资金密码成功!", 1);
        }
    }


	/**
	 * 管理员修改自己密码
	 * 资金密码和登录密码
	 */
	public function editPassword () {
		$admin = auth()->guard('admin_api')->user();
		if (!$admin) {
			return Help::returnApiJson("对不起, 用户未登录！", 0);
		}
		$password   = request("password");
		$confirmPassword   = request("confirm_password");
		$oldPassword = request('old_password');
		if ($password != $confirmPassword) {
			return Help::returnApiJson("对不起, 密码输入不一致!", 0);
		}

		// 模型
		$type   = request("type");

		if ($type == 1) {
			if (!Hash::check($oldPassword, $admin->password)) {
				return Help::returnApiJson('对不起,旧密码错误',0);
			}
			if (Hash::check($admin->password, $admin->fund_password)) {
				return Help::returnApiJson("对不起, 资金密码不能和登录密码一致!", 0);
			}
			$admin->password = Hash::make($password);
			$admin->save();
			AdminBehavior::saveItem($admin, "login_password", ['password' => Str::replaceMiddle($password, 3, 3)]);
			return Help::returnApiJson("恭喜, 修改登录密码成功!", 1);
		} else {
			if (!Hash::check($oldPassword, $admin->fund_password)) {
				return Help::returnApiJson('对不起,旧密码错误',0);
			}
			if (Hash::check($password, $admin->password)) {
				return Help::returnApiJson("对不起, 资金密码不能和登录密码一致!", 0);
			}
			$admin->fund_password = Hash::make($password);
			$admin->save();
			AdminBehavior::saveItem($admin, "change_fund_password", ['password' => Str::replaceMiddle($password, 3, 3)]);
			return Help::returnApiJson("恭喜, 修改资金密码成功!", 1);
		}

	}


    /** =================================== 管理组 @ 相关 ===================================== */

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
     * 修改管理员 组列表
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

            return Help::returnApiJson('修改管理组成功!', 1);
        }

        $group->name    = $groupName;
        $group->pid     = $parentGroup ? $parentGroup->id : 0;
        $group->rid     = 0;

        $group->save();
        $group->rid     = $parentGroup ? $parentGroup->rid . '|' . $group->id : $group->id;
        $group->save();


        return Help::returnApiJson('添加管理组成功!', 1);
    }

    public function adminGroupDel($id) {
        $model   = AdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("无效的管理组!", 0);
        }
        // 删除
        $model->delete();

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
        $group = AdminGroup::find($id);
        if (!$group) {
            return Help::returnApiJson("无效的管理组!", 1);
        }

        $ids        = AdminMenu::getAclIds($group);
        $allMenus   = AdminMenu::getAclMenus($ids);

        return Help::returnApiJson("恭喜, 查看管理组状态成功！", 1, $allMenus);

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

    /** =================================== 管理菜单 @ 相关 ===================================== */

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
    /*
    public function adminMenuAdd($id = 0)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id) {
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
    */

	/** ==================================== 审核　================================== */
	// 审核列表
	public function reviewList()
	{
		$c          = request()->all();

		// 获取数据
		$data       = AdminActionReview::getList($c);
        $data['partner_options'] = Partner::getOptions();

		$allTypes = config("admin.main.review_type");
		$_data = [];
		foreach ($data["data"] as $item) {
			$_data[] = [
				"id"                  => $item->id,
				"config_id"           => $item->config_id,
				"config_pid"          => $item->config_pid,
				"config_name"         => $item->config_name,
				"config_sign"         => $item->config_sign,
				"config_value"        => $item->config_value,
				"config_description"  => $item->config_description,
				"config_partner_show" => $item->config_partner_show,
				"config_partner_edit" => $item->config_partner_edit,
				"config_is_edit_pid"  => $item->config_is_edit_pid,
				"config_partner_sign" => $item->config_partner_sign,

				"partner_admin_id"    => $item->partner_admin_id,
				"partner_admin_name"  => $item->partner_admin_name,
				"type"                => $item->type,
                "request_desc"        => $item->request_desc,
				"value"               => $item->value,
				"process_desc"        => $item->process_desc,
                "process_config"      => $item->process_config,
				"request_ip"          => $item->request_ip,
				"review_ip"           => $item->review_ip,
				"request_time"        => date("m-d H:i", strtotime($item->request_time)),
				"review_time"         => date("m-d H:i", strtotime($item->review_time)),

				"request_admin_name"  => $item->request_admin_name,
				"review_admin_name"   => $item->review_admin_name,

				"review_fail_reason"  => $item->review_fail_reason,
				"status"              => $item->status,

				"created_at"		  => date_format($item->created_at,"Y-m-d H:i:s"),
				"updated_at"		  => date_format($item->updated_at,"Y-m-d H:i:s"),

			];
		}

		$data['data']               = $_data;
		$data['type_options']       = $allTypes;
		return Help::returnApiJson('获取数据成功!', 1,  $data);
	}

	public function reviewDetail($id)
	{
		$adminUser = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}

		$data['detail']  = [];

		// 获取用户银行卡
		$detail = AdminActionReview::find($id);
		if ($detail) {
			$allTypes = config("admin.main.review_type");
			$detail->type = $allTypes[$detail->type]['name'];
			$data['detail']   = $detail;
		}

		return Help::returnApiJson("恭喜, 获取审核详情成功！", 1, $data);
	}


	// 处理审核
	public function reviewProcess($id)
	{
		$adminUser = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}

		// 获取详情
		$detail = AdminActionReview::find($id);
		if (!$detail) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}

		// 审核人是否同一个人
		if ($adminUser->id == $detail->request_admin_id) {
			return Help::returnApiJson("对不起, 审核人和申请人不能为同一个人！", 0);
		}

		// 资金密码
		$password   = trim(request("fund_password"));
		if (!$password || !Hash::check($password, $adminUser->fund_password)) {
			return Help::returnApiJson('对不起, 无效的资金密码!', 0);
		}

		$c = request()->all();

		$desc = request('desc','');

		$mode = trim(request("mode"));
		if ($mode === 'fail') {
			$c['fail'] =request('fail','');
			$detail->review_admin_id      = $adminUser->id;
			$detail->review_admin_name    = $adminUser->username;
			$detail->review_ip            = real_ip();
			$detail->review_time          = time();
			$detail->review_fail_reason   = $c['fail'];
			$detail->status = -2;
			$detail->save();
		}

		// 处理
		$res = $detail->process($adminUser, $desc);
		if (true !== $res) {
			return Help::returnApiJson($res, 0);
		}

		return Help::returnApiJson("恭喜, 处理成功！", 1, []);
	}

}
