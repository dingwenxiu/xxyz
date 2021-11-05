<?php

namespace App\Http\Controllers\PartnerApi\Finance;

use App\Lib\Help;
use App\Models\Admin\SysBank;
use App\Exports\RechargeExport;
use App\Models\Finance\Recharge;
use App\Models\Finance\RechargeLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Finance\FinanceChannelType;
use App\Http\Controllers\PartnerApi\ApiBaseController;


/**
 * version 1.0
 * 充值
 * Class ApiRechargeController
 * @package App\Http\Controllers\AdminApi\Finance
 */
class ApiRechargeController extends ApiBaseController
{
    /** ============================= 充值 - 记录 ============================= */
    // 列表
    public function rechargeList()
    {
        $c                 = request()->all();
        $c['partner_sign'] = $this->partnerSign;

        // 如果是下载
        if (isset($c['is_export']) && $c['is_export'] == 1) {
            $date = date("Y-m-d");
            return (new RechargeExport($c))->download("recharge-{$date}.csv", \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv']);
        }

        $data                           = Recharge::getList($c, true);
        $totalRealAmount                = $totalRequestAmount = 0;
        foreach ($data['data'] as $item) {
            $item->amount               = number4($item->amount);
            $item->real_amount          = $item->real_amount ? number4($item->real_amount) : 0;

            // 操作者
            $pAdminUser                 = PartnerAdminUser::getAdminUserOptions('');
            $item -> partner_admin_id   = empty($pAdminUser[$item -> partner_admin_id])?'':$pAdminUser[$item -> partner_admin_id];
            $totalRealAmount           += $item->real_amount;
            $totalRequestAmount        += $item->amount;
        }

        $data['pageTotalAmount']         = number4(moneyUnitTransferIn($totalRequestAmount));
        $data['pageTotalRealAmount']     = number4(moneyUnitTransferIn($totalRealAmount));

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 列表
    public function rechargeExport()
    {
        $c                  = request()->all();
        $c['partner_sign']  = $this->partnerSign;
        $date               = date("Y-m-d");
        return Excel::download(new RechargeExport($c), "recharge-{$date}.xlsx");
    }

    // 人工
    public function rechargeHand($id)
    {
        $adminUser = $this->partnerAdminUser;

        $item = Recharge::find($id);
        // 操作者
        $pAdminUser                 = PartnerAdminUser::getAdminUserOptions('');
        $item -> partner_admin_id   = empty($pAdminUser[$item -> partner_admin_id])?'':$pAdminUser[$item -> partner_admin_id];

        if (!$item) {
            return Help::returnApiJson("对不起, 不存在的充值记录!", 0);
        }

        if ($item->status != 1 && $item->status != 0) {
            return Help::returnApiJson("对不起，该订单已进行过人工处理!", 0);
        }

        // 获取配置
        $action     = request('action', 'process');
        if ($action == 'option') {
            $data['model']          = $item;
            $data['type_options']   = Recharge::$handType;
            return Help::returnApiJson("恭喜, 获取数据成功！", 1, $item);
        }

        $type   = request("type");
        $amount = request("amount");
        $reason = request("reason");

        if (!$amount) {
            return Help::returnApiJson("对不起, 无效的金额!", 0);
        }

        $realAmount     = moneyUnitTransferIn($amount);
        if ($realAmount > $item -> amount) {
            return Help ::returnApiJson("对不起, 资金不能超过充值资金!", 0);
        }

        // 人工失败
        if ($type == 1) {
            $item -> status           = Recharge::STATUS_MANUAL_FAIL;
            $item -> fail_reason      = $reason;
            $item -> real_amount      = number4(0);
            $item -> partner_admin_id = $adminUser -> id;
            $item -> day_m            = time();
            $item -> save();
        } else {
            $res = $item -> process($realAmount, $adminUser -> id, $reason);
            if ($res !== true) {
                return Help ::returnApiJson($res, 0);
            }
        }
        return Help ::returnApiJson("恭喜, 人工处理成功!", 1);
    }

    // 日志
    public function rechargeLog($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取充值记录
        $recharge = Recharge::find($id);
        if (!$recharge) {
            return Help::returnApiJson("对不起, 充值记录不存在！", 0);
        }

        // 获取充值日志
        $rechargeLog = RechargeLog::where('order_id', $recharge->id)->first();
        if ($rechargeLog) {
            $cType                                  = FinanceChannelType::getOptions();
            $sBank                                  = SysBank::getOption();
            $rechargeLog->amount                    = number4($rechargeLog->amount);
            $rechargeLog->request_back              = json_decode($rechargeLog->request_back);
            $rechargeLog->request_params            = json_decode($rechargeLog->request_params);
            $rechargeLog->request_time              = date("y-m-d H:i", $rechargeLog->request_time);
            $rechargeLog->request_params->amount    = number4(moneyUnitTransferIn($rechargeLog->request_params->amount));
            $rechargeLog->request_params->channel   = $cType[$rechargeLog->request_params->channel];
            $rechargeLog->request_params->bank_sign = $sBank[strtolower($rechargeLog->request_params->bank_sign)];
        }
        return Help::returnApiJson("恭喜, 获取详情数据成功！", 1, $rechargeLog);
    }

    // 获取 提现日志 列表
    public function rechargeLogList()
    {
        $adminUser = $this->partnerAdminUser;

        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;


        $data   = RechargeLog::getList($c);

        foreach ($data['data'] as $item) {
            $item->amount   = number4($item->amount);
        }

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
