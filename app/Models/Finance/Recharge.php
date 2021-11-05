<?php namespace App\Models\Finance;

use Exception;
use App\Lib\Clog;
use Carbon\Carbon;
use App\Lib\Pay\Pay;
use App\Models\Base;
use App\Models\Player\Player;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use Illuminate\Support\Facades\DB;
use App\Models\Report\ReportStatStack;

/**
 * Tom 2019 整理
 * Class Recharge
 * @package App\Models\Finance
 */
class Recharge extends Base
{
    protected $table = 'user_recharge';

    static $handType = [
         3 => "人工成功",
        -3 => "人工失败",
    ];

    const STATUS_INIT             =  0; // 初始化
    const STATUS_SEND_SUCCESS     =  1; // 代发成功
    const STATUS_CALLBACK_SUCCESS =  2; // 回调成功
    const STATUS_MANUAL_SUCCESS   =  3; // 人工成功
    const STATUS_SEND_FAIL        = -1; // 代发失败
    const STATUS_CALLBACK_FAIL    = -2; // 回调失败
    const STATUS_MANUAL_FAIL      = -3; // 人工失败

    // 状态
    static $status = [
         0 => "初始化",
         1 => "代发成功",
         2 => "回调成功",
         3 => "人工成功",
        -1 => "代发失败",
        -2 => "回调失败",
        -3 => "人工失败",
    ];

    static function getList($c, $countTotal = false)
    {
        $query = self ::select(
            DB ::raw('user_recharge.id'),
            DB ::raw('user_recharge.user_id'),
            DB ::raw('user_recharge.username'),
            DB ::raw('user_recharge.user_id'),
            DB ::raw('user_recharge.order_id'),
            DB ::raw('user_recharge.amount'),
            DB ::raw('user_recharge.real_amount'),
            DB ::raw('user_recharge.partner_sign'),
            DB ::raw('user_recharge.pay_order_id'),
            DB ::raw('users.vip_level'),

            DB ::raw('user_recharge.from_device'),
            DB ::raw('finance_platform_account_channel.fee_amount'),
            DB ::raw('finance_platform_account_channel.platform_child_sign'),
            DB ::raw('user_recharge.client_ip'),
            DB ::raw('user_recharge.request_time'),
            DB ::raw('user_recharge.day_m'),
            DB ::raw('user_recharge.callback_time'),

            DB ::raw('user_recharge.channel'),
            DB ::raw('finance_platform_account_channel.platform_sign'),
            DB ::raw('finance_platform_account_channel.type_sign'),
            DB ::raw('user_recharge.partner_admin_id'),
            DB ::raw('user_recharge.status')
        )
            -> leftJoin('users', 'users.username', 'user_recharge.username')
            -> leftJoin('finance_platform_account_channel', 'finance_platform_account_channel.channel_sign', 'user_recharge.channel')
            -> orderBy('user_recharge.updated_at', 'desc')
            -> groupBy('user_recharge.order_id');

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query -> where('user_recharge.partner_sign', $c['partner_sign']);
        }

        // 来源-手机或则web端口
        if (isset($c['from_device'])) {
            $query -> where('user_recharge.from_device', $c['from_device']);
        }

        // 等级
        if (isset($c['level']) && $c['level']) {
            $query -> where('finance_platform_account_channel.level', $c['level']);
        }

        // 渠道 熊猫-东南-...
        if (isset($c['platform_sign']) && $c['platform_sign']) {
            $query -> where('finance_platform_account_channel.platform_sign', $c['platform_sign']);
        }

        // type_sign
        if (isset($c['type_sign']) && $c['type_sign']) {
            $query -> where('finance_platform_account_channel.type_sign', $c['type_sign']);
        }

        // 支付方式
        if (isset($c['channel_sign']) && $c['channel_sign']) {
            $query -> where('user_recharge.channel', $c['channel_sign']);
        }

        // platform_child_sign
        if (isset($c['platform_child_sign']) && $c['platform_child_sign']) {
            $query -> where('finance_platform_account_channel.platform_child_sign', $c['platform_child_sign']);
        }

