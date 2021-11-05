<?php namespace App\Console\Commands\Stat;

use App\Console\Commands\Command;

use App\Models\Player\Player;
use App\Models\Report\ReportStatUserDay;


class InitPlayerStat extends Command {

    protected $signature    = 'stat:initPlayerDay {startDay}';

    protected $description  = "初始化用户的统计数据!!";

    public function handle()
    {

        $startDay   = $this->argument('startDay');

        $this->info("初始化用户结算记录-" . date('Y-m-d H:i:s'));

        $lastItem = ReportStatUserDay::orderBy('id', 'DESC')->first();

        if (empty($lastItem)) {
            $startTime  = time();
        } else {
            if ($startDay == "next") {
                $startTime  = strtotime($lastItem->day) + 86400;
            } else {
                $startTime  = strtotime($startDay) + 86400;
            }
        }

        $endTime    = time() + 86400 * 3;
        $daySet     = getDaySet($startTime, $endTime);

        $this->info("生成日期-" . implode("|", $daySet) );

        $totalPlayer = Player::where('status', 1)->count();

        $pageSize   = 1500;
        $totalPage  = ceil($totalPlayer / $pageSize);

        $allPlayer = 0;
        $i = 1;

        do {
            $offset = $pageSize * ($i - 1);

            $res    = Player::where('status', 1)->skip($offset)->take($pageSize)->get();

            $data       = [];
            foreach ($res as $user) {
                foreach ($daySet as $day) {
                    $check = ReportStatUserDay::where("user_id", $user->id)->where("day", $day)->first();

                    if ($check) {
                        continue;
                    }

                    $tmp = [
                        'partner_sign'  => $user->partner_sign,
                        'user_id'       => $user->id,
                        'top_id'        => $user->top_id,
                        'is_tester'     => $user->is_tester,
                        'parent_id'     => $user->parent_id,
                        'username'      => $user->username,
                        'day'           => $day,
                    ];

                    $data[] = $tmp;
                }
            }

            $allPlayer += count($data);
            ReportStatUserDay::insert($data);

            $i++;
        } while ($totalPage >= $i);

        $totalDay = count($daySet);

        $this->info("统计-Stat-Init:一共用户{$totalPlayer}个, 日期{$totalDay}天, 生成记录{$allPlayer}个!");
    }

}
