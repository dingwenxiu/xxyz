<?php

namespace App\Http\Controllers\AdminApi\Lottery;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Models\Game\LotteryIssue;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryIssueCancel;
use App\Models\Partner\Partner;
use Illuminate\Support\Facades\Hash;

/**
 * version 1.0
 * 奖期
 * Class ApiLotteryIssueController
 * @package App\Http\Controllers\AdminApi\Lottery
 */
class ApiLotteryIssueController extends ApiBaseController
{
    // 奖期列表
    public function issueList()
    {
        $c      = request()->all();
        $data   = LotteryIssue::getList($c);

        $_data = [];
        $seriesList = config('game.main.series');

        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                        => $item->id,
                "series_id"                 => $item->series_id,
                "issue"                     => $item->issue,
                "lottery_name"              => $item->lottery_name,
                "lottery_sign"              => $item->lottery_sign,
                "begin_time"                => date("Y-m-d H:i:s", $item->begin_time),
                "end_time"                  => date("Y-m-d H:i:s", $item->end_time),
                "allow_encode_time"         => date("Y-m-d H:i:s", $item->allow_encode_time),
                "official_code"             => $item->official_code,

                "time_open"                 => $item->time_open ? date("Y-m-d H:i:s", $item->time_open) : '',
                "time_send"                 => $item->time_send ? date("Y-m-d H:i:s", $item->time_send) : '',
                "time_trace"                => $item->time_trace ? date("Y-m-d H:i:s", $item->time_trace) : '',
                "time_commission"           => $item->time_commission ? date("Y-m-d H:i:s", $item->time_commission) : '',

                "time_end_open"             => $item->time_end_open ? date("Y-m-d H:i:s", $item->time_end_open) : '',
                "time_end_send"             => $item->time_end_send ? date("Y-m-d H:i:s", $item->time_end_send) : '',
                "time_end_trace"            => $item->time_end_trace ? date("Y-m-d H:i:s", $item->time_end_trace) : '',
                "time_end_commission"       => $item->time_end_commission ? date("Y-m-d H:i:s", $item->time_end_commission) : '',

                "status_process"            => $item->status_process,
                "status_commission"         => $item->status_commission,
                "status_trace"              => $item->status_trace,

                "can_encode"                => $item->status_process == 0 && $item->allow_encode_time < time() ? 1 : 0,
                "can_cancel"                => $item->status_process < 2 && $item->allow_encode_time + 30 < time() ? 1 : 0,
                "can_open"                  => $item->status_process == 1 && $item->allow_encode_time + 60 < time() ? 1 : 0,
                "can_send"                  => $item->status_process == 2 && $item->allow_encode_time + 60 < time() ? 1 : 0,
                "can_commission"            => $item->status_process == 3 && $item->status_commission == 0 ? 1 : 0,
                "can_trace"                 => $item->status_process == 3 && $item->status_trace  == 0 ? 1 : 0,
                "encode_username"           => $item->encode_username,
            ];
        }

        $data['data'] = $_data;

        $data['lottery_options']    = Lottery::getSelectOptions(true);
        $data['partner_options']    = Partner::getOptions();
        $data['series_options']     = $seriesList;

        // tab 选项
        $data['series_tab_options'] = Lottery::getSeriesLotteryTabOptions();
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 奖期详情
    public function issueDetail($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['issue'] = [];

        // 获取用户
        if ($id) {
            $issue = LotteryIssue::find($id);
            if ($issue) {
                $data['issue']    = $issue;
            }
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 录号
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueEncode($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $issue = LotteryIssue::find($id);
        if (!$issue) {
            return Help::returnApiJson("对不起, 无效的奖期！", 0);
        }

        // 奖期是否已处理
        if ($issue->status_process > 0) {
            return Help::returnApiJson("对不起, 奖期已经处理！", 0);
        }

        // 是否可以录号
        if ($issue->allow_encode_time > time()) {
            return Help::returnApiJson("对不起, 未到录号时间-！" . date("Y-m-d H:i:s", $issue->allow_encode_time), 0);
        }

        $lottery  = Lottery::findBySign($issue->lottery_sign);

        $code = request("code", "");
        if (!$code) {
            return Help::returnApiJson("对不起, 请输入号码！", 0);
        }

        // 检查格式
        $codeArr = explode(",", $code);
        if (!$lottery->checkCodeFormat($codeArr)) {
            return Help::returnApiJson("对不起, 无效的号码格式！", 0);
        }

        $fundPassword = request("fund_password", "");
        if (!$fundPassword) {
            return Help::returnApiJson("对不起,　请输入资金密码！", 0);
        }

        // 资金密码
        if (!Hash ::check($fundPassword, $adminUser -> fund_password)) {
            return Help ::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        $res = IssueLogic::encode($issue, $code, $adminUser->id);

        if ($res !== true) {
            return Help ::returnApiJson($res, 0);
        }

        return Help::returnApiJson('恭喜, 录号成功!', 1);
    }

    /**
     * 撤单
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueCancel($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $issue = LotteryIssue::find($id);
        if (!$issue) {
            return Help::returnApiJson("对不起, 无效的奖期！", 0);
        }

        // 获取辅助数据
        $action = request("action", "process");
        if ($action == 'option') {
            $item = LotteryIssueCancel::where('issue_id', $id)->first();

            if ($item) {
                return Help::returnApiJson("获取数据成功", 1, $item);
            }

            return Help::returnApiJson("未处理完成, 请稍后", 0);
        }
        // 彩票
        $lottery = Lottery::findBySign($issue->lottery_sign);
        if (!$lottery) {
            return Help::returnApiJson("对不起, 无效的彩种！", 0);
        }

        // 时间是否可用
        if ($issue->begin_time > time()) {
            return Help::returnApiJson("对不起, 当前奖期不可撤单, 未到时间！", 0);
        }

        // 是否已经开奖
        if ($issue->status_process > 1) {
            return Help::returnApiJson("对不起, 当前奖期不可撤单, 已经开奖！", 0);
        }

        $res = $issue->cancelProjects($adminUser);
        if ($res === true) {
            return Help::returnApiJson('恭喜, 发起撤单成功, 请稍等片刻!', 1);
        }

        return Help::returnApiJson($res, 0);
    }

    /**
     * 开奖
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueOpen($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $issue = LotteryIssue::find($id);
        if (!$issue) {
            return Help::returnApiJson("对不起, 无效的奖期！", 0);
        }

        // 时间是否可用
        if ($issue->status_process != LotteryIssue::STATUS_PROCESS_ENCODE) {
            return Help::returnApiJson("对不起, 当前奖期不可开奖,　未录号！", 0);
        }

        IssueLogic::open($issue);

        return Help::returnApiJson('恭喜, 发起开奖成功, 请稍等片刻!', 1);
    }

    /**
     * 开奖
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueSend($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $issue = LotteryIssue::find($id);
        if (!$issue) {
            return Help::returnApiJson("对不起, 无效的奖期！", 0);
        }

        // 时间是否可用
        if ($issue->status_process != LotteryIssue::STATUS_PROCESS_OPEN) {
            return Help::returnApiJson("对不起, 当前奖期不可派奖,　未开奖！", 0);
        }

        IssueLogic::sendBonus($issue);

        return Help::returnApiJson('恭喜, 发起开奖成功, 请稍等片刻!', 1);
    }

    /**
     * 追号
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function issueTrace($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $issue = LotteryIssue::find($id);
        if (!$issue) {
            return Help::returnApiJson("对不起, 无效的奖期！", 0);
        }

        // 时间是否可用
        if ($issue->status_process != LotteryIssue::STATUS_PROCESS_SEND) {
            return Help::returnApiJson("对不起, 当前奖期不可追号,　未派奖！", 0);
        }

        IssueLogic::trace($issue);

        return Help::returnApiJson('恭喜, 发起开奖成功, 请稍等片刻!', 1);
    }

    // 奖期生成
    public function issueGen()
    {
        $params     = request()->all();
        $lotteryId  = $params['lottery_sign'];

        $lottery    = Lottery::findBySign($lotteryId);

        if (!$lottery) {
            return Help::returnApiJson("对不起, 无效的彩种!", 0);
        }

        // 获取辅助数据
        if (isset($params['action']) && $params['action'] == 'option') {
            $lastIssue = LotteryIssue::where("lottery_sign", $lotteryId)->orderBy("begin_time", "desc")->first();
            if ($lastIssue) {
                $startDay = date("Y-m-d", strtotime($lastIssue->day) + 86400);
            } else {
                $startDay = date("Y-m-d");
            }

            $endDay = date("Y-m-d", strtotime($startDay) + 86400 * 7);

            return Help::returnApiJson("获取数据成功", 1, ['start_day' => $startDay, 'end_day' => $endDay, 'type' => $lottery->issue_type]);
        }

        /** ======================= 香港六合彩 ===================== */
        if ($lottery->en_name == "hklhc") {
            $issue      = request('issue');
            $openTime   = request('open_time');
            $res = IssueLogic::genLhcIssue($lottery, $issue, $openTime);
            if ($res === true) {
                return Help::returnApiJson("恭喜, 生成香港六合彩奖期-{$issue}-成功", 1);
            } else {
                return Help::returnApiJson("恭喜, 生成香港六合彩奖期-{$issue}-失败", 0);
            }
        }

        /** ======================= 其他彩种 ===================== */
        // 时间检测
        if (!isset($params['start_day']) || !isDateDay($params['start_day'])) {
            return Help::returnApiJson("对不起, 无效的开始时间!", 0);
        }

        if (!isset($params['end_day']) || !isDateDay($params['end_day'])) {
            return Help::returnApiJson("对不起, 无效的结束时间!", 0);
        }

        if (strtotime($params['end_day']) < strtotime($params['start_day'])) {
            return Help::returnApiJson("对不起, 开始时间不能大于结束时间!", 0);
        }

        // 生成
        $res = $lottery->genIssue($params['start_day'], $params['end_day'], $params['start_issue']);

        if(!is_array($res) || count($res) == 0) {
            return Help::returnApiJson($res, 0);
        } else {

            // 成功一部分
            $genRes = true;
            foreach ($res as $day => $_r) {
                if ($_r !== true) {
                    $genRes = false;
                }
            }

            if (!$genRes) {
                return Help::returnApiJson("您好, 生成奖期 {$params['start_day']} - {$params['end_day']} - 部分完成!", 0, ['res' => $res]);
            }
        }

        return Help::returnApiJson("恭喜, 生成奖期 {$params['start_day']} - {$params['end_day']} - 成功", 1, $res);
    }

    // 奖期删除
    public function issueDel()
    {
        $params         = request()->all();
        $lotterySign    = $params['lottery_sign'];

        $lottery    = Lottery::findBySign($lotterySign);

        if (!$lottery) {
            return Help::returnApiJson("对不起, 无效的彩种!", 0);
        }

        // 生成
        $res = LotteryIssue::delIssue($lotterySign, $params['start_day'],  $params['end_day']);
        if(!$res['status']) {
            return Help::returnApiJson($res['msg'], 0);
        }

        return Help::returnApiJson("恭喜, {$params['start_day']} - {$params['end_day']} - {$res['msg']}", 1);
    }
}
