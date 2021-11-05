<?php

namespace App\Http\Controllers\PartnerApi\Finance;

use App\Lib\Help;
use App\Models\Finance\FinancePlatformChannel;
use App\Http\Controllers\PartnerApi\ApiBaseController;

/**
 * version 1.0
 * 支付账户-开放渠道
 * Class ApiPlatformChannelController
 * @package App\Http\Controllers\PartnerApi\Finance
 */
class ApiPlatformChannelController extends ApiBaseController
{
    /**
     * 获取支付账户-开放渠道
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        //获取前台传入参数
        $c = request() -> all();
        //获取列表信息
        $data = FinancePlatformChannel ::getList($c, true);
        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function del($id)
    {
        $model = FinancePlatformChannel ::find($id);
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
        $model = FinancePlatformChannel ::find($id);
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        $model -> status = $model -> status ? 0 : 1;
        $model -> save();

        return Help ::returnApiJson('修改状态成功!', 1, []);
    }

    /**
     * 添加/修改
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($id = 0)
    {
        //获取前台传入参数
        $data = request() -> all();

        // $id>0是编辑
        if ($id) {
            $model = FinancePlatformChannel ::find($id);
            if (!$model) {
                return Help ::returnApiJson("对不起, 目标对象不存在", 0);
            }
        } else {
            $model = new FinancePlatformChannel(); //添加
        }

        $res = $model -> saveItem($data, $this -> partnerAdminUser);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑数据" : "添加数据";
        return Help ::returnApiJson("恭喜, {$msg}成功！", 1);
    }

}
