<?php namespace App\Lib\Logic\Cache;

use App\Lib\Clog;
use App\Lib\Logic\Lottery\LotteryLogic;
use App\Models\Game\Lottery;
use App\Models\Partner\PartnerLottery;
use App\Models\Partner\PartnerMethod;

/**
 * Tom 2019 整理 用户缓存
 * Class UserCache
 * @package App\Lib
 */
class LotteryCache
{

    public static $prefix   = "lottery_";
    public static $expired  = 12;
    public static $store    = "redis";

    /**
     * 刷新商户的所有彩种相关的配置
     * @param $partnerSign
     * @return bool|string
     */
    static function flushPartnerAll($partnerSign) {
        $key    = self::$prefix . "_all_" . $partnerSign;
        $tag    = "lottery_" . $partnerSign;
        $cache  = cache()->store(self::$store);

        return $cache->tags($tag)->forget($key);
    }

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getPartnerAllLottery($partnerSign = "system") {
        $key    = self::$prefix . "_all_" . $partnerSign;
        $tag    = "lottery_" . $partnerSign;

        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $lotteryData = PartnerLottery::getAllLotteriesFromDb($partnerSign);
        if ($lotteryData) {
            $cache->tags($tag)->forever($key, $lotteryData);
            return $lotteryData;
        }

        return [];
    }

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function flushPartnerAllLottery($partnerSign = "system") {
        $key    = self::$prefix . "_all_" . $partnerSign;
        $tag    = "lottery_" . $partnerSign;

        $cache  = cache()->store(self::$store);
        return $cache->tags($tag)->forget($key);
    }


    /**
     * 获取制定彩种
     * @param $lotterySign
     * @param $partnerSign
     * @return array|mixed
     * @throws \Exception
     */
    static function getPartnerLottery($lotterySign, $partnerSign = "system") {
        $key    = self::$prefix . "_one_" . $lotterySign;
        $tag    = "lottery_" . $partnerSign;


        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        if ($partnerSign == "system") {
            $lotteryData = Lottery::findBySign($lotterySign);
        } else {
            $lotteryData = PartnerLottery::getLotteryFromDb($partnerSign, $lotterySign);
        }

        // 彩种
        if ($lotteryData) {
            $cache->tags($tag)->forever($key, $lotteryData);
            return $lotteryData;
        }

        return [];
    }

    /**
     * 刷新商户彩种
     * @param $lotterySign
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function flushPartnerLottery($lotterySign, $partnerSign = "system") {
        $key    = self::$prefix . "_one_" . $lotterySign;
        $tag    = "lottery_" . $partnerSign;
        $cache  = cache()->store(self::$store);

        return $cache->tags($tag)->forget($key);
    }

    /**
     * 获取商户 某一个彩种下的 所有玩法
     * @param string $lotterySign
     * @param string $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getPartnerLotteryAllMethodConfig($lotterySign, $partnerSign = "system") {
        $key    = "mc_all_" . $lotterySign;
        $tag    = "lottery_" . $partnerSign;

        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $methodAllConfig = PartnerMethod::getLotteryMethodConfigFromDb($partnerSign, $lotterySign);
        if ($methodAllConfig) {
            $cache->tags($tag)->forever($key, $methodAllConfig);
            return $methodAllConfig;
        }

        return [];
    }

    /**
     * 刷新商户的玩法配置
     * @param $lotterySign
     * @param string $partnerSign
     * @return bool
     * @throws \Exception
     */
    static function flushPartnerLotteryAllMethodConfig($lotterySign, $partnerSign = "system") {
        $key    = "mc_all_" . $lotterySign;
        $tag    = "lottery_" . $partnerSign;

        $cache  = cache()->store(self::$store);
        $cache->tags($tag)->forget($key);

        return true;
    }

    /**
     * 获取商户 彩种下 指定玩法配置
     * @param string $lotterySign
     * @param string $methodSign
     * @param string $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getPartnerLotteryOneMethodConfig($lotterySign, $methodSign, $partnerSign = "system") {
        $key    = "mc_one_" . $lotterySign . "_" . $methodSign;
        $tag    = "lottery_" . $partnerSign;

        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags([$tag])->has($key)) {
            return $cache->tags([$tag])->get($key);
        }

        // 从数据库获取单个玩法的配置
        $methodConfig = PartnerMethod::getLotteryOneMethodConfigFromDb($partnerSign, $lotterySign, $methodSign);
        if ($methodConfig) {
            $cache->tags([$tag])->put($key, $methodConfig, now()->addDays(7));
            return $methodConfig;
        }

        return [];
    }

    /**
     * 刷新单个配置
     * @param $lotterySign
     * @param $methodSign
     * @param string $partnerSign
     * @return bool
     * @throws \Exception
     */
    static function flushPartnerLotteryOneMethodConfig($lotterySign, $methodSign, $partnerSign = "system") {
        $key    = "mc_one_" . $lotterySign . "_" . $methodSign;
        $tag    = "lottery_" . $partnerSign;

        $cache  = cache()->store(self::$store);
        $cache->tags($tag)->forget($key);

        return true;
    }

    /**
     * 获取玩法对象
     * @param $lotterySign
     * @param $methodSign
     * @param $partnerSign
     * @return array|mixed
     * @throws \Exception
     */
    static function getMethodObject($lotterySign, $methodSign, $partnerSign = "system") {
        $tag        = "lottery_method_object";
        $cache      = cache()->store(self::$store);
        $cacheKey   = "method_object_" . $lotterySign . "_" . $methodSign;

        if ($cache->tags($tag)->has($cacheKey)) {
            return $cache->tags($tag)->get($cacheKey);
        }

        // 获取配置
        $methodConfig = LotteryCache::getPartnerLotteryOneMethodConfig($lotterySign, $methodSign, $partnerSign);
        if (!$methodConfig) {
            Clog::gameError("获取玩法[配置]失败-" . $methodSign);
            return [];
        }

        $methodObject = LotteryLogic::getMethodObject($methodConfig['logic_sign'], $methodConfig['method_group'], $methodSign);
        if (!is_object($methodObject)) {
            Clog::gameError("获取玩法对象失败-" . $methodConfig['logic_sign'] . "-" . $methodSign . '-' . $methodObject);
            return [];
        }

        $cache->tags($tag)->put($cacheKey, $methodObject, now()->addDays(7));

        return $methodObject;
    }

    /**
     * 获取玩法对象
     * @return array|mixed
     * @throws \Exception
     */
    static function flushAllMethodObject() {
        $tag        = "lottery_method_object";
        $cache      = cache()->store(self::$store);

        $cache->tags($tag)->flush();
        return true;
    }


    /**
     * 获取单个玩法的对象
     * @param $lotterySign
     * @return array
     * @throws \Exception
     */
    static function getLotteryAllMethodFileConfig($lotterySign) {
        $key    = "lmfc_" . $lotterySign;
        $tag    = "lottery_system";

        $cache  = cache()->store(self::$store);

        // 存在直接返回
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $data = LotteryLogic::getAllMethodConfig($lotterySign);
        if ($data) {
            $cache->tags($tag)->forever($key, $data);
            return $data;
        }

        return [];
    }
}
