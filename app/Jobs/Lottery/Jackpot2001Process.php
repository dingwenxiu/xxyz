<?php

namespace App\Jobs\Lottery;

use App\Lib\Logic\Lottery\JackpotLogic;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019.12
 * 游侠控水
 * Class Jackpot2001Process
 * @package App\Jobs\Lottery
 */
class Jackpot2001Process implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $projectId       = "";
    public $type            = "add";

    public $timeout = 300;

    public function __construct($projectId, $type) {
        $this->projectId    = $projectId;
        $this->type         = $type;
    }

    // 开始
    public function handle() {
        JackpotLogic::do2000Jackpot($this->projectId, $this->type);
        return true;
    }

}
