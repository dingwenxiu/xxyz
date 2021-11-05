<?php

namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class TelegramChatId extends Model
{
    protected $table = 'sys_telegram_chat_id';

    public $rules = [
        'title'     => 'required',
        'chat_id'   => 'required|min:2|max:32',
        'type'      => 'required|min:1|max:32',
    ];

    // 类型
    static $types = [
        1   => "通用",
        2   => "提现",
        3   => "审核",
        4   => "充值",
        5   => "报表",
    ];


    /**
     * 获取id列表
     * @param $c
     * @param $offset
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $offset, $pageSize) {
        $query = self::orderBy('id', 'DESC');

        // 游戏账户
        if (isset($c['chat_id']) && $c['chat_id']) {
            $query->where('chat_id', $c['chat_id']);
        }

        // 平台
        if (isset($c['type']) && $c['type']) {
            $query->where('type', $c['type']);
        }

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    public function saveItem() {
        $this->title        = request('title');
        $this->chat_id      = request('chat_id');
        $this->type         = request('type');
        $this->status       = 1;

        $this->save();
        return true;
    }

    /**
     * 获取所有Chat Id
     * @return array
     */
    static function getAllChatId() {
        $items  = self::where('status', 1)->get();
        $data   = [];
        foreach ($items as $item) {
            if (!isset($data[$item->type])) {
                $data[$item->type] = [];
            }
            $data[$item->type][] = $item->chat_id;
        }
        return $data;
    }
}
