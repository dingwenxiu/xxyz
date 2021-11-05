<?php

namespace App\Models\System;

use App\Lib\Telegram\TelegramTrait;
use App\Models\Base;

class SysTelegramChannel extends Base
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'sys_telegram_channel';

    use TelegramTrait;

    // 获取列表
    static function getList($c,$pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        if (isset($c['channel_sign']) && $c['channel_sign'] && $c['channel_sign'] != "all") {
            $query->where('channel_sign', '=', $c['channel_sign']);
        }

        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != "all") {
            $query->where('partner_sign',  $c['partner_sign']);
        }

        if (isset($c['status']) && $c['status'] != "all") {
            $query->where('status',  $c['status']);
        }

        if (isset($c['channel_group_name']) && $c['channel_group_name'] && $c['channel_group_name'] != "all") {
            $query->where('channel_group_name',  $c['channel_group_name']);
        }

        if (isset($c['channel_id']) && $c['channel_id'] && $c['channel_id'] != "all") {
            $query->where('channel_id',  $c['channel_id']);
        }

        if (isset($c['channel_sign']) && $c['channel_sign'] && $c['channel_sign'] != "all") {
            $query->where('channel_sign',  $c['channel_sign']);
        }

        if (isset($c['id']) && $c['id'] && $c['id'] != "all") {
            $query->where('id',  $c['id']);
        }

        $total       = $query -> count();

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize    = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;

        $offset      = ($currentPage - 1) * $pageSize;
        $data        = $query -> skip($offset) -> take($pageSize) -> get();
        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];


    }

    public function saveItem($params) {
        $this->channel_sign         = $params["channel_sign"];
        $this->channel_group_name   = $params["channel_group_name"];
        $this->status               = 1;
        $this->save();

        return true;
    }

    /**
     * 总后台保存数据
     * @param $params
     * @return bool
     */
    public function systemSaveItem($params) {
        $this->partner_sign         = $params["partner_sign"]      ??$this->partner_sign;
        $this->channel_sign         = $params["channel_sign"]      ??$this->channel_sign;
        $this->channel_group_name   = $params["channel_group_name"]??$this->channel_group_name;
        $this->channel_id           = $params["channel_id"]        ??$this->channel_id;
        $this->status               = $params["status"]            ??$this->status;
        $this->save();
        return true;
    }

    /**
     * 找到频道信息　并更新
     * @return string
     */
    public function updateChannelId() {
        if (!$this->channel_group_name) {
            return "对不起, 请完善你的频道信息";
        }

        $res = self::findChannelId(configure('web_send_boot_token'), $this->channel_group_name);

        if ($res !== false) {
            $this->channel_id = $res;
            $this->save();
            return true;
        }

        return "对不起, 没有查找到信息, 请在群组发一消息然后再尝试";
    }

    /**
     * 初始化用户的channel
     * @param $partnerSign
     * @return bool
     */
    static function initDefaultTelegramChannel($partnerSign) {
        $config     = config("partner.main.telegram_channel");

        // 师否存在默认ID
        $channelIdArr = config("partner.main.telegram_channel_id");
        $partnerChannelIdArr = [];
        if (isset($channelIdArr[$partnerSign])) {
            $partnerChannelIdArr = $channelIdArr[$partnerSign];
        }

        $data = [];

        foreach ($config as $sign => $name) {
            $data[] = [
                'partner_sign'          => $partnerSign,
                'channel_sign'          => $sign,
                'channel_group_name'    => $partnerSign . "_" . $name,
                'channel_id'            => isset($partnerChannelIdArr[$sign]) ? $partnerChannelIdArr[$sign] : '',
                'status'                => 1,
            ];
        }

        self::insert($data);

        return true;
    }
}
