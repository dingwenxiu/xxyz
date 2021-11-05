<?php

namespace App\Http\Controllers\PartnerApi\Admin;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Lib\Logic\Cache\PartnerCache;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminGroup;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerCasinoPlatform;
use App\Models\Partner\PartnerHome;
use App\Models\Partner\PartnerModule;
use App\Models\Partner\PartnerNavigation;
use App\Models\System\SysTelegramChannel;
use Illuminate\Support\Facades\Hash;
use App\Models\Partner\PartnerAdminBehavior;
use App\Lib\Telegram\TelegramTrait;

/**
 * version 1.0
 * Class ApiPartnerAdminController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiPartnerAdminController extends ApiBaseController
{
    use TelegramTrait;

    // =========================== 模块开关 =========
    /**
     * 设置模块
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerModuleSet($id)
    {
        $c = request()->all();
        $partnerM = new PartnerModule();
        if ($partnerM->saveItem($c, $this->partnerSign, $id)) {
            return Help::returnApiJson('修改成功!', 1);
        }
        return Help::returnApiJson('修改失败!', 0);
    }

    /**
     * 删除模块
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerModuleDel($id)
    {
        $delStatus = PartnerModule::where('id', $id)->where('partner_sign', $this->partnerSign)->delete();
        if ($delStatus) {
            return Help::returnApiJson('删除成功!', 1);
        }
        return Help::returnApiJson('删除失败!', 0);
    }

    public function partnerModelList()
    {
        $dataList = PartnerModule::where('partner_sign', $this->partnerSign)->get();
        return Help::returnApiJson('成功!', 1, $dataList);
    }
    // ===========================导航===============

    /**
     * 设置首页导航
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAdminNavigationSet($id)
    {
        $c = request()->all();
        $PartnerNavigation = new PartnerNavigation();
        if ($PartnerNavigation->saveItem($c, $this->partnerSign, $id)) {
            return Help::returnApiJson('修改成功!', 1);
        }
        return Help::returnApiJson('修改失败!', 0);
    }

    /**
     * 删除导航
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAdminNavigationDel($id)
    {

        $delStatus = PartnerNavigation::where('id', $id)->where('partner_sign', $this->partnerSign)->delete();
        if ($delStatus) {
            return Help::returnApiJson('删除成功!', 1);
        }
        return Help::returnApiJson('删除失败!', 0);
    }

    //小飞机设置
    public function partnerTelegramChannelList()
    {
        $c['partner_sign'] = $this->partnerSign;
        $data = SysTelegramChannel::getList($c);
        return Help::returnApiJson('操作成功!', 1, $data['data']);

    }

    // 编辑名称
    public function partnerTelegramChannelEdit($id)
    {
        $channelName    = request('channel_group_name','');

        $channel = SysTelegramChannel::find($id);
        if (!$channel) {
            return Help::returnApiJson('无效的ID!', 0);
        }

        // 频道
        if ($channel->partner_sign != $this->partnerSign) {
            return Help::returnApiJson('对不起, 您没有权限!', 0);
        }

        // 不能重复　和　必须下划线开头
        if ($channelName != $channel->channel_group_name) {
            $count = SysTelegramChannel::where('channel_group_name', $channelName)->count();
            if ($count > 1) {
                return Help::returnApiJson('对不起, 渠道名称不能重复!', 0);
            }
        }

        $channel->channel_group_name    = $channelName;
        $channel->save();

        return Help::returnApiJson('操作成功!', 1);

    }

    /**
     * 生成ID
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerTelegramChannelGenId($id)
    {

        $channel = SysTelegramChannel::find($id);
        if (!$channel) {
            return Help::returnApiJson('无效的ID!', 0);
        }

        $res = $channel->updateChannelId();
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson('操作成功!', 1);

    }

    // 清空缓存
    public function partnerAdminCacheClean()
    {
        $partnerSign = $this->partnerSign;
        $templateSign = $this->partner->template_sign;
        PartnerCache::partnerAdminCacheClean($partnerSign, $templateSign);

        return Help::returnApiJson('清空成功!', 1);
    }


    public function partnerAdminNavigationList()
    {
        $partnerNavigation = PartnerNavigation::where('partner_sign', $this->partnerSign)->get();
        $partnerNavigationNew = [];
        foreach ($partnerNavigation as $item) {
        if (intval($item->style) === 2) {
        $item['platM'] = PartnerCasinoPlatform::whereIn('id', explode(',', $item->casino_plat_id))->get(['id', 'main_game_plat_code as code', 'main_game_plat_name as name']);
        } else {
            $item['platM'] = [];
        }
        $partnerNavigationNew[] = $item;
        }
        return Help::returnApiJson('获取成功!', 1, $partnerNavigationNew);
    }


    /**
     * 更新模型
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAdminHomeModuleSet($id)
    {
        $c = request()->all();
        $partnerModuel = new PartnerModule();
        if ($partnerModuel->saveItem($c, $this->partnerSign, $id) === true) {
            return Help::returnApiJson('更新成功!', 1);
        }
        if($partnerModuel->errorMsg) {
            return Help::returnApiJson($partnerModuel->errorMsg, 0);
        }
        return Help::returnApiJson('更新失败', 0);
    }


    // ========================= 首页模块 第二方案 =============================
    /**
     * 设置首页导航
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAdminHomeSet($id)
    {
        $c = request()->all();
        $partnerM = Partner::where('sign', $this->partnerSign)->first();
        $c['template_sign'] = $partnerM->template_sign ?? '';

        $partnerHome = new PartnerHome();
        if ($partnerHome->saveItem($c, $this->partnerSign, $id) === true) {
            return Help::returnApiJson('更新成功!', 1);
        }
        return Help::returnApiJson($partnerHome->error, 0);
    }

    /**
     * 删除导航
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAdminHomeDel($id)
    {
        $delStatus = PartnerHome::where('id', $id)->where('partner_sign', $this->partnerSign)->delete();
        if ($delStatus) {
            return Help::returnApiJson('删除成功!', 1);
        }
        return Help::returnApiJson('删除失败!', 0);
    }

    /**
     * 模块列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAdminHomeList()
    {
        $partnerSign = $this->partnerSign;
        $Partner = Partner::where('sign', $partnerSign)->first();
        if (is_null($Partner)) {
            return Help::returnApiJson('商户不存在!', 0);
        }
        $partnerHome = PartnerModule::where(['partner_sign' => $this->partnerSign, 'template_sign' => $Partner->template_sign, 'status' => 1])->get();
        return Help::returnApiJson('获取成功!', 1, $partnerHome);
    }

    /**
     * 模块内容列表
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function partnerAdminHomeContentList($id)
    {
        $partnerModule = PartnerModule::find($id);
        if ($partnerModule !== null) {
            $dataC = [];
            switch ($partnerModule->m_name) {
                case 'hot':
                case 'is_hot':
                case 'recommend_open_lottery':
                    $dataC = PartnerHome::leftjoin('partner_lottery', 'partner_homes.other_id', '=', 'partner_lottery.lottery_sign')
                        ->where('partner_lottery.partner_sign', $partnerModule->partner_sign)
                        ->where('partner_homes.partner_sign', $partnerModule->partner_sign)
                        ->where('partner_homes.module_id', $id)
                        ->orderBy('partner_homes.order', 'desc')
                        ->get([
                            'partner_homes.*',
                            'partner_lottery.lottery_name',
                            'partner_lottery.lottery_sign',
                            'partner_lottery.is_fast',
                            'partner_lottery.is_sport',
                            'partner_lottery.ad_img',
                            'partner_lottery.lottery_sign'
                        ]);
                    break;
                case 'popular':
                    $dataC = PartnerHome::leftjoin('partner_methods', 'partner_homes.other_id', '=', 'partner_methods.id')
                        ->where('partner_methods.partner_sign', $partnerModule->partner_sign)
                        ->where('partner_homes.partner_sign', $partnerModule->partner_sign)
                        ->where('partner_homes.module_id', $id)->orderBy('partner_homes.order', 'desc')
                        ->get(['partner_homes.*', 'partner_methods.lottery_name', 'partner_methods.method_name']);
                    break;

                case 'hotGame':
                $dataC = PartnerHome::leftjoin('partner_casino_methods', 'partner_homes.other_id', '=', 'partner_casino_methods.id')
                    ->where('partner_homes.partner_sign', $partnerModule->partner_sign)
                    ->where('partner_homes.module_id', $id)
                    ->orderBy('partner_homes.order', 'desc')
                    ->get(['partner_homes.*',
                        'partner_casino_methods.main_game_plat_code',
                        'partner_casino_methods.cn_name as lottery_name',
                        'partner_casino_methods.id as method_id',
                        'partner_casino_methods.ad_img'
                        ]);
                break;

                case 'templateColor':
                    $dataC = PartnerHome::leftjoin('template_colors', 'partner_homes.other_id', '=', 'template_colors.id')
//                        ->where('template_colors.partner_sign', $partnerModule->partner_sign)
                        ->where('partner_homes.partner_sign', $partnerModule->partner_sign)
                        ->where('partner_homes.module_id', $id)->orderBy('partner_homes.order', 'desc')
                        ->get(['partner_homes.*', 'template_colors.name as lottery_name']);
                break;
            }
            return Help::returnApiJson('获取成功!', 1, $dataC);
        }
    }

    /** =================================== 商户管理员 @ 相关 ===================================== */
    /**
     * 获取商户管理员列表
     * @return mixed
     */
    public function partnerAdminUserList()
    {
        $adminUser = $this->partnerAdminUser;

        $c          = request()->all();
        $c['partner_sign']  = $this->partnerSign;
        $c['id']      = $adminUser->group_id;

        // 是否是超管
        $name = PartnerAdminGroup::where('id', $c['id'])->where('acl', '!=', '*')->first();
        if ($name){
            $c['group_id'] = $adminUser->group_id;
        }

        $data       = PartnerAdminUser::getAdminUserList($c);
        $_data = [];
        foreach ($data['data'] as $index => $item) {
            $_tmp = [
				'last_login_time' => date('Y-m-d H:i:s', $item->last_login_time),
				'id'              => $item->id,
				'username'        => $item->username,
				'avatar'          => $item->avatar,
				'email'           => $item->email,
				'group_id'        => $item->group_id,
				'group_name'      => $item->group_name,
				'created_at'      => date('Y-m-d H:i:s', strtotime($item->created_at)),
				'register_ip'     => $item->register_ip,
				'status'          => $item->status,
				'last_login_ip'   => $item->last_login_ip,
				'add_admin_id'    => $item->add_admin_id,
            ];
            $_data[] = $_tmp;
        }
        $data['data'] = $_data;

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 高级管理 修改其他管理员 登录密码
    public function setAdminPassword ($adminId) {
        $adminUser = $this->partnerAdminUser;
        // 是否是超管
        $name = PartnerAdminGroup::where('id', $adminUser->group_id)->where('acl', '=', '*')->first();

        if (!$name) {
            return Help::returnApiJson('对不起, 您没有权限!', 0);
        }
        if (!$adminId ) {
            return Help::returnApiJson('对不起, 无效的管理员Id!', 0);
        }
        $codeOne = base64_decode(trim(request("admin_fund_password")));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);

        $codeO = base64_decode(trim(request("password", '')));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        $codeA = base64_decode(trim(request("password_confirm", '')));
        $codeB = substr($codeA, 0, -4);
        $finalC = base64_decode($codeB);
        $pwd_confirm = substr($finalC, 5, 37);


        // 资金密码
        if (!$fundPassword || !Hash::check($fundPassword, $adminUser->fund_password)) {
            return Help::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        if (empty($password)) {
            return Help::returnApiJson("对不起, 密码不能为空!", 0);
        }

        if ($password != $pwd_confirm) {
            return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
        }

        $adminUser = PartnerAdminUser::find($adminId);

        $passRet = PartnerAdminUser::checkPassword($password);
        if (true !== $passRet) {
            return Help::returnApiJson("对不起, 密码输入格式不正确!", 0);
        }

        $adminUser->password = bcrypt($password);
        $adminUser->save();

        return Help::returnApiJson("恭喜, 修改密码成功!", 1);
    }


    // 高级管理 修改其他管理员 资金密码
    public function setAdminFundPassword ($adminId) {
        $adminUser = $this->partnerAdminUser;
        // 是否是超管
        $name = PartnerAdminGroup::where('id', $adminUser->group_id)->where('acl', '=', '*')->first();

        if (!$name) {
            return Help::returnApiJson('对不起, 您没有权限!', 0);
        }

        if (!$adminId ) {
            return Help::returnApiJson('对不起, 无效的管理员Id!', 0);
        }

        $codeOne = base64_decode(trim(request("admin_fund_password")));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);

        $codeO = base64_decode(trim(request("password", '')));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        $codeA = base64_decode(trim(request("password_confirm", '')));
        $codeB = substr($codeA, 0, -4);
        $finalC = base64_decode($codeB);
        $pwd_confirm = substr($finalC, 5, 37);

        // 资金密码
        if (!$fundPassword || !Hash::check($fundPassword, $adminUser->fund_password)) {
            return Help::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        if (empty($password)) {
            return Help::returnApiJson("对不起, 密码输入为空!", 0);
        }

        if ($password != $pwd_confirm) {
            return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
        }

        $adminUser = PartnerAdminUser::find($adminId);

        $passRet = PartnerAdminUser::checkFundPassword($password);
        if (true !== $passRet) {
            return Help::returnApiJson("对不起, 资金密码格式不正确!", 0);
        }

        if (Hash::check($password, $adminUser->password)) {
            return Help::returnApiJson("对不起, 资金密码不能和登录密码一致!", 0);
        }

        $adminUser->fund_password = bcrypt($password);
        $adminUser->save();

        return Help::returnApiJson("恭喜, 修改密码成功!", 1);

    }
    // 添加管理员
    public function partnerAdminUserAdd()
    {
        $partnerAdminUser = $this->partnerAdminUser;
        $id = request('id','');
        if ($id) {
            $model = PartnerAdminUser::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerAdminUser();
        }

        $params = request()->all();
        $params['partner_sign'] = $this->partnerSign;
        $codeOne = base64_decode(request("fund_password"));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);

        $codeO = base64_decode(request("password"));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        // 密码和资金密码不能一致
        if ($password == $fundPassword) {
            return Help::returnApiJson("对不起, 登录密码和资金密码不能一致！", 0);
        }

        $params['password'] = $password;
        $params['fund_password'] = $fundPassword;
        $res    = $model->saveItem($params, $partnerAdminUser);
        if(!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加管理员成功！", 1);
    }

    /**
     * 获取商户详情
     * @param $id
     * @return mixed
     */
    public function adminUserDetail($id)
    {
        // 获取用户
        $user = PartnerAdminUser::find($id);
        if (!$user) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $data['user']           = $user;
        $data['group_options']  = PartnerAdminGroup::getGroupOptions($user->group_id);
        return Help::returnApiJson("恭喜, 获取用户详情成功！", 1, $data);
    }

    public function delAdminUser($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;
        $codeOne = base64_decode(request("admin_fund_password"));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);
        // 资金密码
        if (!$fundPassword || !Hash::check($fundPassword, $partnerAdminUser->fund_password)) {
            return Help::returnApiJson('对不起, 无效的资金密码!', 0);
        }
        $model = PartnerAdminUser::where('id', $id)->delete();
        return Help::returnApiJson('删除成功', 1);
    }

    /**
     * 修改管理员状态
     * @param $id
     * @return mixed
     */
    public function partnerAdminUserStatus($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        // 获取用户
        $model = PartnerAdminUser::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

		$admin     = PartnerAdminGroup::find($partnerAdminUser->group_id);
		$adminSelf = PartnerAdminGroup::find($model->group_id);

        // 是否是超管
        if ($adminSelf->acl == '*'  || $admin->acl != '*') {
            return Help::returnApiJson("对不起，您无权操作", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->update_partner_admin_id = $partnerAdminUser->id;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 管理员权限
    public function partnerAdminUserDetail($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;
        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 管理员密码 修改
    public function editPassword () {

        $adminUser = $this->partnerAdminUser;
        $codeOne = base64_decode(request("old_password", ''));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $oldPassword = substr($final, 5, 37);

        $codeO = base64_decode(request("password", ''));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        $codeA = base64_decode(request("password_confirm", ''));
        $codeB = substr($codeA, 0, -4);
        $finalC = base64_decode($codeB);
        $pwd_confirm = substr($finalC, 5, 37);


        if (empty($oldPassword)) {
            return Help::returnApiJson("对不起, 请输入旧密码!", 0);
        }

        if (empty($password)) {
            return Help::returnApiJson("对不起, 密码不能为空!", 0);
        }

        if ($password != $pwd_confirm) {
            return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
        }

        $passRet = PartnerAdminUser::checkPassword($password);
        if (true !== $passRet) {
            return Help::returnApiJson("对不起, 密码输入格式不正确!", 0);
        }

        if (!Hash::check($oldPassword, $adminUser->password)) {
            return Help::returnApiJson("对不起, 旧密码输入错误!", 0);
        }

        $adminUser->password = bcrypt($password);
        $adminUser->save();

        // 行为
        PartnerAdminBehavior::saveItem($adminUser, "set_password","修改登录密码");

        return Help::returnApiJson("恭喜, 修改密码成功!", 1);
    }

    // 管理员 资金密码修改
    public function setFundPassword()
    {

        $adminUser = $this->partnerAdminUser;
        $codeO = base64_decode(request("password"));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        $codeA = base64_decode(request("password_confirm"));
        $codeB = substr($codeA, 0, -4);
        $finalC = base64_decode($codeB);
        $pwd_confirm = substr($finalC, 5, 37);


        if (empty($password)) {
            return Help::returnApiJson("对不起, 密码输入为空!", 0);
        }

        if ($password != $pwd_confirm) {
            return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
        }

        $passRet = PartnerAdminUser::checkFundPassword($password);
        if (true !== $passRet) {
            return Help::returnApiJson("对不起, 资金密码格式不正确!", 0);
        }

        if (Hash::check($password, $adminUser->password)) {
            return Help::returnApiJson("对不起, 资金密码不能和登录密码一致!", 0);
        }

        $adminUser->fund_password = bcrypt($password);
        $adminUser->save();

        // 行为
        PartnerAdminBehavior::saveItem($adminUser, "set_fund_password", "修改资金密码");

        return Help::returnApiJson("恭喜, 修改资金密码成功!", 1);
    }

    /** =================================== 商户管理组 @ 相关 ===================================== */

    /**
     * 获取管理组列表
     * @return mixed
     */
    public function partnerAdminGroupList()
    {
        $adminUser = $this->partnerAdminUser;;
		$c                 = request()->all();
		$c['partner_sign'] = $this->partnerSign;
		$c['id']           = $adminUser->group_id;
        // 是否是超管
        $name = PartnerAdminGroup::where('id', $c['id'])->where('acl', '!=', '*')->first();
        $c['group_name'] = $name['name'];

        $data       = PartnerAdminGroup::getAdminGroupList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 添加管理组 或者 修改管理组
     * @return mixed
     */
    public function partnerAdminGroupAdd()
    {
        $id = request('id','');
        $partnerAdminUser = $this->partnerAdminUser;
        $admin = PartnerAdminGroup::where('id', $partnerAdminUser->group_id)->first();
        if ($admin->acl != '*') {
            return Help::returnApiJson("对不起, 无权限操作！", 0);
        }

        $model = new PartnerAdminGroup();

        $params                  = request()->all();
        $params['partner_sign']  = $this->partnerSign;

        $res    = $model->saveItem($params, $partnerAdminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 操作成功！", 1);
    }

    /**
     * 修改管理组状态
     * @param $id
     * @return mixed
     */
    public function partnerAdminGroupStatus($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        // 获取用户
        $model = PartnerAdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->update_partner_admin_id = $partnerAdminUser->id;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    /**
     * 删除管理组
     * @param $id
     * @return mixed
     */
    public function partnerAdminGroupDel($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;
        // 获取组
        $model = PartnerAdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的管理组id！", 0);
        }
        if($model->level==1)
        {
            return Help::returnApiJson("对不起, 默认管理组不能删除！", 0);
        }
        $res = $model->delete();
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        // 行为
        PartnerAdminBehavior::saveItem($partnerAdminUser, "delete_group", "删除管理组");

        return Help::returnApiJson("恭喜, 删除管理组成功！", 1);
    }

    /**
     * 设置管理组权限
     * @param $id
     * @return mixed
     */
    public function partnerAdminGroupSetAcl($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        $admin = PartnerAdminGroup::where('id', $partnerAdminUser->group_id)->first();
        if ($admin->acl != '*') {
            return Help::returnApiJson("对不起, 无权限操作！", 0);
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

        $res = $model->setPartnerAcl($ids, $partnerAdminUser);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 设置权限成功！", 1);
    }

    /**
     * 查看管理组权限
     * @param $id
     * @return mixed
     */
    public function adminUserBehaviorList($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        // 获取用户
        $model = PartnerAdminGroup::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $data = $model->getPartnerAcl();
        return Help::returnApiJson("恭喜, 获取权限！", 1, $data);
    }


    /**
     * 获取管理组列表
     * @return mixed
     */
    public function partnerAdminGroupAcl()
    {
        $c                  = request()->all();
        $c['partner_sign']  =  $this->partnerSign;
        $data       = PartnerAdminGroup::getAdminGroupList($c);

        $data['partner_options']   = Partner::getOptions($this->partnerSign);
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
