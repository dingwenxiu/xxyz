<?php

namespace App\Lib\Pay;

use Curl\Curl;
use App\Lib\Clog;
use App\Lib\Help;
use App\Models\Finance\Withdraw;
use App\Models\Finance\WithdrawLog;
use Illuminate\Support\Facades\Log;
use App\Models\Finance\RechargeLog;
use App\Models\Finance\UserRecharge;
use Illuminate\Support\Facades\Validator;
use App\Models\Finance\FinancePlatformAccount;
use App\Models\Finance\FinancePlatformAccountChannel;

class Panda extends BasePay
{

    public $sign = "panda";
    public $configure = "";


    /**
     * 过滤参数
     * @var array
     */
    public $rechargeCallbackNeedParams = [
        'pay_order_id'      => "required|min:4,max:32",
        'game_order_id'     => "required|min:4,max:32",
        'money'             => "required|numeric|regex:/^[0-9]+(.[0-9]{1,2})?$/",
        'sign'              => "required|min:6,max:64",
    ];

    /**
     * @var array
     */
    public $constant = [
        'key' => '1',
        'withdrawal_url' => '1',
        'merchantId' => '1',
    ];


    /**
     * Panda constructor.
     */
    public function __construct()
    {

    }

    /**
     *
     */
    public function renderSuccess()
    {
        echo "success";
        die;
    }

    /**
     *
     */
    public function renderFail()
    {
        echo "fail";
        die;
    }

    /**
     * 签名和发起操作
     * @return mixed|void
     * @throws \ErrorException 异常.
     */
    public function handle($payInfo)
    {
        $this->payInfo = $payInfo->payInfo;
        //1.组装数据
        $postData = [
            'merchant_id'    => $this->payInfo['merchant_code'],                             // 商户号
            'amount'         => sprintf('%d', $this->payInfo['amount']) . '.' . '00', // 金额，保留两位小数
            'order_id'       => $this->payInfo['order_no'],                                  // 订单号
            'source'         => $this->payInfo['source'],                                    // 来源(web/phone)
            'client_channel' => $this->payInfo['client_channel'],                            // 获取充值渠道接口返回的id(id指定渠道)
            'username'       => $this->payInfo['partner_sign'],                              // 商户平台用户名 (每个用户唯一)
            'user_level'     => $this->payInfo['user_level'],                                // 商户平台用户级别 1～10 |
            'channel'        => $this->payInfo['channel'],                                   // 渠道((参考渠道列表)) |
            'callback_url'   => $this->payInfo['callback_url'],                              // 异步回调地址
            'return_url'     => $this->payInfo['callback_url'],                              // 同步通知地址
            'client_ip'      => real_ip(),                                                   // ip
            'bank_sign'      => $this->payInfo['bank_sign'],                                 // 银行代码(参考银行标识) 个别渠道时必填
            'time'           => time(),                                                      // 10位 unix时间戳
        ];

        //2.生成签名
        $key = $this -> payInfo['merchant_secret'];
        $postData['sign'] = $this -> encrypt($postData, $key);
        Log ::channel('post-data') -> info($postData);

        //3.发起请求
        $requestRes = (new Curl()) -> post($this -> payInfo['gateway'], $postData);
        $requestRes = json_decode(json_encode($requestRes), true);
        //记录日志
        Log ::channel('request_res') -> info($requestRes);
        if ($requestRes['status'] === 'success') {
            return redirect($requestRes['data']['pay_url']);
            //return $requestRes['data'];
        } else {
            return $requestRes['msg'] ?? '通道异常';
        }
    }


