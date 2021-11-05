<?php namespace App\Exports;

use App\Models\Finance\Withdraw;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WithdrawExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public $c = [];
    public function __construct($c) {
        $this->c = $c;
    }

    public function headings(): array
    {
        return [
            '用户名',
            '用户ID',
            '订单号',
            '提现金额',
            '实际到帐',
            '提现时间',
            '处理时间',
            '提现结果'
        ];
    }

    public function collection()
    {
        $query = Withdraw::select("nickname", 'user_id', 'order_id', "amount", "real_amount", 'request_time', 'process_time', 'status');
        $query->orderBy('id', "DESC");

        $c = $this->c;

        // status
        if (isset($c['status']) && $c['status'] != "all") {
            $query->where('status', $c['status']);
        }

        // status
        if (isset($c['hand_status']) && $c['hand_status'] != "all") {
            $query->whereIn('status', $c['hand_status']);
        }

        // 用户ID
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', $c['username']);
        }

        // 订单号
        if (isset($c['order_id']) && $c['order_id']) {
            $query->where('order_id', $c['order_id']);
        }

        // 开始时间
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('request_time', ">=",  $c['start_time']);
        }

        // 结束时间
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('request_time', "<=",  $c['end_time']);
        }

        $data                       =  $query->get();

        foreach ($data as $item) {
            $item->request_time     = $item->request_time ? date("Y-m-d H:i:s", $item->request_time) : '';
            $item->process_time     = $item->process_time ? date("Y-m-d H:i:s", $item->process_time) : '';
            $item->status           = Withdraw::$status[$item->status];

            $item->amount           = number4(moneyUnitTransferIn($item->amount));
            $item->real_amount      = number4(moneyUnitTransferIn($item->real_amount));
        }

        return $data;
    }
}
