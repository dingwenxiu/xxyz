<?php

namespace App\Models\Game;

use App\Models\Partner\PartnerLottery;
use App\Lib\Logic\Cache\IssueCache;
use Illuminate\Support\Carbon;

class LotteryIssueBackup extends BaseGame {
    protected $table = 'lottery_issues_backup';

    const STATUS_PROCESS_INIT           = 0;
    const STATUS_PROCESS_ENCODE         = 1;
    const STATUS_PROCESS_OPEN           = 2;
    const STATUS_PROCESS_SEND           = 3;

    const STATUS_PROCESS_EXCEPTION      = -10;
    const STATUS_PROCESS_CANCEL         = -11;


    static public $statusProcess = [
        0       => '初始化',
        1       => '已录号',
        2       => '已开奖',
        3       => '已派奖',
        -10     => "异常回滚",
        -11     => "撤单",
    ];

    static public $iIssueLimit = 300;

    /**
     * 通过issue_no获取奖期
     * @param $lotterySign
     * @param $issue
     * @return mixed
     */
    static function findByIssueNo($lotterySign, $issue) {
        return self::where('lottery_sign', $lotterySign)->where('issue', $issue)->first();
    }

    /**
     * @param $c
     * @return array
     * @throws \Exception
     */
    static function getList($c)
    {
        $timeToday = Carbon::now();
        $timeNow = strtotime($timeToday) - 60 * 60 * 24 * 6;
        $timeFuture = strtotime($timeToday);

        $query = self::orderBy('id', 'desc');

        // 彩种标识
        if(isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', $c['lottery_sign']);
        }

        // 奖期
        if(isset($c['issue']) && $c['issue']) {
            $query->where('issue', $c['issue']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 开始时间
        // 结束时间
        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
            if (strtotime($c['start_time']) - strtotime($c['end_time']) >= 60 * 60 * 24 * 30) {
                self::$errStatic = '最长只能查询一个月';
                return false;
            }
            $query->whereBetween('created_at',[$c['start_time'], $c['end_time']]);
        }else{
            $query->whereBetween('created_at',[date('Y-m-d H:i:s', $timeNow), date('Y-m-d H:i:s', $timeFuture)]);
        }


        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;

        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 获取用户
    public static function getGenIssueOptions() {
        $lotteries  = Lottery::getAllLotteries();
        $issueRule  = LotteryIssueRule::distinct('lottery_sign')->get();
        $data = [];
        foreach ($issueRule as $item) {
            $lottery = $lotteries[$item->lottery_sign];
            $lastIssue = LotteryIssue::where('lottery_sign', $item->lottery_sign)->orderBy('id', 'desc')->first();
            if (!$lastIssue) {
                $startDay = date('Y-m-d');
            } else {
                $startDay = date('Y-m-d', $lastIssue->begin_time + 86400);
            }

            $data[$item->lottery_sign] = [
                'name'          => $lottery->cn_name,
                'issue_type'    => $lottery->issue_type,
                'start_day'     => $startDay,
                'last_issue'    => $lastIssue
            ];
        }
        return $data;
    }

}
