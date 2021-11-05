<?php

use Illuminate\Database\Seeder;

// 商户 == 自开彩
class PartnerSelfOpenLotteryTableSeeder extends Seeder
{

    public function run()
    {
        $selfOpenLottery = config("game.self_open_lottery.lottery");
        $allPartner = \App\Models\Partner\Partner::where('status', 1)->get();

        foreach ($allPartner as $partner) {
            foreach ($selfOpenLottery as $lotteryData) {
                $lottery = new \App\Models\Game\Lottery();
                $lotteryData['partner_sign'] = $partner->sign;
                $res = $lottery->addPartnerLottery($lotteryData, $partner->sign);
                if (is_object($res)) {
                    $selfOpenLotteryIssueRule = config("game.self_open_lottery.rule");
                    $rule = isset($selfOpenLotteryIssueRule[$lotteryData['en_name']]) ? $selfOpenLotteryIssueRule[$lotteryData['en_name']] : [];

                    if ($rule) {
                        $rule['lottery_sign'] = $res->en_name;
                        \Illuminate\Support\Facades\DB::table('lottery_issue_rules')->insert($rule);
                    }
                } else {
                    echo $res;
                }
            }
        }
    }
}
