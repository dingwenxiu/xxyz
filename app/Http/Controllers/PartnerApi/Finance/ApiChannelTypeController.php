<?php

namespace App\Http\Controllers\PartnerApi\Finance;

use App\Lib\Help;
use App\Models\Finance\FinanceChannelType;
use App\Http\Controllers\PartnerApi\ApiBaseController;

/**
 * version 1.0
 * 支付种类表
 * Class ApiChannelTypeController
 * @package App\Http\Controllers\PartnerApi\Finance
 */
class ApiChannelTypeController extends ApiBaseController
{
    /**
     * 获取支付种类表信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        //获取前台传入参数
        $c = request() -> all();

        //获取列表信息
        $data = FinanceChannelType ::getList($c);
        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function del($id)
    {
        $model = FinanceChannelType ::find($id);
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        $model -> delete();

        return Help ::returnApiJson('删除成功!', 1, []);
    }

    /**
     * 添加/编辑
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($id=0)
    {
        //获取前台传入参数
        $data = request() -> all();

        // $id>0是编辑
        if ($id) {
            $model = FinanceChannelType ::find($id);
            if (!$model) {
                return Help ::returnApiJson("对不起, 目标对象不存在", 0);
            }
        } else {
            $model = new FinanceChannelType(); //添加
        }

        $res = $model -> saveItem($data);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑数据" : "添加数据";
        return Help ::returnApiJson("恭喜, {$msg}成功！", 1);
    }
}
