<?php
namespace App\Lib\Logic\Lottery;

use App\Jobs\Lottery\CommissionProcess;
use App\Jobs\Lottery\Jackpot1002Process;
use App\Jobs\Lottery\OpenProcess;
use App\Jobs\Lottery\PrizeProcess;
use App\Jobs\Lottery\SendProcess;
use App\Jobs\Lottery\TraceProcess;
use App\Lib\BaseCache;
use App\Lib\Clog;
use App\Lib\Help;
use App\Lib\Logic\Cache\IssueCache;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Telegram\TelegramTrait;
use App\Models\Admin\AdminUser;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryCommission;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryIssueBet;
use App\Models\Game\LotteryIssueRule;
use App\Models\Game\LotteryProject;
use Curl\Curl;
use Illuminate\Support\Facades\DB;

/**
 * 奖期逻辑　
 * 2019-10 整理
 * 2020-02 日志梳理
 * Class IssueLogic
 * @package App\Lib\Lottery\IssueLogic
 */
class IssueLogic
{
    use BaseCache;
    use TelegramTrait;

    /** ====================== 队列操作逻辑 ========================= */

    /**
     * 好啊录入
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    static function officialEncode($params) {
        Clog::gameEncode("encode-start-", "default", $params);

        $code           = request('number');
        $_lotterySign   = request('lottery');
        $issueNo        = request('issue');

        $lotterySignTransfer = config("game.issue.encode_transfer");
        if (!isset($lotterySignTransfer[$_lotterySign])) {
            Clog::gameEncode("encode-标识转换-{$_lotterySign}-未开放的游戏", $_lotterySign, $params);
            return Help::returnApiJson('对不起, 未开放的游戏!', 0);
        }

        $lotterySign = $lotterySignTransfer[$_lotterySign];

        Clog::gameEncode("encode-init-{$_lotterySign}-{$lotterySign}", $lotterySign, $params);

        // 是不是禁止录号
        $disableLottery = configure("lottery_disable_encode_lottery", "");
        if ($disableLottery) {
            $disableLotteryArr = explode(',', $disableLottery);
            if ($disableLotteryArr && in_array($lotterySign, $disableLotteryArr)) {
                Clog::gameEncode("encode-{$_lotterySign}-{$lotterySign}-被禁止录号", $lotterySign, $params);
                return Help::returnApiJson('恭喜 录号成功(0x001)!', 1);
            }
        }

        // 彩种
        $lottery = Lottery::findBySign($lotterySign);
        if (!$lottery) {
            return Help::returnApiJson('对不起,不存在的彩种!', 0);
        }

        if (Lottery::isClosedMarket($lotterySign)) {
            return Help::returnApiJson('对不起, 休市中!', 0);
        }

        // 奖期
        Clog::gameEncode("encode-issue-{$issueNo}", $lotterySign, []);

        $issue = LotteryIssue::on('second')->where('lottery_sign', $lotterySign)->where('issue', $issueNo)->first();
        if (!$issue || $issue->status_process > 1) {
            return Help::returnApiJson('对不起, 奖期已经录号!', 0);
        }

        Clog::gameEncode("encode-get-code-start-{$code}", $lotterySign, []);

        $code = $lottery->codeTransferOnEncode($code);
        Clog::gameEncode("encode-get-code-end-{$code}", $lotterySign);

        IssueLogic::encode($issue, $code, -2);

        $params['ip'] = real_ip();

        Clog::gameEncode("issue-{$issueNo}-success", $lotterySign, $params);

        return Help::returnApiJson('恭喜 录号成功!', 1);
    }

    /**
     * ==== && 已经录号 && ====
     * 录号   　开奖, 所有状态都为原始状态
     * 总后台   开奖, 有可能已经处理了某一个步骤, 开奖 派奖 中的某一个环节
     *
     * @param $issue
     * @return bool
     * @throws \Exception
     */
    static function open($issue) {
        // 1. 如果没有任何订单　直接跳过
        Clog::gameStat("trace-open-logic-start-{$issue->lottery_sign}-{$issue->issue}-" . date("Y-m-d H:i:s"));
        $issueProjectStat = self::getLotteryProjectSlotBetStat($issue->lottery_sign, $issue->issue);
        $total = array_sum($issueProjectStat);
        $issue->total_project = $total;
        $issue->save();
        if ($total <= 0) {
            $issue->status_process          = LotteryIssue::STATUS_PROCESS_SEND;
            $issue->status_commission       = 1;

            $issue->time_open               = time();
            $issue->time_send               = time();
            $issue->time_commission         = time();

            $issue->time_end_open           = time();
            $issue->time_end_send           = time();
            $issue->time_end_commission     = time();
            $issue->save();
            Clog::gameStat("trace-open-logic-end-(没有注单)-{$issue->lottery_sign}-{$issue->issue}-" . date("Y-m-d H:i:s"));
            // 追号
            IssueLogic::trace($issue);

            return true;
        }

        // 处理开奖队列
        $projectSlotCount = config("game.main.logic_project_slot");
        for($i = 0; $i < $projectSlotCount; $i ++) {
            // 如果订单量大于0
            if (isset($issueProjectStat[$i]) && $issueProjectStat[$i] > 0) {
                jtq(new OpenProcess($issue->id, $i,  ["from" => 'issue_logic']), 'open_' . $i);
            }
        }

        return true;
    }

