<?php

namespace App\Jobs\Partner;

use App\Lib\Clog;
use App\Models\Partner\PartnerAdminUser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Partner\PartnerAdminBehavior;

class PartnerBehaviorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type = 1;
    public $data = null;

    public function __construct($data) {
        $this->data = $data;
    }

    public function handle() {

        $userId     = $this->data['partner_admin_id'];
        $partnerUser  = PartnerAdminUser::find($userId);

        if (!$partnerUser) {
            Clog::partnerBehavior("save-behavior-" . $userId  . "-无效的管理用户", $this->data);
            return true;
        }

        try {
            $query = new PartnerAdminBehavior();
            $query->partner_admin_id        = isset($this->data['partner_admin_id']) ? $this->data['partner_admin_id'] : "";
            $query->partner_admin_username  = isset($this->data['partner_admin_username']) ? $this->data['partner_admin_username'] : "";

            $query->partner_sign  = isset($this->data['partner_sign']) ? $this->data['partner_sign'] : "";

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
            Clog::partnerBehavior("Error-" . $userId  . "-保存失败-" . $e->getMessage() . "|" . $e->getLine() . "|" . $e->getFile());
            throw $e;
        }

        return true;
    }

}
