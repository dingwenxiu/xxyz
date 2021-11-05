<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/16 0016
 * Time: 18:28
 */
namespace App\Lib\Pay\Unit;

class RsaUnit
{
    private $public_key;
    private $private_key;
    private $encryptData;
    private $decryptData;
    private $sign;
    /** 设置公钥
     * @param string $public_key
     * @return $this
     */
    public function setPublicKey($public_key = '')
    {
        $public_key = openssl_pkey_get_public($this->getPublicKey($public_key));
        if ($public_key === false) {
            echo '打开公钥出错';
            die;
        }
        $this->public_key = $public_key;
        return $this;
    }

    /** 设置私钥
     * @param string $private_key
     * @return $this
     */
    public function setPrivateKey($private_key = '')
    {
        $private_key_source = openssl_pkey_get_private($this->getPrivateKey($private_key));
        if ($private_key_source === false) {
            $private_key_source = openssl_pkey_get_private($this->getPrivateKeyAndRsa($private_key));
            if ($private_key_source === false) {
                echo '打开私钥出错';
                die;
            }
        }
        $this->private_key = $private_key_source;
        return $this;
    }
    private function getPublicKey($publicKey = '', $splitLength = 64)
    {
        $public_key = "-----BEGIN PUBLIC KEY-----\r\n";
        foreach (str_split($publicKey, $splitLength) as $string) {
            $public_key .= $string . "\r\n";
        }
        $public_key .='-----END PUBLIC KEY-----';
        return $public_key;
    }
    private function getPrivateKeyAndRsa($privateKey = '', $splitLength = 64)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\r\n";
        foreach (str_split($privateKey, $splitLength) as $string) {
            $private_key .= $string . "\r\n";
        }
        $private_key .='-----END RSA PRIVATE KEY-----';
        return $private_key;
    }
    private function getPrivateKey($privateKey = '', $splitLength = 64)
    {
        $private_key = "-----BEGIN PRIVATE KEY-----\r\n";
        foreach (str_split($privateKey, $splitLength) as $string) {
            $private_key .= $string . "\r\n";
        }
        $private_key .='-----END PRIVATE KEY-----';
        return $private_key;
    }

    /** 使用公钥进行加密
     * @param string $data
     * @param int $padding
     * @return $this
     */
    public function publicKeyToEncrypt($data = '', $padding = OPENSSL_PKCS1_PADDING)
    {
        if (!isset($this->public_key)) {
            echo 'ERROR_01:Please call method setPublicKey first and set public_key.';
        }
        $json = json_encode($data);
        $crypto = '';
        foreach (str_split($json, openssl_pkey_get_details($this->public_key)['bits']/8-11) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $this->public_key, $padding);
            $crypto = $crypto . $encryptData;
        }
        $this->encryptData = base64_encode($crypto);
        return $this;
    }

    /** 使用私钥进行加密
     * @param string $data
     * @param int $padding
     * @return $this
     */
    public function privateKeyToEncrypt($data = '', $padding = OPENSSL_PKCS1_PADDING)
    {
        if (!isset($this->private_key)) {
            echo 'ERROR_01:Please call method setPrivateKey first and set private_key.';
        }
        $json = json_encode($data);
        $crypto = '';
        foreach (str_split($json, openssl_pkey_get_details($this->private_key)['bits']/8-11) as $chunk) {
            openssl_private_encrypt($chunk, $encryptData, $this->private_key, $padding);
            $crypto = $crypto . $encryptData;
        }
        $this->encryptData = base64_encode($crypto);
        return $this;
    }

    /** 公钥解密
     * @param string $data
     * @param int $padding
     * @return $this
     */
    public function publicKeyToDecrypt($data = '', $padding = OPENSSL_PKCS1_PADDING)
    {
        $resData = base64_decode($data);
        if (!isset($this->public_key)) {
            echo 'ERROR_01:Please call method setPublicKey first and set public_key.';
        }
        $info = openssl_pkey_get_details($this->public_key);
        $decry = '';
        foreach (str_split($resData, $info['bits'] / 8) as $chunk) {
            openssl_public_decrypt($chunk, $decryData, $this->public_key, $padding);
            $decry = $decry . $decryData;
        }
        $finalData = json_decode($decry, true);
        $this->decryptData = $finalData;
        return $this;
    }

    /** 私钥解密
     * @param string $data
     * @param int $padding
     * @return $this
     */
    public function privateKeyToDecrypt($data = '', $padding = OPENSSL_PKCS1_PADDING)
    {
        if (!isset($this->private_key)) {
            echo 'ERROR_01:Please call method setPrivateKey first and set private_key.';
        }
        $resData = base64_decode($data);
        $info = openssl_pkey_get_details($this->private_key);
        $decry = '';
        foreach (str_split($resData, $info['bits'] / 8) as $chunk) {
            openssl_private_decrypt($chunk, $decryData, $this->private_key, $padding);
            $decry = $decry . $decryData;
        }
        $finalData = json_decode($decry, true);
        $this->decryptData = $finalData;
        return $this;
    }

    /** 获取密文
     * @param bool $debug
     * @return mixed
     */
    public function getEncryptData($debug = false)
    {
        if ($debug) {
            echo '<pre>';
            var_dump($this->encryptData);
            die;
        }
        return $this->encryptData;
    }

    /** 获取明文
     * @param bool $debug
     * @return mixed
     */
    public function getDecryptData($debug = false)
    {
        if ($debug) {
            echo '<pre>';
            var_dump($this->decryptData);
            die;
        }
        return $this->decryptData;
    }

    /** 私钥签名
     * @param string $signStr
     * @param int $signature_alg
     * @return $this
     */
    public function grantSignByPrivateKey($signStr = '', $signature_alg = OPENSSL_ALGO_SHA1)
    {
        if (!isset($this->private_key)) {
            echo 'ERROR_01:Please call method setPrivateKey first and set private_key.';
        }
        openssl_sign($signStr, $sign_info, $this->private_key, $signature_alg);
        $sign = base64_encode($sign_info);
        $this->sign = $sign;
        return $this;
    }

    /** 获取签名
     * @param bool $debug
     * @return mixed
     */
    public function getSign($debug = false)
    {
        if ($debug) {
            echo '<pre>';
            var_dump($this->sign);
            die;
        }
        return $this->sign;
    }

    /** 公钥验签
     * @param string $signStr
     * @param string $sign
     * @param int $signature_alg
     * @return bool
     */
    public function verifySignByPublicKey($signStr = '', $sign = '', $signature_alg = OPENSSL_ALGO_SHA1)
    {
        if (!isset($this->public_key)) {
            echo 'ERROR_01:Please call method setPublicKey first and set public_key.';
        }
        $flag = openssl_verify($signStr, $sign, $this->public_key, $signature_alg);
        if ($flag) {
            return true;
        }
        return false;
    }
}
