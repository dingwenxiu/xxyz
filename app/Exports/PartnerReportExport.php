<?php namespace App\Exports;

use App\Models\Report\ReportStatPartnerDay;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PartnerReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public $c = [];
    public function __construct($c) {
        $this->c = $c;
    }

    public function headings(): array
    {
        return [
            '日期',
            '注册人数',
            '投注人数',

            '首冲人数',
            '复冲人数',
            '充值金额',

            '提现金额',

            '投注金额',
            '撤单金额',
            '和局反款',
            '投注返点',
            '下级返点',

            '派奖金额',
            '日工资',
            '分红',
            '礼金',

            '理赔',
            '扣减',

            '盈亏',
        ];
    }

    public function collection()
    {
        $query = ReportStatPartnerDay::select(
            "day",

            "first_register",
            "have_bet",
            "recharge_count",
            "first_recharge_count",
            "repeat_recharge_count",
            "withdraw_count",

            "recharge_amount",
            "withdraw_amount",

            "bets",
            "cancel",
            "he_return",
            "bonus",
            "salary",
            "dividend",
            "gift",

            "commission_from_child",
            "commission_from_bet",
            "system_transfer_add",
            "system_transfer_reduce",
            "profit"
        );

        $query->orderBy('id', "ASC");

        $c  = $this->c;

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 开始时间
        if (isset($c['day']) && $c['day']) {
            $query->where('day', ">=", date("Ym" . "01", strtotime($c['day'])));
            $query->where('day', "<=", $c['day']);
        }

        $data   =  $query->get();

        $_data  = [];
        $_totalData = [
            "day"                       => "合计",
            "first_register"            => 0,
            "have_bet"                  => 0,


            "first_recharge_count"      => 0,
            "repeat_recharge_count"     => 0,
            "recharge_amount"           => 0,

            "withdraw_amount"           => 0,

            "bets"                      => 0,
            "cancel"                    => 0,
            "he_return"                 => 0,
            "commission_from_bet"       => 0,
            "commission_from_child"     => 0,

            "bonus"                     => 0,
            "salary"                    => 0,
            "dividend"                  => 0,
            "gift"                      => 0,

            "system_transfer_add"       => 0,
            "system_transfer_reduce"    => 0,

            "profit"                    => 0,
        ];

        foreach ($data as $item) {

            $_data[] = collect([
                "day"                       => $item->day,
                "first_register"            => $item->first_register,
                "have_bet"                  => $item->have_bet,


                "first_recharge_count"      => $item->first_recharge_count,
                "repeat_recharge_count"     => $item->repeat_recharge_count,
                "recharge_amount"           => number4($item->recharge_amount),

                "withdraw_amount"           => number4($item->withdraw_amount),

                "bets"                      => number4($item->bets),
                "cancel"                    => number4($item->cancel),
                "he_return"                 => number4($item->he_return),
                "commission_from_bet"       => number4($item->commission_from_bet),
                "commission_from_child"     => number4($item->commission_from_child),

                "bonus"                     => number4($item->bonus),
                "salary"                    => number4($item->salary),
                "dividend"                  => number4($item->dividend),
                "gift"                      => number4($item->gift),

                "system_transfer_add"       => number4($item->system_transfer_add),
                "system_transfer_reduce"    => number4($item->system_transfer_reduce),

                "profit"                    => 0 - number4($item->profit),
            ]);

            $_totalData['first_register']           += $item->first_register;
            $_totalData['have_bet']                 += $item->have_bet;
            $_totalData['first_recharge_count']     += $item->first_recharge_count;
            $_totalData['repeat_recharge_count']    += $item->repeat_recharge_count;
            $_totalData['recharge_amount']          += number4($item->recharge_amount);
            $_totalData['withdraw_amount']          += number4($item->withdraw_amount);
            $_totalData['bets']                     += number4($item->bets);
            $_totalData['cancel']                   += number4($item->cancel);
            $_totalData['he_return']                += number4($item->he_return);
            $_totalData['commission_from_bet']      += number4($item->commission_from_bet);
            $_totalData['commission_from_child']    += number4($item->commission_from_child);
            $_totalData['bonus']                    += number4($item->bonus);
            $_totalData['salary']                   += number4($item->salary);
            $_totalData['dividend']                 += number4($item->dividend);
            $_totalData['gift']                     += number4($item->gift);
            $_totalData['system_transfer_add']      += number4($item->system_transfer_add);

            $_totalData['system_transfer_reduce']   += number4($item->system_transfer_reduce);
            $_totalData['profit']                   += number4($item->profit);

        }

        $_totalData['profit'] = 0 - $_totalData['profit'];

        $_data[] = $_totalData;

        return collect($_data);
    }
}
