<?php

namespace App\Http\Controllers\AdminApi\Partner;

use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Models\Admin\AdminUser;
use App\Models\Partner\PartnerDomain;
use App\Models\Partner\PartnerLottery;
use App\Lib\Logic\Cache\PartnerCache;
use App\Lib\Logic\Cache\LotteryCache;
use App\Models\Partner\PartnerMenu;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerConfigure;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerMenuConfig;
use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Http\Controllers\PartnerApi\ApiUploadController;

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
        foreach ($data['data'] as $item) {
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
        }

        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions();
        $data['admin_user']         = AdminUser::getAdminUserOptions();
        $data['partner_options']    = Partner::getOptions();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }


	// 添加商户测试域名
	public function testWebAdd () {
		$adminUser = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}

		$c = request()->all();
		$c['partner_sign'] = request('partner_sign');

		$partner = Partner::where('sign', $c['partner_sign'])->first();
		if (!$partner) {
			return Help::returnApiJson('对不起,商户信息错误',0);
		}

		$config = config('web.domain.test_domain');

		$c['domain']   = $config[0];
		$webApi = 'api'.'.' . $c['domain'];
		$partnerApi = 'partner-api' . '.' . $c['domain'];
		$res = PartnerDomain::where('partner_sign', $c['partner_sign'])->where('type', 1)->where('env_type', 2)->where('domain',$webApi)->first();
		if ($res) {
		    $domain = $res;
        } else {
            $domain = new PartnerDomain();
        }

		$domain->partner_sign = $c['partner_sign'];
		$domain->name         = '投注API　测试';
		$domain->domain       = $webApi;
		$domain->type         = 1;
		$domain->env_type     = 2;
		$domain->add_admin_id = $adminUser->id;
		$domain->remark       = '暂无备注';
		$domain->save();
		PartnerDomain::where('partner_sign', '!=', $c['partner_sign'])->where('type', 1)->where('domain', $webApi)->where('env_type', 2)->delete();

        $resew = PartnerDomain::where('partner_sign', $c['partner_sign'])->where('type', 2)->where('env_type', 2)->where('domain',$partnerApi)->first();
        if ($resew) {
            $domainT = $resew;
        } else {
            $domainT = new PartnerDomain();
        }
		$domainT->partner_sign = $c['partner_sign'];
		$domainT->name         = '商户API　测试';
		$domainT->domain       = $partnerApi;
		$domainT->type         = 2;
		$domainT->env_type     = 2;
		$domainT->add_admin_id = $adminUser->id;
		$domainT->remark       = '暂无备注';
		$domainT->save();
        PartnerDomain::where('partner_sign', '!=', $c['partner_sign'])->where('type', 2)->where('domain', $partnerApi)->where('env_type', 2)->delete();

		return Help::returnApiJson('恭喜,编辑测试域名成功',1);
	}

	// 是否控水
    public function rateOpen () {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $id = request('id');
        if (!$id) {
            return Help::returnApiJson('对不起,商户id不能为空',0);
        }

        $model = Partner::find($id);
        $model->rate_open = $model->rate_open ? 0 : 1;
        $model->save();

        $selfOpenLottery = config("game.self_open_lottery.lottery");
        $selfOpen = [];
        foreach ($selfOpenLottery as $item) {
            $selfOpen[] = $item['cn_name'];
        }

        PartnerLottery::where('partner_sign', $model->sign)->whereIn('lottery_name', $selfOpen)->update(['rate_open' => $model->rate_open]);
        //PartnerCache:flushCache($model->sign);
        PartnerCache::flushPartner($model->sign);
        LotteryCache::flushPartnerAll($model->sign);
        return Help::returnApiJson('恭喜,设置成功',1);
    }


	// 添加商户
    public function partnerAdd($id=0)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id) {
            $model = Partner::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new Partner();
        }

        $params = request()->all();
        if (!isset($params['host']) || empty($params['host'])){
            return Help::returnApiJson('对不起,请输入域名', 0);
        }

        $res    = $model->saveItem($params, $adminUser);
        if(!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加商户成功！", 1);
    }

    // 修改商户状态
    public function partnerStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

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
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $model = Partner::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }
        return Help::returnApiJson('获取数据成功!', 1,  $model);
    }


    /**
     * 获取娱乐城 信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPartnerCasino()
    {
        $c = request()->all();

        $partnerSign = $c['partner_sign'] ?? '';
        if (empty($partnerSign)) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $singArr = ['casino_secret_key', 'casino_merchant', 'casino_gateway', 'casino_encryption_time'];
        $partnerConfigureM = PartnerConfigure::where('partner_sign', $partnerSign)->whereIn('sign', $singArr)->get();

        return Help::returnApiJson('获取数据成功!', 1,  $partnerConfigureM);

    }



    // 设置娱乐城
    public function partnerSetCasino()
    {
        $c = request()->all();
        $partnerSign = $c['partner_sign'] ?? '';
        $singArr = ['casino_secret_key', 'casino_merchant', 'casino_gateway', 'casino_encryption_time'];
        foreach ($singArr as $key => $item) {
            if (empty($c[$item])) {
                return Help::returnApiJson("对不起, 参数错误！", 0);
            }
            $partnerConfigureStatus = PartnerConfigure::where('partner_sign', $partnerSign)->where('sign', $item) -> update(['value' => $c[$item]]);
        }


        return Help::returnApiJson("恭喜, 修改成功！", 1);
    }


    // 设置商户　菜单
    public function partnerSetAdminMenu($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

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

    // 设置游戏
    public function partnerSetLottery($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取商户
        $model = Partcrontner::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的商户id！", 0);
        }

        $lotteryIdArr = request("lottery_ids", []);

        // 设置游戏
        $res = $model->setLottery($lotteryIdArr);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 设置商户图标
    public function partnerSetUploadImage($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        $file       = request()->file('file');
		$partner = partner::find($id);
		if (!$partner){
			return Help::returnApiJson('信息有误',0);
		}

        $data['partner_sign'] = $partner->sign;
        $data['directory']    = 'partnerlogo';
        $data['filename']     = strtolower($partner->sign);
        $ImageArrange = new ImageArrange();
        $ImageArrangeM = $ImageArrange->uploadImage($file, $data);

        if (!$ImageArrangeM['success']) {
            return Help::returnApiJson($ImageArrangeM['msg'], 0);
        }
        $filename = $ImageArrangeM['data']['path'];

        partner::where('id', $id)->update(['logo_image_partner' => $filename]);

        return Help::returnApiJson('编辑商户LOGO成功!', 1, ['path' => $filename]);

    }

}
