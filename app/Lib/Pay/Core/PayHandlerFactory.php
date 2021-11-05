<?php

namespace App\Lib\Pay\Core;

/**
 * Class PayHandlerFactory
 * @package App\Pay\Core
 */
class PayHandlerFactory
{
    public static $instence;

    /**
     * @return PayHandlerFactory
     */
    public static function getInstence()
    {
        if (!isset(self ::$instence)) {
            self ::$instence = new self();
        }
        return self ::$instence;
    }

    /**
     * @param string $className 类名称.
     * @param array $params 实例化的参数.
     * @return mixed
     */
    private function generateClass(string $className, ?array $params)
    {
        return [new $className($params),new \App\Lib\Pay\BasePay($params)];
    }

    /**
     * 生成支付方式的Handle
     * @param string $payName 支付方式标记.
     * @param array $params 实例化前的参数.
     * @return mixed
     */
    public function makePaymentHandler(string $payName, ?array $params)
    {
        return $this -> generateClass('\App\\Lib\\Pay\\' . ucfirst($payName), $params);
    }
}
