<?php

namespace App\Http\Controllers\Api;

/**
 * ApiFinanceController.php
 * @version 2.0.0 2019.09
 */

use App\Lib\Help;
use App\Models\Admin\SysBank;
use App\Models\Finance\Recharge;
use App\Models\Finance\Withdraw;
use App\Models\Player\PlayerCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\Partner\PartnerConfigure;
use App\Models\Finance\FinancePlatformChannel;
use App\Models\Finance\FinancePlatformAccountChannel;

class ApiFinanceController extends ApiBaseController
{

    /**
     * 获取充值渠道
     * @return JsonResponse
     */
    public function getRechargeChannel()
    {
        $player = auth() -> guard('api') -> user();
        if (!$player) {
            return Help ::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // device  电脑端 1
        if (request()->input('device') == 'mobile'){
            $device = [
                FinancePlatformAccountChannel::STATUS_PC,
                FinancePlatformAccountChannel::STATUS_ALL,
                FinancePlatformAccountChannel::STATUS_MOBILE,
            ];
        }else{
            $device = [
                FinancePlatformAccountChannel::STATUS_PC,
                FinancePlatformAccountChannel::STATUS_MOBILE,
            ];
        }

        $c = [
            'partner_sign' => $player -> partner_sign,
            'device'       => $device,
            'direction'    => FinancePlatformChannel::DIR_IN,
            'status'       => FinancePlatformAccountChannel::STATUS_ENABLE,
            'level'        => !empty($player->vip_level)?$player->vip_level:1
        ];

        $channelArr = FinancePlatformAccountChannel ::getRechargeChannel($c, "front");
        if (!$channelArr) {
            return Help ::returnApiJson('对不起, 该商户还没有充值渠道', 0);
        }

        return Help ::returnApiJson('恭喜, 获取充值渠道成功!', 1, $channelArr);
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function recharge()
    {
        // 1.判断用户是否登录
        $player = auth() -> guard('api') -> user();
        if (!$player) {
            return Help ::returnApiJson("对不起, 用户不存在！", 0);
        }

        $channelId = request("client_channel", '');

        // 2.1是否测试用户
        if ($player -> is_tester == 1) {
            return Help ::returnApiJson('对不起, 测试用户不能充值!', 0);
        }

        // 2.2充值维护
        $rechargeMaintain = partnerConfigure($player->partner_sign,'finance_recharge_maintain', 0);
        if ($rechargeMaintain == 1) {
            return Help ::returnApiJson('对不起, 充值维护中!', 0);
        }

        // 3.是否存在
        $oChannel = FinancePlatformAccountChannel ::find($channelId);
        if (!$oChannel) {
            return Help ::returnApiJson("对不起, 充值渠道不存在！", 0);
        }

        // 4.是否开启充值
        if ($oChannel -> status !== 1) {
            return Help ::returnApiJson('对不起, 没有开启充值状态!', 0);
        }

        // 5.是否属于本商户
        if ($oChannel -> partner_sign !== $player -> partner_sign) {
            return Help ::returnApiJson("对不起, 不属于本商户！", 0);
        }

        // 6.是否在允许金额范围
        // 6.1 充值金额必须是在商户规定的范围内
        $amount = request("amount", 1);
        if (!$amount || $amount != intval($amount)) {
            return Help ::returnApiJson('对不起, 无效的资金输入!', 0);
        }
        $finance_min_recharge = partnerConfigure($player->partner_sign,"finance_min_recharge",100);
        $finance_max_recharge = partnerConfigure($player->partner_sign,"finance_max_recharge",5000);
        if ($amount > $finance_max_recharge || $amount < $finance_min_recharge) {
            return Help ::returnApiJson("对不起, 输入金额不在商户允许金额范围!!!", 0);
        }

        // 判断渠道是否在允许的范围
        if ($amount < $oChannel->min || $amount > $oChannel->max) {
            return Help ::returnApiJson("对不起, 输入金额不在渠道允许金额范围!!!", 0);
        }

        // 6.2 充值金额不小于等于0
        if ($amount <= 0) {
            return Help ::returnApiJson("对不起, 输入金额需大于0！", 0);
        }

        // 7. 为了防止刷单，客户不能短时间内发起多笔充值
        $rechargeTime        = Recharge::getRechargeTime($player->id);
        $space_time          = $rechargeTime['recharge_time'] + 30;
        if ($space_time > time()){
          //  return Help ::returnApiJson('对不起, 充值太频繁,请30s后再充值!!!', 0);
        }

        // 8.获取数据
        //$from = 0;//来源 0:web,1:phone
        $from = request()->input('from',0); //来源 0:web,1:phone

        $oChannel -> username   = $player -> username;
        $oChannel -> user_level = $player -> user_level;

        // 9.发起充值请求
        $recharge = Recharge ::requestRecharge($player, $amount, $oChannel, $from, 1);

        // 10.行为
        Help ::savePlayerBehavior("recharge", $player -> id, $recharge);

        // 11.发起充值
        if (isset($recharge['request_model']) && !empty($recharge['request_model'])){
            if ($recharge['request_model'] === 1){
                return redirect($recharge['url']);
            }else{
                return Help ::returnApiJson("", 0, $recharge);
            }
        }
        return Help ::returnApiJson("对不起, 发起充值失败！", 0,$recharge);
   }

    /**
     * 充值列表
     */
    public function rechargeList()
    {
        $user = auth() -> guard('api') -> user();
        if (!$user) {
            return Help ::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c = request() -> all();
        $c['partner_sign'] = $user->partner_sign;

        if (isset($c['status'])) {
            if ($c['status'] == 1) {
                $c['status'] = [1, 2, 3];
            } else if ($c['status'] == 2) {
                $c['status'] = [-1, -2, -3];
            } else {
                $c['status'] = 0;
            }
        }

        $c['user_id'] = $user -> id;

        $data = Recharge ::getList($c);

        $rechargeList = [];

        $channel = config("pay.main.channel");
        foreach ($data['data'] as $item) {
            $_tmp = [
                'amount'       => number4($item -> amount),
                'real_amount'  => number4($item -> real_amount),
                'client_ip'    => $item -> client_ip,
                'order_id'     => $item -> order_id,
                'channel'      => $channel[$item -> channel],
                'request_time' => date("Y-m-d H:i:s", $item -> request_time),
            ];

            if ($item -> status == 2 || $item -> status == 3) {
                $_tmp['status'] = 1;
            } else if ($item -> status == -1 || $item -> status == -2 || $item -> status == -3) {
                $_tmp['status'] = 2;
            } else {
                $_tmp['status'] = 0;
            }

            $rechargeList[] = $_tmp;
        }

        $data['data'] = $rechargeList;

        return Help ::returnApiJson('恭喜, 获取充值记录成功!', 1, $data);
    }

    /**
     * 发起提现
     * @return mixed
     */
    public function withdraw()
    {
        $user = auth() -> guard('api') -> user();
        if (!$user) {
            return Help ::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        // 0. 测试用户不让提现
        if ($user->is_tester == 1) {
            return Help ::returnApiJson("对不起, 测试用户不能提现!", 0);
        }

        // 1. 资金密码
        $codeOne      = base64_decode(trim(request('fund_password', '')));
        $codeTwo      = substr($codeOne, 0, -4);
        $final        = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);
        if (!Hash ::check($fundPassword, $user -> fund_password)) {
            return Help ::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        // 2. 提现维护
        $withdrawMaintain = partnerConfigure($user->partner_sign,'finance_withdraw_maintain', 0);
        if ($withdrawMaintain == 1) {
            return Help ::returnApiJson('对不起, 提现维护中!', 0);
        }

        // 3. 检查是否提现时间
        if (!Withdraw ::isDrawTime($user->partner_sign)) {
            return Help ::returnApiJson('对不起, 当前时间不在提现开放时间内!!!', 0);
        }

        // 4. 测试账户不能提现
//        if ($user -> is_tester) {
//            return Help ::returnApiJson('对不起, 测试用户不能提现!', 0);
//        }

        // 5. 检查资金是否锁   定 账户是否是冻结
        if (!$user -> canWithdraw()) {
            return $this -> returnApiJson("对不起, 冻结用户不能提现！", 0);
        }

        // 6. 检查用户是否绑定银行卡
        $bindCards = PlayerCard ::getCards($user -> id);
        if (count($bindCards) == 0) {
            return Help ::returnApiJson('对不起, 用户没有绑定银行卡!', 0, ['reason_code' => 910]);
        }

        // 7. 检查提现未完成的单子 开启/关闭
        $minWithdrawAmount = partnerConfigure($user->partner_sign,"finance_withdraw_order_can_multi");
        $notFinishedOrder  = Withdraw ::where('user_id', $user -> id) -> whereIn("status", [0, 1]) -> count();
        if (!empty($minWithdrawAmount) && $minWithdrawAmount>0 && $notFinishedOrder >= $minWithdrawAmount) {
            return Help ::returnApiJson("对不起, 您有{$notFinishedOrder}笔未处理的订单, 请联系客服处理!", 0);
        }

        // 8. 账户余额
        $account = $user -> account();
        $userBalance = $account -> balance;
        if ($userBalance <= 0) {
            return Help ::returnApiJson('对不起, 用户资金不足!', 0);
        }

        // 9. 提现金额
        $amount = request('amount', 0);
        if (!$amount || $amount != intval($amount)) {
            return Help ::returnApiJson('对不起, 无效的资金输入!', 0);
        }

        // 10. 金额范围
        $minWithdrawAmount = partnerConfigure($user->partner_sign,"finance_min_withdraw");
        $maxWithdrawAmount = partnerConfigure($user->partner_sign,"finance_max_withdraw");
        if ($amount > $maxWithdrawAmount || $amount < $minWithdrawAmount) {
            return Help ::returnApiJson('对不起, 提现的金额不在允许的范围内!', 0);
        }

        // 11 . 余额是否足够
        if ($userBalance < moneyUnitTransferIn($amount)) {
            return Help ::returnApiJson('对不起, 用户资金不足!', 0);
        }

        // 12. 银行卡
        $cardId = request('card_id', 0);
        if (!$cardId || $cardId != intval($cardId)) {
            return Help ::returnApiJson('对不起, 无效的银行卡!', 0);
        }

        if (!array_key_exists($cardId, $bindCards)) {
            return Help ::returnApiJson('对不起, 银行卡不存在!', 0);
        }

        // 13. 提现的银行卡　是否绑定了超过 N 个小时 判断是否开启时间限制
        $card = $bindCards[$cardId];
        $withdrawIsOpen = partnerConfigure::select('status')->where('partner_sign',$user->partner_sign)->where('sign','finance_card_withdraw_limit_hour')->where('status',1)->first();
        if ($withdrawIsOpen){
            $financeCardWithdrawLimitHour = partnerConfigure($user->partner_sign,'finance_card_withdraw_limit_hour')*3600;
            if (!empty($financeCardWithdrawLimitHour) && (time() - $card['created_at']) < $financeCardWithdrawLimitHour && $financeCardWithdrawLimitHour>0) {
                return Help ::returnApiJson('对不起, 银行卡绑定超过' . partnerConfigure($user->partner_sign,'finance_card_withdraw_limit_hour') . '小时才能提现!', 0);
            }
        }

        // 14. 检查提现成功次数
        $todayDrawTimes          = $user -> getTodayDrawCount();
        $financeDayWithdrawCount = partnerConfigure($user->partner_sign,'finance_day_withdraw_count');
        if (!empty($financeDayWithdrawCount) && $financeDayWithdrawCount > 0 && $todayDrawTimes >= $financeDayWithdrawCount) {
            return Help ::returnApiJson("对不起,您今天已经提现{$todayDrawTimes}次,提现次数已用完!", 0);
        }

        //  15. 需要投注额
        $res = $user -> checkBetCondition($user->username);
        if ($res !== true) {
            return Help ::returnApiJson('对不起, 流水不足', 0);
        }

        // 16. 生成审核订单
        $from = 0;//来源 0:web,1:phone
        $res = $user -> requestWithdraw($amount, $card, $from);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        // 17. 行为
        Help ::savePlayerBehavior("withdraw", $user -> id, ['amount' => $amount, 'card_id' => $cardId]);

        return Help ::returnApiJson('恭喜, 发起提现成功!', 1);
    }

    /**
     * 提现列表
     */
    public function withdrawList()
    {
        $c = request() -> all();
        if (isset($c['status'])) {
            if ($c['status'] == 1) {
                $c['status'] = [4, 5];
            } else if ($c['status'] == 2) {
                $c['status'] = [-1, -2, -3];
            } else {
                $c['status'] = [0];
            }
        }

        $user = auth() -> guard('api') -> user();
        if (!$user) {
            return Help ::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        
        $c['partner_sign'] = $user -> partner_sign;
        $c['user_id']      = $user -> id;
        $data = Withdraw ::getList($c);

        $withdrawList = [];
        $bankOption = SysBank ::getOption();
        foreach ($data['data'] as $item) {
            $_tmp = [
                'amount'       => number4(moneyUnitTransferIn($item -> amount)),
                'real_amount'  => number4(moneyUnitTransferIn($item -> real_amount)),
                'client_ip'    => $item -> client_ip,
                'channel'      => $bankOption[$item -> bank_sign],
                'order_id'     => $item -> order_id,
                'owner_name'   => "* * " . mb_substr($item -> owner_name, -1),
                'request_time' => date("Y-m-d H:i:s", $item -> request_time),
                'status'       => $item -> status,
                'status_desc'  => $item -> status
            ];


            $withdrawList[] = $_tmp;
        }

        $data['data'] = $withdrawList;

        return Help ::returnApiJson('恭喜, 获取提现记录成功!', 1, $data);
    }

    /**
     * 获取绑卡时间和所有财务配置列表
     * @return JsonResponse
     */
    public function configureList()
    {
        $player                         = auth() -> guard('api') -> user();
        if (!$player) {
            return Help ::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $query                          = PartnerConfigure::where('partner_sign',$this->partner->sign)->select('partner_sign','sign','name','value');
        $type                           = request('type');
        if ($type == 'all'){
            $data                       = $query->get();
        }elseif($type == 'Withdrawal_remaining_times') {
            // 剩余提现次数,已经提现次数
            $TodayWithdrawTimes = Withdraw ::getTodayWithdrawTimes($player -> id);
            $finance_day_withdraw_count = $query -> where('sign', 'finance_day_withdraw_count') -> first();
            if (isset($finance_day_withdraw_count) && !empty($finance_day_withdraw_count)) {
                $data = $finance_day_withdraw_count -> value - $TodayWithdrawTimes;
            } else {
                $data = '';
            }
        }elseif ($type == 'finance_lave_bind_card') {
            // 剩余绑卡数量
            $finance_lave_bind_card = PlayerCard ::getLaveBindCard($player -> id, $this -> partner -> sign);
            $data = [
                'partner_sign' => $this -> partner -> sign,
                'sign' => 'finance_lave_bind_card',
                'name' => "剩余绑卡数",
                'value' => $finance_lave_bind_card,
            ];
        }elseif ($type == 'finance_recharge_withdraw'){
            // 充值金额
            $finance_min_recharge  = PartnerConfigure::where('partner_sign',$this->partner->sign)->select('value')->where('sign','finance_min_recharge')->first();
            $finance_max_recharge  = PartnerConfigure::where('partner_sign',$this->partner->sign)->select('value')->where('sign','finance_max_recharge')->first();
            $finance_min_withdraw  = PartnerConfigure::where('partner_sign',$this->partner->sign)->select('value')->where('sign','finance_min_withdraw')->first();
            $finance_max_withdraw  = PartnerConfigure::where('partner_sign',$this->partner->sign)->select('value')->where('sign','finance_max_withdraw')->first();
            $data = [
                'partner_sign' => $this -> partner -> sign,
                'finance_min_recharge' => $finance_min_recharge->value??'',
                'finance_max_recharge' => $finance_max_recharge->value??'',
                'finance_min_withdraw' => $finance_min_withdraw->value??'',
                'finance_max_withdraw' => $finance_max_withdraw->value??'',
            ];
        }else{
            $data  = $query->where('sign','finance_card_withdraw_limit_hour')->first();
        }
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
