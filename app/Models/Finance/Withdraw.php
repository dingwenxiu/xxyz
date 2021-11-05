<?php

namespace App\Models\Finance;

use App\Lib\Clog;
use Carbon\Carbon;
use App\Lib\Pay\Pay;
use App\Models\Base;
use App\Models\Admin\SysBank;
use App\Models\Player\Player;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use App\Models\Player\PlayerCard;
use Illuminate\Support\Facades\DB;
use App\Models\Report\ReportStatStack;

/**
 * Tom 2019 整理
 * Class Withdraw
 * @package App\Models\Finance
 */
class Withdraw extends Base
{
    protected $table = 'user_withdraw';

    const STATUS_WAIT_SUCCESS        =  0;  // 等待审核
    const STATUS_FETCH_SUCCESS       =  1;  // 领取审核
    const STATUS_CHECK_SUCCESS       =  2;  // 财务审核成功 第二次审核（财务银行卡）
    const STATUS_CHECK_FAIL          = -2;  // 财务审核失败 第二次审核（财务银行卡）
    const STATUS_SEND_SUCCESS        =  3;  // 代付成功
    const STATUS_SEND_FAIL           = -3;  // 代付失败
    const STATUS_CALLBACK_SUCCESS    =  4;  // 回调成功
    const STATUS_CALLBACK_FAIL       = -4;  // 回调失败
    const STATUS_HAND_SUCCESS        =  5;  // 人工成功
    const STATUS_HAND_FAIL           = -5;  // 人工失败
    const STATUS_WIND_CHECK_SUCCESS  =  6;  // 风控审核成功 第一次审核（风控） 财务等待审核
    const STATUS_WIND_CHECK_FAIL     = -6;  // 风控审核失败 第一次审核（风控）
    const STATUS_HAND_WAIT_SUCCESS   =  7;  // 人工等待审核
    const STATUS_HAND_FETCH_SUCCESS  =  8;  // 人工领取审核
    const STATUS_CHECK_FETCH_SUCCESS =  9;  // 财务领取审核

     static $status = [
        0  => '等待审核',
        1  => '领取审核',
        2  => '审核成功',
        3  => '审核成功',
        4  => '提现成功',
        5  => '提现成功',
        6  => '审核成功',
        7  => '等待审核',
        8  => '领取审核',
        9  => '领取审核',
        -2 => '提现失败',
        -3 => '代付失败',
        -4 => '提现失败',
        -5 => '提现失败',
        -6 => '提现失败',

    //  2  => '财务审核成功',
    //  3  => '代付成功',
    //  4  => '回调成功',
    //  5  => '人工成功',
    //  6  => '风控审核成功',
    //  7  => '人工等待审核',
    //  8  => '人工领取审核',
    //  9  => '财务领取审核',
    //    -2 => '财务审核失败',
    //    -3 => '代付失败',
    //    -4 => '回调失败',
    //    -5 => '人工失败',
    //    -6 => '风控审核失败',
    ];


    // 风控审核可选状态
    static $checkWindStatus = [
         6 => "风控审核成功",
        -6 => "风控审核失败",
    ];

    // 财务审核可选状态
    static $checkStatus = [
         2 => "财务审核成功",
        -2 => "财务审核失败",
         7 => "人工等待审核",
    ];

    // 人工审核可选状态
    static $handCheckStatus = [
         5 => "人工审核成功",
        -5 => "人工审核失败",
         7 => "人工等待审核",
    ];

