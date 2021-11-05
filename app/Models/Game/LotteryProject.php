<?php

namespace App\Models\Game;


use App\Jobs\Lottery\IssueCancelJob;
use App\Models\Player\Player;
use Illuminate\Support\Carbon;

class LotteryProject extends BaseGame
{
    // 表
    protected $table = 'lottery_projects';

    // 去除默认　创建/更新　时间
    public $timestamps = false;

    const STATUS_PROCESS_INIT           = 0;
    const STATUS_PROCESS_CANCEL         = 1;
    const STATUS_PROCESS_OPEN           = 2;
    const STATUS_PROCESS_SEND           = 3;
    const STATUS_PROCESS_SYSTEM_CANCEL  = 4;

    const WIN_TRUE  = 1;
    const WIN_FAIL  = 2;
    const WIN_HE    = 3;

    static public $statusDesc = [
        0 => '待开奖',
        1 => '已撤单',
        2 => '未中奖',
        3 => '已派奖',
        4 => '系统撤单',
		5 => '和值返款',
    ];

    /**
     * 前/后 台获取数据标准模板
     * @param $c
     * @return array
     */
    static function getList($c = [])
    {
        $timeToday = Carbon::now()->startOfWeek();
        $timeTom   = Carbon::now()->endOfWeek();
        $timeNow = strtotime($timeToday);
        $timeFuture = strtotime($timeTom);

        $query = self::orderBy('id', 'desc');
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
                $parent = Player::where('username', $c['username'])->first();
                if ($parent) {
                    $query->where(function ($query) use ($parent){
                        $query->where('parent_id', $parent->id)->orWhere('user_id', $parent->id);
                    });
                    
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


        // 奖期
        if (isset($c['issue']) && $c['issue']) {
            $query->where('issue', $c['issue']);
        }

        // 注单编号
        if (isset($c['hash_id']) && $c['hash_id']) {
            $id = hashId()->decode($c['hash_id']);
            if ($id){
                $query->where('id', $id);
            } else {
                $query->where('id', '');
            }
        }

        // 中奖状态
        if (isset($c['is_win']) && array_key_exists($c['is_win'], [0 => 1, 1 => 1, 2 => 1, 3 => 1])) {
            $query->where('is_win', $c['is_win']);
        }

        // 开奖状态
        if (isset($c['status']) && array_key_exists($c['status'], [0 => 1, 1 => 1, 2 => 2, 3 => 1, 4 => 1])) {
            $query->where('status_process', $c['status']);
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
        if (isset($c['is_tester']) && array_key_exists($c['is_tester'], [0 => 1, 1 => 1])) {
            $query->where('is_tester', $c['is_tester']);
        }

        // 游戏玩法
        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign']!='all') {
            $query->where('method_sign', $c['method_sign']);
        }

        // 投注模式
        if (isset($c['price']) && $c['price']) {
            $query->where('price', $c['price']);
        }

        // 开始时间
        // 结束时间
        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
            $query->whereBetween('time_bought',[strtotime($c['start_time']), strtotime($c['end_time'])]);
        }else{
            $query->whereBetween('time_bought',[$timeNow,$timeFuture]);
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset = ($currentPage - 1) * $pageSize;

        $total      =$query->count();
        $totalDatas = $query->get();
        $data       = $query->skip($offset)->take($pageSize)->get();

        $total_total_cost = 0;
        $page_total_cost  = 0;
        $page_bonus       = 0;
        $total_bonus      = 0;

        foreach($data as $item) {
            $page_total_cost = bcadd($page_total_cost,$item->total_cost);
            $page_bonus      = bcadd($page_bonus,$item->bonus);
        }

        foreach($totalDatas as $item) {
            $total_total_cost = bcadd($total_total_cost,$item->total_cost);
            $total_bonus      = bcadd($total_bonus,$item->bonus);
        }
        $stat['total_total_cost'] = number4($total_total_cost);
        $stat['page_total_cost']  = number4($page_total_cost);
        $stat['page_bonus']       = number4($page_bonus);
        $stat['total_bonus']      = number4($total_bonus);
        

        return ['data' => $data,'total' => $total,'stat'=>$stat, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取投注页需要的注单数据
     * @param  $userId
     * @param $c
     * @param string $lotterySign
     * @param int $start
     * @param int $count
     * @return array
     */
    static function getProjectListForFrontend($userId, $lotterySign='', $start = 0, $count = 10, $c)
    {
        // 最大数量
        $count = $count > 100 ? 100 : $count;

        $timeToday = Carbon::now()->startOfWeek();
        $timeTom   = Carbon::now()->endOfWeek();
        $timeNow = strtotime($timeToday);
        $timeFuture = strtotime($timeTom);
        if (isset($c['username']) && $c['username']) {
            //查询下级
            $query = self::where('username', $c['username'])->orderBy('id', 'desc');
        } else {
            $query = self::where('user_id', $userId)->orderBy('id', 'desc');
        }

        if(empty(trim($lotterySign)))
        {
            // 注单号
            if (isset($c['hash_id']) && $c['hash_id']) {
                $id = hashId()->decode($c['hash_id']);
                if ($id){
                    $query->where('id', $id);
                } else {
                    $query->where('id', '');
                }
            }

            // 彩票名
            if (isset($c['lottery_sign']) && $c['lottery_sign']) {
                $query->where('lottery_sign', $c['lottery_sign']);
            }

            // 方法名
            if (isset($c['method_name']) && $c['method_name']) {
                $query->where('method_name', $c['method_name']);
            }

            // 元角模式
            if (isset($c['mode']) && $c['mode']) {
                $query->where('mode', $c['mode']);
            }

            // 奖期
            if (isset($c['issue']) && $c['issue']) {
                $query->where('issue', $c['issue']);
            }

            // 中奖状态
            if (isset($c['is_win']) && array_key_exists($c['is_win'], [0 => 1, 1 => 1, 2 => 1 , 3=> 1])) {
                $query->where('is_win', $c['is_win']);
            }

            // 开奖状态
            if (isset($c['status'])) {
                $query->where('status_process', $c['status']);
            }

            // 开始时间
            // 结束时间
            if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
                $query->whereBetween('time_bought',[strtotime($c['start_time']), strtotime($c['end_time'])]);
            }else{
                $query->whereBetween('time_bought',[$timeNow,$timeFuture]);
            }

            $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
            $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 10;
            $offset = ($currentPage - 1) * $pageSize;

            $total = $query->count();
            $data  = $query->skip($offset)->take($pageSize)->get();

            return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];

        }
        else
        {
            // 注单号
            if (isset($c['hash_id']) && $c['hash_id']) {
                $id = hashId()->decode($c['hash_id']);
                if ($id){
                    $query->where('id', $id);
                } else {
                    $query->where('id', '');
                }
            }

            // 彩票名
            if (isset($c['lottery_sign']) && $c['lottery_sign']) {
                $query->where('lottery_sign', $c['lottery_sign']);
            }

            // 方法名
            if (isset($c['method_name']) && $c['method_name']) {
                $query->where('method_name', $c['method_name']);
            }

            // 元角模式
            if (isset($c['mode']) && $c['mode']) {
                $query->where('mode', $c['mode']);
            }

            // 奖期
            if (isset($c['issue']) && $c['issue']) {
                $query->where('issue', $c['issue']);
            }

            // 中奖状态
            if (isset($c['is_win']) && array_key_exists($c['is_win'], [0 => 1, 1 => 1, 2 => 1])) {
                $query->where('is_win', $c['is_win']);
            }

            // 开奖状态
            if (isset($c['status'])) {
                $query->where('status_process', $c['status']);
            }

            // 开始时间
            // 结束时间
            if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
                $query->whereBetween('time_bought',[strtotime($c['start_time']), strtotime($c['end_time'])]);
            }else{
                $query->whereBetween('time_bought',[$timeNow,$timeFuture]);
            }

            $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
            $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 10;
            $offset = ($currentPage - 1) * $pageSize;

            $total = $query->count();
            $data  = $query->skip($offset)->take($pageSize)->get();

            return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
//            return LotteryProject::orderBy('id', 'desc')->where('user_id', $userId)->where('lottery_sign', $lotterySign)->skip($start)->take($count)->get();
        }
        
    }