    /**
     * 返点处理
     * @param $issue
     * @return bool
     */
    static function sendCommission($issue) {
        // 获取所有 中奖 / 和局注单
        $totalCount = LotteryCommission::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status', 0)->count();
        if ($totalCount <= 0) {
            $issue->status_commission          = 1;
            $issue->time_end_commission        = time();
            $issue->save();
        }

        // 处理追号
        $slotCount = config("game.main.logic_commission_slot");
        for($i = 0; $i < $slotCount; $i ++) {
            jtq(new CommissionProcess($issue, $i,  ["from" => 'issue_logic']), 'commission_' . $i);
        }

        return true;
    }

    /**
     * 派奖
     * @param $issue
     * @return bool
     * @throws \Exception
     */
    static function sendBonus($issue) {

        // 获取所有 中奖 / 和局注单
        $openStat   = self::getLotteryProjectSlotOpenStat($issue->lottery_sign, $issue->issue);
        $totalCount = array_sum($openStat);
        if ($totalCount <= 0) {
            $issue->status_process          = LotteryIssue::STATUS_PROCESS_SEND;
            $issue->time_end_send           = time();
            $issue->time_trace              = time();
            $issue->time_commission         = time();

            $issue->save();
            IssueLogic::trace($issue);
            IssueLogic::sendCommission($issue);
            return true;
        }

        // 处理派奖
        $sendSlotCount = config("game.main.logic_project_slot");
        for($i = 0; $i < $sendSlotCount; $i ++) {
            if (isset($openStat[$i]) && $openStat[$i] > 0) {
                jtq(new SendProcess($issue, $i,  ["from" => 'issue_logic']), 'send_' . $i);
            }
        }

        return true;
    }

    /**
     * 追号处理
     * @param $issue
     * @return bool
     */
    static function trace($issue) {
        // 处理追号
        $slotCount = config("game.main.logic_trace_slot");
        for($i = 0; $i < $slotCount; $i ++) {
            jtq(new TraceProcess($issue, $i,  ["from" => 'issue_logic']), 'trace_' . $i);
        }

        return true;
    }

    /**
     * 追号处理
     * @param $issue
     * @param $slot
     * @return bool
     */
    static function prizeCheck($issue, $slot = 0) {
        jtq(new PrizeProcess($issue, ["from" => 'issue_logic', 'slot' => $slot]), 'issue');

        return true;
    }

    /**
     * 控水　计算奖金
     * @param $issue
     * @param $partnerLottery
     * @return bool
     */
    static function jackpotIssueBonus($issue, $partnerLottery) {
        jtq(new Jackpot1002Process($issue, $partnerLottery), 'jackpot');

        return true;
    }


    /**
     * 记录任务
     * @param $lottery
     * @param $issue
     * @param $project
     * @param $oMethod
     * @return string
     */
    static function lockerTask($lottery, $issue, $project, $oMethod) {
        $plan   = $lottery->lottery_sign . '_' . $issue->issue;

        $betGroup = $project->bet_prize_group - $lottery->diff_prize_group;
        $prizes = $oMethod->getLockPrizes($betGroup, $project->times, $project->pirce,  $project->mode);
        $script = $oMethod->lockScript($project->bet_number, $plan, $prizes);

        return " \n ".$script." \n";
    }


    /**
     * @param $issue
     * @param $code
     * @param int $adminId
     * @return bool|string
     * @throws \Exception
     */
    static function encode($issue, $code, $adminId = -1) {

        $key = "encode_{$issue->id}";

        if (!cache()->add($key, $code, now()->addMinutes(1))) {
            return "对不起, 录号进行中{$issue->issue}!";
        }

        Clog::gameEncode("encode-{$issue->issue}-start-code:{$code}", $issue->lottery_sign, []);

        try {
            // 组装
            $codeArr = explode(",", $code);

            //  检查号码格式
            $lottery = Lottery::findBySign($issue->lottery_sign);
            if (!$lottery->checkCodeFormat($codeArr) ) {
                Clog::gameEncode("encode-{$issue->issue}-error-不符合格式({$code})", $issue->lottery_sign, $codeArr);
                cache()->forget($key);
                return "录入的号码,不符合格式-{$code}!";
            }

            // 号码写入
            $issue->official_code    = $code;
            $issue->time_encode      = time();
            $issue->status_process   = LotteryIssue::STATUS_PROCESS_ENCODE;
            $issue->encode_id        = $adminId;
            $issue->encode_username  = self::getEncodeUsername($adminId);
            $issue->time_open        = time();
            $issue->save();

            // 开奖
            Clog::gameEncode("encode-{$issue->issue}-end", $issue->lottery_sign, $codeArr);

            // 如果本期已经追号
            if ($issue->status_trace == 1 ) {
                self::open($issue);
            } else {

                $text = "奖期异常:{$issue->lottery_sign}-{$issue->issue}, 本期未追号, 尝试处理";
                telegramSend("sendNotOpenIssue", $text);

                // 如果上期
                $lastIssue  = LotteryIssue::where('lottery_sign', $issue->lottery_sign)->where("begin_time", "<", $issue->begin_time)->orderBy('id', 'DESC')->first();

                if ($lastIssue) {
                    if ( $lastIssue->status_process == LotteryIssue::STATUS_PROCESS_INIT) {
                        $text = "奖期异常:{$issue->lottery_sign}-{$lastIssue->issue},未录号, 请录号";
                        telegramSend("sendNotOpenIssue", $text);
                    } elseif ( $lastIssue->status_process == LotteryIssue::STATUS_PROCESS_ENCODE) {
                        self::open($lastIssue);
                    } elseif ($lastIssue->status_process == LotteryIssue::STATUS_PROCESS_OPEN) {
                        self::sendBonus($lastIssue);
                    } elseif ($lastIssue->status_process == LotteryIssue::STATUS_PROCESS_SEND && $lastIssue->status_commission == 0) {
                        self::sendCommission($lastIssue);
                    } elseif ($issue->status_trace == 0) {
                        self::trace($lastIssue);
                    }
                }
            }

            // 上一期
            IssueCache::updateLastOpenIssue($issue);

            cache()->forget($key);
        } catch (\Exception $e) {
            Clog::gameEncode("encode-{$issue->issue}-exception-" . $e->getMessage() .  "|" . $e->getLine() .  "|" . $e->getFile(), $issue->lottery_sign, []);

        }


        // 录号日志
        return true;
    }

