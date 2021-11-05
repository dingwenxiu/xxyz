<?php

namespace App\Http\Controllers\AdminApi\Partner;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Admin\AdminActionReview;
use App\Models\Admin\AdminGroup;
use App\Models\Admin\AdminUser;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminGroup;
use App\Models\Partner\PartnerAdminUser;
use Illuminate\Support\Facades\Hash;

/**
 * version 1.0
 * Class ApiPartnerAdminController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiPartnerAdminController extends ApiBaseController
{
    /** =================================== 商户管理员 @ 相关 ===================================== */
    /**
     * 获取商户管理员列表
     * @return mixed
     */
    public function adminUserList()
    {
        $c = request()->all();
        $data = PartnerAdminUser::getAdminUserList($c);
        $data['partner_options'] = Partner::getOptions();
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 添加管理员
    public function adminUserAdd($id=0)
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
            $from = 1;
        } else {
            $model = new PartnerAdminUser();
			$from = 0;
        }

        $params = request()->all();

        $res = $model->saveItem($params, $adminUser);
        if (!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }
		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[$from]} 功能, 被操作管理员："  .  $params['username'] . '</b>';
		telegramSend('send_admin_behavior',$text);

        return Help::returnApiJson("恭喜, 添加超级管理员成功！", 1);
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
        $model = PartnerAdminUser::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->update_admin_id = $adminUser->id;
        $model->save();

		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig['15']} 功能, 被操作管理员："  .  $model->username . '</b>';
		telegramSend('send_admin_behavior',$text);

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 管理员权限 待明确
    public function adminUserAcl($id)
    {
		$adminUser = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户未登录！", 0);
		}

		// 获取组
		$model = PartnerAdminGroup::find($id);
		if (!$model) {
			return Help::returnApiJson("对不起, 无效的用户id！", 0);
		}

		$ids = request("menu_ids");
		if (!$ids || !is_array($ids)) {
			return Help::returnApiJson("对不起, 无效的菜单ID传入！", 0);
		}

		$res = $model->setPartnerAcl($ids, $adminUser);
		if ($res !== true) {
			return Help::returnApiJson($res, 0);
		}

		return Help::returnApiJson("恭喜, 设置权限成功！", 1);
    }


	/**
	 * 商户管理员 权限查看
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function partnerAdminUserAcl ($id) {
		// 获取用户
		$model = PartnerAdminGroup::find($id);
		if (!$model) {
			return Help::returnApiJson("对不起, 无效的用户id！", 0);
		}

		$data = $model->getPartnerAcl();
		return Help::returnApiJson("恭喜, 获取权限！", 1, $data);
	}

    /** =================================== 商户管理组 @ 相关 ===================================== */

    /**
     * 获取管理组列表
     * @return mixed
     */
    public function adminGroupList()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $c = request()->all();
        $c['id'] = $adminUser->group_id;

        // 是否是超管
        $name = PartnerAdminGroup::where('id', $c['id'])->where('acl', '!=', '*')->first();
        $c['group_name'] = $name['name'];

        $data = PartnerAdminGroup::getAdminGroupList($c);
        foreach ($data['data'] as $item) {
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';


            $item->acl_list = PartnerAdminGroup::getAkkPartnerAcl($item->acl,$item->partner_sign);

        }

        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions();
        $data['admin_user']         = AdminUser::getAdminUserOptions();
        $data['partner_options']    = Partner::getOptions();
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 添加管理组
     * @param $id
     * @return mixed
     */
    public function adminGroupAdd($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

		$params = request()->all();

        if ($id) {
            $model = PartnerAdminGroup::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
			$from = 4;
            $msg = '修改';
        } else {
            $model = new PartnerAdminGroup();
            $from = 3;
            $msg = '添加';
        }


        $res = $model->saveItem($params, $adminUser);
        if (true !== $res) {
            return Help::returnApiJson($res, 0);
        }

		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[$from]} 功能, 被操作管理组："  .  $params['name'] . '</b>';
		telegramSend('send_admin_behavior',$text);

        return Help::returnApiJson("恭喜, {$msg}管理组成功！", 1);
    }

    /**
     * 修改管理组状态
     * @param $id
     * @return mixed
     */
    public function adminGroupStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $model = PartnerAdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->update_partner_admin_id = $adminUser->id;
        $model->save();
		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[16]} 功能, 被操作管理组："  . $model->name . '</b>';
		telegramSend('send_admin_behavior',$text);
        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    /**
     * 设置管理组权限
     * @param $id
     * @return mixed
     */
    public function adminGroupSetAcl($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取组
        $model = PartnerAdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $ids = request("menu_ids");
        if (!$ids || !is_array($ids)) {
            return Help::returnApiJson("对不起, 无效的菜单ID传入！", 0);
        }

        $res = $model->setPartnerAcl($ids);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[14]} 功能, 被操作管理员："  .  $model->username . '</b>';
		telegramSend('send_admin_behavior',$text);

        return Help::returnApiJson("恭喜, 设置权限成功！", 1);
    }

    /**
     * 查看管理组权限
     * @param $id
     * @return mixed
     */
    public function adminGroupAcl($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $model = PartnerAdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $data = $model->getPartnerAcl();

        return Help::returnApiJson("恭喜, 获取权限！", 1, $data);
    }

    public function adminUserPassword($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }
        $c = request()->all();
        $partner = PartnerAdminUser::find($id);
        if (!$partner) {
            return Help::returnApiJson("对不起, 商戶不存在！", 0);
        }

        if (!isset($c['fund_password'])) {
        	return Help::returnApiJson('对不起,请输入资金密码',0);
		}

        if (!Hash::check($c['fund_password'], $partner->fund_password)) {
				return Help::returnApiJson("对不起, 资金密码不正确!", 0);
        }

        // 是否需要审核
        $allTypes = config("admin.main.review_type");
        $type    = 'login_password';
		$c['partner_sign'] = $partner->partner_sign;
        if (!isset($c['password'])) {
        	return Help::returnApiJson('您好,密码不能为空',0);
		}
        if (array_key_exists($type, $allTypes)) {
            $c['values'] = $c['password'];
            $c['request_desc'] = request('request_desc');
            if (!$c['request_desc']) {
                return Help::returnApiJson('对不起,请输入审核描述',0);
            }
        	//需要审核
			$res = AdminActionReview::addReview($c , $type, $adminUser, $partner);
			if ($res !== true) {
				return Help::returnApiJson($res, 0);
			}
			return Help::returnApiJson("恭喜, 操作已提交, 等待风控审核！", 1);
		}

        $c['password'] = bcrypt($c['password']);
        $res = PartnerAdminUser::where('partner_sign',$partner->sign)->where('id', $id)->update(['password'=>$c['password']]);

        if ($res == true) {

            return Help::returnApiJson("恭喜, 修改成功！", 1, $res);
        } else {

            return Help::returnApiJson("修改失敗！", 0, $res);
        }

    }


}
