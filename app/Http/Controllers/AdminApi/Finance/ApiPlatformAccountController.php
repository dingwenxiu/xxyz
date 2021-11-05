<?php

namespace App\Http\Controllers\AdminApi\Finance;

use Exception;
use App\Lib\Help;
use App\Models\Finance\FinancePlatform;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Finance\FinancePlatformAccount;
use App\Http\Controllers\AdminApi\ApiBaseController;

/**
 * version 1.0
 * 支付账户
 * Class ApiPlatformAccountController
 * @package App\Http\Controllers\AdminApi\Finance
 */
class ApiPlatformAccountController extends ApiBaseController
{
    /**
     * 获取支付账户信息
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function list()
    {
        //获取前台传入参数
        $c = request() -> all();

        $adminUser = auth("admin_api") -> user();
        $c['partner_sign'] = $adminUser -> partnerSign;

        //获取列表信息
        $data = FinancePlatformAccount ::getList($c);
        $platformOption             = FinancePlatform::getOptions();
        foreach ($data['data'] as $item) {
            $item->platform_name    = $platformOption[$item->platform_sign];
            $item->public_key       = $item->public_key ? $item->public_key : '';
            $item->private_key      = $item->private_key ? $item->private_key : '';
            $item->merchant_code    = substr_replace($item->merchant_code, '*****', 2, 4);
            $item->merchant_secret  = substr_replace($item->merchant_secret, '*****', 5, 22);
        }

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function del($id)
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        $model = FinancePlatformAccount ::find($id);
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        // 启用状态不能删除
        if ($model -> status > 0) {
            return Help ::returnApiJson("对不起, 您需要先禁用状态！", 0);
        }

        $model -> delete();

        return Help ::returnApiJson('删除成功!', 1, []);
    }

    /**
     * 状态
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($id)
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        $model = FinancePlatformAccount ::find($id);
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        $model -> status = $model -> status ? 0 : 1;
        $model -> save();

        return Help ::returnApiJson('修改状态成功!', 1, []);
    }

    /**
     * 添加
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($id = 0)
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        //获取前台传入参数
        $data = request() -> all();

        // $id>0是编辑
        if ($id) {
            $model = FinancePlatformAccount ::find($id);
            if (!$model) {
                return Help ::returnApiJson("对不起, 目标对象不存在", 0);
            }

        } else {
            $model = new FinancePlatformAccount(); //添加
        }


        $res = $model -> saveItem($data, $adminUser -> partnerSign);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑数据" : "添加数据";
        return Help ::returnApiJson("恭喜, {$msg}成功！", 1);
    }

    /**
     * 更新支付渠道
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateForeignChannel($id)
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        $model = FinancePlatformAccount ::find($id);
        if (!$model) {
            return Help ::returnApiJson("对不起, 目标对象不存在", 0);
        }

        //根据登录用户获取partner_sign
        $partnerAdminUser = PartnerAdminUser::where('email', $adminUser->email)->first();
        if (!$partnerAdminUser) {
            return Help::returnApiJson('该用户还没有添加支付渠道信息!', 0, []);
        }

        if ($model -> platform_sign === 'panda') {
            $res = $model -> updateChannel($model, $partnerAdminUser);
            if (true !== $res) {
                return Help ::returnApiJson($res, 0);
            }
            return Help ::returnApiJson("恭喜,更新支付渠道成功", 1, []);
        }
        return Help ::returnApiJson("恭喜,更新支付渠道成功！", 1, []);
    }
}