    /**
     * @param $issue
     * @param $adminUser
     * @return array
     * @throws \Exception
     */
    static function cancel($issue, $adminUser) {

        $return = [
            'status'        => true,
            'total_project' => 0,
            'total_success' => 0,
            "msg"           => "",
        ];

        $key = "issue_cancel_" . $issue->id;
        if (!cache()->add($key, 1, now()->addMinutes(2))) {
            $return['status']   = false;
            $return['msg']      = "对不起, 撤单正在执行!!!!";
            return $return;
        }

        // 1. 获取所有的未开
        $totalCount = LotteryProject::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status_process', LotteryProject::STATUS_PROCESS_INIT)->count();
        if ($totalCount <= 0) {
            $return['msg'] = "订单数为0";
            cache()->forget($key);
            return $return;
        }

        Clog::gameCancel("issue-cancel-start-管理员{$adminUser->username}-{$issue->issue}" . time() , $issue->lottery_sign);

        $return['total_project'] = $totalCount;

        $pageSize   = 1000;
        $totalPage  = ceil($totalCount / $pageSize);

        try {
            $i = 1;
            do {
                $offset     = $pageSize * $i;
                $projects   = LotteryProject::where('lottery_sign', $issue->lottery_sign)
                                ->where('issue', $issue->issue)
                                ->where('status_process', LotteryProject::STATUS_PROCESS_INIT)
                                ->orderBy("id", "ASC")
                                ->skip($offset)->take($pageSize)->get();

                foreach ($projects as $project) {
                    $res = ProjectLogic::cancel($project);
                    if ($res === true) {
                        $return['total_success'] ++;
                    } else {
                        Clog::gameCancel("对不起,后台管理员{$adminUser->username}-撤单失败-{$project->id}-" . $res, $project->lottery_sign);
                    }
                }

            } while($i < $totalPage);

        } catch (\Exception $e) {
            $return['msg']      = $e->getMessage();
            $return['status']   = false;

            cache()->forget($key);
        }

        $return['status']       = $return['total_project'] == $return['total_success'] ;
        $totalProject           = count($return['total_project']);
        $totalSuccessProject    = count($return['total_success']);
        $return['msg']          = $return['total_project'] == $return['total_success'] ? "撤单成功, 共{$totalProject}单" : "对不起, 部分完成,总{$totalProject}单, 成功{$totalSuccessProject}单";

        if ($return['status']) {
            $issue->status_process = LotteryIssue::STATUS_PROCESS_CANCEL;
            $issue->save();
        }

        cache()->forget($key);
        Clog::gameCancel("issue-cancel-end-管理员{$adminUser->username}-{$issue->issue}" . time() , $issue->lottery_sign);
        return $return;
    }

    /**
     * 录号人姓名
     * @param $adminId
     * @return string
     */
    static function getEncodeUsername($adminId) {
        if ($adminId == -1) {
            return "机器随机";
        } else if ($adminId == -2) {
            return "开奖中心";
        } else {
            $adminUser = AdminUser::find($adminId);
            return $adminUser ? $adminUser->username : "未知";
        }
    }

    /** ================================= 奖期生成 ================================== */

    /**
     * 根据开始时间和结束时间生成奖期
     * type:day 每日增加型
     * type:increase 递增型 需要初始奖期
     * type:random 日期随机型　需要开奖时间
     * @param $lottery  object 彩种
     * @param $startDay string 开始时间 2019-05-21
     * @param $endDay   string 结束时间 2019-05-29
     * @param $openTime string 开奖时间 六合彩专用
     * @return array|string
     */
    static function genIssue($lottery, $startDay, $endDay, $openTime = null) {
        // 是否开启
        if ($lottery->status != 1) {
            return "对不起,彩种{$lottery->cn_name}未开启!!";
        }

        // 时间范围
        if (strtotime($startDay) > strtotime($endDay)) {
            return "对不起,结束时间不能小于开始时间!!";
        }

        // 是否选择了开始奖期
        if ($lottery->issue_type == 'random' && !$openTime) {
            return "对不起, 您选择的彩种需要开奖时间!";
        }

        $rules  = LotteryIssueRule::where('lottery_sign', $lottery->en_name)->orderBy('id', "ASC")->get();

        $daySet = self::getDaySet($startDay, $endDay);

        $return = [];
        foreach ($daySet as $day) {
            $return[$day] = self::_genIssue($lottery, $day, $rules);
        }

        return $return;
    }