    /**
     * 数据列表
     * @param $c
     * @param bool $countTotal
     * @return array
     */
    static function getList($c, $countTotal = false)
    {
        $query = self ::select(
            DB ::raw('user_withdraw.*'),
            DB ::raw('user_bank_cards.owner_name'),
            DB ::raw('user_bank_cards.card_number'),
            DB ::raw('user_bank_cards.bank_name'),
            DB ::raw('user_bank_cards.branch'),
            DB ::raw('user_bank_cards.city_id'),
            DB ::raw('user_bank_cards.province_id'),
            DB ::raw('sys_city.region_name'),
            DB ::raw('users.is_tester'),
            DB ::raw('users.frozen_type'),
            DB ::raw('users.mark'),
            DB ::raw('user_withdraw_log.request_params')
        )
            -> leftJoin('user_bank_cards', 'user_withdraw.card_id', '=', 'user_bank_cards.id')
            -> leftJoin('users', 'user_withdraw.user_id', '=', 'users.id')
            -> leftJoin('user_withdraw_log', 'user_withdraw_log.order_id', '=', 'user_withdraw.id')
            -> leftJoin('sys_city', 'sys_city.id', '=', 'user_bank_cards.city_id')
            -> orderBy('user_withdraw.created_at', 'desc');

        // 搜索条件
        // ID查询
        if (isset($c['uid']) && !empty($c['uid'])) {
            $query -> where('user_withdraw.id', $c['uid']);
        }

        if (isset($c['id']) && !empty($c['id'])) {
            $query -> where('user_withdraw.id', $c['id']);
        }

        // 审核通过
        if (isset($c['status_fail']) && !empty($c['status_fail'])) {
            $query -> whereIn('user_withdraw.status', $c['status_fail']);
        }
        // 除审核通过
        if (isset($c['status_passed']) && !empty($c['status_passed'])) {
            $query -> whereIn('user_withdraw.status', $c['status_passed']);
        }

        // UserId查询
        if (isset($c['user_id']) && $c['user_id']) {
            if (is_array($c['user_id'])) {
                $query -> whereIn('user_withdraw.user_id', $c['user_id']);
            } else {
                $query -> where('user_withdraw.user_id', $c['user_id']);
            }
        }

        // 状态：status
        if (isset($c['status']) && $c['status'] != 'all') {
            if (is_array($c['status'])) {
                $query -> whereIn('user_withdraw.status', $c['status']);
            } else {
                $query -> where('user_withdraw.status', $c['status']);
            }
        }

        // 人工成功：hand_status
        if (isset($c['hand_status']) && $c['hand_status'] != 'all') {
            if (is_array($c['hand_status'])) {
                $query -> whereIn('user_withdraw.status', $c['hand_status']);
            } else {
                $query -> where('user_withdraw.status', $c['hand_status']);
            }
        }

        // 风控 hand_wind_status
        if (isset($c['hand_wind_status']) && $c['hand_wind_status'] != 'all') {
            if (is_array($c['hand_wind_status'])) {
                $query -> whereIn('user_withdraw.status', $c['hand_wind_status']);
            } else {
                $query -> where('user_withdraw.status', $c['hand_wind_status']);
            }
        }

        // 财务：hand_finance_status
        if (isset($c['hand_finance_status']) && $c['hand_finance_status'] != 'all') {
            if (is_array($c['hand_finance_status'])) {
                $query -> whereIn('user_withdraw.status', $c['hand_finance_status']);
            } else {
                $query -> where('user_withdraw.status', $c['hand_finance_status']);
            }
        }

        // 审核管理员：check_admin_id
        if (isset($c['check_admin_id'])) {
            $query -> where('user_withdraw.check_admin_id', $c['check_admin_id']);
        }

        // 财务认领人：check_admin_id
        if (isset($c['finance_admin_id'])) {
            $query -> where('user_withdraw.finance_admin_id', $c['finance_admin_id']);
        }

        // 财务审核人：check_admin_id
        if (isset($c['finance_check_admin_id'])) {
            $query -> where('user_withdraw.finance_check_admin_id', $c['finance_check_admin_id']);
        }

        // 提现记录审核人：hand_admin_id
        if (isset($c['hand_admin_id'])) {
            $query -> where('user_withdraw.hand_admin_id', $c['hand_admin_id']);
        }

        // 提现领取人：claim_admin_id
        if (isset($c['claim_admin_id'])) {
            $query -> where('user_withdraw.claim_admin_id', $c['claim_admin_id']);
        }

        // 网络地址：client_ip
        if (isset($c['client_ip']) && $c['client_ip']) {
            $query -> where('user_withdraw.client_ip', $c['client_ip']);
        }

        // 用户身份:黑名单：black_list,正常用户：nomal_user,frozen_type
        if (isset($c['frozen_type']) && $c['frozen_type'] && $c['frozen_type'] != 'all') {
            if (is_array($c['frozen_type'])) {
                $query -> whereIn('users.frozen_type', $c['frozen_type']);
            } else {
                $query -> where('users.frozen_type', $c['frozen_type']);
            }
        }

        // 申请时间：开始时间 request_time
        if (isset($c['start_request_time']) && $c['start_request_time']) {
            $query -> where('user_withdraw.request_time', ">=", strtotime($c['start_request_time']));
        }
        // 申请时间：结束时间
        if (isset($c['end_request_time']) && $c['end_request_time']) {
            $query -> where('user_withdraw.request_time', "<=", strtotime($c['end_request_time']));
        }

        // 审核时间：开始时间 check_time
        if (isset($c['start_check_time']) && $c['start_check_time']) {
            $query -> where('user_withdraw.check_time', ">=", strtotime($c['start_check_time']));
        }
        // 审核时间：结束时间
        if (isset($c['end_check_time']) && $c['end_check_time']) {
            $query -> where('user_withdraw.check_time', "<=", strtotime($c['end_check_time']));
        }

        // 成功时间：开始时间 day_m
        if (isset($c['start_day_m']) && $c['start_day_m']) {
            $query -> where('user_withdraw.day_m', ">=", strtotime($c['start_day_m']));
        }
        // 成功时间：结束时间
        if (isset($c['end_day_m']) && $c['end_day_m']) {
            $query -> where('user_withdraw.day_m', "<=", strtotime($c['end_day_m']));
        }

        // 申请时间：开始时间 request_time
        if (isset($c['start_claim_time']) && $c['start_claim_time']) {
            $query -> where('user_withdraw.request_time', ">=", strtotime($c['start_claim_time']));
        }
        // 申请时间：结束时间
        if (isset($c['end_claim_time']) && $c['end_claim_time']) {
            $query -> where('user_withdraw.request_time', "<=", strtotime($c['end_claim_time']));
        }

        // 财务处理时间：开始时间 finance_time
        if (isset($c['start_finance_time']) && $c['start_finance_time']) {
            $query -> where('user_withdraw.finance_time', ">=", strtotime($c['start_finance_time']));
        }
        // 财务处理时间：结束时间
        if (isset($c['end_finance_time']) && $c['end_finance_time']) {
            $query -> where('user_withdraw.finance_time', "<=", strtotime($c['end_finance_time']));
        }
        
        // 用户名：username
        if (isset($c['username']) && $c['username']) {
            $query -> where('user_withdraw.username', "=", $c['username']);
        }
        //银行名称：bank_sign
        if (isset($c['bank_sign']) && $c['bank_sign'] && $c['bank_sign'] != 'all') {
            if (is_array($c['bank_sign'])) {
                $query -> whereIn('user_withdraw.bank_sign', $c['bank_sign']);
            } else {
                $query -> where('user_withdraw.bank_sign', $c['bank_sign']);
            }
        }

        //总代列表：partner_sign
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query -> where('user_withdraw.partner_sign', $c['partner_sign']);
        }

