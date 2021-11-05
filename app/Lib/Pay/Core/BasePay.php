<?php

namespace App\Lib\Pay\Core;

use App\Lib\Help;
use App\Models\Finance\FinancePlatformAccount;
use Illuminate\Support\Facades\Log;

abstract class BasePay
{
    protected $payInfo = [
        'merchant_code' => null,    //商户号
        'merchant_secret' => null, //密钥
        'public_key' => null,
        'private_key' => null,
        'app_id' => null,
        'gateway' => null,
        'callback_url' => null,
        'platform_domain_url' => null,
        'return_url' => null,
        'bank_sign' => null,
        'card_number' => null,
        'card_username' => null,
        'order_no' => null,
        'amount' => null,
        'partner_sign' => null,
        'source' => null,
        'user_level' => null,
        'client_channel' => null,
        'channel' => null,
    ];

    protected $verifyRes = [
        'flag' => false,
        'back_param' => 'success',
        'mer_order_no' => null,
        'plat_order_no' => null,
        'amount' => null,
        'real_amount' => null,
    ];

    protected $ajaxReturn = [
        'code' => 200,
        'msg' => 'xxx',
        'pay_info' => [
            'order_id' => null,
            'money' => null,
            'pay_link' => null,
        ],
    ];

    public function __construct(array $params)
    {
        //$channel = FinancePlatformChannel::where('channel_sign',$params['partner_sign'])->first();
        $account = FinancePlatformAccount ::where('partner_sign', $params['partner_sign']) -> first();

        if ($account == null) {
            return Help ::returnApiJson('该商户还没有创建支付账户', 0);
        }
        $this -> payInfo['channel'] = $params['channel'] ?? '';
        $this -> payInfo['merchant_code'] = $account -> merchant_code ?? '';
        $this -> payInfo['merchant_secret'] = $account -> merchant_secret ?? '';
        $this -> payInfo['public_key'] = $account -> public_key ?? '';
        $this -> payInfo['private_key'] = $account -> private_key ?? '';
        $this -> payInfo['app_id'] = $account -> app_id ?? '';
        $this -> payInfo['gateway'] = $account -> gateway ?? '';
        $this -> payInfo['callback_url'] = $account -> callback_url ?? '';
        $this -> payInfo['platform_domain_url'] = $account -> platform_domain_url ?? '';
        $this -> payInfo['return_url'] = configure('redirect_url')??'';
        $this -> payInfo['bank_sign'] = $params['bank_code'] ?? 'https://api.cqvip9.com';
        $this -> payInfo['card_number'] = $params['card_number'] ?? '';
        $this -> payInfo['card_username'] = $params['card_username'] ?? '';
        $this -> payInfo['order_no'] = $params['order_no'] ?? '';
        $this -> payInfo['amount'] = $params['amount'] ?? '';
        $this -> payInfo['partner_sign'] = $params['partner_sign'] ?? '';
        $this -> payInfo['source'] = $params['source'] ?? '';
        $this -> payInfo['user_level'] = $params['user_level'] ?? '1';
        $this -> payInfo['client_channel'] = $params['client_channel'] ?? '';
        Log ::channel('pay') -> info($this -> payInfo);
    }

    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * @param array|null $data 回调参数.
     * @return array
     */
    abstract public function verify(?array $data): array;
}