    /**
     * 根据开始时间和结束时间生成奖期
     * type:day 每日增加型
     * type:increase 递增型 需要初始奖期
     * type:random 日期随机型　需要开奖时间
     * @param $lottery  object 彩种
     * @param $issue string 奖期
     * @param $openTime int 开奖时间 六合彩专用
     * @return array|string
     */
    static function genLhcIssue($lottery, $issue, $openTime) {
        // 是否开启
        if ($lottery->status != 1) {
            return "对不起,彩种{$lottery->cn_name}未开启!!";
        }

        // 获取上一期
        $lastIssue = LotteryIssue::where("lottery_sign", $lottery->en_name)->orderBy('begin_time', "DESC")->first();

        $openTime = strtotime($openTime);

        $item = [
            'lottery_sign'          => $lottery->en_name,
            'issue_rule_id'         => 0,
            'lottery_name'          => $lottery->cn_name,
            'begin_time'            => $lastIssue ? $lastIssue->end_time : time(),
            'end_time'              => $openTime - 15 * 60,
            'official_open_time'    => $openTime,
            'allow_encode_time'     => $openTime + 5 * 60,
            'day'                   => date("Ymd"),
            'issue'                 => $issue
        ];

        $res = DB::table("lottery_issues")->insert($item);

        return !!$res;
    }

    /**
     * 更具规则 日期 生成奖期
     * @param $lottery
     * @param $day
     * @param $rules
     * @return bool|string
     */
    static function _genIssue($lottery, $day, $rules) {
        if (!$rules) {
            return "对不起, 彩种{$lottery->cn_name}未配置奖期规则!!";
        }

        // 整数形式的日期
        $intDay = date('Ymd', strtotime($day));

        // 检查是否存在奖期
        $issueCount = LotteryIssue::where('lottery_sign', $lottery->en_name)->where('day', $intDay)->count();

        // 删除重新来
        if ($issueCount > 0 && $issueCount < $lottery->day_issue) {
            LotteryIssue::where('lottery_sign', $lottery->en_name)->where('day', $intDay)->delete();
        } else if ($issueCount == $lottery->day_issue) {
            return "对不起, 彩种{$lottery->cn_name}-{$intDay}-已经生成!!";
        }

        $firstIssueNo   = "";
        $data           = [];

        // 累加型的获取
        if ($lottery->issue_type == "increase") {
            $config = config("game.issue.issue_fix");
            if (isset($config[$lottery->en_name])) {
                $_config    = $config[$lottery->en_name];
                $_day       = (strtotime($day) - strtotime($_config['day'])) / 86400;
                $_day       = ceil($_day);

                if (isset($_config['zero_start'])) {
                    $firstIssueNo = intval($_config['start_issue']) + $_day * $lottery->day_issue;
                    $firstIssueNo = $_config['zero_start'] . $firstIssueNo;
                } else {
                    $firstIssueNo = $_config['start_issue'] + $_day * $lottery->day_issue;
                }
            }
        }

        // 生成
        $issueNo = $firstIssueNo ? $firstIssueNo : "";
        foreach ($rules as $index => $rule) {

            $adjustTime = $rule->adjust_time;

            // 如果1天1期的彩种 开始时间 前置1天
            if ($rule->issue_count == 1) {
                $beginTime  = strtotime($day . " " . $rule['begin_time']) - 86400;
            } else {
                $beginTime  = strtotime($day . " " . $rule['begin_time']);
            }

            // 结束时间的修正
            if ($rule['end_time'] == "00:00:00") {
                $endTime    = strtotime($day . " " . $rule['end_time']) + 86400   - $adjustTime;
            } else {
                $endTime    = strtotime($day . " " . $rule['end_time'])   - $adjustTime;
                // 如果跨天
                if (strtotime($day . " " . $rule['begin_time']) > strtotime($day . " " . $rule['end_time'])) {
                    $endTime = $endTime + 86400;
                }
            }

            $issueTime  = $rule['issue_seconds'];

            $index   = 1;
            do {
                if (1 == $index) {
                    $issueEnd           = strtotime($day . " " . $rule['first_time']) - $adjustTime;
                    $officialOpenTime   = strtotime($day . " " . $rule['first_time']);
                } else {
                    $issueEnd           = $beginTime + $issueTime;
                    $officialOpenTime   = $beginTime + $issueTime + $adjustTime;
                }

                $item = [
                    'lottery_sign'          => $lottery->en_name,
                    'issue_rule_id'         => $rule->id,
                    'lottery_name'          => $lottery->cn_name,
                    'begin_time'            => $beginTime,
                    'end_time'              => $issueEnd,
                    'official_open_time'    => $officialOpenTime,
                    'allow_encode_time'     => $officialOpenTime + $rule['encode_time'],
                    'day'                   => $intDay,
                ];

                if ($firstIssueNo) {
                    $item['issue'] = $issueNo;
                    $issueNo = self::getNextIssueNo($issueNo, $lottery, $rule, $day);
                } else {
                    $issueNo = self::getNextIssueNo($issueNo, $lottery, $rule, $day);
                    $item['issue'] = $issueNo;
                }

                $data[] = $item;

                $beginTime = $issueEnd;
                $index ++;

            }while($beginTime < $endTime);

        }

        $totalGenCount  = count($data);

        if ($totalGenCount != $lottery->day_issue) {
            return "-----生成的期数不正确,-{$lottery->cn_name} 应该：{$lottery->day_issue} - 实际:{$totalGenCount}";
        }

        // 插入
        $res = DB::table("lottery_issues")->insert($data);

        if ($res) {
            return true;
        }

        return "插入数据失败!!";
    }