    /**
     **获取支付渠道
     * @param string $source //来源(web/phone)
     * @param $account //获取account信息
     * @return array
     * @throws \ErrorException
     */
    public function getRechargeChannel($account, $source = "phone")
    {
        $params = [
            'client_ip'   => real_ip(),                    // IP地址
            'merchant_id' => $account -> merchant_code,    // 商户号
            'source'      => $source,                      // 来源(web/phone)
            'user_level'  => $account -> user_level??'',   // 商户等级
            'username'    => $account -> username??'',     // 商户平台用户名 (每个用户唯一)
        ];
        $gateway = "https://api.cqvip9.com/v1_beta/foreign_channel";
        //生成支付渠道，参数传递日志
        Clog ::rechargeLog("foreign_channel:【支付渠道，参数传递】" . json_encode($params, true));
        $params['sign'] = $this -> encrypt($params, $account -> merchant_secret);
        $result         = $this -> curlPost($gateway, $params);
        //生成获取支付渠道日志
        Clog ::rechargeLog("支付渠道-account:", $result);
        //判断是否获取支付渠道成功
        if ($result['status']) {
            if (isset($result['status']) && $result['status'] === "success") {
                $data = $result["data"];
                return $data['data'];
            }
        }
        return [];
    }

    /**
     * 充值下单
     * @param string $from
     * @return mixed
     * @throws \Exception
     */
    public function recharge($from)
    {
        $account = FinancePlatformAccount::find($this->rechargeChannel->account_id);
        if (!isset($account) && empty($account)){
            return "对不起, 未知错误(0x0002)";
        }
        $callbackUrl    = $account -> callback_url;
        $gateway        = $account -> gateway;
        $merchantId     = $account -> merchant_code;
        if ($from == 1){
            $from = "phone";
        }else{
            $from = "web";
        }
        $amount                     = moneyUnitTransferOut($this->rechargeOrder->amount);
        $returnUrl                  = partnerConfigure($this->rechargeChannel->partner_sign,"finance_notify_url");
        $financeCallbackUrl         = partnerConfigure($this->rechargeChannel->partner_sign,"finance_callback_url");

        // 获取参数
        $param = [];
        $param['merchant_id']       = $merchantId;                                 // 商户号
        $param['amount']            = $amount;                                     // 金额，保留两位小数
        $param['order_id']          = $this->rechargeOrder->order_id;              // 订单号
        $param['source']            = $from;                                       // 来源(web/phone)
//        $param['source']            = "web";                                     // 来源(web/phone)
        $param['client_channel']    = $this->rechargeChannel->platform_channel_id; // 获取充值渠道接口返回的id(id指定渠道)
        $param['username']          = $this->rechargeChannel->username;            // 商户平台用户名 (每个用户唯一) |
        $param['user_level']        = $this->rechargeChannel->user_level;          // 商户平台用户级别 1～10 |
        $param['channel']           = $this->rechargeChannel->type_sign;           // 渠道((参考渠道列表)
        $param['callback_url']      = $callbackUrl??$financeCallbackUrl;           // 异步回调地址
        $param['return_url']        = $returnUrl;                                  // 同步通知地址
        $param['client_ip']         = real_ip();                                   // ip
        $param['bank_sign']         = $this->rechargeChannel->bank_sign ?? 'ICBC'; // 银行代码(参考银行标识) 个别渠道时必填
        $param['time']              = time();                                      // 10位 unix时间戳
        $key                        = $account -> merchant_secret;

        // 加密获取sign
        $param['sign'] = $this -> encrypt($param, $key);

        Clog ::rechargeLog("发起请求前面参数", $param);

        // 设置充值参数
        $this->setRechargeParams($param);
        $this -> initRechargeLog();

        $result = $this->curlPost($gateway, $param, ['time_out' => 15]);

        Clog ::rechargeLog("发起请求后--返回数据数", $result);

        // 文件日志
        $logData = [
            'params'    => $param,
            'url'       => $gateway,
            'result'    => $result
        ];

        if ($result['status'] === 'success') {
            $data = $result['data'];
            if ($data['status'] == "success") {
                $this -> updateRechargeLog(['request_status' => 1, 'request_reason' => "发起充值请求成功", "request_back" => json_encode($result['data'])]);
                return [
                    'url'           => $data['data']->pay_url,
                    'pay_order_id'  => $data['data']->pay_order_id, // 第三方订单号
                    'request_model' => 1,                           // 支付的请求方式 1 jump 2 json
                    'amount'        => $param['amount'],
                    'order_id'      => $param['order_id'],
                    'result'        => $result
                ];
            } else {
                Clog ::rechargeLog("Error-Panda-{$data['msg']}-", $logData);
                $this -> updateRechargeLog(['request_status' => -1, 'request_reason' => $data['msg']]);
                return $data['msg'];
            }
        } else {
            $this -> updateRechargeLog(['request_status' => -2, 'request_reason' => $result['msg']]);
            Clog ::rechargeLog("Error-Panda-{$result['msg']}-", $logData);
        }

        $this->updateRechargeLog(['request_status' => -2, 'request_reason' => $result['msg']]);
        return isset($result['msg']) ? $result['msg'] : "对不起, 未知错误(0x0002)";


    }

