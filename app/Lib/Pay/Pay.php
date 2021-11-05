<?php

namespace App\Lib\Pay;

use Illuminate\Support\Facades\Log;

class Pay
{
    protected $partner    = null;
    protected $handle     = null;
    protected $sign       = null;
    // 充值参数
    public $rechargeOrder = null;
    public $rechargeUser  = null;

    public function getHandle($sign)
    {
        $class = "\\App\\Lib\\Pay\\" . ucfirst($sign);

        if (!class_exists($class)) {
            return "支付方式-{$sign}-不存在!";
        }

        // 获取玩法对象
        $this -> handle = new $class();
        return $this -> handle;
    }

    /**
     * 获取支付渠道
     * @param  $user
     * @param string $source
     * @return array
     */
    public function getRechargeChannel($user, $source = 'phone')
    {
        $params = [
            'merchant_id' => $this -> constant['merchantId'],
            'client_ip'   => real_ip(),
            'source'      => $source,
            'username'    => $user -> username,
            'user_level'  => 1,
        ];
        Log ::channel('pay-recharge') -> info('recharge-channel:【充值通道，参数传递】' . json_encode($params));
        $params['sign'] = $this -> encrypt($params, $this -> constant['key']);
        $result         = json_decode(curl_post($this -> constant['recharge_channel_url'], $params), true);

        Log ::channel('pay-recharge') -> info('recharge-channel:【充值通道请求返回】', $result);
        if ($result['status']) {
            return $result['data'];
        }
        return [];
    }
}
