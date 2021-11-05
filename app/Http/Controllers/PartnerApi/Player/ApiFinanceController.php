<?php

namespace App\Http\Controllers\PartnerApi\Player;

use App\Lib\Clog;
use App\Lib\Help;
use App\Lib\Pay\Pay;
use App\Models\Admin\SysBank;
use App\Models\Admin\SysCity;
use App\Models\Player\Player;
use App\Exports\RechargeExport;
use App\Exports\WithdrawExport;
use App\Models\Account\Account;
use App\Models\Finance\Recharge;
use App\Models\Finance\Withdraw;
use App\Models\Player\PlayerCard;
use App\Models\Finance\RechargeLog;
use App\Models\Finance\WithdrawLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Report\ReportStatUser;
use App\Models\Player\PlayerVipConfig;
use App\Models\Finance\FinancePlatform;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerConfigure;
use App\Models\Report\ReportStatUserDay;
use Illuminate\Support\Facades\Validator;
use App\Models\Finance\FinanceChannelType;
use App\Models\Account\AccountChangeReport;
use App\Models\Finance\FinancePlatformAccount;
use App\Models\Finance\FinancePlatformChannel;
use App\Models\Finance\FinancePlatformAccountChannel;
use App\Http\Controllers\PartnerApi\ApiBaseController;

class ApiFinanceController extends ApiBaseController
{
    /** ============================= 充值 - 记录 ============================= */
    // 列表
    public function rechargeList()
    {
        $c = request() -> all();
        $c['partner_sign']              = $this->partnerSign;
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
            $item->day_m                = empty($item->day_m)?'':date("Y-m-d H:i:s", $item->callback_time);
            $item->channel_sign         = $item->channel;
            $item->channel              = isset($item->channel)?FinancePlatformChannel::getOption($item->channel):'';
            $item->amount               = number4($item -> amount);
            $item->real_amount          = $item -> real_amount ? number4($item -> real_amount) : 0;
            $item->channel_name         = $item -> channel;
            // 操作者
            $pAdminUser                 = PartnerAdminUser::getAdminUserOptions('');
            $item->partner_admin_id     = isset($pAdminUser[$item -> partner_admin_id])?$pAdminUser[$item -> partner_admin_id]:'';
            $item->channel              = str_ireplace('H5','',$item->channel);
            $item->channel_name         = str_ireplace('H5','',$item->channel_name);
            $totalRealAmount           += $item -> real_amount;
            $totalRequestAmount        += $item -> amount;
        }