    /**
     * 获取下一期的
     * @param $issueNo
     * @param $lottery
     * @param $day
     * @return mixed
     */
    static function getNextIssueNo($issueNo, $lottery, $rule, $day) {
        $dayTime        = strtotime($day);
        $issueFormat    = $lottery->issue_format;

        $formats = explode('|', $issueFormat);

        // C 开头
        if (count($formats) == 1 and strpos($formats[0], 'C') !== false) {
            $currentIssueNo = intval($issueNo);
            $nextIssue      = $currentIssueNo + 1;

            if (strlen($currentIssueNo) == strlen($issueNo)) {
                return $nextIssue;
            } else {
                return str_pad($nextIssue, strlen($issueNo), '0', STR_PAD_LEFT);
            }
        }

        // 日期型
        if (count($formats) == 2) {
            $numberLength = substr($formats[1], -1);

            // 时时彩 / 乐透
            if (strpos($formats[1], 'N') !== false) {

                $suffix = date($formats[0], $dayTime);

                if ($issueNo) {
                    return $suffix . self::getNextNumber($issueNo, $numberLength);
                } else {
                    return $suffix . str_pad(1, $numberLength, '0', STR_PAD_LEFT);
                }
            }

            // 特殊号
            if (strpos($formats[1], 'T') !== false) {

                $suffix = date($formats[0], $dayTime);

                if ($issueNo) {
                    return $suffix . self::getNextNumber($issueNo, $numberLength);
                } else {
                    return $suffix . str_pad(1, $numberLength, '0', STR_PAD_LEFT);
                }
            }
        }
    }

    /**
     * 获取下一个
     * @param $issueNo
     * @param $count
     * @return string
     */
    static function getNextNumber($issueNo, $count) {
        $currentNo  = substr($issueNo, -$count);
        $nextNo     = intval($currentNo) + 1;
        return str_pad($nextNo, $count, '0', STR_PAD_LEFT);
    }

    /**
     * 获取时间集合
     * @param $startDay
     * @param $endDay
     * @return array
     */
    static function getDaySet($startDay, $endDay) {
        $data = [];
        $dtStart = strtotime($startDay);
        $dtEnd   = strtotime($endDay);

        if ($dtStart > $dtEnd) {
            return $data;
        }

        do {
            $data[] = date('Y-m-d', $dtStart);
        } while (($dtStart += 86400) <= $dtEnd);

        return $data;
    }

    /** ================================= 开奖 ================================== */

