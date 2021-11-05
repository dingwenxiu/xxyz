<?php

namespace App\Http\Controllers\PartnerApi\Finance;

use Exception;
use App\Lib\Help;
use App\Models\Finance\FinancePlatform;
use App\Http\Controllers\PartnerApi\ApiBaseController;

/**
 * version 1.0
 * 支付厂商列表
 * Class ApiPlatformController
 * @package App\Http\Controllers\PartnerApi\Finance
 */
class ApiPlatformController extends ApiBaseController
{
    /**
     * 获取支付厂商列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
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


        $res = $model -> saveItem($data, $this -> partnerAdminUser);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑数据" : "添加数据";
        return Help ::returnApiJson("恭喜, {$msg}成功！", 1);
    }

    /**
     * 获取支付关联信息表
     * @param $sign
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function listChild($sign)
    {
        // 获取前台传入参数
        $c                 = request() -> all();
        $c['partner_sign'] = $this->partnerSign;
        if ($sign == 'platformList'){
            $data          = FinancePlatform ::getPlatformList();
            return Help ::returnApiJson('获取数据成功!', 1, $data);
        }elseif($sign == 'platformAccountList'){
            $data          = FinancePlatform ::getPlatformAccountList($c);
            return Help ::returnApiJson('获取数据成功!', 1, $data);
        }

        // 获取列表信息
        $c['sign']         = $sign;
        $data              = FinancePlatform ::getListChild($c);
        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }


}