    // 获取订单状态
    public function getStatus()
    {
        if ($this->is_win == 1) {
            return 3;
        } else if ($this->is_win == 2) {
            return 2;
        }else if ($this->is_win == 3) {
            return 5;
        } else {
            if ($this->status_process == self::STATUS_PROCESS_INIT) {
                return 0;
            } else if ($this->status_process == self::STATUS_PROCESS_CANCEL  ) {
                return 1;
            } else if ($this->status_process == self::STATUS_PROCESS_SYSTEM_CANCEL) {
                return 4;
            }
        }
        return 0;
    }

    // 格式化返点
    public function formatCommission() {
        $commission = unserialize($this->commission);
        $data = [];
        foreach ($commission as $userId => $item) {
            $item['amount'] = number4($item['amount']);
            $data[$userId]  = $item;
        }
        return $data;
    }

    static function getPlayerDaySum($playerId, $day) {
        $startTime = strtotime($day);
        $endTime   = $startTime + 86400;
        $items = self::where('user_id', $playerId)->whereBetween('time_bought', [$startTime, $endTime])->get();
        $data = [
			'bets'      => 0,
			'cancel'    => 0,
			'he_return' => 0,
			'bonus'     => 0,
        ];

        foreach ($items as $project) {
            $data['bets'] += $project->total_cost;
            if ($project->status_process == 1 || $project->status_process == 4) {
                $data['cancel'] += $project->total_cost;
            }

            if ($project->is_win == 1 ) {
                $data['bonus'] += $project->bonus;
            }

            if ($project->is_win == 3 ) {
                $data['he_return'] += $project->total_cost;
            }

        }

        return $data;
    }

    /**
     * @param $id
     * @return string
     */
    static function getTotalTodayCost($id)
    {
        $today                = Carbon::today()-> format('Ymd');
        $total_today_cost = self ::where("user_id", $id) -> where('time_real_bet',$today) -> sum('total_cost');
        $total_today_cost = number4(moneyUnitTransferIn($total_today_cost));
        return $total_today_cost;
    }
    // 撤单
    public function cancelProjects($project,$adminUser) {

        // 设置成撤单状态
        $project->status_process   = self::STATUS_PROCESS_CANCEL;
        $project->time_cancel      = time();
        $project->save();

        // 仍到队列
        jtq(new IssueCancelJob($this->id, $adminUser->id), 'issue');

        return true;
    }
}