    /**
     * return
     *          1:
     *          2:
     * @param $issue
     * @param $slot
     * @return array|int
     * @throws \Exception
     */
    static function _open($issue, $slot) {

        // 1. 检测奖期状态 是否为录号状态
        if ($issue->status_process != LotteryIssue::STATUS_PROCESS_ENCODE) {
            Clog::gameOpenProcess("open-process-logic-slot($slot)-issue-{$issue->issue}-error-奖期已经处理($issue->status_process)-第1次!", $issue->lottery_sign);

            // 如果未录号　可能是同步复制延迟　再吃查询
            if ($issue->status_process == LotteryIssue::STATUS_PROCESS_INIT) {
                $issue = LotteryIssue::where('id', $issue->id)->first();
                if ($issue->status_process == LotteryIssue::STATUS_PROCESS_INIT) {
                    Clog::gameOpenProcess("open-process-logic-slot($slot)-issue-{$issue->issue}-error-奖期已经处理($issue->status_process)-第2次!", $issue->lottery_sign);
                    return 2;
                }
            } else {
                return 1;
            }

        }

        // 2. 获取所有中奖注单
        $totalSlotCount = LotteryProject::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where("slot", $slot)->where('status_process', LotteryProject::STATUS_PROCESS_INIT)->count();

        // 开始
        Clog::gameOpenProcess("open-process-logic-start-slot($slot)-issue-{$issue->issue}-total:{$totalSlotCount}-status:{$issue->status_process}", $issue->lottery_sign, []);

        $return = [
            'total_count'   => $totalSlotCount,
            'win_count'     => 0,
            'lose_count'    => 0,
            'he_count'      => 0,
            'fail_count'    => 0,
            'status'        => true,
        ];

        $failCount  = 0;
        $pageSize   = configure("system_open_page_size", 1000);
        $totalPage  = ceil($totalSlotCount / $pageSize);

        $i = 0;
        $k = 0;

        Clog::gameStat("open-stat-start-lottery:{$issue->lottery_sign}-issue:{$issue->issue}-status:{$issue->status_process}-slot:{$slot}-count:{$totalSlotCount}-page:{$totalPage}-size:{$pageSize}" . time(), $issue->lottery_sign);

        $totalWinCount = 0;
        do {
            $offset     = $pageSize * $k;
            $projects   = LotteryProject::where('lottery_sign', $issue->lottery_sign)
                ->where('issue', $issue->issue)
                ->where("slot", $slot)
                ->where('status_process', LotteryProject::STATUS_PROCESS_INIT)
                ->orderBy("id", "ASC")
                ->skip($offset)->take($pageSize)->get();

            Clog::gameStat("open-stat--aaaaa---lottery:{$issue->lottery_sign}-issue:{$issue->issue}-slot:{$slot}-count:{$totalSlotCount}-i:{$i}-k{$k}-offset:{$offset}-pageCount:" . count($projects). "-" . time(), $issue->lottery_sign);

            // 中奖注单
            $winProjectIdArr = [
                'win_count' => [],
                'bonus'     => [],
            ];

            // 未中奖
            $loseProjectIdArr   = [];

            // 追号详情
            $traceDetailIdArr   = [
                'bonus' => [],
            ];

            // 追号主要
            $traceMainIdArr     = [
                "total_bonus" => []
            ];

            $traceDetailIds = [];
            $winProjectIds  = [];
            $heProjectIds   = [];
            $traceMainIds   = [];

            // 组装数据
            foreach ($projects as $project) {
                $countRes = ProjectLogic::count($project, $issue->official_code);

                if ($countRes !== false) {
                    // 和局模式
                    if ($countRes['is_win'] == 3) {
                        $heProjectIds[] = $project->id;
                        continue;
                    }

                    $winProjectIds[] = $project->id;
                    $winProjectIdArr['win_count'][$project->id] = $countRes['win_count'];
                    $winProjectIdArr['bonus'][$project->id]     = $countRes['bonus'];

                    if ($project->trace_detail_id > 0) {
                        $traceDetailIds[]   = $project->trace_detail_id;
                        $traceMainIds[]     = $project->trace_main_id;

                        // 详情
                        $traceDetailIdArr['bonus'][$project->trace_detail_id]       = $countRes['bonus'];
                        // 总中奖
                        $traceMainIdArr['total_bonus'][$project->trace_main_id]     = $countRes['bonus'];
                    }
                } else {
                    $loseProjectIdArr[] = $project->id;
                }
            }

            $totalWinCount += count($winProjectIds);

            $processRes = [];

            // 未中奖订单 按已派奖处理
            if ($loseProjectIdArr) {
                $processRes['lose_process_count'] = LotteryProject::whereIn('id', $loseProjectIdArr)->update([
                    'time_open'         => time(),
                    'open_number'       => $issue->official_code,
                    'is_win'            => 2,
                    'win_count'         => 0,
                    'bonus'             => 0,
                    'status_process'    => LotteryProject::STATUS_PROCESS_SEND,
                ]);

                $return['lose_count'] += count($loseProjectIdArr);
            }

            // 和局订单　需要在派奖阶段处理
            if ($heProjectIds) {
                $processRes['he_process_count'] = LotteryProject::whereIn('id', $heProjectIds)->update([
                    'time_open'         => time(),
                    'open_number'       => $issue->official_code,
                    'is_win'            => 3,
                    'win_count'         => 0,
                    'bonus'             => 0,
                    'status_process'    => LotteryProject::STATUS_PROCESS_OPEN,
                ]);

                $return['he_count'] += count($heProjectIds);
            }

            db()->beginTransaction();
            try {
                // 中奖
                if ($winProjectIdArr['win_count']) {
                    $winProjectUpdateSql = "update `lottery_projects` set `time_open` = " . time() . ", `is_win` = 1, `status_process` = " . LotteryProject::STATUS_PROCESS_OPEN . ", `open_number` = '{$issue->official_code}' ";
                    $sql = self::getWinBatchSql($winProjectIdArr);
                    $winProjectUpdateSql = $winProjectUpdateSql . $sql;

                    $inStr = implode(",", $winProjectIds);

                    $winProjectUpdateSql = $winProjectUpdateSql . " where id in (" . $inStr . ")";

                    $processRes['win_process_count'] = DB::update(DB::raw($winProjectUpdateSql));
                }

                // ==追号== [主要] 更新
                if ($traceMainIdArr['total_bonus']) {
                    $traceMainSql = "update `lottery_traces` set";
                    $sql = self::getTraceMainBatchSql($traceMainIdArr);
                    $traceMainSql = $traceMainSql . $sql;
                    $inStr = implode(",", $traceMainIds);
                    $traceMainSql = $traceMainSql . " where id in (" . $inStr . ")";

                    $processRes['trace_main_count'] = DB::update(DB::raw($traceMainSql));
                }

                // ==追号== [详情] 更新
                if ($traceDetailIdArr['bonus']) {
                    $traceDetailSql = "update `lottery_trace_detail` set `is_win` = 1 ";
                    $sql = self::getTraceDetailBatchSql($traceDetailIdArr);
                    $traceDetailSql = $traceDetailSql . $sql;
                    $inStr = implode(",", $traceDetailIds);
                    $traceDetailSql = $traceDetailSql . " where id in (" . $inStr . ")";

                    $processRes['trace_detail_count'] = DB::update(DB::raw($traceDetailSql));
                }

                db()->commit();

            } catch (\Exception $e) {
                db()->rollback();

                Clog::gameOpenProcess("open-process-job-exception-slot($slot)-issue-{$issue->issue}-{$e->getMessage()}-i($i)-status:{$issue->status_process}", $issue->lottery_sign, $processRes);

                // 推送异常
                $noticeMsg = "开奖异常-彩种:{$issue->lottery_sign}-issue:{$issue->issue}-{$slot}-原因:{$e->getMessage()}-{$e->getLine()}-{$e->getFile()}";
                telegramSend("send_exception", $noticeMsg);
                $return['fail_count'] += count($winProjectIdArr);

                $k ++;
                $i ++;
                continue;
            }

            $i ++;
        } while($i < $totalPage);

        $return['win_count'] = $totalWinCount;

        // 检测是否完成
        $totalCount = LotteryProject::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status_process', LotteryProject::STATUS_PROCESS_INIT)->count();
        if ($totalCount <= 0) {
            $issue->status_process          = LotteryIssue::STATUS_PROCESS_OPEN;
            $issue->time_end_open           = time();
            $issue->time_send               = time();
            $issue->save();

            self::sendBonus($issue);

            Clog::gameOpenProcess("open-process-logic-finished-slot($slot)-issue-{$issue->issue}-end-fail({$failCount})-status:{$issue->status_process}", $issue->lottery_sign, $processRes);
        }

        Clog::gameOpenProcess("open-process-logic-end-slot($slot)-issue-{$issue->issue}-end-fail({$failCount})", $issue->lottery_sign, $processRes);
        Clog::gameStat("open-stat-end-lottery:{$issue->lottery_sign}-issue:{$issue->issue}-slot:{$slot}-count:{$totalSlotCount}-win:{$totalWinCount}-total:{$totalCount}" . time(), $issue->lottery_sign);

        return $return;
    }

