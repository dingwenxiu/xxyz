<?php

namespace App\Http\Controllers\PartnerApi\Admin;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Models\Casino\CasinoPlatform;
use App\Models\Partner\PartnerAdminActionReview;
use App\Models\Partner\PartnerAdminBehavior;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminAccessLog;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerMenu;
use App\Models\Partner\PartnerMenuConfig;
use App\Models\Partner\PartnerReviewFlow;
use App\Models\Partner\PartnerSetting;
use App\Models\Player\Player;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * version 1.0
 * Class ApiPartnerController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiPartnerController extends ApiBaseController
{

    /** =================================== 商户 @ 相关 ===================================== */
    // 获取商户列表
    public function partnerList()
    {
        $c          = request()->all();
        $data       = Partner::getList($c);

        $data['platform_options']   = CasinoPlatform::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加商户
    public function partnerAdd($id)
    {
        $adminUser = $this->partnerAdminUser;

        if ($id) {
            $model = Partner::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new Partner();
        }

        $params = request()->all();

        $res    = $model->saveItem($params, $adminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加商户成功！", 1);
    }

    // 修改商户状态
    public function partnerStatus($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取用户
        $model = Partner::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->update_admin_id = $adminUser->id;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 商户详情
    public function partnerDetail($id)
    {

    }

    // 设置娱乐城
    public function partnerSetCasino($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取商户
        $model = Partner::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的商户id！", 0);
        }

        $platformId = request("casino_platform_id", '');
        $siteId     = request("casino_site_id", '');

        $model->casino_platform_id  = $platformId;
        $model->casino_site_id      = $siteId;
        $model->save();

        $codeArr    = request("code", []);
        $res        = $model->setCasinoPlatform($codeArr, $adminUser);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 设置直属
    public function partnerSetTopPlayer($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取商户
        $model = Partner::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的商户id！", 0);
        }

        // 获取选项
        $action     = request('action', 'process');
        if ($action == 'option') {

            $data['player_options'] = Player::getNotBindToPartnerTopPlayer();
            return Help::returnApiJson("恭喜, 添加数据成功！", 1, $data);
        }

        $topIds = request("ids", []);
        $res    = $model->setTopPlayer($topIds, $adminUser);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 设置商户　菜单
    public function partnerSetAdminMenu($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取商户
        $model = Partner::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的商户id！", 0);
        }

        // 获取选项
        $action     = request('action', 'process');
        if ($action == 'option') {
            $checkedMenu = PartnerMenu::where("partner_sign", $model->sign)->where("status", 1)->pluck('menu_id');
            $data['menu_options'] = PartnerMenuConfig::getMenuList($checkedMenu->toArray());
            return Help::returnApiJson("恭喜, 添加数据成功！", 1, $data);
        }

        // 保存
        $menus  = request("menu_ids", []);
        $res    = $model->setAdminMenus($menus, $adminUser, $model->sign);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加成功！", 1);
    }

    /** =================================== 域名 @ 相关 ===================================== */


    /** =================================== 直属 @ 相关 ===================================== */
    // 获取 商户直属 列表
    public function partnerPlayerList()
    {
        $adminUser = $this->partnerAdminUser;

        $c          = request()->all();
        $data       = PartnerTopPlayer::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取 商户直属 添加
    public function partnerPlayerAdd($id)
    {
        $adminUser = $this->partnerAdminUser;

        if ($id) {
            $model = PartnerTopPlayer::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerTopPlayer();
        }

        $params = request()->all();

        $res    = $model->saveItem($params, $adminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加超级管理员成功！", 1);
    }

    /** =================================== 日志 @ 相关 ===================================== */
    // 商户管理员访问日志
    public function partnerAccessLogList()
    {

        $c          = request()->all();
        $c['partner_sign']  = $this->partnerSign;
        $data       = PartnerAdminAccessLog::getList($c);

        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions($c['partner_sign']);
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** =================================== 行为 @ 相关 ===================================== */
    // 商户管理员行为
    public function partnerAdminBehavior()
    {

        $c          = request()->all();
        $data       = PartnerAdminBehavior::getList($c);

        foreach ($data['data'] as $item) {
            $item->add_time = date("Y-m-d H:i:S", $item->add_time);
        }

        $data['partner_admin_user']     = PartnerAdminUser::getAdminUserOptions();
        $data['partner_options']        = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** =================================== 审核 @ 相关 ===================================== */
    /**
     * 获取商户管理员日志
     * @return mixed
     */
    public function adminUserBehaviorList()
    {
        $adminUser = $this->partnerAdminUser;

        $c                  = request()->all();
        $c['partner_sign']  = $adminUser->partner_sign;
        $data       = PartnerAdminBehavior::getList($c);

        foreach ($data['data'] as $item) {
            $item->add_time = $item->add_time ? date("Y-m-d H:i:S", $item->add_time) : "---";
            $item->add_time = $item->review_time ? date("Y-m-d H:i:S", $item->review_time) : "---";
        }

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
    
    /** =================================== 直属 @ 相关 ===================================== */

    // 修改二维码
    public function editUpLoadImg()
    {
        try {
            //获取前台传入参数
            $c = request()->all();

            $file                 = $c['file'];
            $data['partner_sign'] = $this->partnerSign;
            $data['filename']     = $c['type'];
            $data['directory']    = 'logo';
            $ImageArrange = new ImageArrange();
            $ImageArrangeM = $ImageArrange->uploadImage($file, $data);

            if (!$ImageArrangeM['success']) {
                return Help::returnApiJson($ImageArrangeM['msg'], 0);
            }
            $filename = $ImageArrangeM['data']['path'];

            PartnerSetting::where('partner_sign', $this->partnerSign)->update([$c['type'] => $filename]);

            return Help::returnApiJson('编辑成功!', 1, ['path' => $filename]);
        } catch (\Exception $e) {
            //删除上传成功的图片
            return Help::returnApiJson('编辑失败!', 0);
        }
    }

    //获取二维码
    public function qrImage()
    {
        $data = [];
        $partner = PartnerSetting::where('partner_sign', $this->partnerSign)->first();
        if (is_null($partner)) {
            $data['qr_code_1'] = '' ;
            $data['qr_code_2'] = '' ;
            $data['qr_code_3'] = '' ;
            return Help::returnApiJson('获取成功!', 1, $data);
        }
        $data['qr_code_1'] = $partner->qr_code_1 ?  $partner->qr_code_1 : '' ;
        $data['qr_code_2'] = $partner->qr_code_2 ?  $partner->qr_code_2 : '' ;
        $data['qr_code_3'] = $partner->qr_code_3 ?  $partner->qr_code_3 : '' ;

        return Help::returnApiJson('获取成功!', 1, $data);
    }

    // 删除二维码
    public function qrCodeDel ()
    {
        $type = request('type','');
        if (!in_array($type,['qr_code_1', 'qr_code_2', 'qr_code_3'])) {
            return Help::returnApiJson('对不起,　图片参数不正确', 0);
        }
        $partner = PartnerSetting::where('partner_sign', $this->partnerSign)->update([$type => '']);
        return Help::returnApiJson('删除成功!', 1, $partner);
    }

    /**
     * 客服设置
     * @return \Illuminate\Http\JsonResponse
     */
    public function csSet($id)
    {
        $c = request()->all();
        if ($id == 0) {
            $partnerSetting = PartnerSetting::where('partner_sign', $this->partnerSign)->first();
            if ($partnerSetting !== null && !empty($partnerSetting->cs_url)) {
                return Help::returnApiJson('添加失败，客服已存在!', 0, []);
            }
            $c['partner_sign'] = $this->partnerSign;
            PartnerSetting::insert($c);
        } else {
            $c['partner_sign'] = $this->partnerSign;
            PartnerSetting::where('partner_sign', $this->partnerSign)->where('id', $id)->update($c);
        }
        return Help::returnApiJson('更新成功!', 1, []);
    }

    /**
     * 客服列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function csList()
    {
        $data = PartnerSetting::where('partner_sign', $this->partnerSign)->first();
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // ==================== ============  ===================
    /**
     * 修改审核人员
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveCheckUser()
    {
        $c                  = request()->all();
        $partnerSign        = $this->partnerSign;
        $PartnerReviewFlowM = new PartnerReviewFlow();
        $status = $PartnerReviewFlowM->saveItem($c, $partnerSign);
        if ($status) {
            return Help::returnApiJson('更新成功!', 1);
        }
        return Help::returnApiJson($PartnerReviewFlowM->errorMsg, 0);
    }

    /**
     * 显示列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCheckUser()
    {
        $c                  = request()->all();
        $partnerSign        = $this->partnerSign;
        $PartnerReviewFlowM = new PartnerReviewFlow();
        $data = $PartnerReviewFlowM->getItem($c, $partnerSign);
        $checkType = config('partner.check_type');
        foreach ($checkType as $key => $item) {
            foreach ($data as $key1 => $item1) {
                if ($item1['type'] == $item['sign']) {
                    $item1['type_name'] = $item['name'];
                    foreach ($item['data'] as $key2 => $item2) {
                        if ($item1['type_detail'] == $item2['key']) {
                            $item1['type_detail_name'] = $item2['name'];
                        }
                    }
                }
            }
        }
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 显示列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCheckUserOne()
    {
        $c                  = request()->all();
        $partnerSign        = $this->partnerSign;
        $data = PartnerReviewFlow::find($c['id']);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 删除用户
     * @return \Illuminate\Http\JsonResponse
     */
    public function delCheckUser()
    {
        $c                  = request()->all();
        $PartnerReviewFlowM = new PartnerReviewFlow();
        $data = $PartnerReviewFlowM->delItem($c);

        return Help::returnApiJson('删除数据成功!', 1, $data);
    }

    /**
     * 获取审核类型
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCheckType()
    {
        $checkType = config('partner.check_type');
        return Help::returnApiJson('获取数据成功!', 1, $checkType);
    }

}
