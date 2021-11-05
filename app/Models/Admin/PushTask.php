<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\Validator;
use JPush\Exceptions\JPushException;

// 推送
class PushTask  extends Base
{

    protected $table = 'push_tasks';

    public $rules = [
        'title'             => 'required',
        'content'           => 'required',
        'push_msg_type'     => 'required|in:1,2',
        'push_device_type'  => 'required|in:1,2,3',
        'push_time_type'    => 'required|in:1,2',
    ];

    static $msgTypes = [
        1 => "通知",
        2 => "消息"
    ];

    static $deviceTypes = [
        1 => "所有",
        2 => "苹果",
        3 => "安卓",
    ];

    static $timeTypes = [
        1 => "立即推送",
        2 => "每日固定",
    ];

    static $status = [
        0 => "初始化",
        1 => "已发送",
        2 => "异常",
    ];

    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $pageSize = 20) {
        $query          = self::orderBy('id', 'desc');

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
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

        // 时间判定
        if (isset($data['push_time_config'])) {
            $timeConfig = $data['push_time_config'];
            $timeArr    = explode('|', $timeConfig);
            foreach ($timeArr as $h) {
                if ($h != intval($h) || $h < 0 || $h > 23) {
                    return "对不起, 时间配置不正确";
                }
            }
        }

        $isEdit = $this->id && $this->id > 0 ? 1 : 0;

        $this->push_msg_type        = $data['push_msg_type'];
        $this->push_device_type     = $data['push_device_type'];
        $this->title                = $data['title'];
        $this->content              = $data['content'];

        $this->push_time_type       = $data['push_time_type'];
        $this->push_time_config     = isset($data['push_time_config']) ? $data['push_time_config'] : '';
        $this->admin_id             = $adminId;

        $this->save();

        // 立刻推送
        if ($data['push_time_type'] == 1 && !$isEdit) {
            // 发送
            $key        = configure("system_push_key");
            $secret     = configure("system_push_secret");

            $client = new \JPush\Client($key, $secret, storage_path() . "/logs/push.log");
            $client = $client->push();
            $client->setPlatform(['ios', 'android']);
            $client->addAllAudience();

            $client->setNotificationAlert($this->content);
            $client->options(['apns_production' => true]);
            try {
                $client->send();
                $this->status = 1;
                $this->save();
            } catch( JPushException $e) {
                $this->result = $e->getMessage();
                $this->status = 2;
                $this->save();
            }
        }

        return true;
    }

    // 修改状态
    public function changeStatus() {
        $this->status = $this->status > 0 ? 0 : 1;
        $this->save();
        return true;
    }
}