    /**
     * 由于每个第三方接受的值都不一样， 此方法只处理接收的字段名称，外层会有try catch 捕获异常
     * @return array 订单号, 三方订单号, 金额
     */
    public function receive()
    {
        $body    = file_get_contents("php://input");
        $params  = json_decode($body, true);
        $orderId = $params['game_order_id'];
        $trxId   = $params['game_order_id'];
        $amt     = $params['money'];
        if (isset($params['status']) && $params['status'] === 1) {
            return [$orderId, $trxId, $amt];
        }
        echo 'invalid';
        exit;
    }

    /**
     * 接受的回调地址参数是否合法
     * @return bool
     * @throws \Exception
     */
    public function checkRechargeCallbackParams()
    {
        $params = $this -> rechargeCallbackParams;
        if (!is_array($params)) {
            return false;
        }
        Clog ::rechargeCallback("panda", "checkRechargeCallbackParams", $params);

        $needParams = $this -> rechargeCallbackNeedParams;
        $validator  = Validator ::make($params, $needParams);
        if ($validator -> fails()) {
            return $validator -> errors() -> first();
        }
        return true;
    }

    /**
     * @param $params
     * @return bool|int
     */
    public function checkParamsIfIsArray($params)
    {
        if (!is_array($params) || !isset($params) || empty($params)) {
            return "对不起,没有回调参数";
        }

        // 平台订单号
        if (!isset($params['pay_order_id'])  || empty($params['pay_order_id']) ){
            return "对不起,支付订单号不存在";
        }

        // 发起请求的订单号
        if (!isset($params['game_order_id']) ||empty($params['game_order_id']) ){
            return "对不起,订单号不存在";
        }

        if (!isset($params['status'])        || empty($params['status']) ){
            return "对不起,支付状态不存在";
        }

        if (!isset($params['money'])         || empty($params['money']) ){
            return "对不起,支付金额不存在";
        }

        if (!isset($params['time'])          || empty($params['time']) ){
            return "对不起,支付时间不存在";
        }

        if (!isset($params['sign'])          || empty($params['sign']) ){
            return "对不起,支付sign不存在";
        }

        return true;
    }

    /**
     * 订单处理
     * @return bool|string
     * @throws \Exception
     */
    public function processOrder()
    {
        $data = $this -> rechargeCallbackParams;

        // 检查订单
        $order = $this -> rechargeOrder;

        if (!in_array($order -> status, [0, 1], true)) {
            Clog ::rechargeCallback("panda", "订单已经处理-" . $order -> status);
            return "订单已经处理-" . $order -> status;
        }

        // 检测金额
        $amount = intval($data['money'] * 10000);
        if ($order -> amount != $amount) {
            Clog ::rechargeCallback("panda", "订单金额不符合-" . $order -> amount, $data);
            return "订单金额不符合-" . $order -> amount;
        }

        // 处理订单
        $res = $order -> process($amount, 0, "");
        if (true !== $res) {
            Clog ::rechargeCallback("panda", "处理订单失败:$res");
            return $res;
        }

        return true;
    }

    protected $platform_sign = null;

    // 渠道id
    public function setSign($sign)
    {
        $this -> platform_sign = $sign;
        return $this;
    }

    // key
    public function setKey($setKey)
    {
        $this -> constant['key'] = $setKey;
        return $this;
    }

    // 设置卡号信息
    public function setWithdrawCard($card)
    {
        $this -> WithdrawCard  = $card;
    }


