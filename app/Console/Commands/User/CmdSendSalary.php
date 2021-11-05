<?php namespace App\Console\Commands\User;

use App\Console\Commands\Command;
use App\Lib\Logic\Player\SalaryLogic;

class CmdSendSalary extends Command {

    protected $signature = 'player:send_salary {action}';

    protected $description = "发放玩家日工资";

    public function handle()
    {
        $action   = $this->argument('action', 'last_day');

        // 判定初始分钟
        if ($action == "last_day") {
            $day = date("Ymd");
        } else {
            $day = $action;
        }

        $key = "send_salary_1_" . $day;

        if (!cache()->add($key, 1, 10)) {
            $this->info("send-salary-error-{$day}-日工资发放中!");
            return true;
        }

        SalaryLogic::process($day, true);

        cache()->forget($key);
        $this->info("send-salary-{$day}-finished!");
        return true;
    }

}
