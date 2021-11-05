<?php

namespace App\Job\Admin;

use App\Lib\Clog;
use App\Models\Admin\AdminUser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Admin\AdminBehavior;

class AdminBehaviorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type = 1;
    public $data = null;

    public function __construct($data) {
        $this->data = $data;
    }

    public function handle() {

        $userId     = $this->data['admin_id'];
        $adminUser  = AdminUser::find($userId);

        if (!$adminUser) {
            Clog::adminBehavior("save-behavior-" . $userId  . "-无效的管理用户", $this->data);
            return true;
        }

        try {
            $query = new AdminBehavior();
            $query->admin_id        = isset($this->data['admin_id']) ? $this->data['admin_id'] : "";
            $query->admin_username  = isset($this->data['admin_username']) ? $this->data['admin_username'] : "";

            $query->device          = isset($this->data['device']) ? $this->data['device'] : "";
            $query->platform        = isset($this->data['platform']) ? $this->data['platform'] : "";
            $query->browser         = isset($this->data['browser']) ? $this->data['browser'] : "";
            $query->agent           = isset($this->data['agent']) ? $this->data['agent'] : "";

            $query->route           = isset($this->data['route']) ? $this->data['route'] : "";
            $query->ip              = isset($this->data['ip']) ? $this->data['ip'] : "";
            $query->proxy_ip        = isset($this->data['proxy_ip']) ? $this->data['proxy_ip'] : "";
            $query->params          = isset($this->data['params']) ? $this->data['params'] : "";
            $query->day             = isset($this->data['day']) ? $this->data['day'] : "";

            $query->domain          = isset($this->data['domain']) ? $this->data['domain'] : "";
            $query->action          = isset($this->data['action']) ? $this->data['action'] : "";

            $query->country         = isset($this->data['country']) ? $this->data['country'] : "";
            $query->city            = isset($this->data['city']) ? $this->data['city'] : "";
            $query->save();
        } catch(\Exception $e) {
            Clog::adminBehavior("Error-" . $userId  . "-保存失败-" . $e->getMessage() . "|" . $e->getLine() . "|" . $e->getFile());
            throw $e;
        }

        return true;
    }

}
