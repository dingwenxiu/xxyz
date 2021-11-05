<?php

namespace App\Jobs\User;

use App\Lib\Clog;
use App\Models\Player\PlayerInviteLinkLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Behavior implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type = '';
    public $data = null;

    public function __construct($type, $data) {
        $this->type = $type;
        $this->data = $data;
    }

    public function handle() {
        // 链接注册 == 保存到日志
        if ($this->type == 'link_register') {
            try {
                $item = PlayerInviteLinkLog::create($this->data);
            } catch(\Exception $e) {
                Clog::userBehavior("behavior-link-register-保存失败-" . $e->getMessage() . "|" . $e->getLine() . "|" . $e->getFile());
                throw $e;
            }

            Clog::userBehavior("behavior-link-register-成功", $this->data);
        }


        return true;
    }

}