    /**
     * 发起提现操作
     * @param $iBankId
     * @param $sCompanyOrderNum
     * @param $fAmount
     * @param $sCardNum
     * @param $sCardName
     * @param $platformChannelId
     * @return array
     * @throws \ErrorException
     */
    public function withdrawal($iBankId, $sCompanyOrderNum, $fAmount, $sCardNum, $sCardName, $platformChannelId)
    {
        $key       = $this -> constant['key'];
        $url       = $this -> constant['withdrawal_url'];
        $merchant  = $this -> constant['merchantId'];
        $cBcakUrl  = $this -> constant['callback_url']??'';

        $params = [
            'merchant_id'   => $merchant,
            'amount'        => number4(moneyUnitTransferIn($fAmount)),
            'order_id'      => $sCompanyOrderNum,
            'source'        => 'web',
            'platform_sign' => $platformChannelId??'',
            'username'      => $this->withdrawOrder['username']??'',
            'callback_url'  => $cBcakUrl??'',
            'bank_sign'     => $iBankId,

            'card_number'   => $sCardNum,
            'card_username' => $sCardName,
            'card_branch'   => $this -> WithdrawCard['branch']??'',
            'card_province' => $this -> WithdrawCard['province_id']??'',
            'card_city'     => $this -> WithdrawCard['city_id']??'',
            'client_ip'     => real_ip(),

        ];
        $params['sign'] = $this -> encrypt($params, $key);

        $this -> setWithdrawParams($params);
        $this -> initWithdrawLog();

        // 文件日志
        $logData = [
            'url'  => $url,
            'post' => $params,
        ];

        Clog ::withdrawLog("Panda-准备发起请求-", $logData);

        $data['data'] = BasePay ::curlPost($url, $params);

        // 文件日志
        $logData = [
            'response' => $data,
        ];

        Clog ::withdrawLog("Panda-请求结果-", $logData);
        //
        if ($data['data']['status'] === 'success') {
            if ($data['data']['data']["status"] == 'success') {
                // 代发成功
                Clog ::withdrawLog("Success-Panda-{$data['data']['msg']}-", $logData);
                $this -> updateWithdrawLog(['request_status' => 3,'back_status' => 3, 'request_reason' => $data["data"]['msg'],"request_back" => json_encode($data["data"])]);
                return ['status' => true, 'msg'  => $data["data"]['msg'], 'data' => []];
            }

            // 代发失败
            Clog ::withdrawLog("Error-Panda-{$data['data']['msg']}-", $logData);
            $this -> updateWithdrawLog(['request_status' => -3,'back_status' => -3, 'request_reason'  => $data["data"]['msg']]);
            return ['status'     => false, 'msg' => $data["data"]['msg'], 'data' => []];
        }

        Clog ::withdrawLog("Error-Panda-{$data['data']['msg']}-", $logData);
        $this -> updateWithdrawLog(['request_status' => -3,'back_status' => -3, 'request_reason' => $data['data']['status']]);
        return ['status'         => false, 'msg' => $data['data']["msg"], 'data' => $data['data']["msg"]];
    }

    /**
     * 查看订单状态
     * @param $oWithdrawal
     * @param null $channel
     * @return array
     * @throws \Exception
     */
    public function queryWithdrawOrderStatus($oWithdrawal, $channel = null)
    {
        $paccount = FinancePlatformAccount::where('partner_sign',$this->withdrawOrder->partner_sign)
            ->first();
        $this -> constant['key']            = $paccount->merchant_secret;
        $this -> constant['withdrawal_url'] = 'https://api.cqvip9.com/v1_beta/payment_query';
        $this -> constant['merchantId']     = $paccount->merchant_code;

        $key      = $this -> constant['key'];
        $url      = $this -> constant['withdrawal_url'];
        $merchant = $this -> constant['merchantId'];
        $params = [
            'merchant_id' => $merchant,
            'order_id'    => $oWithdrawal -> order_id,
            'client_ip'   => real_ip(),
        ];

        $params['sign'] = $this -> encrypt($params, $key);

        $this -> setWithdrawQueryParams($params);

        $returnData = $this -> curlPost($url, $params);
        $this -> updateWithdrawQueryLog(['content' => json_encode($returnData)]);

        Clog ::withdrawQuery("请求结果", $returnData);

        if (isset($returnData["status"])) {
            if ($returnData['status'] == "success") {
                $data = $returnData['data'];
                $this -> updateWithdrawQueryLog(['back_status' => 4, 'back_reason' => "成功"]);
                return [ 'status' => 1, 'amount' => $data['data']->amount];
            } else {
                $this -> updateWithdrawQueryLog(['back_status' => 4, 'back_reason' => $returnData["msg"]]);
                return ['status' => -1, 'msg' => $returnData["msg"]];
            }
        } else {
            $this -> updateWithdrawQueryLog(['back_status' => -4, 'back_reason' => $returnData["msg"]]);
            return ['status' => -1, 'msg' => $returnData["msg"]];
        }
    }

