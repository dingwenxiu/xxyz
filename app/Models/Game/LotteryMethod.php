<?php

namespace App\Models\Game;

use Illuminate\Support\Facades\Validator;

class LotteryMethod extends BaseGame
{
    protected $table = 'lottery_methods';

    public    $rules = [
        "challenge_type"      => "integer",
        "challenge_min_count" => "numeric",
        "challenge_bonus"     => "numeric",
        "challenge_config"    => "string",
    ];
    public    $save_items_rules = [
        "challenge_type"      => "integer",
        "challenge_min_count" => "numeric",
        "challenge_bonus"     => "numeric",
    ];

    // 获取列表
    static function getList($c) {
        $query = self::orderBy('id', 'desc');

        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign',  $c['lottery_sign']);
        }

        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != "all") {
            $query->where('method_sign', $c['method_sign']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    static function  getMethodConfig($lotterySign) {
        return self::where("lottery_sign", $lotterySign)->where('status', 1)->where('show', 1)->get();
    }

    // 保存
    public function saveItem($params) {
        // 验证参数
        $validator  = Validator::make($params, $this->save_items_rules);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $this->challenge_type                 = $params['challenge_type']      ??$this->challenge_type;
        $this->challenge_min_count            = $params['challenge_min_count'] ??$this->challenge_min_count;
        $this->challenge_bonus                = $params['challenge_bonus']     ??$this->challenge_bonus;
        $this->challenge_config               = $params['challenge_config']    ??$this->challenge_config;
        $this->save();
        return true;
    }

}
