<?php namespace App\Exports;

use App\Models\Game\LotteryProject;
use App\Models\Player\Player;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProjectExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public $c = [];
    public function __construct($c) {
        $this->c = $c;
    }

    public function headings(): array
    {
        return [
            '订单号',
            '用户名',
            '彩种',
            '玩法',
            '奖期',
            '追号ID',
            '金额',
            '倍数',
            '投注模式',
            '模式',
            '奖金组',
            '中奖',
            '奖金',
            '投注时间',
            'IP',
            '测试状态',
            '状态',
        ];
    }

    public function collection()
    {
        $query = LotteryProject::select("id as hash_id", 'username', 'lottery_name', "method_name", 'issue', "count", 'times', 'price', 'mode', 'user_prize_group', 'bonus', 'time_bought', 'ip', 'is_tester');
        $query->orderBy('id');

        $c = $this->c;

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户id
        if (isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // 用户名下级
        if (isset($c['username_next']) && $c['username_next']) {
            if (isset($c['username']) && $c['username']) {
                $username = Player::where('username', $c['username'])->first();
                if ($username !== null) {
                    $query->where('top_id', $username->id);
                }
            }
        } else {
            if (isset($c['username']) && $c['username']) {
                $query->where('username', $c['username']);
            }
        }

        // 系列
        if (isset($c['series_id']) && $c['series_id'] && $c['series_id'] != 'all') {
            $query->where('series_id', $c['series_id']);
        }

        // 彩种
        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != 'all') {
            $query->where('lottery_sign', $c['lottery_sign']);
        }

        // 玩法
        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != 'all') {
            $query->where('method_sign', $c['method_sign']);
        }

        // 订单号
        if (isset($c['project_id']) && $c['project_id']) {
            $query->where('id', $c['project_id']);
        }

        // 奖期
        if (isset($c['issue']) && $c['issue']) {
            $query->where('issue', $c['issue']);
        }

        // 注单编号
        if (isset($c['hash_id']) && $c['hash_id']) {
            $query->where('hash_id', $c['hash_id']);
        }

        // 开奖状态
        if (isset($c['is_win']) && $c['is_win']) {
            $query->where('is_win', $c['is_win']);
        }

        // 元角模式
        if (isset($c['mode']) && $c['mode']) {
            $query->where('mode', $c['mode']);
        }

        // ip
        if (isset($c['ip']) && $c['ip']) {
            $query->where('ip', $c['ip']);
        }

        // 测试人员
        if (isset($c['is_tester']) && $c['is_tester']) {
            $query->where('is_tester', $c['is_tester']);
        }

        // 游戏玩法
        if (isset($c['method_sign']) && $c['method_sign']) {
            $query->where('method_sign', $c['method_sign']);
        }

        // 投注模式
        if (isset($c['price']) && $c['price']) {
            $query->where('price', $c['price']);
        }

        // 开始时间
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('time_bought', ">=", strtotime($c['start_time']));
        }

        // 结束时间
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('time_bought', "<=", strtotime($c['end_time']));
        }


        if ($c['all']) {
            $data =  $query->get();
        } else {
            $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
            $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 15;
            $offset = ($currentPage - 1) * $pageSize;

            $data = $query->skip($offset)->take($pageSize)->get();
        }
        foreach ($data as $item) {
            $item->count   = $item->count * $item->price;
            $item->hash_id = hashId()->encode($item->hash_id);
            $item->status  = $item->getStatus();
        }
        return $data;
    }
}
