<?php namespace App\Lib\Logic;

use App\Lib\Clog;

/**
 * cache 必须支持 tags
 * Class AccountGiftLocker
 * @package App\Lib
 */
class AccountGiftLocker {

    static $tag = "account_gift_lock";

    // 缓存
    protected $memKey       = "";
    protected $memValue     = "";
    protected $prefix       = "account_gift_lock_";

    protected $context      = [];

    // 时间
    protected $cacheTimeout     = 2;  // 秒
    protected $lockerTimeout    = 3;  // 秒

    // 睡眠时间 目前支持微妙
    protected $sleepSeconds     = 300000; // 300毫秒

    public function __construct($playerId, $context = "", $cacheTimeout = 2, $lockerTimeout = 3, $sleep = 500000) {
        $this->memKey           = $this->prefix . $playerId;
        $this->memValue         = $playerId . "_" .  date("Y-m-d H:i:s") . "-" . $context;

        $this->cacheTimeout     = $cacheTimeout;
        $this->lockerTimeout    = $lockerTimeout;
        $this->sleepSeconds     = $sleep;
    }

    // 获取锁
    public function getLock() {

        $time = time();

        while (time() - $time < $this->lockerTimeout) {

            if(cache()->tags(self::$tag)->add($this->memKey, $this->memValue, now()->addSeconds($this->cacheTimeout))) {
                return true;
            }

            usleep($this->sleepSeconds);
        }

        Clog::lockError("账户礼金锁-获取锁失败-" . $this->memKey, ['context' => cache()->tags(self::$tag)->get($this->memKey)]);
        // 释放
        $this->release();
        return false;
    }

    // 释放当前
    public function release() {

        try {
            $ret = cache()->tags(self::$tag)->forget($this->memKey);
        } catch (\Exception $e) {
            Clog::lockError("账户礼金锁-释放锁失败-" . $e->getMessage(), $this->context);
            $ret = false;
        }

        return $ret;
    }

    // 释放所有
    static function releaseAll() {
        cache()->tags(self::$tag)->flush();
    }
}
