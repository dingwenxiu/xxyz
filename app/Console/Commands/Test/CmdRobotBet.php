<?php namespace App\Console\Commands\Test;

use App\Console\Commands\Command;

use App\Lib\Help;
use App\Models\Game\LotteryIssue;
use App\Models\Player\Player;

class CmdRobotBet extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'test:robotBet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "机器人投注!";

    public function handle()
    {
        $lotteries      = \App\Models\Game\Lottery::getAllLotteryByCache();
        $lotteryId      = "zx1fc";

        if (!array_key_exists($lotteryId, $lotteries)) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        $lottery = $lotteries[$lotteryId];

        $userId = 1;
        $currentIssue = LotteryIssue::getCurrentIssue($lotteryId);

        $data = [
            'balls' => [
                [
                    'method_sign'   => 'QZX3',
                    'mode'          => 1,
                    'prize_group'   => 1956,
                    'code'          => '1|2|3',
                    'times'         => 1,
                    'price'         => 2,

                ],

            ],
            'total_cost'    => 2,
        ];

        $traceIssues[$currentIssue->issue] = 1;
        $data['trace_issue'] = $traceIssues;

        $user = Player::find($userId);

        $res = $user->bet($lottery, $data, 1);

        $this->info($lotteryId . "-" . $currentIssue->issue . "-"  . $res);
    }

}
