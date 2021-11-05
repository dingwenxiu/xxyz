<?php

namespace App\Models\Game;

class LotteryIssueBonus extends BaseGame
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'lottery_issue_bonus';

    // 获取列表
    static function getList($c) {
        $query = self::orderBy('id', 'desc');

        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', '=', $c['lottery_sign']);
        }

        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != "all") {
            $query->where('partner_sign', '=', $c['partner_sign']);
        }

        if (isset($c['username']) && $c['username']) {
            $query->where('username', '=', $c['username']);
        }

        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != "all") {
            $query->where('method_sign', '=', $c['method_sign']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }
}