        //是否测试用户：is_tester
        if (isset($c['is_tester']) && $c['is_tester'] != 'all') {
            if (is_array($c['is_tester'])) {
                $query -> whereIn('users.is_tester', $c['is_tester']);
            } else {
                $query -> where('users.is_tester', $c['is_tester']);
            }
        }

        //订单编号：order_id
        if (isset($c['order_id']) && $c['order_id']) {
            $query -> where('user_withdraw.order_id', $c['order_id']);
        }

        //提现渠道：channel
        if (isset($c['channel']) && $c['channel'] && !empty($c['channel'])) {
            $query -> where('user_recharge.channel', $c['channel']);
        }

        //金额：min、max
        if (isset($c['min']) && $c['min']) {
            $query -> where('user_withdraw.amount', ">=", $c['min']);
        }
        if (isset($c['max']) && $c['max']) {
            $query -> where('user_withdraw.amount', "<=", $c['max']);
        }


        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize    = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset      = ($currentPage - 1) * $pageSize;

        //　统计总实际上分
        $totalRealAmount = 0;
        if ($countTotal) {
            $totalRealAmount = $query -> sum('user_withdraw.real_amount');
        }

        //　统计总上分
        $totalAmount = 0;
        if ($countTotal) {
            $totalAmount = $query -> sum('user_withdraw.amount');
        }

