<?php namespace App\Exports;

use App\Models\Finance\Recharge;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RechargeExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            '充值金额',
            '实际到帐',
            '充值时间',
            '回调时间',
            '充值结果'
        ];
    }

    public function collection()
    {
        $query = Recharge::select('nickname', 'user_id', 'order_id', "amount", "real_amount",  'request_time', 'callback_time', 'status');
        $query->orderBy('id', "DESC");

        $c = $this->c;

        // status
        if (isset($c['status']) && $c['status'] != "all") {
            $query->where('status', $c['status']);
        }

        // 用户id
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id',  $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username',  $c['username']);
        }

        // 昵称
        if (isset($c['nickname']) && $c['nickname']) {
            $query->where('nickname', $c['nickname']);
        }

        // 开始时间
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('init_time', ">=",  strtotime($c['start_time']));
        }

        // 结束时间
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('init_time', "<=",  strtotime($c['end_time']));
        }

        // 订单号
        if (isset($c['order_id']) && $c['order_id']) {
            $query->where('order_id',  strtotime($c['order_id']));
        }

        $data =  $query->get();

        foreach ($data as $item) {
            $item->callback_time    = $item->callback_time ? date("Y-m-d H:i:s", $item->callback_time) : '';
            $item->init_time        = $item->init_time ? date("Y-m-d H:i:s", $item->init_time) : '';
            $item->status           = Recharge::$status[$item->status];

            $item->amount           = number4($item->amount);
            $item->real_amount      = number4($item->real_amount);
        }

        return $data;
    }
}
