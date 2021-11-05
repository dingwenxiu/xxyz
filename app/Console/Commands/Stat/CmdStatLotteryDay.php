<?php namespace App\Console\Commands\Stat;

use App\Console\Commands\Command;
use App\Models\Account\AccountChangeReport;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerLottery;
use App\Models\Report\ReportStatLotteryDay;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 统计 每日　彩种销量
 * Class CmdStatLottery
 * @package App\Console\Commands\Stat
 */
class CmdStatLotteryDay extends Command {

    protected $signature    = 'stat:lottery {day}';
    protected $description  = "统计彩种销量!!";

    public function handle()
    {
        $day   = $this->argument('day', 'last');

        // 判定初始分钟
        if ($day == "last") {
            $dayStartM  = Carbon::yesterday()->format("Ymd") . "0000";
            $dayEndM    = Carbon::yesterday()->format("Ymd") . "2359";

            $statDay = Carbon::yesterday()->format("Ymd");
        } else {
            $dayStartM  = $day . "0000";
            $dayEndM    = $day . "2359";
            $statDay    = $day;
        }

        $key = "stat_lottery_d_" . $dayStartM;

        if (!cache()->add($key, $dayStartM . "_" . $dayEndM, now()->addDays(10))) {
            $this->info("对不起, {$dayStartM} - {$dayEndM} 统计中");
            return true;
        }

        // 获取所有的商户
        $allPartner = Partner::getOptions();

        foreach ($allPartner as $sign => $name) {

            $this->info("商户-" . $name . "-开始" . time());
            $allLotteryOption = PartnerLottery::getOption($sign);

            $statData = [];
            foreach ($allLotteryOption as $lotterySign => $lotteryName){
                $statData[$lotterySign] = [
                    'name'                      => $lotteryName,
                    'total_bet'                 => 0,
                    'total_cancel'              => 0,
                    'total_bonus'               => 0,
                    'commission_from_bet'       => 0,
                    'commission_from_child'     => 0,
                    'total_he_return'           => 0,

                    'bonus_challenge_reduce'    => 0,
                    'bonus_limit_reduce'        => 0,
                ];
            }

            try {
                // 投注
                $dataBetArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as bets')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "bet_cost")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataBetArr as $item) {
                    $statData[$item->lottery_sign]['total_bet'] = $item->bets;
                }

                // 撤单
                $dataCancelArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as cancel')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "cancel_order")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataCancelArr as $item) {
                    $statData[$item->lottery_sign]['total_cancel'] = $item->cancel;
                }

                // 中奖
                $dataBonusArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as bonus')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "game_bonus")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataBonusArr as $item) {
                    $statData[$item->lottery_sign]['total_bonus'] = $item->bonus;
                }

                // 单挑扣除
                $dataBonusArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as bonus_challenge_reduce')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "bonus_challenge_reduce")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataBonusArr as $item) {
                    $statData[$item->lottery_sign]['bonus_challenge_reduce'] = $item->bonus_challenge_reduce;
                }

                // 限额扣除
                $dataBonusArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as bonus_limit_reduce')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "bonus_limit_reduce")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataBonusArr as $item) {
                    $statData[$item->lottery_sign]['bonus_limit_reduce'] = $item->bonus_limit_reduce;
                }

                // 下级返点
                $dataCommissionChildArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as commission_from_child')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "commission_from_child")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataCommissionChildArr as $item) {
                    $statData[$item->lottery_sign]['total_child_commission'] = $item->commission_from_child;
                }

                // 投注返点
                $dataCommissionBetArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as commission_from_bet')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "commission_from_bet")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataCommissionBetArr as $item) {
                    $statData[$item->lottery_sign]['total_bet_commission'] = $item->commission_from_bet;
                }

                // 和局返
                $dataHeReturntArr = AccountChangeReport::select(
                    'account_change_report.partner_sign',
                    'account_change_report.lottery_name',
                    'account_change_report.lottery_sign',
                    DB::raw('SUM(account_change_report.amount) as he_return')
                )->where("account_change_report.day_m", ">=", $dayStartM)->where("account_change_report.day_m", "<=", $dayEndM)
                    ->where("account_change_report.partner_sign", $sign)->where("account_change_report.type_sign", "he_return")
                    ->groupBy("account_change_report.lottery_sign")->get();

                foreach ($dataHeReturntArr as $item) {
                    $statData[$item->lottery_sign]['total_he_return'] = $item->he_return;
                }

                $insertData = [];
                foreach ($statData as $lotterySign => $data) {
                    $insertData[] = [
                        'partner_sign'              => $sign,
                        'lottery_sign'              => $lotterySign,
                        'lottery_name'              => $data['name'],
                        'bets'                      => $data['total_bet'],
                        'bonus'                     => $data['total_bonus'],
                        'cancel'                    => $data['total_cancel'],
                        'limit_reduce'              => $data['bonus_limit_reduce'],
                        'challenge_reduce'          => $data['bonus_challenge_reduce'],

                        'commission_from_bet'       => $data['commission_from_bet'],
                        'commission_from_child'     => $data['commission_from_child'],

                        'he_return'                 => $data['total_he_return'],
                        'day'                       => $statDay,
                    ];
                }

                ReportStatLotteryDay::insert($insertData);
                ReportStatLotteryDay::sendReport($statDay, $sign);
                $this->info("商户-" . $name . "-结束" . time());
            } catch(\Exception $e) {
                $this->info("商户-" . $name . "-异常-" . $e->getMessage());
                cache()->forget($key);
            }
        }

        cache()->forget($key);
        return true;
    }

}
