<?php

namespace App\Http\Controllers\AdminApi\Finance;

use App\Lib\Help;
use App\Lib\Pay\Panda;
use App\Models\Player\Player;
use App\Models\Admin\SysBank;
use App\Models\Partner\Partner;
use App\Exports\RechargeExport;
use App\Models\Finance\Recharge;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\RechargeLog;
use App\Pay\Core\PayHandlerFactory;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Player\PlayerVipConfig;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerAdminGroup;
use App\Models\Finance\FinanceChannelType;
use App\Models\Finance\FinancePlatformChannel;
use App\Http\Requests\Frontend\Pay\RechargeRequest;
use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Models\Finance\FinancePlatformAccountChannel;


/**
 * version 1.0
 * 充值
 * Class ApiRechargeController
 * @package App\Http\Controllers\AdminApi\Finance
 */
class ApiRechargeController extends ApiBaseController
{
    /** ============================= 充值 - 记录 ============================= */
    /**
     * 充值列表
     * @return JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    // 列表
    public function rechargeList()
    {
        $c = request() -> all();
        // 如果是下载
        if (isset($c['is_export']) && $c['is_export'] == 1) {
            $date = date("Y-m-d");
            return Excel ::download(new RechargeExport($c), "recharge-{$date}.xlsx");
        }

        $data = Recharge ::getList($c, true);
        $totalRealAmount                = $totalRequestAmount = 0;

        foreach ($data['data'] as $item) {
            $item->pay_order_id         = empty($item->pay_order_id)?'':$item->pay_order_id;
            $item->request_time         = empty($item->request_time)?'':date("Y-m-d H:i:s", $item->request_time);
            $item->day_m                = empty($item->day_m)?'':date("Y-m-d H:i:s", strtotime($item->day_m));
            $item->channel_sign         = $item->channel;
            $item->channel              = implode('', FinancePlatformAccountChannel::getOption($item->channel, ''));
            $item -> amount             = number4($item -> amount);
            $item -> real_amount        = $item -> real_amount ? number4($item -> real_amount) : 0;
            $item -> partner_name       = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
            $item->channel_name         = $item -> channel;

            // 操作者
            $pAdminUser                 = PartnerAdminUser::getAdminUserOptions('');
            $item -> partner_admin_id   = isset($pAdminUser[$item -> partner_admin_id])?$pAdminUser[$item -> partner_admin_id]:'';
            $item->channel              = str_ireplace('H5','',$item->channel);
            $item->channel_name         = str_ireplace('H5','',$item->channel_name);
            $totalRealAmount           += $item -> real_amount;
            $totalRequestAmount        += $item -> amount;
        }

        $data['pageTotalAmount']        = number4(moneyUnitTransferIn($totalRequestAmount));
        $data['pageTotalRealAmount']    = number4(moneyUnitTransferIn($totalRealAmount));
        $data['partner_admin_user']     = PartnerAdminUser::getAdminUserOptions();
        $data['partner_options']        = Partner::getOptions();
        $data['channel_sign_options']   = Recharge ::getChannelSignList('');
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 列表
    public function rechargeExport()
    {
        $c = request()->all();
        $date = date("Y-m-d");
        return Excel::download(new RechargeExport($c), "recharge-{$date}.xlsx");
    }

    // 人工
    public function rechargeHand($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $item = Recharge::find($id);
        if (!$item){
            return Help ::returnApiJson("对不起, 不存在的ID号!", 0);
        }

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
        $action = request('action', 'process');
        if ($action == 'option') {
            $data['model'] = $item;
            $data['type_options'] = Recharge::$handType;
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
            $item -> day_m            = date("YmdHi");
            $item -> save();
        } else {
            $res = $item -> process($realAmount, $adminUser -> id, $reason);
            if ($res !== true) {
                return Help ::returnApiJson($res, 0);
            }
        }

        // 获取充值数据更新用户等级
        if (!isset($item) || empty($item) || !$item -> user_id){
            return Help ::returnApiJson("对不起, 用户信息不存在!", 0);
        }

        // 充值会员等级变化 获取总充值金额
        $vip_level = PlayerVipConfig::getUserLevel($item->partner_sign,$item -> user_id,$realAmount);
        Player::where('id',$item -> user_id)->update(['vip_level'=>$vip_level]);
        return Help ::returnApiJson("恭喜, 人工处理成功!", 1);
    }

    // 日志
    public function rechargeLog($order_id)
    {
        // 获取提现记录
        $recharge = Recharge ::find($order_id);

        if (!$recharge) {
            return Help ::returnApiJson("对不起, 充值记录不存在！", 0);
        }
        $recharge->day_m = 0;
        $pAdminUser                                     = PartnerAdminUser::getAdminUserOptions('');
        $recharge->request_time                         = empty($recharge->request_time)?'':date("Y-m-d H:i:s", $recharge->request_time);
        $recharge->callback_time                        = empty($recharge->callback_time)?'':date("Y-m-d H:i:s", $recharge->callback_time);
        $recharge->day_m                                = empty($recharge->day_m)?'':date("Y-m-d H:i:s", strtotime($recharge->day_m));
        $recharge->amount                               = number4($recharge->amount);
        $recharge->partner_admin_id                     = isset($pAdminUser[$recharge->partner_admin_id])?$pAdminUser[$recharge->partner_admin_id]:'';

        // 获取充值日志
        $rechargeLog = RechargeLog ::where('order_id', $recharge -> id) -> first();
        if ($rechargeLog) {
            $cType                                      = FinanceChannelType::getOptions();
            $sBank                                      = SysBank::getOption();
            $rechargeLog->amount                        = number4($rechargeLog->amount);
            $rechargeLog->request_back                  = json_decode($rechargeLog->request_back);
            $rechargeLog->request_params                = json_decode($rechargeLog->request_params);
            $rechargeLog->request_time                  = empty($rechargeLog->request_time)?'':date("Y-m-d H:i:s", $rechargeLog->request_time);
            if ($rechargeLog->request_params){
                $rechargeLog->request_params->amount    = number4(moneyUnitTransferIn($rechargeLog->request_params->amount));
                $rechargeLog->request_params->channel   = $cType[$rechargeLog->request_params->channel];
                $rechargeLog->request_params->bank_sign = $sBank[strtolower($rechargeLog->request_params->bank_sign)];
                $rechargeLog->request_params->time      = empty($rechargeLog->request_params->time)?'':date("Y-m-d H:i:s", $rechargeLog->request_params->time);
            }

        }
        $rechargeLog->order                             = $recharge;
        return Help ::returnApiJson("恭喜, 获取详情数据成功！", 1, $rechargeLog);
    }

    // 获取 提现日志 列表
    public function rechargeLogList()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $c = request()->all();
        $data = RechargeLog::getList($c);

        foreach ($data['data'] as $item) {
            $item->amount = number4($item->amount);
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
        }

        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions();
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }


    /**
     * 获取支付渠道
     * @return JsonResponse
     */
    public function getRechargeChannel()
    {
        //判断用户是否未登录！
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        //获取用户等级和用户
        $user_level = PartnerAdminGroup::select('level')->find($adminUser->group_id);
        if (!$user_level) {
            return Help::returnApiJson("对不起, 无效的用户id！", 0);
        }
        //根据登录用户获取partner_sign
        $partnerAdminUser = PartnerAdminUser::where('email', $adminUser->email)->first();
        if (!$partnerAdminUser) {
            return Help::returnApiJson('该用户还没有添加支付渠道信息!', 0, []);
        }
        $adminUser->partner_sign = $partnerAdminUser->partner_sign;
        $adminUser->user_level = $user_level->level;
        //获取列表信息
        $Channel = new FinancePlatformAccountChannel;
        $data = $Channel->getRechargeChannel($adminUser,FinancePlatformAccountChannel::DIRECTION_IN);
        $data = $data['data'];
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }
}
