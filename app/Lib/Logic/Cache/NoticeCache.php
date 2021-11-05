<?php namespace App\Lib\Logic\Cache;

use App\Models\Partner\PartnerNotice;


/**
 * Tom 2019 整理 公告缓存
 * Class NoticeCache
 * @package App\Lib\Cache
 */
class NoticeCache
{

    public static $prefix   = "n_";
    public static $expired  = 12;
    public static $store    = "redis";
    public static $tag      = "notice";

    /**
     * @param $partnerSign
     * @return bool|string
     */
    static function flushCache($partnerSign) {
        $tag    = "t_notice_" . $partnerSign;

        try {
            $cache  = cache()->store(self::$store);
            $cache->tags($tag)->flush();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getAllNotice($partnerSign) {
        $cache  = cache()->store(self::$store);

        $tag    = "t_notice_" . $partnerSign;

        $key    = self::$prefix . "_notice";
        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $item = PartnerNotice::where('status', 1)->get();

        if ($item) {
            $cache->tags($tag)->forever($key, $item->value);
            return $item->value;
        }

        return [];
    }
}
