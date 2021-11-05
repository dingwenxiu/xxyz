<?php

namespace App\Http\Controllers\AdminApi\Partner;

use App\Models\Admin\AdminActionReview;
use Exception;
use App\Lib\Help;
use App\Models\Partner\Partner;
use Illuminate\Support\Facades\DB;
use App\Models\Partner\PartnerDomain;
use App\Http\Controllers\AdminApi\ApiBaseController;


/**
 * version 1.0
 * Class ApiPartnerDomainController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiPartnerDomainController extends ApiBaseController
{

    /** =================================== 域名 @ 相关 ===================================== */
    // 获取商户域名列表
    public function domainList()
    {
        $c          = request()->all();
        $data       = PartnerDomain::getList($c);
        foreach ($data['data'] as $item) {
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
        }

        $config = config('web.domain.test_domain');
        $testDomain = $config[0];

        $data['partner_options']    = Partner::getOptions();
        $data['type_options']       = PartnerDomain::$typeList;
        $data['env_type_options']   = PartnerDomain::$envTypeList;
        $data['test_domain']		= 'www'.'.'.$testDomain;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加商户域名
    public function domainAdd($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($id) {
            $model = PartnerDomain::find($id);
            if (!$model) {
                return Help::returnApiJson('无效的Id!', 0);
            }
        } else {
            $model = new PartnerDomain();
        }

        $params = request()->all();

        $res    = $model->saveItem($params, $adminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");
		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[18]} 功能, 新增域名："  .  $params['domain'] . '</b>';
		telegramSend('send_admin_behavior',$text);
        return Help::returnApiJson("恭喜, 添加域名成功！", 1);
    }

    // 修改域名状态
    public function domainStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $model = PartnerDomain::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->update_admin_id = $adminUser->id;
        $model->save();
        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    //域名设置
    public function domainTestSet($sign){

        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }
        $partner = Partner::findBySign($sign);
        if(!$partner){

            return Help::returnApiJson("对不起, 商戶不存在！", 0);
        }
        $c = request()->all();
        if(!isset($c['test_domain_name'])||!trim($c['test_domain_name'])){
            return Help::returnApiJson("对不起, 測試域名不可為空！", 0);
        }
        $res = PartnerDomain::domainTestSet($c,$partner);
        if($res['res'] == 1){
        	// 添加telegram提现推送消息
			$fromConfig = config("admin.main.admin_behavior_type");
			$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[17]} 功能, 被操作域名："  .  $c['test_domain_name'] . '</b>';
			telegramSend('send_admin_behavior',$text);
            return Help::returnApiJson("修改成功！", 1);
        }

        return Help::returnApiJson("对不起, 修改失敗！", 0,$res);
    }

    // 删除域名状态
    public function domainDel()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        $ids = request()->all();

        foreach ($ids['ids'] as $id){
            // 获取用户
            $model = PartnerDomain::find($id);
            if (!$model) {
                DB ::rollBack();
                return Help::returnApiJson("对不起, 无效的用户id！", 0);
            }

            // 禁用状态
            if ($model->status == 1) {
                DB ::rollBack();
                return Help::returnApiJson("对不起, 您需要先禁用域名！", 0);
            }

        }

        // 是否需要审核
        $allTypes = config("admin.main.review_type");
        $type     = 'partner_domain_del';
        $ids['partner_sign'] = $model->partner_sign;

        $domains = PartnerDomain::whereIn('id',$ids['ids'])->get();

        $webs = [];
        foreach ($domains as $item) {
            $webs[] = $item->domain;
        }

        $ids['config'] = implode(',',$webs);

        if (array_key_exists($type, $allTypes)) {
            $domainId = implode(',',$ids['ids']);
            $ids['values'] = $domainId;
            $ids['request_desc'] = request('request_desc');
            if (!$ids['request_desc']) {
                return Help::returnApiJson('对不起,请输入审核描述',0);
            }
            //需要审核
            $res = AdminActionReview::addReview($ids , $type, $adminUser, $adminUser);
            if ($res !== true) {
                return Help::returnApiJson($res, 0);
            }
            return Help::returnApiJson("恭喜, 操作已提交, 等待风控审核！", 1);
        }

        // 开启事务处理
        DB ::beginTransaction();
        try {
            foreach ($ids['ids'] as $id){
                // 获取用户
                $model = PartnerDomain::find($id);
                if (!$model) {
                    DB ::rollBack();
                    return Help::returnApiJson("对不起, 无效的用户id！", 0);
                }

                // 禁用状态
                if ($model->status == 1) {
                    DB ::rollBack();
                    return Help::returnApiJson("对不起, 您需要先禁用域名！", 0);
                }

                // 无需审核 删除
                $res = $model->delete();
                if ($res !== true) {
                    DB ::rollBack();
                }
            }

            DB ::commit();
            return Help::returnApiJson("恭喜, 删除数据成功！", 1);
        } catch (Exception $e) {
            DB ::rollBack();
        }

    }
}
