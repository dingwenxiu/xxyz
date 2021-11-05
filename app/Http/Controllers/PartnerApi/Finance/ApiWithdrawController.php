<?php

namespace App\Http\Controllers\PartnerApi\Finance;

use App\Lib\Help;
use App\Lib\Pay\Pay;
use App\Models\Player\Player;
use App\Models\Admin\SysBank;
use App\Models\Admin\SysCity;
use App\Exports\WithdrawExport;
use App\Models\Account\Account;
use App\Models\Finance\Recharge;
use App\Models\Finance\Withdraw;
use App\Models\Player\PlayerCard;
use App\Models\Finance\WithdrawLog;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Report\ReportStatUser;
use App\Models\Finance\FinancePlatform;
use App\Models\Finance\FinancePlatformAccount;
use App\Models\Finance\FinancePlatformAccountChannel;
use App\Http\Controllers\PartnerApi\ApiBaseController;

/**
 * version 1.0
 * 充值
 * Class ApiRechargeController
 * @package App\Http\Controllers\AdminApi\Finance
 */
class ApiWithdrawController extends ApiBaseController
{
    /** ============================= 提现 - 记录 ============================= */
    // 列表
    public function withdrawList()
    {
        $c = request() -> all();

        // 如果是下载
        if (isset($c['is_export']) && $c['is_export'] == 1) {
            $date = date("Y-m-d");
            return (new WithdrawExport($c)) -> download("withdraw-{$date}.csv", \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv']);
        }

        $c['partner_sign'] = $this->partnerSign;

        $data = Withdraw ::getList($c, true);

        $banks = config("web.banks");

        $totalRealAmount = $totalRequestAmount = 0;

        foreach ($data['data'] as $item) {
            $item -> bank           = isset($banks[$item -> bank_sign]) ? $banks[$item -> bank_sign] : $item -> bank_sign;
            $item -> amount         = number4(moneyUnitTransferIn($item -> amount));
            $item -> real_amount    = number4(moneyUnitTransferIn($item -> real_amount));
            $item -> content        = json_decode($item -> content, true);
            $item -> request_params = json_decode($item -> request_params, true);
            $item -> channel        = $item -> request_params['platform_sign'];
            $item -> need_check     = Withdraw ::needWithdrawCheck() && in_array($item -> status, [0, 1]) ? 1 : 0;
            $aChannel               = FinancePlatformAccountChannel::getOptions($this->partnerSign);
            $item -> channel        = $aChannel[$item -> channel]??$item -> channel;

            $item -> request_time   = empty($item -> request_time)?'':date("Y-m-d H:i:s", $item -> request_time);
            $item -> check_time     = empty($item -> check_time)?'':date("Y-m-d H:i:s", $item   -> check_time);
            $item -> process_time   = empty($item -> process_time)?'':date("Y-m-d H:i:s", $item -> process_time);


            $item -> can_hand       = $item -> canHand();

            $totalRealAmount += $item -> real_amount;
            $totalRequestAmount += $item -> amount;
        }

        $data['pageTotalAmount']     = $totalRequestAmount;
        $data['pageTotalRealAmount'] = $totalRealAmount;

        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 人工
     * @param $id
     * @return mixed
     */
    public function withdrawHand($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取提现记录
        $info = Withdraw ::find($id);
        if (!$info) {
            return Help ::returnApiJson("对不起, 提现记录不存在！", 0);
        }

        // 获取配置
        $action = request('action', 'process');
        if ($action == 'option') {
            $info -> amount = number4($info -> amount);
            $data['model'] = $info;
            $data['type_options'] = Withdraw ::$status;
            return Help ::returnApiJson("恭喜, 获取数据成功！", 1, $data);
        }

        $type   = request("type");
        $amount = request("amount");
        $reason = request("reason");
        if ($amount < 0 || $amount != intval($amount)) {
            return Help ::returnApiJson("对不起, 无效的金额!", 0);
        }

        if ($amount > $info -> amount) {
            return Help ::returnApiJson("对不起, 资金不能超过充值资金!", 0);
        }

        // 人工失败 -5：失败  5成功
        if ($type == -5) {
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
        return Help ::returnApiJson("恭喜, 人工处理成功!", 1);
    }

    /**
     * 提现日志
     * @param $id
     * @return mixed
     */
    public function withdrawLog($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取提现记录
        $withdraw = Withdraw ::find($id);
        if (!$withdraw) {
            return Help ::returnApiJson("对不起, 充值记录不存在！", 0);
        }

        // 获取提现日志
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
     * @param int $id
     * @return mixed
     */
    public function withdrawCheckProcess($id)
    {
        $adminUser = $this -> partnerAdminUser;
        $order     = Withdraw ::find($id);
        if (!$order) {
            return Help ::returnApiJson("对不起, 不存在的订单！", 0);
        }

        // 如果状态不对
        if (!in_array($order -> status, [0, 1])) {
            return Help ::returnApiJson("对不起, 订单已经被处理过！", 0);
        }

        $action = request("action", 'process');

        $checkStatus = Withdraw ::$checkStatus;
        if ('fetch' == $action) {
            if ($order -> status  != 1) {
                $order -> status           = Withdraw::STATUS_FETCH_SUCCESS; // 领取
                $order -> check_time       = time();                         // 审核时间
                $order -> check_admin_id   = $adminUser->id;                 // 审核人
                $order -> save();
            }

            $order -> status_desc    = Withdraw ::getStatusDesc($order -> status);
            $order -> amount         = number4(moneyUnitTransferIn($order -> amount));
            $data['order']           = $order;

            // 充值
            $lastRecharge            = Recharge ::getOrders($order -> user_id, 10);
            $data['last_recharge']   = $lastRecharge;

            // 提现
            $lastWithdraw            = Withdraw ::getOrders($order -> user_id, 10);
            $data['last_withdraw']   = $lastWithdraw;
            foreach ($data['last_withdraw'] as $v){
                $v['amount']         = number4(moneyUnitTransferIn($v['amount']));
                $v['real_amount']    = number4(moneyUnitTransferIn($v['real_amount']));
            }

            // 总提现和总充值
            $total_recharge          = Recharge ::getTotalRecharge($order -> user_id, 10);
            $total_withdraw          = Withdraw ::getTotalWithdraw($order -> user_id, 10);
            $data['order']->total_recharge = $total_recharge;
            $data['order']->total_withdraw = $total_withdraw;

            // 账户
            $account                 = Account ::findFormatAccountByUserId($order -> user_id);
            $account->total_recharge = $total_recharge;
            $account->total_withdraw = $total_withdraw;
            $data['account']         = $account;

            $data['withdraw_need_bet_times'] = partnerConfigure($adminUser->partner_sign,"finance_withdraw_bet_times");

            // 统计
            $stat                    = ReportStatUser ::findFormatDataByUserId($order -> user_id);
            $data['stat']            = $stat;

            // 用户
            $player                  = Player ::findFormatUserById($order -> user_id);
            $data['user']            = $player;

            return Help ::returnApiJson("恭喜, 领取成功", 1, $data);
        }

        // 状态：2  -2
        $status = request("status");
        if (!array_key_exists($status, $checkStatus)) {
            return Help ::returnApiJson("对不起, 状态无效!", 0);
        }

        $order -> check_time          = time(); // 审核时间
        $order -> check_admin_id      = time(); // 审核人
        // 2:审核通过/-2未通过/0待定处理

        if($status == 2){
            // 处理
            $user  = Player ::find($order                -> user_id);
            $card  = PlayerCard ::find($order            -> card_id);
            $pCard = PlayerCard::where('id',$card['id']) -> first();
            if (!$pCard){
                return "对不起,　卡号不存在!";
            }
            $pay   = new Pay();
            $pay   = $pay -> getHandle('panda');
            $pay          -> setWithdrawOrder($order);
            $pay          -> setWithdrawUser($user);
            $pay          -> setWithdrawCard($pCard);
            $pAccount = FinancePlatformAccount::where('partner_sign',$user->partner_sign)->first();
            if (!$pAccount->merchant_secret){
                return "对不起,　获取商户密匙失败!";
            }

            $pay -> constant['key']            = $pAccount -> merchant_secret;
            $financePlatform                   = FinancePlatform::where('platform_sign',$pAccount->platform_sign)->first();
            if (empty($financePlatform)){
                return '对不起，厂商不存在';
            }
            $pay -> constant['withdrawal_url'] = $financePlatform->platform_url.config('finance.main.fmis.payment');

            $pay -> constant['merchantId']     = $pAccount->merchant_code;

            $fmis_bank         = config('finance.main.banks');
            $platform_sign     = $pAccount->platform_sign;
            $bank_sign         = isset($fmis_bank[$platform_sign])?$fmis_bank[$platform_sign]:$card -> bank_sign;
            $card -> bank_sign = isset($bank_sign[strtoupper($card -> bank_sign)])?$bank_sign[strtoupper($card -> bank_sign)]:$card -> bank_sign;
            $r = $pay -> withdrawal($card -> bank_sign, $order -> order_id, $order -> amount, $card -> card_number, $card -> owner_name, $platformChannelId);

            $order -> check_time       = time();
            $order -> check_admin_id   = $adminUser -> id;
            //判断接收订单状态-进行保存数据
            if ($r['status'] === true) {
                $order -> request_time = time();
                //$order -> real_amount  = $order -> amount;
                $order -> status       = Withdraw::STATUS_SEND_SUCCESS;
                $order -> save();
            } else {
                $order -> request_time = time();
                $order -> status       = Withdraw::STATUS_SEND_FAIL;
                $order -> save();
            }
        }

        return Help ::returnApiJson("恭喜, 处理成功", 1);
    }

    /**
     * 获取提现日志列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawLogList()
    {
        $adminUser = $this->partnerAdminUser;

        $c    = request() -> all();
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
        $adminUser = $this->partnerAdminUser;

        // 用户
        $userId = trim(request("user_id"));
        $user   = Player ::find($userId);
        if (!$user) {
            return Help ::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 不绑定手机号不能提款
        $mobileSwitch = partnerConfigure($adminUser->partner_sign,'finance_mobile_switch', 0);
        if ($mobileSwitch && !$user -> mobile) {
            return Help ::returnApiJson('对不起, 请先绑定手机号', 0);
        }

        // 密码检测
        $codeOne = base64_decode(request("fund_password", ''));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);
        if (!$fundPassword || !Hash ::check($fundPassword, $adminUser -> fund_password)) {
            return Help ::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        // 检查用户是否绑定银行卡
        $bindCards = PlayerCard ::getCards($user -> id);
        if (count($bindCards) == 0) {
            return Help ::returnApiJson('对不起, 用户没有绑定银行卡!', 0, ['reason_code' => 910]);
        }

        // 获取可用余额 余额为０　还能提现么？
        $account     = $user -> account();
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
        $card      = $bindCards[0];
        $source    = request('from', "iphone");

        // 生成审核订单
        $res = $user -> requestWithdraw($amount, $card, $source);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        return Help ::returnApiJson('恭喜, 发起提现成功!', 1);
    }

    // 导出数据列表
    public function withdrawExport()
    {
        $c    = request() -> all();
        $date = date("Y-m-d");
        return Excel ::download(new WithdrawExport($c), "withdraw-{$date}.xlsx");
    }

}