        $data['pageTotalAmount']        = number4(moneyUnitTransferIn($totalRequestAmount));
        $data['pageTotalRealAmount']    = number4(moneyUnitTransferIn($totalRealAmount));
        $data['channel_sign_options']   = Recharge ::getChannelSignList($this->partnerSign);

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }


    /**
     * 充值人工审核
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechargeHand($id)
    {
        $adminUser = $this -> partnerAdminUser;
        if ($id == 0){
            return Help ::returnApiJson("对不起, 不存在的ID号!", 0);
        }

        $item = Recharge ::find($id);
        if (!$item){
            return Help ::returnApiJson("对不起, 不存在的ID号!", 0);
        }

        // 操作者
        $pAdminUser                 = PartnerAdminUser::getAdminUserOptions('');
        $item -> partner_admin_id   = isset($pAdminUser[$item -> partner_admin_id])?$pAdminUser[$item -> partner_admin_id]:0;

        if (!$item) {
            return Help ::returnApiJson("对不起, 不存在的充值记录!", 0);
        }

        if ($item -> status != 1 && $item -> status != 0) {
            return Help ::returnApiJson("对不起，该订单已进行过人工处理!", 0);
        }

        // 获取配置
        $action = request('action', 'process');
        if ($action == 'option') {
            $data['model'] = $item;
            $data['type_options'] = Recharge ::$handType;
            return Help ::returnApiJson("恭喜, 获取数据成功！", 1, $item);
        }

        $type   = request("type");
        $amount = request("amount");
        $reason = request("reason",'');

        if (!$amount) {
            return Help ::returnApiJson("对不起, 无效的金额!", 0);
        }

        $realAmount     = moneyUnitTransferIn($amount);
        if ($realAmount > $item -> amount) {
            return Help ::returnApiJson("对不起, 资金不能超过充值资金!", 0);
        }

        // 人工失败
        if ($type == 1) {
            $item -> status            = Recharge::STATUS_MANUAL_FAIL;
            $item -> fail_reason       = $reason;
            $item -> real_amount       = number4(0);
            $item -> partner_admin_id  = $adminUser -> id;
            $item -> day_m             = date("YmdHi");
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
        $vip_level = PlayerVipConfig::getUserLevel($this->partnerSign,$item -> user_id,$realAmount);
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
        $c = request() -> all();
        $c['partner_sign'] = $this->partnerSign;

        $data = RechargeLog ::getList($c);

        foreach ($data['data'] as $item) {
            $item -> amount = number4($item -> amount);
        }

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }


    /** ============================= 人工提现 - 记录 ============================= */
    // 人工提现列表
    public function withdrawList()
    {
        $c = request() -> all();
        $c['partner_sign'] = $this->partnerSign;

        // 如果是下载
        if (isset($c['is_export']) && $c['is_export'] == 1) {
            $date = date("Y-m-d");
            return (new WithdrawExport($c)) -> download("withdraw-{$date}.csv", \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv']);
        }

        //判断输入金额是否在范围之内
        if (isset($c['min']) && $c['min'] && isset($c['max']) && $c['max']) {
            if ($c['max'] <= $c['min']) {
                return Help ::returnApiJson("对不起, 输入金额不在范围之内！", 0);
            }
        }

        //判断IP是否正确
        if (isset($c['client_ip']) && $c['client_ip']) {
            $validator = Validator ::make($c, ['client_ip' => 'ip']);
            if ($validator -> fails()) {
                return Help ::returnApiJson('对不起,请重新输入IP', 0);
            }
        }

        $data = Withdraw ::getList($c, true);

        $banks = config("web.banks");

        $totalRealAmount = $totalRequestAmount = 0;

        foreach ($data['data'] as $item) {
            $item -> bank           = isset($banks[$item -> bank_sign]) ? $banks[$item -> bank_sign] : $item -> bank_sign;
            $item -> amount         = number4(moneyUnitTransferIn($item -> amount));
            $item -> real_amount    = number4(moneyUnitTransferIn($item -> real_amount));
            $item -> content        = json_decode($item -> content, true);
            $item -> request_params = json_decode($item -> request_params, true);
            $item -> channel        = $item -> request_params['platform_sign']??'';
            $item -> need_check     = Withdraw ::needWithdrawCheck() && in_array($item -> status, [0, 1]) ? 1 : 0;
            $aChannel               = FinancePlatformAccountChannel::getOptions($this->partnerSign);
            $item -> channel        = $aChannel[$item -> channel]??$item -> channel;

            $item -> request_time   = empty($item -> request_time)?'':date("Y-m-d H:i:s", $item -> request_time);
            $item -> check_time     = empty($item -> check_time)  ?'':date("Y-m-d H:i:s", $item -> check_time);
            $item -> process_time   = empty($item -> process_time)?'':date("Y-m-d H:i:s", $item -> process_time);


            $item -> can_hand       = $item -> canHand();

            $totalRealAmount += $item -> real_amount;
            $totalRequestAmount += $item -> amount;
        }
        $data['pageTotalAmount']     = number4(moneyUnitTransferIn($totalRequestAmount));
        $data['pageTotalRealAmount'] = number4(moneyUnitTransferIn($totalRealAmount));

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 提现列表
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function viewWithdrawList($id)
    {
        $c                       = request() -> all();
        if ($id){
            $c['uid']                = $id;
        }
        $c['partner_sign']       = $this->partnerSign;
        $pAdminUserId            = PartnerAdminUser::getAdminUserIdOptions('');
        if (!empty($c['check_admin_id'])){
            $c['check_admin_id'] = isset($pAdminUserId[$c['check_admin_id']])?$pAdminUserId[$c['check_admin_id']]:'';
        }
        if (!empty($c['hand_admin_id'])){
            $c['hand_admin_id']  = isset($pAdminUserId[$c['hand_admin_id']])?$pAdminUserId[$c['hand_admin_id']]:'';
        }
        if (!empty($c['claim_admin_id'])){
            $c['claim_admin_id']  = isset($pAdminUserId[$c['claim_admin_id']])?$pAdminUserId[$c['claim_admin_id']]:'';
        }
        if (!empty($c['finance_admin_id'])){
            $c['finance_admin_id']  = isset($pAdminUserId[$c['finance_admin_id']])?$pAdminUserId[$c['finance_admin_id']]:'';
        }
        if (!empty($c['finance_check_admin_id'])){
            $c['finance_check_admin_id']  = isset($pAdminUserId[$c['finance_check_admin_id']])?$pAdminUserId[$c['finance_check_admin_id']]:'';
        }

        //判断ID是否存在
        if (isset($c['id']) && !empty($c['id'])) {
            $validator = Validator ::make($c, ['id' => 'required|numeric|exists:user_withdraw,id']);
            if ($validator -> fails()) {
                return Help ::returnApiJson('对不起,ID不存在', 0);
            }
        }

        // type值含义 2：人工提现表
        $action = request('type');
        if ($action == 2){
            $c['hand_status'] = [5,7,8,-5];
            // 如果是下载
            if (isset($c['is_export']) && $c['is_export'] == 1) {
                $date = date("Y-m-d");
                return Excel ::download(new WithdrawExport($c), "withdraw-{$date}.xlsx");
            }
        }elseif ($action == 1){
            // 风控状态
            $c['hand_wind_status'] = [0,1,3,4,5,6,-2,-3,-4,-5,-6,9];
        }elseif ($action == 3) {
            // 财务状态
            $c['hand_finance_status'] = [2,3,4,5,6,7,8,9,-2,-3,-4,-5,-6];
        }

        // 如果是下载
        if (isset($c['is_export']) && $c['is_export'] == 1) {
            $date = date("Y-m-d");
            return Excel ::download(new WithdrawExport($c), "withdraw-{$date}.xlsx");
        }

        $data                                   = Withdraw ::getList($c, true);
        $banks                                  = config("web.banks");
        $totalRealAmount                        = $totalRequestAmount = 0;
        $pAdminUser                             = PartnerAdminUser::getAdminUserOptions('');
        foreach ($data['data'] as $item) {
            $item -> bank                       = isset($banks[$item -> bank_sign]) ? $banks[$item -> bank_sign] : $item -> bank_sign;
            $item -> amount                     = number4(moneyUnitTransferIn($item -> amount));
            $item -> real_amount                = empty($item -> real_amount)?'':number4(moneyUnitTransferIn($item -> real_amount));
            $item -> content                    = json_decode($item -> content, true);
            $item -> request_params             = json_decode($item -> request_params, true);
            $item -> channel                    = $item->request_params['platform_sign']??'';
            $item -> need_check                 = Withdraw ::needWithdrawCheck() && in_array($item -> status, [0, 1]) ? 1 : 0;
            $aChannel                           = FinancePlatformAccountChannel::getOptions($this->partnerSign);
            $item -> channel_sign               = $item -> channel??'';
            $item -> channel                    = $aChannel[$item -> channel]??$item -> channel;
            $item -> request_time               = empty($item -> request_time)        ?'':date("Y-m-d H:i:s", $item -> request_time);
            $item -> check_time                 = empty($item -> check_time)          ?'':date("Y-m-d H:i:s", $item   -> check_time);
            $item -> claim_time                 = empty($item -> claim_time)          ?'':date("Y-m-d H:i:s", $item -> claim_time);
            $item -> process_time               = empty($item -> process_time)        ?'':date("Y-m-d H:i:s", $item -> process_time);
            $item -> wind_process_time          = empty($item -> wind_process_time)   ?'':date("Y-m-d H:i:s", $item -> wind_process_time);
            $item -> finance_process_time       = empty($item -> finance_process_time)?'':date("Y-m-d H:i:s", $item -> finance_process_time);
            $item -> hand_process_time          = empty($item -> hand_process_time)   ?'':date("Y-m-d H:i:s", $item -> hand_process_time);
            $item -> hand_time                  = empty($item -> hand_time)           ?'':date("Y-m-d H:i:s", $item -> hand_time);
            $item -> day_m                      = empty($item -> day_m)               ?'':date("Y-m-d H:i:s", $item -> day_m);
            $item -> claim_admin_id             = isset($pAdminUser[$item -> claim_admin_id])?$pAdminUser[$item -> claim_admin_id]:'';
            $item -> check_admin_id             = isset($pAdminUser[$item -> check_admin_id])?$pAdminUser[$item -> check_admin_id]:'';
            $item -> hand_admin_id              = isset($pAdminUser[$item -> hand_admin_id])?$pAdminUser[$item -> hand_admin_id]:'';
            $item -> hand_check_admin_id        = isset($pAdminUser[$item -> hand_check_admin_id])?$pAdminUser[$item -> hand_check_admin_id]:'';

            // 财务显示部分
            $item -> finance_admin_id           = isset($pAdminUser[$item -> finance_admin_id])?$pAdminUser[$item -> finance_admin_id]:'';
            $item -> finance_check_admin_id     = isset($pAdminUser[$item -> finance_check_admin_id])?$pAdminUser[$item -> finance_check_admin_id]:'';
            $item -> finance_time               = empty($item -> finance_time)?'':date("Y-m-d H:i:s", $item -> finance_time);

            $item -> can_hand                    = $item -> canHand();
            $totalRealAmount                    += empty($item -> real_amount)?0:$item -> real_amount;
            $totalRequestAmount                 += empty($item -> amount)?0:$item -> amount;

            $str_key = '************';
            // 详情
            if ($id > 0){
                $c['id']                         = $id;
                $order                           = Withdraw ::getList($c);
                if (!$order || !count($order['data'])) {
                    return Help ::returnApiJson("对不起, 不存在的订单！", 0);
                }
                $order                           = $order['data'][0];
                unset($order->top_id, $order->parent_id,
                    $order->nickname, $order->bank_sign,
                    $order->request_time,$order->from_device,
                    $order->day_m,$order->created_at,
                    $order->updated_at,$order->bank_name,
                    $order->city_id,$order->province_id,
                    $order->frozen_type,$order->mark, $order->id,
                    $order->total_recharge,$order->total_withdraw,
                );

                $sCity                           = SysCity::getOption();
                $order -> card_city              = $sCity[$order->city_id]??'';                                                                         // 开卡城市
                $order -> region_name            = $sCity[$order->province_id]??'';// 省份
                $order -> request_params         = json_decode($order -> request_params)??'';                                                           // 请求参数
                $order -> amount                 = number4(moneyUnitTransferIn($order -> amount));                                                      // 金额
                $order -> real_amount            = number4(moneyUnitTransferIn($order -> real_amount));                                                 // 确认金额
                $order -> request_time           = empty($order -> request_time)               ?'':date("Y-m-d H:i:s", $order -> request_time);                     // 审核时间
                $order -> claim_time             = empty($order -> claim_time)                 ?'':date("Y-m-d H:i:s", $order -> claim_time);                     // 审核时间
                $order -> process_time           = empty($order -> process_time)               ?'':date("Y-m-d H:i:s", $order -> process_time);                     // 审核时间
                $order -> check_time             = empty($order -> check_time)                 ?'':date("Y-m-d H:i:s", $order -> check_time);                     // 审核时间
                $order -> wind_process_time      = empty($order -> wind_process_time)          ?'':date("Y-m-d H:i:s", $order -> wind_process_time);       // 审核时间
                $order -> finance_process_time   = empty($order -> finance_process_time)       ?'':date("Y-m-d H:i:s", $order -> finance_process_time); // 审核时间
                $order -> claim_admin_id         = isset($pAdminUser[$order -> claim_admin_id])?$pAdminUser[$order -> claim_admin_id]:'';                                                     // 操作者
                $order -> check_admin_id         = isset($pAdminUser[$order -> check_admin_id])?$pAdminUser[$order -> check_admin_id]:'';                                                    // 操作者
                $order -> hand_admin_id          = isset($pAdminUser[$order -> hand_admin_id]) ?$pAdminUser[$order -> hand_admin_id]:'';                                                     // 操作者
                // 人工处理领取人才能看到卡信息
                if($order->hand_check_admin_id !== $this->partnerAdminUser->id || in_array($order->status,[Withdraw::STATUS_HAND_FAIL,Withdraw::STATUS_HAND_SUCCESS,Withdraw::STATUS_HAND_WAIT_SUCCESS])){
                    $order -> owner_name                          = $str_key;
                    $order -> card_number                         = $str_key;
                    foreach ($data['data'] as $items) {
                        $items -> owner_name                      = $str_key;
                        $items -> card_number                     = $str_key;
                        if (isset($items -> request_params) && !empty($items -> request_params)){
                            $items -> request_params              = json_decode(json_encode($items -> request_params));
                            $items->request_params->card_username = $str_key;
                            $items->request_params->card_number   = $str_key;
                        }
                    }
                }

                // 提现渠道：channel_sign
                $order -> channel_sign           = '';
                $order -> fee_amount             = 0;
                if (isset($order -> request_params) && !empty($order -> request_params)){
                    $fee_amount                  = FinancePlatformAccountChannel::getFeeAmount($this->partnerSign,$order->request_params->platform_sign);
                    $order -> fee_amount         = $fee_amount;
                    $order -> channel_sign       = $item -> channel;
                    $order -> card_city          = $sCity[$order->request_params->card_city]??'';   // 开卡城市
                    $order -> region_name        = $sCity[$order->request_params->card_province]??'';// 省份
                    // 人工处理领取人才能看到卡信息
                    if($order->hand_check_admin_id !== $this->partnerAdminUser->id || $order->status !== Withdraw::STATUS_HAND_FETCH_SUCCESS){
                        $order->request_params->owner_name         = $str_key;
                        $order->request_params->card_username      = $str_key;
                        $order->request_params->card_number        = $str_key;
                    }

                }

                $data['order']                   = $order;

                // 当天充值/提现金额
                $total_today_recharge            = Recharge ::getTotalTodayRecharge($order -> user_id);
                $total_today_withdraw            = Withdraw ::getTotalTodayWithdraw($order -> user_id);

                // 账户
                $account                         = Account ::findFormatAccountByUserId($order -> user_id);

                // 可提现余额
                if ($total_today_recharge == 0){
                    $withdrawable_balance = 0;
                }else{
                    $withdrawable_balance        = $total_today_recharge/5-$total_today_withdraw;
                }
                $data['account']                 = $account;
                $account -> total_today_recharge = $total_today_recharge;
                $account -> total_today_withdraw = $total_today_withdraw;
                $account -> total_today_cost     = moneyUnitTransferOut(ReportStatUserDay::getTotalTodayCost($order -> user_id));
                $account -> total_cost           = moneyUnitTransferOut(ReportStatUserDay::getTotalCost($order -> user_id));

                $data['account']->withdrawable_balance   = $withdrawable_balance; // 可用余额
                $data['withdraw_need_bet_times'] = partnerConfigure($this->partnerSign,"finance_withdraw_bet_times");

                // 统计
                $reportParams['start_time']      = request('start_time');
                $reportParams['end_time']        = request('end_time');
                $reportParams['partner_sign']    = $this->partnerSign;

                $bet_times                       = ReportStatUser ::findFormatDataByUserId($order -> user_id);
                $stat                            = AccountChangeReport ::getProfitList($order -> user_id,$reportParams);
                $stat->bet_times                 = is_null($bet_times)?$bet_times->bet_times:'';
                $data['stat']                    = $stat;

                // 用户
                $player                          = Player ::findFormatUserById($order -> user_id);
                $data['user']                    = $player;

                // 支付账户
                $aChannel                      = FinancePlatformAccountChannel ::select('id','platform_channel_id','platform_child_sign')
                    ->where('partner_sign',$this->partnerSign)
                    ->where('type_sign','withdraw')
                    ->get();
                // 追加人工出款渠道信息
                $handWithdraw = [
                    'id'                  => '000',
                    'platform_child_sign' => '人工出款',
                    'platform_channel_id' => '人工出款',
                ];
                $handWithdraw = [$handWithdraw];
                if (count($aChannel)){
                    $aChannel  = array_merge(end($aChannel),$handWithdraw);
                }else{
                    $aChannel  = $handWithdraw;
                }

                $data['account_channel']       = $aChannel;
                $data['data']                  = '';
            }
            if (in_array($item->status,[Withdraw::STATUS_HAND_FAIL,Withdraw::STATUS_HAND_SUCCESS,Withdraw::STATUS_HAND_WAIT_SUCCESS])){
                // 人工处理领取人才能看到卡信息
                if(empty($item->hand_check_admin_id) || !in_array($item->status, [Withdraw::STATUS_HAND_FETCH_SUCCESS])){
                    $item->owner_name            = $str_key;
                    $item->card_branch           = $str_key;
                    $item->card_username         = $str_key;
                    $item->card_number           = $str_key;
                }
                if (isset($item -> request_params) && !empty($item -> request_params)) {
                    $item -> request_params = json_decode(json_encode($item -> request_params));
                    // 人工处理领取人才能看到卡信息
                    if ($item->status !== Withdraw::STATUS_HAND_FETCH_SUCCESS || $item->hand_check_admin_id !== $this->partnerAdminUser->id){
                        $item -> request_params -> owner_name    = $str_key;
                        $item -> request_params -> card_username = $str_key;
                        $item -> request_params -> card_number   = $str_key;
                    }
                }
            }
        }
        $data['pageTotalAmount']                 = number4(moneyUnitTransferIn($totalRequestAmount));
        $data['pageTotalRealAmount']             = number4(moneyUnitTransferIn($totalRealAmount));

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    // 手动成功/失败
    public function withdrawHand($id)
    {
        $adminUser = $this -> partnerAdminUser;

        // 获取提现记录
        $info = Withdraw ::find($id);
        if (!$info) {
            return Help ::returnApiJson("对不起, 提现记录不存在！", 0);
        }

        // 本商户只能处理自己的订单
        if ($info->partner_sign != $this->partnerSign) {
            return Help ::returnApiJson("对不起, 商户不合法！", 0);
        }

        // 如果状态不对
        if (in_array($info->status, [Withdraw::STATUS_HAND_SUCCESS,Withdraw::STATUS_HAND_FAIL])) {
            return Help ::returnApiJson("对不起, 订单已经被人工处理过！", 0);
        }

        // 人工领取人,领取时间,认领状态
        $info -> hand_check_admin_id = $adminUser->id;                      // 人工领取人
        $info -> hand_time           = time();                              // 人工领取时间
        $info -> status              = Withdraw::STATUS_HAND_FETCH_SUCCESS; // 人工认领状态
        $info -> save();

        // 获取配置
        $action = request('action', 'process');
        if ($action == 'option') {
            $info -> amount       = number4($info -> amount);
            $data['model']        = $info;
            $data['type_options'] = Withdraw ::$status;
            return Help ::returnApiJson("恭喜, 获取数据成功！", 1, $data);
        }

        $type   = request("type");
        $amount = request("amount");
        $reason = request("reason",'');
        if ($amount < 0 || $amount != intval($amount)) {
            return Help ::returnApiJson("对不起, 无效的金额!", 0);
        }

        if ($amount > $info -> amount) {
            return Help ::returnApiJson("对不起, 资金不能超过提现资金!", 0);
        }

        // 人工失败 -5：失败  5成功
        if ($type == Withdraw::STATUS_HAND_FAIL) {
            if (!isset($reason)){
                return Help ::returnApiJson("对不起, 请填写人工审核失败原因!!!", 0);
            }
            $res = $info -> processFail($reason, $adminUser);
            if ($res !== true) {
                return Help ::returnApiJson($res, 0);
            }
        } else {
            $res = $info -> process($amount, $adminUser, $reason);
            if ($res !== true) {
                return Help ::returnApiJson($res, 0);
            }
        }
        return Help ::returnApiJson("恭喜,人工处理成功!", 1);
    }

    /**
     * 提现日志
     * @param $order_id
     * @return mixed
     */
    public function withdrawLog($order_id)
    {

        // 获取提现记录
        $withdraw = Withdraw ::find($order_id);
        if (!$withdraw) {
            return Help ::returnApiJson("对不起, 提现记录不存在！", 0);
        }

        // 获取提现日志
        $withdrawLog = WithdrawLog ::where('order_id', $withdraw -> id) -> first();
        if ($withdrawLog) {
            $sBank                                       = SysBank::getOption();
            $sCity                                       = SysCity::getOption();
            $withdrawLog -> amount                       = number4(moneyUnitTransferIn($withdrawLog -> amount));
            $withdrawLog -> request_params               = json_decode($withdrawLog -> request_params);
            $withdrawLog -> request_back                 = json_decode($withdrawLog -> request_back);
            $withdrawLog ->request_params->bank_sign     = $sBank[strtolower($withdrawLog->request_params->bank_sign)];
            $withdrawLog ->request_params->card_city     = $sCity[$withdrawLog->request_params->card_city]??$withdrawLog->request_params->card_city;
            $withdrawLog ->request_params->card_province = $sCity[$withdrawLog->request_params->card_province]??$withdrawLog->request_params->card_province;
        }
        return Help ::returnApiJson('获取数据成功!', 1, $withdrawLog);
    }

    /**
     * 审核处理
     * 1. 领取
     * 2. 处理
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Exception
     */
    public function withdrawCheckProcess($id)
    {
        $adminUser = $this -> partnerAdminUser;
        $c['id']   = $id;
        $order     = Withdraw ::getList($c);
        if (!$order || !count($order['data'])) {
            return Help ::returnApiJson("对不起, 不存在的订单！", 0);
        }
        $order     = $order['data'][0];
        unset($order->top_id, $order->parent_id,
            $order->nickname, $order->bank_sign,
            $order->request_time,$order->from_device,
            $order->day_m,$order->created_at,
            $order->updated_at,$order->bank_name,
            $order->city_id,$order->province_id,
            $order->frozen_type,$order->mark,
            $order->total_recharge,$order->total_withdraw,
        );
        // 如果状态不对
//        if (!in_array($order -> status, [0, 1, 6, -6])) {
//            return Help ::returnApiJson("对不起, 订单已经被处理过！", 0);
//        }
        $action = request("action", 'process');

        $checkStatus = Withdraw ::$checkStatus;
        if ('fetch' == $action) {
            if ($order -> status  == 0) {
                $order -> status           = Withdraw::STATUS_FETCH_SUCCESS;       // 风控领取状态
                $order -> claim_time       = time();                               // 风控领取时间
                $order -> claim_admin_id   = $adminUser->id;                       // 风控领取人
                $order -> save();
            }

            // 财务领取人,领取时间,处理人
            if (array_key_exists($order -> status, Withdraw ::$checkWindStatus)){
                $order -> status           = Withdraw::STATUS_CHECK_FETCH_SUCCESS; // 财务领取状态
                $order -> finance_time     = time();                               // 财务领取时间
                $order -> finance_admin_id = $adminUser->id;                       // 财务领取人
                $order -> save();
            }

            $pAdminUser                    = PartnerAdminUser::getAdminUserOptions('');
            $order -> status_desc          = Withdraw ::getStatusDesc($order -> status);
            $order -> request_params       = json_decode($order -> request_params);
            $order -> amount               = number4(moneyUnitTransferIn($order -> amount));
            $sCity                         = SysCity::getOption();
            $order -> card_city            = $sCity[$order->city_id]??'';
            $order -> region_name          = $sCity[$order->province_id]??'';
            $order -> day_m                = empty($order -> day_m)                      ?'':date("Y-m-d H:i:s", $order -> day_m);
            $order -> claim_time           = empty($order -> claim_time)                 ?'':date("Y-m-d H:i:s", $order -> claim_time);
            $order -> wind_process_time    = empty($order -> wind_process_time)          ?'':date("Y-m-d H:i:s", $order -> wind_process_time);
            $order -> finance_process_time = empty($order -> finance_process_time)       ?'':date("Y-m-d H:i:s", $order -> finance_process_time);
            $order -> process_time         = empty($order -> process_time)               ?'':date("Y-m-d H:i:s", $order -> process_time);
            $order -> check_time           = empty($order -> check_time)                 ?'':date("Y-m-d H:i:s", $order -> check_time);
            $order -> request_time         = empty($order -> request_time)               ?'':date("Y-m-d H:i:s", $order -> request_time);
            $order -> claim_admin_id       = isset($pAdminUser[$order -> claim_admin_id])?$pAdminUser[$order -> claim_admin_id]:'';
            $order -> check_admin_id       = isset($pAdminUser[$order -> check_admin_id])?$pAdminUser[$order -> check_admin_id]:'';
            $order -> hand_admin_id        = isset($pAdminUser[$order -> hand_admin_id]) ?$pAdminUser[$order -> hand_admin_id]:'';

            // 财务显示部分
            $order -> finance_admin_id       = isset($pAdminUser[$order -> finance_admin_id])?$pAdminUser[$order -> finance_admin_id]:'';
            $order -> finance_check_admin_id = isset($pAdminUser[$order -> finance_check_admin_id])?$pAdminUser[$order -> finance_check_admin_id]:'';
            $order -> finance_time           = empty($order -> finance_time)?'':date("Y-m-d H:i:s", $order -> finance_time);

            // 提现渠道：channel_sign
            $order -> channel_sign = '';
            $order -> fee_amount   = 0;
            if (isset($order -> request_params) && !empty($order -> request_params)){
                $fee_amount = FinancePlatformAccountChannel::getFeeAmount($this->partnerSign,$order->request_params->platform_sign);
                $order -> fee_amount   = $fee_amount;
                $order -> channel_sign = $order->request_params->platform_sign;
                $order -> card_city          = $sCity[$order->request_params->card_city]??'';   // 开卡城市
                $order -> region_name        = $sCity[$order->request_params->card_province]??'';// 省份
            }
            $data['order']                 = $order;

            // 总提现和总充值
            $total_recharge                = Recharge ::getTotalRecharge($order -> user_id);
            $total_withdraw                = Withdraw ::getTotalWithdraw($order -> user_id);

            // 当天充值/提现金额
            $total_today_recharge          = Recharge ::getTotalTodayRecharge($order -> user_id);
            $total_today_withdraw          = Withdraw ::getTotalTodayWithdraw($order -> user_id);
            $data['order']->total_recharge = $total_recharge;
            $data['order']->total_withdraw = $total_withdraw;
            // 可提现余额
            if ($total_today_recharge == 0){
                $withdrawable_balance = 0;
            }else{
                $withdrawable_balance = $total_today_recharge/5-$total_today_withdraw;
            }

            // 账户
            $account                       = Account ::findFormatAccountByUserId($order -> user_id);
            $bet_times                     = ReportStatUser ::findFormatDataByUserId($order -> user_id);
            $account->total_recharge       = $total_recharge;
            $account->total_withdraw       = $total_withdraw;
            $account->total_today_recharge = $total_today_recharge;
            $account->total_today_withdraw = $total_today_withdraw;
            $account->total_today_cost     = moneyUnitTransferOut(ReportStatUserDay::getTotalTodayCost($order -> user_id));
            $account->total_cost           = moneyUnitTransferOut(ReportStatUserDay::getTotalCost($order -> user_id));
            $account->bet_times            = is_null($bet_times)?$bet_times->bet_times:'';
            $account->withdrawable_balance = $withdrawable_balance; // 可用余额
            $data['account']               = $account;

            $data['withdraw_need_bet_times'] = partnerConfigure($adminUser->partner_sign,"finance_withdraw_bet_times");
            // 统计
            $reportParams['start_time']    = request('start_time');
            $reportParams['end_time']      = request('end_time');
            $reportParams['partner_sign']  = $this->partnerSign;
            $stat                          = AccountChangeReport ::getProfitList($order -> user_id,$reportParams);
            $data['stat']                  = $stat;

            // 用户
            $player                        = Player ::findFormatUserById($order -> user_id);
            $data['user']                  = $player;

            // 支付账户
            $aChannel                      = FinancePlatformAccountChannel ::select('id','platform_channel_id','platform_child_sign')
                ->where('partner_sign',$this->partnerSign)
                ->where('type_sign','withdraw')
                ->get();

            // 追加人工出款渠道信息
            $handWithdraw = [
                'id'                  => '000',
                'platform_child_sign' => '人工出款',
                'platform_channel_id' => '人工出款',
            ];

            $handWithdraw = [$handWithdraw];
            if (count($aChannel)){
                $aChannel  = array_merge(end($aChannel),$handWithdraw);
            }else{
                $aChannel  = $handWithdraw;
            }

            $data['account_channel']       = $aChannel;
            if ($order -> status  == 1) {
                return Help ::returnApiJson("对不起, 订单已经被领取过！", 1,$data);
            }
            return Help ::returnApiJson("恭喜, 领取成功", 1, $data);
        }

        $checkWindStatus = Withdraw ::$checkWindStatus;
        $status = request("status");
        $reason = request("reason",'');

        // 风控审核 状态：6：成功  -6：失败
        if (array_key_exists($status, $checkWindStatus)) {
            // 必须是领取人操作
            if(!empty($order -> claim_admin_id) && $order -> claim_admin_id != $adminUser->id){
                return Help ::returnApiJson("对不起, 该订单已经被认领,你无权处理！", 0);
            }
            if (in_array($order -> status, [Withdraw::STATUS_WIND_CHECK_FAIL,Withdraw::STATUS_WIND_CHECK_SUCCESS])) {
                return Help ::returnApiJson("对不起, 订单已经被处理过!", 0);
            }
            if ($status == Withdraw::STATUS_WIND_CHECK_FAIL && !isset($reason)){
                return Help ::returnApiJson("对不起, withdrawHand!!!", 0);
            }
            $order -> wind_process_time   = time();         // 风控审核时间
            $order -> check_admin_id      = $adminUser->id; // 风控审核人
            $order -> description         = $reason;        // 审核描述
            // 时间记录线 1：风控审核
            $withdrawLog = WithdrawLog::where('order_id',$order->id)->first();
            if (isset($withdrawLog) && !empty($withdrawLog)){
                $checkWindProcess = [
                    'desc'          => '风控审核',
                    'status'        => $order->status,
                    'check_time'    => $order->check_time,
                    'check_admin_id'=> $order->check_admin_id,
                ];
                $checkWindProcess     = json_encode($checkWindProcess);
                $content              = array('params'=>$withdrawLog->content,'request_params'=>$order->request_params??'','check_wind_process'=>$checkWindProcess);
                $content              = json_encode($content);
                $withdrawLog->content = $content;
                $withdrawLog->save();
            }

            if ($status == Withdraw::STATUS_WIND_CHECK_SUCCESS){
                $order -> status      = Withdraw::STATUS_WIND_CHECK_SUCCESS;
                $order -> save();
                return Help ::returnApiJson("恭喜，审核成功!", 1);
            }else{
                $order -> status      = Withdraw::STATUS_WIND_CHECK_FAIL;
                $order -> save();
                return Help ::returnApiJson("对不起，审核失败!", 1);
            }
        }

        // 财务审核 状态：2：成功  -2：失败  人工审核等待
        if (!array_key_exists($status, $checkStatus)) {
            return Help ::returnApiJson("对不起, 状态无效!", 0);
        }else{
            if ($status == Withdraw::STATUS_CHECK_FAIL){
                $order -> status = Withdraw::STATUS_CHECK_FAIL;
            }

            if ($status == Withdraw::STATUS_HAND_WAIT_SUCCESS){
                $order -> status = Withdraw::STATUS_HAND_WAIT_SUCCESS;
            }

            $order -> process_time        = time();         // 处理时间
            $order -> check_admin_id      = $adminUser->id; // 审核人
            $order -> save();
        }

        if ($status == 2 && request('platform_channel_id') == '人工出款'){
            $status = Withdraw::STATUS_HAND_WAIT_SUCCESS;
        }

        // 2:审核通过/-2未通过/0待定处理
        if($status == Withdraw::STATUS_CHECK_SUCCESS){
            // 必须是领取人操作
            if($order -> check_admin_id != $adminUser->id){
                return Help ::returnApiJson("对不起, 订单已经被领取,你无权处理！", 0);
            }
            if ($status == -2 && !isset($reason)){
                return Help ::returnApiJson("对不起, 请填写审核失败原因!!!", 0);
            }
            // 接收参数
            $platformSign       = request("platform_sign",'fmis');  // 平台
            $platformChannelId  = request("platform_channel_id");          // 通道ID

            // 处理
            $user  = Player ::find($order                -> user_id);
            $card  = PlayerCard ::find($order            -> card_id);
            $pCard = PlayerCard::where('id',$card['id']) -> first();
            if (!$pCard){
                return "对不起,　卡号不存在!";
            }
            Clog ::getHandleLog("记录handle日志", [$order->toArray(),$platformSign]);
            
            $pay   = new Pay();
            $pay   = $pay -> getHandle($platformSign);

            $pay          -> setWithdrawOrder($order);
            $pay          -> setWithdrawUser($user);
            $pay          -> setWithdrawCard($pCard);

            $pAccount = FinancePlatformAccount::where('partner_sign',$user->partner_sign)->first();
            if (!$pAccount->merchant_secret){
                return "对不起,　获取商户密匙失败!";
            }

            $pay         -> constant['key']            = $pAccount->merchant_secret;
            $financePlatform                           = FinancePlatform::where('platform_sign',$pAccount->platform_sign)->first();
            if (empty($financePlatform)){
                return '对不起，厂商不存在';
            }
            $pay         -> constant['withdrawal_url'] = $financePlatform->platform_url.config('finance.main.fmis.payment');
            $pay         -> constant['merchantId']     = $pAccount->merchant_code;


            $fmis_bank         = config('finance.main.banks');
            $platform_sign     = $pAccount->platform_sign;
            $bank_sign         = isset($fmis_bank[$platform_sign])?$fmis_bank[$platform_sign]:$card -> bank_sign;
            $card -> bank_sign = isset($bank_sign[strtoupper($card -> bank_sign)])?$bank_sign[strtoupper($card -> bank_sign)]:$card -> bank_sign;

            $r = $pay -> withdrawal($card -> bank_sign, $order -> order_id, $order -> amount, $card -> card_number, $card -> owner_name, $platformChannelId);

            $order -> finance_process_time   = time();            // 财务审核时间
            $order -> check_admin_id         = $adminUser -> id;
            $order -> finance_check_admin_id = $adminUser -> id;  // 财务审核人

            // 判断接收订单状态-进行保存数据
            if ($r['status'] === true) {
                $order -> request_time = time();
                $order -> status       = Withdraw::STATUS_SEND_SUCCESS;
                $order -> description  = $reason;
                $order -> save();
            } else {
                $order -> request_time = time();
                $order -> status       = Withdraw::STATUS_SEND_FAIL;
                $order -> desc         = $r['msg'];
                $order -> save();
            }
        }
        // 人工审核 状态：7
        if ($status == Withdraw::STATUS_HAND_WAIT_SUCCESS){
            $order -> status = $status;
            $order -> save();
        }

        // 人工审核 状态：7
        if ($status == Withdraw::STATUS_HAND_FETCH_SUCCESS){
            return Help ::returnApiJson("恭喜, 处理成功", 1);
        }
        return Help ::returnApiJson("恭喜, 处理成功", 1);
    }

    /**
     * 获取提现日志列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawLogList()
    {

        $c = request() -> all();
        $c['partner_sign'] = $this->partnerSign;

        $data = WithdrawLog ::getList($c);

        foreach ($data['data'] as $item) {
            $item -> amount       = number4(moneyUnitTransferIn($item -> amount));
            $item -> request_time = $item -> created_at -> format("Y-m-d H:i:s");
        }
        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 生成提现订单
     * @return mixed
     */
    public function withdrawGenOrder()
    {
        $adminUser = $this -> partnerAdminUser;

        // 用户
        $userId = trim(request("user_id"));
        $user = Player ::find($userId);
        if (!$user) {
            return Help ::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 流水倍数获取
        $betTimes =partnerConfigure($adminUser->partner_sign,'finance_withdraw_bet_times');

        // 获取玩家的充值金额
        $recharge = Recharge ::getTotalRecharge($userId);

        // 获取玩家的流水金额
        $account    = ReportStatUserDay ::getTotalCancel($user->username);
        //$account = Withdraw ::findByUserId($userId);

        // 检测流水是否达到标准
        if ($betTimes * $recharge < $account) {
            return Help ::returnApiJson('对不起, 流水不足', 0);
        }

        // 不绑定手机号不能提款
        $mobileSwitch = partnerConfigure($adminUser->partner_sign,'finance_mobile_switch', 0);
        if ($mobileSwitch && !$user -> mobile) {
            return Help ::returnApiJson('对不起, 请先绑定手机号', 0);
        }

        // 密码检测
        $fundPassword = request('fund_password', '');
        if (!$fundPassword || !Hash ::check($fundPassword, $adminUser -> fund_password)) {
            return Help ::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        // 检查用户是否绑定银行卡
        $bindCards = PlayerCard ::getCards($user -> id);
        if (count($bindCards) == 0) {
            return Help ::returnApiJson('对不起, 用户没有绑定银行卡!', 0, ['reason_code' => 910]);
        }

        // 获取可用余额 余额为０　还能提现么？
        $account = $user -> account();
        $userBalance = $account -> balance;
        if ($userBalance <= 0) {
            return Help ::returnApiJson('对不起, 用户资金不足!', 0);
        }

        // 提现金额
        $amount = request('amount', 0);
        if (!$amount || $amount != intval($amount)) {
            return Help ::returnApiJson('对不起, 无效的资金输入!', 0);
        }

        // 余额是否足够
        if ($userBalance < $amount * 10000) {
            return Help ::returnApiJson('对不起, 用户资金不足!', 0);
        }

        $bindCards = array_values($bindCards);
        $card = $bindCards[0];
        $source = request('from', "iphone");

        // 生成审核订单
        $res = $user -> requestWithdraw($amount, $card, $source);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        return Help ::returnApiJson('恭喜, 发起提现成功!', 1);
    }

	/**
	 * 设置提现需要的流水倍数
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function withDrawBetTimes()
    {
        $partnerConfigure = PartnerConfigure::where(['partner_sign'=>$this->partner_sign,'sign'=>'finance_withdraw_bet_times'])->first();
        $c = request() -> all();

        $partnerConfigure -> value = $c['value'];
        $res = $partnerConfigure -> save();

        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        return Help ::returnApiJson('恭喜, 流水倍数设置成功!', 1);

    }
}