    // 批量更新 追号 主要 数据
    static function getTraceMainBatchSql($data) {
        $sql = " ";

        // 拼接语句
        foreach ($data as $__f => $_changeData) {
            $sql .= "`" . $__f . "` = CASE ";
            foreach ($_changeData as $_id => $__v) {
                $sql .= " WHEN `id` = " . $_id . " THEN `" . $__f . "` + " . $__v;
            }
            $sql .= " ELSE `$__f` + 0 END,";
        }

        return rtrim($sql, ",");
    }

    // 批量更新 ==== 详情 ==== 数据
    static function getTraceDetailBatchSql($data) {
        $sql = ", ";

        // 拼接语句
        foreach ($data as $__f => $_changeData) {
            $sql .= "`" . $__f . "` = CASE ";
            foreach ($_changeData as $_id => $__v) {
                $sql .= " WHEN `id` = " . $_id . " THEN " . $__v;
            }
            $sql .= " ELSE `$__f` + 0 END,";
        }

        return rtrim($sql, ",");
    }

    // 批量更新数据
    static function getWinBatchSql($data) {
        $sql = ", ";

        // 拼接语句
        foreach ($data as $__f => $_changeData) {
            $sql .= "`" . $__f . "` = CASE ";
            foreach ($_changeData as $_id => $__v) {
                $sql .= " WHEN `id` = " . $_id . " THEN " . $__v;
            }
            $sql .= " ELSE `$__f` + 0 END,";
        }

        return rtrim($sql, ",");
    }

    /** ================================= 派奖 ================================== */

