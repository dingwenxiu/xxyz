<?php namespace App\Exports;

use App\Models\Report\ReportStatLotteryDay;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PartnerLotteryReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public $c = [];
    public function __construct($c) {
        $this->c = $c;
    }

    public function headings(): array
    {
        return [
            '彩种',
            '日期',
            '投注额',
            '超额扣除',
            '单挑扣除',

            '奖金',
            '撤单',
            '和局反款',
            '下级返点',
            '投注返点',
            '盈亏',
        ];
    }

    public function collection()
    {
        $query = ReportStatLotteryDay::select("lottery_name", "day", 'bets', "limit_reduce", "challenge_reduce", 'bonus', 'cancel', 'he_return', 'commission_from_child', "commission_from_bet" );
        $query->orderBy('id', "DESC");

        $c  = $this->c;

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 开始时间
        if (isset($c['day']) && $c['day']) {
            $query->where('day', $c['day']);
        }

        $data   =  $query->get();

        $_data = [];
        $totalBet = $totalBonus = $totalCancel = $totalLimitReduce = $totalChallengeReduce = $totalHeReturn = 0;
        $totalCommissionFromChild = $totalCommissionFromBet = $totalProfit = 0;
        foreach ($data as $item) {
            $totalBet                   += $item->bets;
            $totalBonus                 += $item->bonus;
            $totalCancel                += $item->cancel;
            $totalLimitReduce           += $item->limit_reduce;
            $totalChallengeReduce       += $item->challenge_reduce;
            $totalHeReturn              += $item->he_return;
            $totalCommissionFromChild   += $item->commission_from_child;
            $totalCommissionFromBet     += $item->commission_from_bet;

            $profit = $item->bonus + $item->cancel + $item->he_return + $item->commission_from_child + $item->commission_from_bet - ($item->bets + $item->limit_reduce + $item->challenge_reduce);

            $totalProfit += $profit;

            $_data[] = collect([
                "lottery_name"          => $item->lottery_name,
                "day"                   => $item->day,
                "bets"                  => number4($item->bets),
                "limit_reduce"          => number4($item->limit_reduce),
                "challenge_reduce"      => number4($item->challenge_reduce),

                "bonus"                 => number4($item->bonus),
                "cancel"                => number4($item->cancel),
                "he_return"             => number4($item->he_return),
                "commission_from_child" => number4($item->commission_from_child),
                "commission_from_bet"   => number4($item->commission_from_bet),
                "profit"                => 0 - number4($profit),
            ]);
        }

        $_data = collect($_data)->sortByDesc('profit');

        $_data[] = collect([
            "lottery_name"          => "",
            "day"                   => "合计",
            "bets"                  => number4($totalBet),
            "limit_reduce"          => number4($totalLimitReduce),
            "challenge_reduce"      => number4($totalChallengeReduce),

            "bonus"                 => number4($totalBonus),
            "cancel"                => number4($totalCancel),
            "he_return"             => number4($totalHeReturn),
            "commission_from_child" => number4($totalCommissionFromChild),
            "commission_from_bet"   => number4($totalCommissionFromBet),
            "profit"                => 0 - number4($totalProfit),
        ]);

        return collect($_data);
    }
}
