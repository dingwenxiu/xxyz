<?php

namespace App\Jobs\Player;

use App\Models\Player\PlayerIp;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PlayerIpLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type = 1;
    public $data = null;

    public function __construct($data) {
        $this->data = $data;
    }

    public function handle() {
        

        $where['user_id']         = isset($this->data['user_id']) ? $this->data['user_id'] : "";
        $where['partner_sign']    = isset($this->data['partner_sign']) ? $this->data['partner_sign'] : "";
        $where['ip']              = isset($this->data['ip']) ? $this->data['ip'] : "";

        $playerIp                 = PlayerIp::where($where)->first();

        if($playerIp)
        {
           $playerIp->login_count=$playerIp->login_count+1;
           $playerIp->save();
        }
        else
        {
            $query = new PlayerIp();
            $query->user_id         = isset($this->data['user_id']) ? $this->data['user_id'] : "";
            $query->partner_sign    = isset($this->data['partner_sign']) ? $this->data['partner_sign'] : "";
            $query->ip              = isset($this->data['ip']) ? $this->data['ip'] : "";
             

            $query->username        = isset($this->data['username']) ? $this->data['username'] : "";
            $query->nickname        = isset($this->data['nickname']) ? $this->data['nickname'] : "";
            
            $query->top_id          = isset($this->data['top_id']) ? $this->data['top_id'] : "";
            $query->parent_id       = isset($this->data['parent_id']) ? $this->data['top_id'] : "";
            
            $query->country         = isset($this->data['country']) ? $this->data['country'] : "";
            $query->city            = isset($this->data['city']) ? $this->data['city'] : "";
            $query->login_count     = 1;

            $query->save();
        }
        
        

        return true;
    }

}
