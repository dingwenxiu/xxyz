<?php

namespace App\Models\Game;

class LotteryIssueCancel extends BaseGame
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table    = 'lottery_issue_cancel';

    public $timestamps  = false;

    // 获取列表
    static function getList($c) {
        $query = self::orderBy('id', 'desc');

        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', '=', $c['lottery_sign']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存撤单记录
    static function saveItem($issue, $return, $startTime, $endTime) {
        $lotteryIssueCancel = new LotteryIssueCancel();
        $lotteryIssueCancel->lottery_sign   = $issue->lottery_sign;
        $lotteryIssueCancel->lottery_name   = $issue->lottery_name;
        $lotteryIssueCancel->issue_id       = $issue->id;
        $lotteryIssueCancel->issue          = $issue->issue;

        $lotteryIssueCancel->total_project  = $return['total_project'];
        $lotteryIssueCancel->total_success  = $return['total_success'];
        $lotteryIssueCancel->total_fail     = $return['total_fail'];

        $lotteryIssueCancel->start_time     = $startTime;
        $lotteryIssueCancel->end_time       = $endTime;
        $lotteryIssueCancel->status         = $return['status'];
        $lotteryIssueCancel->msg            = $return['msg'];

        $lotteryIssueCancel->save();

        return true;
    }
}
