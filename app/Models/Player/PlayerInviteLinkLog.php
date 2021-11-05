<?php

namespace App\Models\Player;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PlayerInviteLinkLog extends Model
{
    protected $table = 'user_invite_link_log';

    public $rules = [
        'prize_group'   => 'required',
        'desc'          => 'required|min:2|max:256',
        'expired_at'    => 'required|date_format:Y-m-d H:i:s',
    ];

    /**
     * 获取用户列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
        $query = self::orderBy('id', 'DESC');

        // 奖金组
        if (isset($c['prize_group']) && $c['prize_group']) {
            $query->where('prize_group', $c['prize_group']);
        }

        // status
        if (isset($c['status']) && $c['status'] && $c['status'] != 'all') {
            $query->where('status', $c['status']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $pageSize       = isset($c['pageSize']) ? intval($c['pageSize']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 保存链接地址
     * @param $user
     * @param $params
     * @return bool|string
     */
    public function saveItem($user, $params)
    {
        $validator  = Validator::make($params, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // 奖金组设定
        $minGroup = configure("user_min_prize_group");
        $maxGroup = configure("user_min_prize_group");
        $maxGroup = $maxGroup > $user->prize_group ? $user->prize_group : $maxGroup;

        // 奖金组
        if ($params['prize_group'] > $maxGroup || $params['prize_group'] < $minGroup) {
            return "对不起, 奖金组范围不正确";
        }

        $channel = config("user.main.market_channel");
        if (!array_key_exists($params['channel'], $channel)) {
            return "对不起, 无效的渠道";
        }

        $this->user_id      = $user->id;
        $this->username     = $user->username;
        $this->code         = $user->id;
        $this->qq           = isset($params['qq']) ? $params['qq'] : "";
        $this->wechat       = isset($params['wechat']) ? $params['wechat'] : "";
        $this->desc         = isset($params['desc']) ? $params['desc'] : "";

        $this->prize_group  = $params['prize_group'];
        $this->expired_at   = $params['expired_at'];
        $this->channel      = $params['channel'];


        $this->save();
        return true;
    }
}
