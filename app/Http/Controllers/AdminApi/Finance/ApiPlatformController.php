<?php

namespace App\Http\Controllers\AdminApi\Finance;

use App\Lib\Help;
use App\Models\Finance\FinancePlatform;
use App\Http\Controllers\AdminApi\ApiBaseController;

/**
 * version 1.0
 * 支付厂商列表
 * Class ApiPlatformController
 * @package App\Http\Controllers\AdminApi\Finance
 */
class ApiPlatformController extends ApiBaseController
{
    /**
     * 获取支付厂商列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        //获取前台传入参数
        $c = request() -> all();

        //获取列表信息
        $data = FinancePlatform ::getList($c, true);
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

        $model = FinancePlatform ::find($id);
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        $model -> delete();

        return Help ::returnApiJson('删除成功!', 1, []);
    }

    /**
     * 添加支付厂商信息
     * @param int $id
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
            $model = FinancePlatform ::find($id);
            if (!$model) {
                return Help ::returnApiJson("对不起, 目标对象不存在", 0);
            }
        } else {
            $model = new FinancePlatform(); //添加
        }


        $res = $model -> saveItem($data, $adminUser -> adminUser);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑数据" : "添加数据";
        return Help ::returnApiJson("恭喜, {$msg}成功！", 1);
    }

}
