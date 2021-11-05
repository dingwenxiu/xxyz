<?php namespace App\Lib\Logic\Cache;

use App\Lib\Clog;
use App\Models\Game\LotteryIssue;
use App\Models\Partner\PartnerActivityRule;
use App\Models\Partner\PartnerNotice;

/**
 * Tom 2019 整理 奖期缓存
 * Class IssueCache
 * @package App\Lib
 */
class ActivityCache
{

    public static $prefix   = "";
    public static $expired  = 1;
    public static $store    = "redis";
    public static $tag      = "activity";

    /**
     * 刷新所有缓存
     * @throws \Exception
     */
    static function clearAll($partnerSign) {
        try {
            $cache  = cache()->store(self::$store);
            $tag    = self::$tag;

            $cache->tags($tag)->flush();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取活动规则
     * @param $partnerSign  商户
     * @param $type         活动类型
         *
     * @return array
     * @throws \Exception
     */
    static function getAllActivity($partnerSign, $type)
    {
        $cache  = cache()->store(self::$store);
        $tag    = self::$tag;

        $key    = $partnerSign . '_' . $tag . '_' . $type;

        if ($cache->tags($tag)->has($key)) {
            return $cache->tags($tag)->get($key);
        }

        $item = PartnerActivityRule::where([
            'partner_sign' => $partnerSign,
            'status'       => 1,
            'type'         => $type,
        ])->first();

        $cache->tags($tag)->forever($key, $item);

        return $item;
    }
}
