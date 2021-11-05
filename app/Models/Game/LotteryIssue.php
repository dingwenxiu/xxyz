<?php

namespace App\Models\Game;

use App\Jobs\Lottery\IssueCancelJob;
use App\Models\Partner\PartnerLottery;
use App\Lib\Logic\Cache\IssueCache;
use App\Lib\Logic\Lottery\IssueLogic;
use Illuminate\Support\Carbon;

class LotteryIssue extends BaseGame {
    protected $table = 'lottery_issues';

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
    static function getList($c) {
		$timeToday = Carbon::now()->startOfWeek();
		$timeTom   = Carbon::now()->endOfWeek();
        $query = self::orderBy('id', 'desc');
        // 彩种标识
        if(isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', $c['lottery_sign']);
        }
//        else {
//            if (isset($c['partner_sign']) && $c['partner_sign']) {
//                $lotteryArr     = PartnerLottery::where("partner_sign", $c['partner_sign'])->where("status", 1)->pluck("lottery_sign")->toArray();
//            } else {
//                $lotteryArr     = PartnerLottery::where("status", 1)->pluck("lottery_sign")->toArray();
//            }
//
//            $lastOpenArr    = IssueCache::getLastOpenIssue();
//
//            $idArr = [];
//            foreach ($lotteryArr as $sign) {
//                if (!isset($lastOpenArr[$sign])) {
//                    $issue = IssueCache::getLastIssue($sign);
//                } else {
//                    $issue = $lastOpenArr[$sign];
//                }
//
//                if ($issue) {
//                    $idArr[] = $issue->id;
//                }
//            }
//
//            $data = LotteryIssue::whereIn("id", $idArr)->get();
//
//
//            $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
//            $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
//
//            return ['data' => $data, 'total' => count($data), 'currentPage' => $currentPage, 'totalPage' => intval(ceil(count($data) / $pageSize))];
//        }

        // 日期
        if(isset($c['issue']) && $c['issue']) {
            $query->where('issue', '=', $c['issue']);
        }

        // 日期 开始
        if(isset($c['start_time']) && $c['start_time']) {
            $query->where('begin_time', ">=", strtotime($c['start_time']));
        } else {
            $query->where('begin_time', ">=", strtotime($timeToday));
        }

        // 日期 结束
        if(isset($c['end_time']) && $c['end_time']) {
            $query->where('end_time', "<=", strtotime($c['end_time']));
        } else {
            $query->where('end_time', "<=", strtotime($timeTom));
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

    /**
     * 删除
     * @param $lotterySign
     * @param $startDay
     * @param $endDay
     * @return mixed
     */
    static function delIssue($lotterySign, $startDay, $endDay) {
        $return = ['status' => true, "msg" => "",];

        if (!isDatetime($startDay)) {
            $return["status"]   = false;
            $return["msg"]      = "无效的开始时间";
            return $return;
        }

        if (!isDatetime($endDay)) {
            $return["status"]   = false;
            $return["msg"]      = "无效的结束时间";
            return $return;
        }

        if (strtotime($endDay) < strtotime($startDay)) {
            $return["status"]   = false;
            $return["msg"]      = "开始时间不能大于结束时间";
            return $return;
        }

        $count = self::where('lottery_sign', $lotterySign)->where('begin_time', '>=', strtotime($startDay))->where('end_time', '<=',  strtotime($endDay))->delete();
        $return["msg"]      = "删除成功!共{$count}条";

        return $return;
    }

    // 撤单
    public function cancelProjects($adminUser) {

        // 设置成撤单状态
        $this->status_process   = self::STATUS_PROCESS_CANCEL;
        $this->time_cancel      = time();
        $this->save();

        // 仍到队列
        jtq(new IssueCancelJob($this->id, $adminUser->id), 'issue');

        return true;
    }

    /** =============== 功能函数 ============= */

    /**
     * 获取当前的奖期
     * @param $lotteryId
     * @return mixed
     */
    static function getCurrentIssue($lotteryId) {
        return self::where('lottery_sign', $lotteryId)->where('end_time', '>', time())->orderBy('id', 'ASC')->first();
    }

    /**
     * 获取当前的奖期
     * @param $lotteryId
     * @return mixed
     */
    static function getNeedOpenIssue($lotteryId) {
        return self::where('lottery_sign', $lotteryId)->where('allow_encode_time', '<', time())->where('allow_encode_time', '>', time() - 3600)->where("status_process", self::STATUS_PROCESS_INIT)->orderBy('id', 'asc')->get();
    }

    /**
     * 获取以往未开的奖期
     * @param $lotteryId
     * @return mixed
     */
    static function getEncodeAndNotOpenIssue($lotteryId) {
        return self::where('lottery_sign', $lotteryId)->where('end_time', '<', time())->where("status_process", self::STATUS_PROCESS_ENCODE)->orderBy('id', 'asc')->first();
    }

    /**
     * 获取所有的奖期
     * @param $issueArr
     */
    public function getIssues($issueArr) {
        if (is_array($issueArr)) {
             self::whereIn("issue", $issueArr)->get();
        }
    }

    /**
     * 获取所有可投奖期
     * @param $lotteryId
     * @param int $count
     * @return mixed
     */
    static function getCanBetIssue ($lotteryId, $count = 50) {
        $time = time();
        return self::where("lottery_sign", $lotteryId)->where("end_time", ">", $time)->orderBy("id", "ASC")->skip(0)->take($count)->get();
    }

    /**
     * 获取所有历史奖期
     * @param $lotterySign
     * @param array $c
     * @return mixed
     */
    static function getHistoryIssue ($lotterySign, $c) {
        $time   = time();
        $query  = self::where("lottery_sign", $lotterySign)->where("end_time", "<=", $time)->orderBy("id", "DESC");

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;

        $offset         = ($currentPage - 1) * $pageSize;

        // 前端的返回
        if (isset($c['from_frontend']) && $c['from_frontend'] == 1) {
            return $query->take($pageSize)->get();
        }

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取上一期
     * @param string $lotterySign
     * @return mixed
     */
    static function getLastIssue ($lotterySign) {
        $time = time();
        return self::where("lottery_sign", $lotterySign)->where("end_time", "<=", $time)->orderBy("id", "DESC")->first();
    }

    /**
     * 获取下一期期
     * @return mixed
     */
    public function getNextIssue () {
        $time = time();
        return self::where("lottery_sign", $this->lottery_sign)->where("end_time", ">=", $time)->orderBy("id", "ASC")->first();
    }

    /**
     * @param $partnerSign
     * @param array $lotteryArr
     * @return array
     * @throws \Exception
     */
    static function getOpenList($partnerSign, $lotteryArr = []) {
        $lotteryArr = PartnerLottery::where("partner_sign", $partnerSign)->whereIn('lottery_sign', $lotteryArr)->where("status", 1)->get(["lottery_sign", 'icon_path'])->toArray();
        $seriesOption   = Lottery::getSeriesOptions();
        $lastOpenArr    = IssueCache::getLastOpenIssue();

        $data = [];
        foreach ($lotteryArr as $signItem) {
            $sign = $signItem['lottery_sign'] ?? '';
            $icon = $signItem['icon_path'] ?? '';

            if (!isset($lastOpenArr[$sign])) {
                $issue = IssueCache::getLastIssue($sign);
            } else {
                $issue = $lastOpenArr[$sign];
            }

            if ($issue) {
                $data[$sign] = [
                    'lottery_name'  => $issue->lottery_name,
                    'icon'          => $icon,
                    'issue'         => $issue->issue,
                    'encode_time'   => $issue->end_time,
                    'lottery_sign'  => $issue->lottery_sign,
                    'official_code' => $issue->official_code,
                    'series'        => $seriesOption[$issue->lottery_sign],
                ];
            }
        }

        return $data;
    }
}