        // 用ID
        if (isset($c['user_id']) && $c['user_id']) {
            $query -> where('user_recharge.user_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query -> where('user_recharge.username', trim($c['username']));
        }

        // 状态
        if (isset($c['status']) && $c['status'] != 'all') {
            if (is_array($c['status'])) {
                $query -> whereIn('user_recharge.status', $c['status']);
            } else {
                $query -> where('user_recharge.status', $c['status']);
            }
        }

        // 订单号
        if (isset($c['order_id']) && $c['order_id']) {
            $query -> where('user_recharge.order_id', trim($c['order_id']));
        }

        // 支付方式
        if (isset($c['channel']) && $c['channel']) {
            $query -> where('user_recharge.channel', $c['channel']);
        }

        // 开始时间
        if (isset($c['start_time']) && $c['start_time']) {
            $query -> where('user_recharge.request_time', ">=", strtotime($c['start_time']));
        }

        // 结束时间
        if (isset($c['end_time']) && $c['end_time']) {
            $query -> where('user_recharge.request_time', "<=", strtotime($c['end_time']));
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize    = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset      = ($currentPage - 1) * $pageSize;

        //　统计总实际上分
        $totalRealAmount = 0;
        if ($countTotal) {
            $totalRealAmount = self::sum('real_amount');
        }

        //统计总页数
        $total = count($query -> pluck('id')->toArray());

        $data  = $query -> skip($offset) -> take($pageSize) -> get();
        return ['data' => $data, 'totalRealAmount' => number4($totalRealAmount), 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取充值订单
     * @param $id
     * @param int $count
     * @return mixed
     */
    static function getOrders($id, $count = 10)
    {
        $orders = self ::where("user_id", $id) -> whereIn('status',[Recharge::STATUS_SEND_SUCCESS,Recharge::STATUS_MANUAL_SUCCESS]) -> orderBy("id", "desc") -> take($count) -> get();
        foreach ($orders as $order) {
            if (substr($order -> channel,'0',6) === 'panda_'){
                $type = FinanceChannelType::where('type_sign',substr($order -> channel,'6'))->first();
            }else{
                $type = FinanceChannelType::where('type_sign',$order -> channel)->first();
            }
            $order -> channel       = $type          -> type_name??$order -> channel;
            $order -> real_amount   = number4($order -> real_amount);
            $order -> amount        = number4($order -> amount);
            $order -> request_time  = $order         -> request_time ? date("Y-m-d H:i", $order  -> request_time) : "";
            $order -> callback_time = $order         -> callback_time ? date("Y-m-d H:i", $order -> callback_time) : "";
            $order -> send_time     = $order         -> send_time ? date("Y-m-d H:i", $order     -> send_time) : "";
            $order -> status        = self ::$status[$order -> status];
        }

        return $orders;
    }

    /**
     * @param $player
     * @param $amount
     * @param $oChannel
     * @param string $bankSign
     * @param string $from
     * @return mixed
     * @throws Exception
     */
    static function requestRecharge($player, $amount, $oChannel, $from ,$bankSign = '')
    {
        $amount = moneyUnitTransferIn($amount);

        $order  = Recharge ::request($player, $amount, $oChannel -> channel_sign, $bankSign, $from);
        if (!$order) {
            return '对不起, 生成订单失败!';
        }
        // 发起充值
        $pay = new Pay();
        $pay = $pay->getHandle('Fmis');
        $pay       -> setRechargeOrder($order);
        $pay       -> setRechargeUser($player);
        $pay       -> setRechargeChannel($oChannel);
        $data = $pay->recharge($from);
        if (!is_array($data)) {
            $order -> status = Recharge::STATUS_SEND_FAIL;
            $order -> fail_reason = $data ?? '';
            $order -> save();
            RechargeLog::where('order_id',$order->id)->update(['back_status'=>Recharge::STATUS_SEND_FAIL,'back_reason'=>$data]);
            return "对不起, 充值失败-{$data}!";
        }

        // 发起请求时间和第三方订单号
        $order -> send_time    = time();
        $order -> pay_order_id = isset($data['pay_order_id'])?$data['pay_order_id']:'';
        RechargeLog::where('order_id',$order->id)->update(['back_status'=>Recharge::STATUS_SEND_SUCCESS,'request_reason'=>$data['result']['msg']]);
        $order -> save();
        return $data;
    }

    /**
     * @param $user
     * @param $money
     * @param $channel
     * @param $bankSign
     * @param integer $from
     * @param string $description
     * @return Recharge|bool
     * @throws \Exception
     */
    static public function request($user, $money, $channel, $bankSign, $from, $description = '')
    {
        db() -> beginTransaction();
        try {
            // 加入请求
            $request = new Recharge;
            $request -> partner_sign = $user -> partner_sign;
            $request -> user_id      = $user -> id;
            $request -> top_id       = $user -> top_id;
            $request -> parent_id    = $user -> parent_id;
            $request -> username     = $user -> username;
            $request -> nickname     = $user -> nickname;

            $request -> channel      = $channel;             // 类型 支付宝
            $request -> bank_sign    = $bankSign;

            $request -> amount       = $money;               // 充值金额
            $request -> request_time = time();               // 请求时间
            $request -> client_ip    = real_ip();            // 客户端IP
            $request -> desc         = $description;         // 充值描述

            $request -> sign         = "";                   // 附言
            $request -> from_device  = $from;                // 来源
            $ret                     = $request -> save();

            if (!$ret) {
                db() -> rollback();
                return false;
            }

            // 处理订单号
            $rechargeOrderPlus   = partnerConfigure($user -> partner_sign, "finance_recharge_order_plus",20013000);
            $prefix              = partnerConfigure($user -> partner_sign, "finance_recharge_order_prefix",'BW');
            $request -> order_id = $user->partner_sign.$prefix . ($request -> id + $rechargeOrderPlus);
            $ret                 = $request -> save();

            if (!$ret) {
                db() -> rollback();
                return false;
            }

            db() -> commit();
        } catch (\Exception $e) {
            db() -> rollback();
            Clog ::rechargeLog("recharge-request-exception-:" . $e -> getMessage() . "|" . $e -> getLine() . "|" . $e -> getFile());
            return false;
        }

        return $request;
    }

    // 快速通道
    static public function fastRecharge($user, $money, $adminId = 0)
    {
        db() -> beginTransaction();
        try {
            // 加入请求
            $request = new Recharge;
            $request -> user_id      = $user -> id;
            $request -> top_id       = $user -> top_id;
            $request -> username     = $user -> username;
            $request -> nickname     = $user -> nickname;
            $request -> parent_id    = $user -> parent_id;

            $request -> channel      = 'fast';                // 类型 支付宝
            $request -> bank_sign    = 'icbc';

            $request -> amount       = $money * 10000;        // 充值金额
            $request -> request_time = time();                // 请求时间
            $request -> client_ip    = real_ip();             // 客户端IP
            $request -> desc         = "快速通道充值";          // 充值描述

            $request -> sign         = "";                    // 附言
            $request -> source       = 'web';                 // 来源

            $ret = $request -> save();
            if (!$ret) {
                db() -> rollback();
                return "对不起, 保存失败!!";
            }

            // 处理订单号
            $rechargeOrderPlus   = partnerConfigure($user -> partner_sign, "finance_recharge_order_plus",20013000);
            $prefix              = partnerConfigure($user -> partner_sign, "finance_recharge_order_prefix",'BW');
            $request -> order_id = $user->partner_sign.$prefix . ($request -> id + $rechargeOrderPlus);
            $ret                 = $request -> save();

            if (!$ret) {
                db() -> rollback();
                return "对不起, 保存订单号失败!!";
            }

            // 处理
            $res = $request -> process($request -> amount, $adminId, "快速通道自动上分");
            if ($res !== true) {
                return $res;
            }

            db() -> commit();
        } catch (\Exception $e) {
            db() -> rollback();
            Clog ::rechargeLog("充值订单入库异常-" . $e -> getMessage() . "|" . $e -> getLine() . "|" . $e -> getFile());
            return $e -> getMessage();
        }

        return true;
    }

    /**
     * 人工审核成功
     * @param $realMoney
     * @param int $adminId
     * @param string $reason
     * @return bool|string
     * @throws Exception
     */
    public function process($realMoney, $adminId = 0, $reason = "")
    {
        if ($realMoney > $this -> amount) {
            return "对不起, 无效的上分资金!!";
        }

        if ($this -> status > 1) {
            return "对不起, 订单已经处理!!";
        }

        $locker = new AccountLocker($this -> user_id, "recharge-process");
        if (!$locker -> getLock()) {
            Clog ::rechargeLog("对不起, 获取用户锁失败!!:");
            return "对不起, 获取用户锁失败!!";
        }

        db() -> beginTransaction();
        try {
            $user    = Player ::find($this -> user_id);
            $account = $user -> account();

            // 充值上分
            $params = [
                'user_id'    => $user -> id,
                'amount'     => $realMoney,
                'desc'       => $adminId ? $adminId . "|" . $reason : "",
                'project_id' => $user->parent_id,
                'admin_id'   => $adminId,
            ];

            $accountChange = new AccountChange();
            $res           = $accountChange -> change($account, 'recharge', $params);
            if ($res !== true) {
                Clog ::rechargeLog("对不起, 充值帐变失败!!:", [$res]);
                $locker -> release();
                db()    -> rollback();
                return $res;
            }

            $this -> real_amount      = $realMoney;
            $this -> partner_admin_id = $adminId;
            $this -> callback_time    = time();
            $this -> status           = $adminId ? self::STATUS_MANUAL_SUCCESS : self::STATUS_CALLBACK_SUCCESS;
            $this -> desc             = $reason;
            $this -> day_m            = date("YmdHi");
            $this -> save();
            // 统计
            ReportStatStack::doRecharge($user, $this->real_amount);
            $locker -> release();
            db()    -> commit();
        } catch (\Exception $e) {
            $locker -> release();
            db()    -> rollback();
            Clog ::rechargeLog("人工审核异常:" . $e -> getMessage() . "-" . $e -> getLine() . "-" . $e -> getFile());
            return $e -> getMessage();
        }

        $locker -> release();

        return true;
    }

    /**
     * 获取玩家所有的充值金额
     * @param $uid
     * @return mixed
     */
    static function getTotalRecharge($uid)
    {
        // 2 , 3 状态是回调成功和人工成功
        $total_recharge = self ::where("user_id", $uid) -> whereIn('status',[Recharge::STATUS_CALLBACK_SUCCESS,Recharge::STATUS_MANUAL_SUCCESS]) -> sum('real_amount');
        $total_recharge = number4(moneyUnitTransferIn($total_recharge));
        return $total_recharge;
    }

    /**
     * 获取玩家当天的充值金额
     * @param $id
     * @return mixed
     */
    static function getTotalTodayRecharge($id)
    {
        // 2 , 3 状态是回调成功和人工成功
        $timeNow              = date("YmdHi", strtotime(Carbon::today('PRC')));
        $timeFuture           = date("YmdHi", strtotime(Carbon::tomorrow('PRC')));

        $total_today_recharge = self ::where("user_id", $id) -> whereBetween('day_m',[$timeNow,$timeFuture])-> whereIn('status',[Recharge::STATUS_CALLBACK_SUCCESS,Recharge::STATUS_MANUAL_SUCCESS]) -> sum('real_amount');
        $total_today_recharge = number4($total_today_recharge);
        return $total_today_recharge;
    }

    /**
     * 为了防止刷单，客户不能短时间内发起多笔充值
     * 充值次数和时间
     * @param $uid
     * @return mixed
     */
    static function getRechargeTime($uid)
    {
        $recharge_time   = self ::where("user_id", $uid) -> orderby('updated_at','desc') -> first();
        $recharge_number = self ::where("user_id", $uid) -> count('id');
        // 2019-12-13 16:18:40
        $data = [
            'recharge_time'   => empty($recharge_time->updated_at)?(strtotime(date("Y-m-d H:i:s"))-60):strtotime($recharge_time->updated_at),
            'recharge_number' => $recharge_number,
        ];
        return $data;
    }

    /**
     * 获取玩家某天的成功充值数据
     * @param $playerId
     * @param $day
     * @return mixed
     */
    static function getPlayerTotalRechargeByDay($playerId, $day) {
        // 2 , 3 状态是回调成功和人工成功
        $timeNow              = date("YmdHi", strtotime($day));
        $timeFuture           = date("YmdHi", strtotime($day) + 86400);

        $total_today_recharge = self ::where("user_id", $playerId) -> whereBetween('day_m', [$timeNow, $timeFuture])-> whereIn('status', [Recharge::STATUS_CALLBACK_SUCCESS, Recharge::STATUS_MANUAL_SUCCESS]) -> sum('real_amount');
        $total_today_recharge = number4($total_today_recharge);
        return $total_today_recharge;
    }

    /**
     * 获取下拉列表--支付方式
     * @param $partnerSign
     * @return array
     */
    static function getChannelSignList($partnerSign)
    {
        $query = self ::select(
            DB ::raw('finance_platform_channel.channel_sign'),
            DB ::raw('finance_platform_channel.channel_name'),
            DB ::raw('finance_platform_channel.type_sign')
        )
            -> leftJoin('finance_platform_channel', 'finance_platform_channel.channel_sign', 'user_recharge.channel')
            -> groupBy('finance_platform_channel.type_sign');

        if (isset($partnerSign) && !empty($partnerSign)){
            $data = $query->where('user_recharge.partner_sign',$partnerSign)->get();
        }else{
            $data = $query->get();
        }

        $options = [];
        foreach ($data as $_data) {
            $_data->type_sign           = str_ireplace('_wap','',$_data->type_sign);
            $_data->channel_name        = str_ireplace('H5','',$_data->channel_name);
            $options[$_data->type_sign] = $_data->channel_name;
        }
        return $options;
    }
}
