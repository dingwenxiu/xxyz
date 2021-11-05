<?php

namespace App\Http\Controllers\PartnerApi\Finance;

use App\Lib\Help;
use App\Models\Finance\FinancePlatform;
use App\Models\Finance\FinancePlatformChannel;
use App\Models\Finance\FinancePlatformAccountChannel;
use App\Http\Controllers\PartnerApi\ApiBaseController;

/**
 * version 1.0
 * 支付厂商-开放渠道
 * Class ApiPlatformAccountChannelController
 * @package App\Http\Controllers\PartnerApi\Finance
 */
class ApiPlatformAccountChannelController extends ApiBaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function list()
    {
        // 获取前台传入参数
        $c = request() -> all();
        $c['partner_sign'] = $this -> partnerSign;

        // 获取列表信息
        $data           = FinancePlatformAccountChannel ::getList($c);
        $platformOption = FinancePlatform::getOptions();
        $channelOption  = FinancePlatformChannel::getOptions();
        foreach ($data['data'] as $item) {
            $item->platform_name    = $platformOption[$item->platform_sign]??'';
            $item->channel_name     = $channelOption[$item->platform_sign][$item->channel_sign]??'';
        }

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 添加支付厂商-开放渠道
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($id=0)
    {
        //获取前台传入参数
        $data = request() -> all();
        // $id>0是编辑
        if ($id) {
            $model = FinancePlatformAccountChannel ::where('partner_sign', $this->partnerSign)->where('id', $id)->first();
            if (!$model) {
                return Help ::returnApiJson("对不起, 目标对象不存在", 0);
            }

            // 是否一个商户的
            if ($model -> partner_sign != $this -> partnerSign) {
                return Help ::returnApiJson("对不起, 只能管理自己商户下面数据！", 0);
            }
        } else {
            if (!isset($data['platform_channel_id']) && empty($data['platform_channel_id'])){
                $data['platform_channel_id'] = '';
            }
            $model = new FinancePlatformAccountChannel(); //添加
        }

        $res = $model -> saveItem($data, $this -> partnerAdminUser);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑数据" : "添加数据";
        return Help ::returnApiJson("恭喜, {$msg}成功！", 1);
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function del($id)
    {
        $model = FinancePlatformAccountChannel ::where('partner_sign', $this->partnerSign)->where('id', $id)->first();
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
        $model = FinancePlatformAccountChannel ::where('partner_sign', $this->partnerSign)->where('id', $id)->first();
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        $model -> status = $model -> status ? 0 : 1;
        $model -> save();

        return Help ::returnApiJson('修改状态成功!', 1, []);
    }

}
