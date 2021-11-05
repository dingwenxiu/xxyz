<?php

namespace App\Models\Player;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PlayerInviteLink extends Model
{
    protected $table = 'user_invite_link';

    public $rules = [
        'prize_group'   => 'required',
        'channel'       => 'required|min:2|max:32',
        'user_type'     => 'required|in:2,3',
        'expire'        => 'required|int',
    ];

    /**
     * 获取用户列表
     * @param $c
     * @return mixed
     */
    static function getList($c)
    {
        $query = self::orderBy('id', 'DESC');

        // 奖金组
        if (isset($c['prize_group']) && $c['prize_group']) {
            $query->where('prize_group', $c['prize_group']);
        }

        // status
        if (isset($c['status']) && $c['status'] && $c['status'] != 'all') {
            $query->where('status', $c['status']);
        }

        $currentPage = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $pageSize = isset($c['pageSize']) ? intval($c['pageSize']) : 15;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $items = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取注册连接给前台
     * @param $c
     * @param $user
     * @param int $pageSize
     * @return array
     */
    static function getRegisterLinkForFrontend($c, $user, $pageSize = 15)
    {
        $query = self::where("partner_sign", $user->partner_sign)->where("user_id", $user->id)->orderBy('id', 'DESC');
        $currentPage = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $pageSize = isset($c['pageSize']) ? intval($c['pageSize']) : $pageSize;
        $offset = ($currentPage - 1) * $pageSize;
        $total = $query->count();
        $items = $query->skip($offset)->take($pageSize)->get();
        $data = [];
        foreach ($items as $item) {
            $tmp = [
                'url' => $item->code,
                'id' => $item->id,
                'prize_group' => $item->prize_group,
                'total_register' => $item->total_register,
                'status' => $item->status,
                'channel' => $item->channel,
                'type' => $item->type == Player::PLAYER_TYPE_PROXY ? "代理" : "会员",
                'expired_at' => $item->expired_at == 0 ? "永久" : date("Y-m-d", strtotime($item->created_at)+intval($item->expired_at)* 86400),
                'created_at' => date("Y-m-d", strtotime($item->created_at)),
            ];

            $data[] = $tmp;
        }

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
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
        $minGroup = configure("player_register_min_prize_group", 1700);
        $maxGroup = configure("player_register_max_prize_group", 1980);
        $maxGroup = $maxGroup > $user->prize_group ? $user->prize_group : $maxGroup;

        // 奖金组
        if ($params['prize_group'] > $maxGroup || $params['prize_group'] < $minGroup) {
            return "对不起, 奖金组范围不正确";
        }

        // 所有过期时间配置
        // @todo

        // 过期时间
        if ($params['expire'] <= 0) {
            $params['expire'] = 0;
        }

        $codeFix = configure("user_invite_link_code_fix", 118020);

        $this->partner_sign     = $user->partner_sign;
        $this->user_id          = $user->id;
        $this->username         = $user->username;

        $this->type             = $params['user_type'];
        $this->qq               = isset($params['qq']) ? $params['qq'] : "";
        $this->wechat           = isset($params['wechat']) ? $params['wechat'] : "";
        $this->remark           = isset($params['remark']) ? $params['remark'] : "";

        $this->prize_group      = $params['prize_group'];
        $this->expired_at       = $params['expire'];
        $this->channel          = $params['channel'];
        $this->save();

        $this->code             = $this->id + $codeFix;
        $this->save();
        return true;
    }
}