    /**
     * 派奖
     * @param $issue
     * @param $slot
     * @return bool
     * @throws \Exception
     */
    static function _send($issue, $slot) {
        // 开始
        Clog::gameSend("send-process-job-slot($slot)-issue:{$issue->issue}-status:{$issue->status_process}-start", $issue->lottery_sign);

        // 1. 是否已经开奖
        if ($issue->status_process != LotteryIssue::STATUS_PROCESS_OPEN) {
            Clog::gameSend("send-process-job-slot($slot)-issue-{$issue->issue}-error-奖期不是开奖状态($issue->status_process)!", $issue->lottery_sign);
            // 如果未开状态 扔回
            if ($issue->status_process < LotteryIssue::STATUS_PROCESS_OPEN) {
                return "status:($issue->status_process)";
            } else {
                return true;
            }
        }

        // 2. 获取所有中奖注单
        $totalSlotCount = LotteryProject::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where("slot", $slot)->where('status_process', LotteryProject::STATUS_PROCESS_OPEN)->whereIn("is_win", [1, 3])->count();

        // 3. 当前槽是否有注单
        if ($totalSlotCount <= 0) {
            Clog::gameSend("send-process-job-end-slot($slot)-issue-{$issue->issue}-槽订单数为0", $issue->lottery_sign, []);
            return true;
        }

        // 开始
        Clog::gameSend("send-process-job-start-slot($slot)-issue-{$issue->issue}-total:{$totalSlotCount}", $issue->lottery_sign, []);

        $failCount = 0;

        Clog::gameStat("send-stat-start-slot($slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);

        try {
            $pageSize   = 1000;
            $totalPage  = ceil($totalSlotCount / $pageSize);

            $i = 0;

            do {
                $offset     = $failCount;
                $projects   = LotteryProject::where('lottery_sign', $issue->lottery_sign)
                    ->where('issue', $issue->issue)
                    ->where("slot", $slot)
                    ->where('status_process', LotteryProject::STATUS_PROCESS_OPEN)
                    ->whereIn("is_win", [1, 3])
                    ->orderBy("id", "ASC")
                    ->skip($offset)->take($pageSize)->get();

                // 帐变
                $data = [];
                foreach ($projects as $project) {
                    if (!isset($data[$project->user_id])) {
                        $data[$project->user_id] = [];
                    }

                    if ($project->is_win == 1) {
                        $data[$project->user_id][] = [
                            'change_type'       => "game_bonus",
                            'top_id'            => $project->top_id,
                            'user_id'           => $project->user_id,
                            'username'          => $project->username,
                            'partner_sign'      => $project->partner_sign,
                            'is_challenge'      => $project->is_challenge,
                            'challenge_prize'   => $project->challenge_prize,
                            'amount'            => $project->bonus,
                            'lottery_sign'      => $project->lottery_sign,
                            'lottery_name'      => $project->lottery_name,
                            'method_sign'       => $project->method_sign,
                            'method_name'       => $project->method_name,
                            'project_id'        => $project->id,
                            'issue'             => $project->issue,
                            'issue_id'          => $issue->id
                        ];
                    }

                    if ($project->is_win == 3) {
                        $data[$project->user_id][] = [
                            'change_type'       => "he_return",
                            'top_id'            => $project->top_id,
                            'user_id'           => $project->user_id,
                            'amount'            => $project->total_cost,
                            'lottery_sign'      => $project->lottery_sign,
                            'lottery_name'      => $project->lottery_name,
                            'method_sign'       => $project->method_sign,
                            'method_name'       => $project->method_name,
                            'project_id'        => $project->id,
                            'issue'             => $project->issue,
                        ];

                        // 删除返点
                        LotteryCommission::where("project_id", $project->id)->update(['status' => -1]);
                    }
                }

                // 帐变
                foreach ($data as $playerId => $items) {
                    $res = ProjectLogic::send($items);
                    if ($res !== true) {
                        $failCount += count($items);
                    }
                }

                $i ++;
            } while($i <= $totalPage);

        } catch (\Exception $e) {
            $noticeMsg = "派奖异常-彩种:{$issue->lottery_sign}-issue:{$issue->issue}-{$slot}-原因:{$e->getMessage()}-{$e->getLine()}-{$e->getFile()}";
            telegramSend("send_exception", $noticeMsg);

            Clog::gameSend("send-process-job-exception-slot($slot)-issue-{$issue->issue}-{$e->getMessage()}", $issue->lottery_sign, []);

            return "派奖异常-" . $e->getMessage();
        }

        // 检测是否完成
        $totalCount = LotteryProject::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status_process', LotteryProject::STATUS_PROCESS_OPEN)->count();
        if ($totalCount <= 0) {
            $issue->status_process          = LotteryIssue::STATUS_PROCESS_SEND;
            $issue->time_end_send           = time();
            $issue->time_trace              = time();
            $issue->time_commission         = time();
            $issue->save();

            IssueLogic::trace($issue);
            IssueLogic::sendCommission($issue);
            IssueLogic::prizeCheck($issue, $slot);

            Clog::gameStat("send-process-finished-slot($slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);
        }

        Clog::gameSend("send-process-job-end-slot($slot)-issue-{$issue->issue}-end-fail({$failCount})", $issue->lottery_sign, []);

        Clog::gameStat("send-stat-end-slot($slot)-lottery:{$issue->lottery_sign}-issue-{$issue->issue}-count:{$totalSlotCount}-" . time(), $issue->lottery_sign);

        // 失败注数
        if ($failCount > 0) {
            return "派奖异常-失败注数-{$failCount}";
        }

        return true;
    }

    /**
     * 派奖结束的时候触发
     * @param $issue
     */
    static function triggerIssueOpenStart($issue) {

    }

    /**
     * 派奖结束的时候触发
     * @param $issue
     */
    static function triggerIssueOpenEnd($issue) {
        self::statJackpotLotteryIssueBonus($issue);
    }

    // 统计每期奖金
    static function statJackpotLotteryIssueBonus($issue) {
        // 官彩不能统计
        $lottery    = LotteryCache::getPartnerLottery($issue->lottery_sign);
        if (!$lottery || $lottery->partner_sign == 'system') {
            return true;
        }

        $partnerSign    = $lottery->partner_sign;
        $partnerLottery = LotteryCache::getPartnerLottery($issue->lottery_sign, $partnerSign);

        // 是否开启
        if (!LotteryLogic::isJackpotLottery($partnerLottery)) {
            return true;
        }

        // 只能统计一次
        $cacheKey = "sjlib_" . $issue->lottery_sign . "_" . $issue->issue;
        if (!cache()->add($cacheKey, 1, now()->addMinutes(10))) {
            Clog::jackpot("stat-jackpot-lottery-issue-repeat:{$issue->issue}-error-已经开始统计-" . $issue->lottery_sign);
            return true;
        }

        LotteryIssueBet::updateLotteryIssueBonus($partnerLottery, $issue);
        return true;
    }

    /**
     * @param $type
     * @param $partnerSign
     * @return Curl
     * @throws \ErrorException
     */
    static function getJackpotCurlHandle($type, $partnerSign) {
        $curlHandle = new Curl();
        $curlHandle->setHeader("ptype",             "BW");
        if (!isProductEnv()) {
            $mark = "test_";
        } else {
            $mark = "";
        }

        $curlHandle->setHeader("platform",          $mark . $partnerSign);
        $curlHandle->setHeader("jackpotmode",       $type);
        $curlHandle->setHeader("Content-Type",      "application/json");

        $curlHandle->setConnectTimeout(10);
        $curlHandle->setTimeout(10);


        return $curlHandle;
    }

    // 报警防御系统
    static function dangDangDang($fileName) {

    }

    /**
     * 获取某期　有注单的槽位
     * @param $lotterySign
     * @param $issueNo
     * @return array
     */
    static function getLotteryProjectSlotBetStat($lotterySign, $issueNo) {
        $items = LotteryProject::on('second')->select(
            "slot",
            DB::raw('COUNT(id) as project_count')
        )->where("lottery_sign", $lotterySign)->where("issue", $issueNo)->groupBy('slot')->get();

        $data = [];
        foreach ($items as $item) {
            $data[$item->slot] = $item->project_count;
        }

        return $data;
    }

    /**
     * 获取某期　有注单的槽位
     * @param $lotterySign
     * @param $issueNo
     * @return array
     */
    static function getLotteryProjectSlotOpenStat($lotterySign, $issueNo) {
        $items = LotteryProject::on('second')->select(
            "slot",
            DB::raw('COUNT(id) as project_count')
        )->where('lottery_sign', $lotterySign)->where('issue', $issueNo)->where('status_process', LotteryProject::STATUS_PROCESS_OPEN)->whereIn("is_win", [1, 3])->groupBy('slot')->get();

        $data = [];
        foreach ($items as $item) {
            $data[$item->slot] = $item->project_count;
        }

        return $data;
    }
}