        //统计总页数
        $total = $query -> count();
        //分页查询数据
        $data  = $query -> skip($offset) -> take($pageSize) -> get();

        return ['data' => $data, 'totalAmount' => number4(moneyUnitTransferIn($totalAmount)), 'totalRealAmount' => number4(moneyUnitTransferIn($totalRealAmount)), 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取提现订单
     * @param $id
     * @param int $count
     * @return mixed
     */
    static function getOrders($id, $count = 10)
    {
        $orders = self ::where("user_id", $id) -> whereIn('status',[Withdraw::STATUS_SEND_SUCCESS,Withdraw::STATUS_HAND_SUCCESS]) -> orderBy("id", "desc") -> take($count) -> get();
        foreach ($orders as $order) {
            $sysBank               = SysBank::where('code',$order -> bank_sign)->first();
            $order -> bank_sign    = $sysBank       -> title;
            $order -> request_time = $order         -> request_time ? date("Y-m-d H:i", $order -> request_time) : "";
            $order -> process_time = $order         -> process_time ? date("Y-m-d H:i", $order -> process_time) : "";
            $order -> check_time   = $order         -> check_time ? date("Y-m-d H:i", $order -> check_time) : "";
            $order -> status       = self ::$status[$order->status];
        }

        return $orders;
    }

    // 保存
    public function saveItem($adminId = 0)
    {
        $data = request() -> all();

        // 用户名
        $user = Player ::where('username', $data['username']) -> first();
        if (!$user -> id) {
            return "无效的用户!";
        }

        // 卡号
        if (strlen($data['card_number']) < 15 || strlen($data['card_number']) > 19) {
            return "银行卡号只能是15位和19位之间!";
        }

        // 银行
        $banks = config("web.banks");
        if (!isset($data['bank_sign']) || !isset($banks[$data['bank_sign']])) {
            return "无效的开户行!";
        }

        // 省份
        $provinceList = Province ::getProvince();
        if (!isset($data['province']) || !isset($provinceList[$data['province']])) {
            return "无效的省份!";
        }

        // 市区
        $cityList = $provinceList[$data['province']]['city'];
        if (!isset($data['city']) || !isset($cityList[$data['city']])) {
            return "无效的市!";
        }

        $this -> username     = $data['username'];
        $this -> nickname     = $data['nickname'];
        $this -> user_id      = $user -> id;
        $this -> bank_sign    = $data['bank_sign'];
        $this -> card_number  = $data['card_number'];
        $this -> branch       = $data['branch'];
        $this -> owner_name   = $data['owner_name'];
        $this -> province     = $provinceList[$data['province']]['name'];
        $this -> city         = $cityList[$data['city']];
        $this -> request_time = time();
        $this -> admin_id     = $adminId;
        $this -> save();
        return true;
    }

    /**
     * 是否提现时间
     * @param $partner_sign
     * @return bool
     */
    static function isDrawTime($partner_sign)
    {
        $drawTimeRange = partnerConfigure($partner_sign,"finance_withdraw_time_range","00:00:00-08:00:00|09:00:00-24:00:00");
        $range         = explode('|', $drawTimeRange);

        $nowSeconds    = time();
        $nowDay        = date('Y-m-d ');
        foreach ($range as $r) {
            $r_time    = explode('-', $r);
            if ($nowSeconds >= strtotime($nowDay . $r_time[0]) && $nowSeconds <= strtotime($nowDay . $r_time[1])) {
                return true;
            }
        }
        return false;
    }
 
    /**
     * 发起请求
     * @param $user
     * @param $amount
     * @param $card
     * @param string $from
     * @return bool|string
     * @throws \Exception
     */
    static function request($user, $amount, $card, $from)
    {
        $locker = new AccountLocker($user -> id, "recharge-request");
        if (!$locker -> getLock()) {
            Clog ::withdrawLog("对不起, 获取用户锁失败!!:");
            return "对不起,　获取用户锁失败!";
        }

        $account = $user -> account();
        // 余额不够
        if ($account -> balance < moneyUnitTransferIn($amount)) {
            return "对不起,　余额不足!";
        }

        db() -> beginTransaction();
        try {
            // 提现冻结
            $accountChange = new AccountChange();
            $ret           = $accountChange -> change($account, 'withdraw_frozen', array(
                'user_id'  => $user -> id,
                'amount'   => moneyUnitTransferIn($amount),
            ));

            if ($ret !== true) {
                $locker -> release();
                db()    -> rollback();
                return $ret;
            }

            $request = new Withdraw();
            $request -> partner_sign = $user -> partner_sign;
            $request -> user_id      = $user -> id;
            $request -> top_id       = $user -> top_id;
            $request -> parent_id    = $user -> parent_id;
            $request -> username     = $user -> username;
            $request -> nickname     = $user -> nickname;
            $request -> card_id      = $card['id'];
            $request -> bank_sign    = $card['bank_sign'];
            $request -> amount       = $amount;
            $request -> request_time = time();
            $request -> desc         = "";

            $request -> from_device  = $from;
            $request -> client_ip    = real_ip();
            $ret = $request -> save();

            // 添加telegram提现推送消息
            $fromConfig = config("web.main.from");
            $text  = "<b>用户{$user -> username}(id:{$user->id})  ,在" . date('Y-m-d H:i:s', time()) . ",通过{$fromConfig[$from]}平台,发起了一笔提现操作请求!!!,金  额 : " . $amount . '元</b>';
            telegramSend("send_finance", $text, $user -> partner_sign);

            if (!$ret) {
                $locker -> release();
                db()    -> rollback();
                return "对不起,　保存记录失败!";
            }

            $withdrawOrderPlus   = partnerConfigure($user -> partner_sign, "finance_withdraw_order_plus",20013000);
            $prefix              = partnerConfigure($user -> partner_sign, "finance_withdraw_order_prefix",'BW');

            $request -> order_id = $user -> partner_sign.$prefix . ($request -> id + $withdrawOrderPlus);
            $ret                 = $request -> save();

            if (!$ret) {
                $locker -> release();
                db()    -> rollback();
                return false;
            }
            db() -> commit();
        } catch (\Exception $e) {
            $locker -> release();
            db()    -> rollback();
            Clog ::withdrawQuery("withdraw-request-exception-:" . $e -> getMessage() . "|" . $e -> getFile() . "|" . $e -> getLine());
            return "对不起, " . $e -> getMessage();
        }

        // 如果不需要审核
        if (!self ::needWithdrawCheck()) {
            $pay = new Pay();
            $pay = $pay -> getHandle('panda');
            $pay        -> setWithdrawOrder($request);
            $pay        -> setWithdrawUser($user);

            $pAccount = FinancePlatformAccount::where('partner_sign',$request['partner_sign'])->first();
            if (!$pAccount->merchant_secret){
                return "对不起,　获取商户密匙失败!";
            }

            $pCard    = PlayerCard::where('id',$card['id'])->first();
            if (!$pCard){
                return "对不起,　卡号不存在!";
            }

            $pay         -> setWithdrawCard($pCard);
            $pay         -> constant['key']            = $pAccount->merchant_secret;
            $pay         -> constant['withdrawal_url'] = configure('payment', 'https://api.cqvip9.com/v1_beta/payment');
            $pay         -> constant['merchantId']     = $pAccount->merchant_code;

            $r = $pay    -> withdrawal($card['bank_sign'], $request -> order_id, $amount, $card['card_number'], $card['owner_name']);
            if ($r['status']) {
                $request -> check_time   = time();
                $request -> process_time = time();
                $request -> day_m        = time();
                $request -> status       = self::STATUS_SEND_SUCCESS; // 代发成功
                $request -> save();
            } else {
                $request -> check_time   = time();
                $request -> process_time = time();
                $request -> day_m        = time();
                $request -> status       = self::STATUS_SEND_FAIL;    // 代发失败
                $request -> save();
            }
        }
        $locker -> release();
        return true;
    }

    /**
     * @param $reason
     * @param $adminUser
     * @return bool|string
     * @throws \Exception
     */
    public function processFail($reason, $adminUser)
    {
        // 判断是否处理
        if (in_array($this -> status,[self::STATUS_HAND_SUCCESS,self::STATUS_HAND_FAIL])) {
            return "对不起, 订单已经处理!!";
        }

        $locker = new AccountLocker($this -> user_id, "withdraw-process-fail");
        if (!$locker -> getLock()) {
            Clog ::withdrawLog("对不起, 获取用户锁失败!!:");
            return "对不起, 获取用户锁失败!!:";
        }

        db() -> beginTransaction();
        try {

            $user    = Player ::find($this -> user_id);
            $account = $user -> account();

            $params  = [
                'user_id' => $user -> id,
                'amount'  => moneyUnitTransferIn($this -> amount),
                'desc'    => "提现解冻-成功"
            ];

            $accountChange = new AccountChange();
            $res = $accountChange -> change($account, 'withdraw_un_frozen', $params);
            if ($res !== true) {
                Clog ::withdrawLog("对不起, 提现回调帐变失败!!:", [$res]);
                $locker -> release();
                db()    -> rollback();
                return "对不起, 提现回调帐变失败!!:";
            }
            $this   -> status            = Withdraw::STATUS_HAND_FAIL;;
            $this   -> desc              = $adminUser->username.'提现记录手动失败'.','.'原因：'.$reason;
            $this   -> hand_admin_id     = $adminUser -> id;
            $this   -> process_time      = time();
            $this   -> day_m             = time();
            $this   -> hand_process_time = time();
            $this   -> save();

            db()    -> commit();
        } catch (\Exception $e) {
            db()    -> rollback();
            $locker -> release();
            Clog ::withdraw("withdraw-fail-exception-:" . $e -> getMessage() . "|" . $e -> getFile() . "|" . $e -> getLine());
            return "提现人工处理失败异常:" . $e -> getMessage() . "-" . $e -> getLine();
        }

        $locker -> release();
        return true;
    }

    /**
     * @TODO hand / query 状态
     * @param $amount
     * @param $adminUser
     * @param string $reason
     * @return bool|string
     * @throws \Exception
     */
    public function process($amount, $adminUser, $reason = "")
    {
        $adminId = $adminUser->id;
        if (in_array($this -> status,[self::STATUS_HAND_SUCCESS,self::STATUS_HAND_FAIL])) {
            return "对不起, 订单已经处理!!";
        }

        $user = Player ::find($this -> user_id);

        $locker = new AccountLocker($user -> id, "withdraw-process");
        if (!$locker -> getLock()) {
            Clog ::withdrawLog("对不起, 获取用户锁失败!!:");
            return "对不起, 获取用户锁失败!";
        }

        $account = $user -> account();

        db() -> beginTransaction();
        try {
            $params = [
                'user_id' => $user -> id,
                'amount'  => moneyUnitTransferIn($this -> amount),
                'desc'    => "提现成功",
            ];

            $accountChange = new AccountChange();
            $res  = $accountChange -> change($account, 'withdraw_finish', $params);
            if ($res !== true) {
                Clog ::withdrawLog("对不起, 提现回调帐变失败!!:", [$res]);
                $locker -> release();
                db()    -> rollback();
                return "对不起, 提现成功帐变失败!";
            }

            $this -> real_amount       = $this -> amount;
            $this -> process_time      = time();
            $this -> hand_process_time = time();
            $this -> hand_admin_id     = $adminId;
            $this -> desc              = $adminUser->username.'提现记录手动成功'.','.'原因：'.$reason;
            $this -> status            = Withdraw::STATUS_HAND_SUCCESS;
            $this -> day_m             = time();
            $this -> save();

            // 统计
            ReportStatStack::doWithdraw($user, $this);
            db() -> commit();
        } catch (\Exception $e) {
            $locker -> release();
            db()    -> rollback();
            Clog ::withdraw("withdraw-success-exception-:" . $e -> getMessage() . "|" . $e -> getFile() . "|" . $e -> getLine());
        }

        return true;
    }

    static function getStatusDesc($status)
    {
        switch ($status) {
            case 0:
                return "<span style='color: grey;'>待审核</span>";
                break;
            case 1:
                return "<span style='color: green;'>审核领取</span>";
                break;
            case 2:
                return "<span style='color: green;'>审核成功</span>";
                break;
            case 3:
                return "<span style='color: green;'>代发成功</span>";
                break;
            case 4:
                return "<span style='color: green;'>提现成功</span>";
                break;
            case 5:
                return "<span style='color: green;'>人工成功</span>";
                break;
            case -2:
                return "<span style='color: red;'>审核失败</span>";
                break;
            case -3:
                return "<span style='color: red;'>代发失败</span>";
                break;
            case -4:
                return "<span style='color: red;'>回调失败</span>";
                break;
            case -5:
                return "<span style='color: red;'>人工失败</span>";
                break;
            default:
                return "未知状态";
        }
    }

    /**
     * 是否可以人工
     * @return bool
     */
    public function canHand()
    {
        if (Withdraw ::needWithdrawCheck()) {
            if (in_array($this -> status, [Withdraw::STATUS_CHECK_SUCCESS, Withdraw::STATUS_SEND_SUCCESS, Withdraw::STATUS_SEND_FAIL])) {
                return true;
            } else {
                return false;
            }
        } else {
            if (in_array($this -> status, [Withdraw::STATUS_WAIT_SUCCESS, Withdraw::STATUS_FETCH_SUCCESS, Withdraw::STATUS_CHECK_SUCCESS, Withdraw::STATUS_SEND_FAIL])) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 提现是否需要审核
     * @return bool
     */
    static function needWithdrawCheck()
    {
        // 检查是否开启审核
        $isNeedCheck = configure('finance_withdraw_need_check', 1);
        if ($isNeedCheck != 1) {
            return false;
        }

        return true;
    }

    static function todayWithdrew($userId)
    {
        return 0;
    }

    // 获取用户投注
    static function findByUserId($userId)
    {
        return self ::where("user_id", $userId) -> first();

    }

    /**
     * 获取玩家所有的提现金额
     * @param $id
     * @return mixed
     */
    static function getTotalWithdraw($id)
    {
        // 4 , 5 状态是代发成功和人工成功
        $total_withdraw = self ::where("user_id", $id) -> whereIn('status',[Withdraw::STATUS_CALLBACK_SUCCESS,Withdraw::STATUS_HAND_SUCCESS]) -> sum('real_amount');
        $total_withdraw = number4(moneyUnitTransferIn($total_withdraw));
        return $total_withdraw;
    }

    /**
     * 获取玩家当天的提现金额
     * @param $id
     * @return mixed
     */
    static function getTotalTodayWithdraw($id)
    {
        $timeNow              = strtotime(Carbon::today('PRC'));
        $timeFuture           = strtotime(Carbon::tomorrow('PRC'));
        $total_today_withdraw = self ::where("user_id", $id) -> whereBetween('day_m',[$timeNow,$timeFuture])-> whereIn('status',[Withdraw::STATUS_CALLBACK_SUCCESS,Withdraw::STATUS_HAND_SUCCESS]) -> sum('real_amount');
        $total_today_withdraw = number4(moneyUnitTransferIn($total_today_withdraw));
        return $total_today_withdraw;
    }

    /**
     * 获取今天提现次数
     * @param $uid
     * @return mixed
     */
    static function getTodayWithdrawTimes($uid)
    {
        // 4 , 5 状态是回调成功和人工成功
        $TodayWithdrawTimes = self ::where("user_id", $uid) -> whereIn('status',[Withdraw::STATUS_CALLBACK_SUCCESS,Withdraw::STATUS_HAND_SUCCESS]) -> count();
        return $TodayWithdrawTimes;
    }
}
