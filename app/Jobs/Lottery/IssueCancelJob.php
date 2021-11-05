<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\Lottery\ProjectLogic;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryIssueCancel;
use App\Models\Game\LotteryProject;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * 撤单
 * Class IssueCancelJob
 * @package App\Jobs\Player
 */
class IssueCancelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $issueId     = 0;
    public $adminId     = 0;
    public $cacheKey    = "";

    public function __construct($issueId, $adminId) {
        $this->issueId  = $issueId;
        $this->adminId  = $adminId;
        $this->cacheKey = "issue_cancel_" . $issueId;
    }

    public function handle() {
        $return = [
            'status'        => true,
            'total_project' => 0,
            'total_success' => 0,
            'total_fail'    => 0,
            "msg"           => "",
        ];


        $issue = LotteryIssue::find($this->issueId);
        if (!$issue) {
            $return['msg'] = "issue-cancel-error-奖期不存在-管理员:{$this->adminId}-奖期ID:{$this->issueId}-" . time();
            Clog::issueCancel($return['msg']);

            LotteryIssueCancel::saveItem($issue, $return, time(), time());
            return true;
        }

        // 已经开了　就不能撤了
        if ($issue->status_process > 1) {
            $return['msg'] = "issue-cancel-error-已开奖无法撤单-管理员:{$this->adminId}-奖期:{$issue->issue}-状态:{$issue->status_process}-" . time();
            Clog::issueCancel("issue-cancel-error-已开奖无法撤单-管理员{$this->adminId}-{$issue->issue}-" . time());

            LotteryIssueCancel::saveItem($issue, $return, time(), time());
            return true;
        }

        // 如果已经在处理
        if (!cache()->add($this->cacheKey, 1, now()->addMinutes(5))) {
            Clog::issueCancel("issue-cancel-error-处理中-管理员{$this->adminId}-{$issue->issue}-" . time());
            return true;
        }

        // 1. 获取所有的未开
        $totalCount = LotteryProject::where('lottery_sign', $issue->lottery_sign)->where('issue', $issue->issue)->where('status_process', LotteryProject::STATUS_PROCESS_INIT)->count();
        if ($totalCount <= 0) {
            $return['msg'] = "订单数为0";
            cache()->forget($this->cacheKey);

            Clog::issueCancel("issue-cancel-finished-订单数为0-管理员:{$this->adminId}-{$issue->issue}-" . time());

            LotteryIssueCancel::saveItem($issue, $return, time(), time());

            return $return;
        }

        Clog::issueCancel("issue-cancel-start-管理员:{$this->adminId}-issue:{$issue->issue}-total-{$totalCount}-" . time() , $issue->lottery_sign);

        $return['total_project'] = $totalCount;

        $pageSize   = 1000;
        $totalPage  = ceil($totalCount / $pageSize);

        $startTIme      = time();

        $hasException = false;
        try {
            $i = 1;
            do {
                $offset     = $return['total_fail'];
                $projects   = LotteryProject::where('lottery_sign', $issue->lottery_sign)
                    ->where('issue', $issue->issue)
                    ->where('status_process', LotteryProject::STATUS_PROCESS_INIT)
                    ->orderBy("id", "ASC")
                    ->skip($offset)->take($pageSize)->get();

                foreach ($projects as $project) {
                    $res = ProjectLogic::cancel($project, true);
                    if ($res === true) {
                        $return['total_success'] ++;
                    } else {
                        $return['total_fail'] ++;
                        Clog::issueCancel("对不起,后台管理员{$this->adminId}-total-{$totalCount}-撤单失败-{$project->id}-" . $res, $project->lottery_sign);
                    }
                }

                $i ++;
            } while($i < $totalPage);

        } catch (\Exception $e) {
            $hasException   = true;
            $return['msg']  = $e->getMessage() . "-" . $e->getLine() . "-" . $e->getFile();
            Clog::issueCancel("issue-cancel-exception-{$return['msg']}-total-{$totalCount}-管理员{$this->adminId}-{$issue->issue}-" . time() , $issue->lottery_sign);
        }

        $endTime = time();

        $return['status']       = $return['total_project'] == $return['total_success'] ;

        if (!$hasException) {
            $return['msg']          = $return['total_project'] == $return['total_success'] ? "撤单成功, 共{$return['total_project']}单" : "对不起, 部分完成,总{$return['total_project']}单, 成功{$return['total_success']}单";
        }

        // 如果撤单完成
        if ($return['status'] === true) {
            $issue->status_process          = LotteryIssue::STATUS_PROCESS_CANCEL;
            $issue->time_open               = time();
            $issue->time_end_open           = time();

            $issue->time_send               = time();
            $issue->time_end_send           = time();

            $issue->time_commission         = time();
            $issue->time_end_commission     = time();

            $issue->save();
        }

        LotteryIssueCancel::saveItem($issue, $return, $startTIme, $endTime);

        cache()->forget($this->cacheKey);
        Clog::issueCancel("issue-cancel-end-管理员{$this->adminId}-total-{$totalCount}-success-{$return['total_success']}-{$issue->issue}-" . time() , $issue->lottery_sign);

        return true;
    }

}
