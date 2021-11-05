<?php namespace App\Console\Commands\User;

use App\Console\Commands\Command;
use App\Lib\Logic\Player\DividendLogic;

class CmdSendDividend extends Command {

    protected $signature = 'player:send_dividend {action}';

    protected $description = "发放玩家分红";

    public function handle()
    {
        $action   = $this->argument('action', 'last_sort');

        $startDay   = '';
        $endDay     = '';
        $month      = '';
        $sort       = '';

        // 判定初始分钟
        if ($action == "last_sort") {
            if (date("j") >= 1 &&  date("j") < 16) {
                $startDay   = date('Ym' . "15", strtotime('-1 month'));
                $endDay     = date('Ymt', strtotime('-1 month'));
                $month      = date("m");
                $sort       = 2;
            }

            if (date("j") >= 16 ) {
                $startDay   = date("Ym" . "01");
                $endDay     = date("Ym" . "15");
                $month      = date("m");
                $sort       = 1;
            }
        } else {
            // 201912-1 月份 和 序列
            $config     = explode("-", $action);
            $yearMonth  = $config[0];
            $sort       = $config[1];

            if ($sort == 1) {
                $startDay   = $yearMonth . "01";
                $endDay     = $yearMonth . "15";
                $month      = date("m", strtotime($yearMonth) + 1);
                $sort       = 1;
            }

            if ($sort == 2) {
                $startDay   = $yearMonth . "15";
                $endDay     = $yearMonth . date("t", strtotime($yearMonth) + 1);
                $month      = date("m", strtotime($yearMonth) + 1);
                $sort       = 2;
            }

        }

        // 如果解析不正确
        if (!$month || !$sort || !$startDay || !$endDay) {
            $this->info("send-bonus-error-无效的月份:{$month}-序列:{$sort}-时间段:{$startDay}-{$endDay}!");
        }

        $key = "send_dividend_2_" . $month . "_" . $sort;

        if (!cache()->add($key, 1, 10)) {
            $this->info("send-bonus-error-执行中:{$month}-序列:{$sort}-时间段:{$startDay}-{$endDay}-发放中!");
            return true;
        }

        DividendLogic::process($month, $sort, $startDay, $endDay);

        cache()->forget($key);
        $this->info("send-bonus-end-发放结束:{$month}-序列:{$sort}-时间段:{$startDay}-{$endDay}!");
        return true;
    }

}
