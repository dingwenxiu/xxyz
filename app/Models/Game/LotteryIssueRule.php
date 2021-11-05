<?php

namespace App\Models\Game;

use Illuminate\Support\Facades\Validator;

class LotteryIssueRule extends BaseGame
{
    public $rules = [
        'lottery_sign'        => 'required|min:4|max:32',
        'begin_time'        => 'required|date_format:"H:i:s"',
        'end_time'          => 'required|date_format:"H:i:s"',
        'issue_seconds'     => 'required|integer',
        'first_time'        => 'required|date_format:"H:i:s"',
        'adjust_time'       => 'required|integer',
        'encode_time'       => 'required|integer',
        'issue_count'       => 'required|integer',
    ];

    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'lottery_issue_rules';

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

    // 保存
    public function saveItem($data, $adminId = 0) {
        $validator  = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // 游戏是否存在
        $lottery = Lottery::findBySign($data['lottery_sign']);
        if (!$lottery) {
            return "对不起, 无效的游戏";
        }

        $this->lottery_sign         = $data['lottery_sign'];
        $this->lottery_name         = $lottery->cn_name;
        $this->begin_time           = $data['begin_time'];
        $this->end_time             = $data['end_time'];
        $this->issue_seconds        = intval($data['issue_seconds']);
        $this->first_time           = $data['first_time'];
        $this->adjust_time          = intval($data['adjust_time']);
        $this->encode_time          = intval($data['encode_time']);
        $this->issue_count          = intval($data['issue_count']);

        return $this->save();
    }
}
