<?php namespace App\Lib\Logic\Cache;

use App\Lib\Clog;
use App\Models\Game\LotteryIssue;

/**
 * Tom 2019 整理 奖期缓存
 * Class IssueCache
 * @package App\Lib
 */
class IssueCache
{

    public static $prefix   = "li_";
    public static $expired  = 1;
    public static $store    = "redis";
    public static $tag      = "lottery_issue";

    /**
     * 刷新所有缓存
     * @throws \Exception
     */
    static function clearAll() {
        try {
            $cache  = cache()->store(self::$store);
            $cache->tags(self::$tag)->flush();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $lotterySign
     * @return |null
     * @throws \Exception
     */
    static function getLastIssue($lotterySign) {
        $key = self::$prefix . $lotterySign . "_last";
        $now = time();

        $cache  = cache()->store(self::$store);

        $lastIssue = null;
        try {
            // 存在直接返回
            if ($cache->tags(self::$tag)->has($key)) {
                $lastIssue =  $cache->tags(self::$tag)->get($key);
            }

            if (!$lastIssue || $lastIssue->status_process == 0){
                $lastIssue = LotteryIssue::select(
                    'lottery_issues.*',
                    'lottery_issue_rules.issue_seconds'
                )->leftJoin('lottery_issue_rules', 'lottery_issues.issue_rule_id', '=', 'lottery_issue_rules.id')
                    ->where("lottery_issues.end_time", "<=", $now)
                    ->where("lottery_issues.lottery_sign", $lotterySign)
                    ->orderBy("lottery_issues.begin_time", "DESC")->first();

                if ($lastIssue) {
                    $expiredSeconds = $lastIssue->end_time + $lastIssue->issue_seconds - $now;
                    $cache->tags(self::$tag)->put($key, $lastIssue, now()->addSeconds($expiredSeconds));
                }
            }
        } catch (\Exception $e) {
            Clog::issueCache("issue-cache-last-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
            $lastIssue = LotteryIssue::where("lottery_sign", $lotterySign)->where("end_time", "<=", $now)->orderBy("begin_time", "DESC")->first();
        }

        return $lastIssue;
    }

    /**
     * 获取下一期
     * @param $issue
     * @return mixed
     */
    static function getNextIssue($issue) {
        return LotteryIssue::where('lottery_sign', $issue->lottery_sign)->where('begin_time', '>', $issue->begin_time)->orderBy("begin_time", "ASC")->first();
    }

    // 获取后面N期
    static function getNextMultipleIssue($lotterySign, $count = 50) {
        $key    = self::$prefix . $lotterySign . "_next";
        $issues = [];

        $cache  = cache()->store(self::$store);

        $now    = time();
        try {
            // 存在直接返回
            if ($cache->tags(self::$tag)->has($key)) {
                $issues = $cache->tags(self::$tag)->get($key);
            }

            // 如果不存在
            if (!$issues) {
                $issues     = LotteryIssue::where('lottery_sign', $lotterySign)->where('end_time', '>', $now)->orderBy("begin_time", "ASC")->take($count)->get();
                if ($issues) {
                    $cache->tags(self::$tag)->put($key, $issues, now()->addMinutes(self::$expired * 60));
                }
            } else {
                $firstIssue = $issues[0];
                if (!$firstIssue || $firstIssue->end_time <= $now) {
                    $issues     = LotteryIssue::where('lottery_sign', $lotterySign)->where('end_time', '>', $now)->orderBy("begin_time", "ASC")->take($count)->get();
                    if ($issues) {
                        $cache->tags(self::$tag)->put($key, $issues, now()->addMinutes(self::$expired * 60));
                    }
                }
            }
        } catch (\Exception $e) {
            Clog::issueCache("issue-cache-next-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
            $issues = LotteryIssue::where('lottery_sign', $lotterySign)->where('end_time', '>', $now)->orderBy("begin_time", "ASC")->take($count)->get();
        }

        return $issues;
    }

    /**
     * @param $lotterySign
     * @return |null
     * @throws \Exception
     */
    static function getCurrentIssue($lotterySign) {
        $key    = self::$prefix . $lotterySign . "_current";

        $now = time();

        $cache  = cache()->store(self::$store);

        $currentIssue = null;
        try {
            // 存在直接返回
            if ($cache->tags(self::$tag)->has($key)) {
                $currentIssue =  $cache->tags(self::$tag)->get($key);
            }

            if (!$currentIssue || $currentIssue->end_time <= $now) {
                $issue = LotteryIssue::where('lottery_sign', $lotterySign)->where('end_time', '>=', $now)->orderBy("end_time", "ASC")->first();
                if ($issue) {
                    $cache->tags(self::$tag)->put($key, $issue, now()->addSeconds($issue->end_time - time()));
                    $currentIssue = $issue;
                }
            }
        } catch (\Exception $e) {
            Clog::issueCache("issue-cache-current-获取异常" .$e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
            $currentIssue = LotteryIssue::where('lottery_sign', $lotterySign)->where('end_time', '>=', $now)->orderBy("end_time", "ASC")->first();
        }

        return $currentIssue;
    }

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getOpenList($partnerSign) {
        $key = self::$prefix  . "_open_list" ;

        $cache  = cache()->store(self::$store);

        $list = [];
        try {
            // 存在直接返回
            if ($cache->tags(self::$tag)->has($key)) {
                $list = $cache->tags(self::$tag)->get($key);
            }

            // 如果不存在
            if (!$list) {
                $list       = LotteryIssue::getOpenList($partnerSign);
            }
        } catch (\Exception $e) {
            Clog::issueCache("issue-open-list-获取异常" . $e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
            $list   = LotteryIssue::getOpenList($partnerSign);
        }

        return $list;
    }

    /**
     * 更新上一期
     * @param $issue
     * @return bool
     * @throws \Exception
     */
    static function updateLastOpenIssue($issue) {
        $key    = self::$prefix  .  "_lottery_issue_last_open";
        $cache  = cache()->store(self::$store);

        if ($cache->tags(self::$tag)->has($key)) {
            $lastIssueArr = $cache->tags(self::$tag)->get($key);
        } else {
            $lastIssueArr = [];
        }

        $lastIssueArr[$issue->lottery_sign] = $issue;
        $cache->tags(self::$tag)->forever($key, $lastIssueArr);

        return true;
    }

    /**
     * 获取所有
     * @return array|\Illuminate\Contracts\Cache\Repository
     * @throws \Exception
     */
    static function getLastOpenIssue() {
        $key    = self::$prefix  .  "_lottery_issue_last_open";
        $cache  = cache()->store(self::$store);

        if ($cache->tags(self::$tag)->has($key)) {
            $lastIssueArr = $cache->tags(self::$tag)->get($key);
        } else {
            $lastIssueArr = [];
        }

        return $lastIssueArr;
    }
}
