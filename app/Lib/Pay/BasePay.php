<?php

namespace App\Lib\Pay;

use Curl\Curl;
use App\Models\Finance\Withdraw;
use App\Models\Finance\RechargeLog;
use App\Models\Finance\WithdrawLog;
use Illuminate\Support\Facades\Log;

class BasePay
{
    // 充值参数
    public $rechargeCallbackParams  = [];
    public $rechargeChannel         = null;
    public $rechargeParams          = [];
    public $rechargeOrder           = null;
    public $rechargeUser            = null;
    public $rechargeLog             = null;

    // 提现参数
    public $withdrawQueryParams = [];
    public $withdrawParams      = [];
    public $withdrawOrder       = null;
    public $withdrawUser        = null;
    public $withdrawLog         = null;



    /**
     * 保存日志
     */
    public function initRechargeLog()
    {
        $order = $this -> rechargeOrder;
        $user = $this -> rechargeUser;
        $params = $this -> rechargeParams;
        $log = RechargeLog ::initLog($user, $order, $params);
        $this -> rechargeLog = $log;
    }

    /**
     * 更新充值日志
     * @param $data
     */
    public function updateRechargeLog($data)
    {
        RechargeLog ::where("id", $this -> rechargeLog -> id) -> update($data);
    }

    /**
     * 更新充值日志-回调
     * @param $data
     */
    public function updateCallbackLog($data)
    {
        $orderId = $this -> rechargeOrder -> id;
        RechargeLog ::where("order_id", $orderId) -> update($data);
    }

    /**
     * 设置充值订单
     * @param $order
     */
    public function setRechargeOrder($order)
    {
        $this -> rechargeOrder = $order;
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
     * 设置充值参数
     * @param $params
     */
    public function setRechargeParams($params)
    {
        $this -> rechargeParams = $params;
    }

    /**
     * 设置充值渠道
     * @param $oChannel
     */
    public function setRechargeChannel($oChannel)
    {
        $this->rechargeChannel = $oChannel;
    }

    /**
     * 设置充值参数-回调
     * @param $params
     */
    public function setRechargeCallbackParams($params)
    {
        $this -> rechargeCallbackParams = $params;
    }

    /** ========================= 提现日志 ====================== */

    /**
     * 保存日志
     */
    public function initWithdrawLog()
    {
        $order  = $this -> withdrawOrder;
        $user   = $this -> withdrawUser;
        $params = $this -> withdrawParams;


        $log = WithdrawLog ::initLog($user, $order, $params);
        $this -> withdrawLog = $log;
    }

    /**
     * 更新充值日志
     * @param $data
     */
    public function updateWithdrawLog($data)
    {
        WithdrawLog ::where("id", $this -> withdrawLog -> id) -> update($data);
    }

    /**
     * 更新充值列表
     * @param $data
     */
    public function updateWithdraw($data)
    {
        Withdraw ::where("id", $this -> withdrawUser -> id) -> update($data);
    }

    /**
     * 更新代发日志-查询
     * @param $data
     */
    public function updateWithdrawQueryLog($data)
    {
        $orderId = $this -> withdrawOrder -> id;
        WithdrawLog ::where("order_id", $orderId) -> update($data);
    }

    /**
     * 设置提现订单
     * @param $order
     */
    public function setWithdrawOrder($order)
    {
        $this -> withdrawOrder = $order;
    }

    /**
     * 设置提现订单
     * @param $order
     */
    public function setWithdrawLog($wLog)
    {
        $this -> withdrawLog = $wLog;
    }

    /**
     * 设置提现用户
     * @param $user
     */
    public function setWithdrawUser($user)
    {
        $this -> withdrawUser = $user;
    }

    /**
     * 设置提现参数
     * @param $params
     */
    public function setWithdrawParams($params)
    {
        $this -> withdrawParams = $params;
    }

    /**
     * 设置提现参数-回调
     * @param $params
     */
    public function setWithdrawQueryParams($params)
    {
        $this -> withdrawQueryParams = $params;
    }

    /**
     * curl 请求
     * @param $url
     * @param array $params
     * @return array
     * @throws \ErrorException
     */
    public function curlPost($url, $params = [])
    {
        $handle = new Curl();
        $handle -> setHeader('Content-Type', 'application/json');

        $handle -> setOpt(CURLOPT_FOLLOWLOCATION, 1);
        $handle -> setOpt(CURLOPT_AUTOREFERER, 1);
        $handle -> setOpt(CURLOPT_POST, true);
        $handle -> setOpt(CURLOPT_TIMEOUT, 15);
        $handle -> setOpt(CURLOPT_CONNECTTIMEOUT, 15);
        $handle -> setOpt(CURLOPT_RETURNTRANSFER, true);


        $handle -> post($url, $params);
        if ($handle -> error) {
            return [
                'status' => "fail",
                'msg' => $handle -> errorCode . ': ' . $handle -> errorMessage
            ];
        } else {
            if ($handle -> getHttpStatusCode() == 200) {
                if (isset($handle -> response -> status) && $handle -> response -> status === 'success') {
                    return [
                        'status' => "success",
                        "msg" => $handle -> response -> msg,
                        'data' => [
                            'status' => 'success',
                            'data' => $handle -> response -> data
                        ],
                    ];
                }
            }
        }
        return [
            'status' => "fail",
            'msg' => "错误码:" . $handle -> getHttpStatusCode().'-'."错误信息:" .$handle -> response -> msg
        ];
    }

    /**
     * 生成充值订单号
     * @return string
     * @throws \Exception
     */
    public static function createRechargeOrderNum(): string
    {
        $md5 = substr(md5(date('YmdHis')), -12);
        return 'R' . date('YmdHis') . $md5 . random_int(1000, 9999);
    }

    /**
     * 生成提现订单号
     * @return string
     * @throws \Exception
     */
    public static function createWithdrawOrderNum(): string
    {
        $md5 = substr(md5(date('YmdHis')), -12);
        return 'W' . date('YmdHis') . $md5 . random_int(1000, 9999);
    }

    /**
     * 获取回调地址
     * @param $sign
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getCallbackUrl($sign)
    {
        return config('pay.' . $sign . '.callback_url');
    }

    /**
     * 获取通知地址
     * @param $sign
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getNotifyUrl($sign)
    {
        return config('pay.' . $sign . '.notify_url');
    }

    public static function curl_get($url){

        $testurl = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $testurl);
        //参数为1表示传输数据，为0表示直接输出显示。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //参数为0表示不带头文件，为1表示带头文件
        curl_setopt($ch, CURLOPT_HEADER,0);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