    /**
     * 异步验签方法
     * @param string $keySign
     * @return bool
     */
    public function checkRechargeCallbackSign($keySign = 'sign')
    {
        $rParams = $this -> rechargeParams;
        $cParams = $this -> rechargeCallbackParams;

        $key     = $this -> constant['key'];
        //判断签名是否一致
        $mySign  = $this -> encrypt($cParams, $key);
        if (isset($rParams[$keySign]) && !empty($rParams[$keySign]) && $rParams[$keySign] == $mySign) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function checkRechargeCallbackOrder()
    {
        $cOrder = $this->rechargeCallbackParams;
        $rOrder = $this -> rechargeOrder;

        if (
            isset($cOrder['pay_order_id']) &&
            isset($rOrder['pay_order_id']) &&

            !empty($cOrder['pay_order_id']) &&
            !empty($rOrder['pay_order_id']) &&

            $rOrder['pay_order_id'] == $cOrder['pay_order_id']
        ) {
            return true;
        }
        return false;
    }


    /**
     * 检查线路是否正常
     * @param $result
     * @param null $info
     * @return array|null
     */
    public function check_curl($result, $info = null)
    {
        if (!empty($info) && $info['http_code'] != 200) {
            return ['status' => -5, 'msg' => '线路异常，无法获取交易结果', 'data' => '000'];
        }
        if ($result === false) {
            return ['status' => -5, 'msg' => '线路异常，无法获取交易结果', 'data' => '000'];
        }
        return null;
    }

    /**
     * 判断是否为空
     * @param $value
     * @return bool
     */
    static public function isEmpty($value)
    {
        return $value === null || $value === [] || $value === '';
    }

    /**
     * 函数加密
     * @param $data //输入参数
     * @param $signKey //signKey
     * @return string
     */
    static function encrypt($data, $signKey)
    {
        $str = "";

        ksort($data);

        foreach ($data as $key => $value) {

            if ('sign' == $key || $value === '') {
                continue;
            }

            $str .= $key . "=" . $value . "&";

        }

        $str .= "key={$signKey}";

        return md5($str);
    }

    /**
     * 查询代付订单
     * @param $merchant_id //商户号
     * @param $order_id //订单号
     * @param $channel //渠道((参考渠道列表))
     * @return array
     * @throws \ErrorException
     */
    public function payment_query($merchant_id, $order_id, $channel)
    {
        $url                  = 'https://api.cqvip9.com/v1_beta/payment_query';
        $param = [];
        $param['merchant_id'] = $merchant_id;//商户号
        $param['order_id']    = $order_id;//订单号
        $param['client_ip']   = real_ip();//IP
        $key                  = $channel -> merchant_secret;
        $param['sign']        = $this -> encrypt($param, $key);//sign
        $this -> setRechargeParams($param);
        $result               = $this -> curlPost($url, $param, ['time_out' => 15]);

        // 文件日志
        $logData = [
            'params' => $param,
            'url'    => $url,
            'result' => $result
        ];

        if ($result['status']) {
            $data = $result['data'];
            if ($data['status'] == "success") {
                $this -> updateRechargeLog(['request_status' => 1, 'request_reason' => "查询代付订单成功", "payment_query" => json_encode($result['data'])]);
                return ['url' => $data['data']['pay_url'], 'type' => "url"];
            } else {
                Clog ::rechargeLog("Error-Panda-{$data['msg']}-", $logData);
                $this -> updateRechargeLog(['request_status' => 2, 'request_reason' => $data['msg']]);
                return $data['msg'];
            }
        } else {
            $this -> updateRechargeLog(['request_status' => 2, 'request_reason' => $result['msg']]);
            Clog ::rechargeLog("Error-Panda-{$result['msg']}-", $logData);
        }

        $this -> updateRechargeLog(['request_status' => 2, 'request_reason' => $result['msg']]);

        return $result['msg'];
    }

    /**
     * 获取代付渠道
     * @param $merchant_id //商户号
     * @param $channel //渠道((参考渠道列表)
     * @return array
     * @throws \ErrorException
     */
    public function payment_channel($merchant_id, $channel)
    {
        $url                  = 'https://api.cqvip9.com/v1_beta/payment_channel';
        $param                = [];
        $param['merchant_id'] = $merchant_id;//商户号
        $key                  = $channel -> merchant_secret;
        $param['sign']        = $this -> encrypt($param, $key);//sign
        $this -> setRechargeParams($param);
        $result               = $this -> curlPost($url, $param, ['time_out' => 15]);

        // 文件日志
        $logData = [
            'params' => $param,
            'url'    => $url,
            'result' => $result
        ];

        if ($result['status']) {
            if ($result['status'] == "success") {
                $data = $result['data'];
                return $data['data'];
            } else {
                Clog ::rechargeLog("Error-Panda-{$result['msg']}-", $logData);
                return [];
            }
        } else {
            $this -> updateRechargeLog(['request_status' => 2, 'request_reason' => $result['msg']]);
            Clog ::rechargeLog("Error-Panda-{$result['msg']}-", $logData);
            return [];
        }
    }

    /**
     * 代付订单
     * @param $amount //金额
     * @param $merchant_id //商户号
     * @param $channel //获取信息
     * @param $order_id //订单ID
     * @param string $source //来源(web/phone)
     * @return array
     * @throws \ErrorException
     */
    public function payment($amount, $source = "web")
    {
        $url                    = 'https://api.cqvip9.com/v1_beta/payment';//修改
        $param                  = [];
        $param['merchant_id']   = $merchant_id;              // 商户号
        $param['amount']        = $amount;                   // 金额，保留两位小数
        $param['order_id']      = $order_id;                 // 订单号
        $param['source']        = $source;                   // 来源(web/phone)
        $param['platform_sign'] = $channel -> platform_sign; // 获取代付渠道接口返回的id
        $param['username']      = $channel -> username;      // 商户平台用户名 (每个用户唯一)
        $param['callback_url']  = $channel -> callback_url;  // 异步回调地址
        $param['bank_sign']     = $channel -> bank_sign;     // 银行代码(参考银行标识)
        $param['card_number']   = $channel -> card_number;   // 收款卡号
        $param['card_username'] = $channel -> card_username; // 收款人姓名
        $param['card_branch']   = $channel -> card_branch;   // 开户网点
        $param['card_province'] = $channel -> card_province; // 开户行所在省份名称
        $param['card_city']     = $channel -> card_city;     // 开户行所在城市名称
        $param['client_ip']     = real_ip();                 // IP

        $key           = $channel -> merchant_secret;
        $param['sign'] = $this -> encrypt($param, $key);//sign
        $this -> setRechargeParams($param);
        $result        = $this -> curlPost($url, $param, ['time_out' => 15]);

        // 文件日志
        $logData = [
            'params' => $param,
            'url'    => $url,
            'result' => $result
        ];

        if ($result['status']) {
            $data = $result['data'];
            if ($data['status'] == "success") {
                $this -> updateRechargeLog(['request_status' => 1, 'request_reason' => "获取代付渠道成功", "payment_query" => json_encode($result['data'])]);
                return ['url' => $data['data']['pay_url'], 'type' => "url"];
            } else {
                Clog ::rechargeLog("Error-Panda-{$data['msg']}-", $logData);
                $this -> updateRechargeLog(['request_status' => 2, 'request_reason' => $data['msg']]);
                return $data['msg'];
            }
        } else {
            $this -> updateRechargeLog(['request_status' => 2, 'request_reason' => $result['msg']]);
            Clog ::rechargeLog("Error-Panda-{$result['msg']}-", $logData);
        }

        $this -> updateRechargeLog(['request_status' => 2, 'request_reason' => $result['msg']]);

        return $result['msg'];
    }

    /**
     * 设置充值用户
     * @param $user
     */
    public function setRechargeUser($user)
    {
        $this -> rechargeUser = $user;
    }

    /**
     * 设置充值用户订单ID
     * @param $orderId
     */
    public function setRechargeOrderId($orderId)
    {
        $this -> RechargeOrderId = $orderId;
    }

    /**
     * 设置充值渠道
     * @param $oChannel
     */
    public function setRechargeChannel($oChannel)
    {
        $this -> rechargeChannel = $oChannel;
    }

    /**
     * 设置充值参数-回调
     * @param $params
     */
    public function setRechargeCallbackParams($params)
    {
        $this -> rechargeCallbackParams = $params;
    }

    /**
     * 生成签名所需的参数
     * @param array $data
     * @return string
     */
    private function getSign(array $data): string
    {
        ksort($data);
        $signClear = urldecode(http_build_query($data)) . '&key=' . $key  = $this -> constant['key'];
        Log ::channel('sign-clear') -> info($signClear);
        $sign = md5($signClear);
        Log ::channel('sign') -> info($signClear . '------' . $sign);
        return $sign;
    }

    /**
     * 设置充值参数
     * @param $params
     */
    public function setRechargeParams($params)
    {
        $this -> rechargeParams = $params;
    }

    /**
     * 熊猫支付-支付宝-接收异步回调-验证签名是否合法
     * @param array|null $data 回调参数.
     * @return array
     */
    public function verify(array $data): array
    {
        $cParams = $data['sign'];
        unset($data['sign']);
        $rSign   = $this -> getSign($data);

        if ($cParams === $rSign && (int)$data['status'] === 1) {
            $this -> verifyRes['back_param']        = 'success';
            $this -> verifyRes['order_money']       = $data['money'] ?? 0;
            $this -> verifyRes['real_money']        = $data['money'] ?? 0;
            $this -> verifyRes['merchant_order_no'] = $data['game_order_id'];
            $this -> verifyRes['pay_order_id']      = $data['pay_order_id'];
            return $this -> verifyRes;
        }
        return [];
    }

    public function withdrawVerify(array $data): array
    {
        $cParams = $this -> withdrawOrder;
        $nowSign = $data['sign'];
        unset($data['sign']);
        $rSign   = $this -> getSign($data);
        if ($rSign === $nowSign) {
            $this -> verifyRes['order_id']      = $cParams;
            return $this -> verifyRes;
        }
        return [];
    }

    /**
     *
     */
    public function getRechargeLog(){
        $uRecharge = UserRecharge::where('order_id', $this->RechargeOrderId)->first();
        if ($uRecharge) {
            $rLog  = RechargeLog::where('order_id',$uRecharge->id)->first();
            if (isset($rLog) && !empty($rLog)){
                return $rLog;
            }
        }
        return Help ::returnApiJson("对不起,充值订单不存在！", 0);
    }

    /**
     * @return bool
     */
    public function checkWithdrawCallbackOrder()
    {
        $qOrder = $this -> withdrawQueryParams; // 请求信息
        $wOrder = $this -> withdrawOrder;       // 回调订单号

        if (
            isset($wOrder) &&
            isset($qOrder['order_id']) &&

            !empty($wOrder) &&
            !empty($qOrder['order_id']) &&

            $wOrder == $qOrder['order_id']
        ) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function getWithdrawLog(){
        $uWithdraw = Withdraw::where('order_id', $this->withdrawOrder)->first();
        if ($uWithdraw) {
            $wLog  = WithdrawLog::where('order_id',$uWithdraw->id)->first();
            if (isset($wLog) && !empty($wLog)){
                return $wLog;
            }
        }
        return Help ::returnApiJson("对不起,提现订单不存在！", 0);
    }
}