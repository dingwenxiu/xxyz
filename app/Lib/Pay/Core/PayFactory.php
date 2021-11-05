<?php


namespace App\Lib\Pay\Core;


class PayFactory
{
    public static $instence;

    /**
     * @return PayFactory
     */
    public static function getInstence()
    {
        if(!isset(self::$instence)){
            self::$instence = new self();
        }
        return self::$instence;
    }

    /**
     * @param string $className
     * @param array|null $params
     * @return mixed
     */
    private function getClass(string $className, ?array $params)
    {
        return new $className($params);
    }

    /**
     * 生成支付方式的Handle
     * @param string $payName
     * @param array|null $params
     * @return mixed
     */
    public function makePaymentHandler(string $payName, ?array $params)
    {
        return $this->getClass('\\App\\Lib\\Pay\\'.ucfirst($payName),$params);
    }

}
