<?php

namespace App\Http\Controllers\PartnerApi\Finance;

use Exception;
use App\Lib\Help;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\FinancePlatform;
use App\Models\Finance\FinancePlatformAccount;
use App\Models\Finance\FinancePlatformAccountChannel;
use App\Http\Controllers\PartnerApi\ApiBaseController;

/**
 * version 1.0
 * 支付账户
 * Class ApiPlatformAccountController
 * @package App\Http\Controllers\PartnerApi\Finance
 */
class ApiPlatformAccountController extends ApiBaseController
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
        $data                       = FinancePlatformAccount ::getList($c);
        $platformOption             = FinancePlatform::getOptions();
        foreach ($data['data'] as $item) {
            $item->platform_name    = $platformOption[$item->platform_sign]??'';
            $item->public_key       = $item->public_key ? $item->public_key : '';
            $item->private_key      = $item->private_key ? $item->private_key : '';
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
        $model = FinancePlatformAccount ::where('partner_sign', $this->partnerSign)->where('id',$id)->first();
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        // 启用状态不能删除
        if ($model -> status > 0) {
            return Help ::returnApiJson("对不起, 您需要先禁用状态！", 0);
        }

        // 事务原子操作
        DB::beginTransaction();
        try {
            $model -> delete();
            FinancePlatformAccountChannel::where('partner_sign',$this -> partnerSign)->where('account_id',$model->id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return Help ::returnApiJson('删除失败!', 1, []);
        }

        return Help ::returnApiJson('删除成功!', 1, []);
    }

    /**
     * 状态
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($id)
    {
        $model = FinancePlatformAccount ::where('partner_sign', $this->partnerSign)->where('id',$id)->first();
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
        //获取前台传入参数
        $data                  = request() -> all();
        $data['partner_sign']  = $this -> partnerSign;

        if (!isset($data['platform_sign']) && empty($data['platform_sign'])){
            return Help ::returnApiJson("对不起, 请选择支付厂商！", 0);
        }

        // $id>0是编辑
        if ($id) {
            $model = FinancePlatformAccount ::where('partner_sign', $this->partnerSign)->where('id',$id)->first();
            if (!$model) {
                return Help ::returnApiJson("对不起, 目标对象不存在", 0);
            }
        } else {
            $model = new FinancePlatformAccount(); //添加
        }

        $res = $model -> saveItem($data, $this -> partnerAdminUser);
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
        $model = FinancePlatformAccount ::where('partner_sign', $this->partnerSign)->where('id',$id)->first();
        if (!$model) {
            return Help ::returnApiJson("对不起, 目标对象不存在", 0);
        }

        $res = $model -> updateChannel($model, $this -> partnerAdminUser);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }
        return Help ::returnApiJson("恭喜,更新支付渠道成功！", 1, []);
    }

    /**
     * 更新代付渠道
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentChannel($id)
    {
        $model = FinancePlatformAccount ::where('partner_sign', $this->partnerSign)->where('id',$id)->first();
        if (!$model) {
            return Help ::returnApiJson("对不起, 目标对象不存在", 0);
        }

        $res = $model -> updatePaymentChannel($model, $this -> partnerAdminUser);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }
        return Help ::returnApiJson("恭喜,更新代付渠道成功！", 1, []);
    }

    /**
     * 更新充值渠道
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRechargeChannel($id)
    {
        $model = FinancePlatformAccount ::where('partner_sign', $this->partnerSign)->where('id',$id)->first();
        if (!$model) {
            return Help ::returnApiJson("对不起, 目标对象不存在", 0);
        }

        $res = $model -> updateRechargeChannel($model, $this -> partnerAdminUser);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }
        return Help ::returnApiJson("恭喜,更新支付渠道成功！", 1, []);
    }
}
