<?php namespace App\Models\Account;

use App\Models\Base;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Account\AccountChangeType;

class AccountChangeReport extends Base
{
    protected $table = 'account_change_report';

    static function getList($c, $pageSize  = 15) {
        $query = self::orderBy('id', 'desc');

        $timeToday = Carbon::now()->startOfWeek();
        $timeTom   = Carbon::now()->endOfWeek();
        $timeNow = strtotime($timeToday);
        $timeFuture = strtotime($timeTom);

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        // 帐变id
        if (isset($c['hash_id']) && $c['hash_id']) {
            $id = hashId()->decode($c['hash_id']);
            if ($id){
                $query->where('id', $id);
            } else {
                $query->where('id', '');
            }
        }
        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', trim($c['partner_sign']));
        }

        // 直属下级
        if (isset($c['parentname']) && $c['parentname']) {
            $query->where('parent_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', trim($c['username']));
        }

        // 游戏名
        if (isset($c['lottery_name']) && $c['lottery_name']) {
            $query->where('lottery_name', trim($c['lottery_name']));
        }

        // 圆角分
        if (isset($c['mode']) && $c['mode']) {
            $query->where('mode', trim($c['mode']));
        }

        // 游戏
        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', trim($c['lottery_sign']));
        }

        // 玩法
        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != "all") {
            $query->where('method_sign', trim($c['method_sign']));
        }


        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', trim($c['parent_id']));
        }

        // 类型
        if (isset($c['type']) && $c['type'] && $c['type'] != 'all') {
            if (is_array($c['type'])) {
                $query->whereIn('type_sign', $c['type']);
            } else {
                $query->where('type_sign', $c['type']);
            }

        }

        // project id
        if (isset($c['project_id']) && $c['project_id']) {
            $query->where('project_id', $c['project_id']);
        }

        // start time
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('process_time', ">=", strtotime($c['start_time']));
        }

        // end time
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('process_time', "<=", strtotime($c['end_time']));
        }

        // 管理员id
        if (isset($c['from_admin_id']) && $c['from_admin_id']) {
            $query->where('from_admin_id', $c['from_admin_id']);
        }

        // 前台所有下级
        if (isset($c['rid']) && $c['rid'] && !isset($c['user_id'])) {
            $query->where('rid', 'like', '%'.$c['rid'].'|%');
        }

        // 总代下级
        if (isset($c['top_id']) && $c['top_id'] && !isset($c['user_id'])) {
            $query->where('top_id', $c['top_id']);
        }

        //总代理以及所有下级
        if (isset($c['user_id'], $c['top_id']) && $c['user_id'] && $c['top_id']) {
            $query->where('rid', 'like','%'.$c['user_id'].'%');
        }

        if (isset($c['user_id']) && $c['user_id'] && !isset($c['top_id'])) {
            $query->where('user_id', $c['user_id']);
        }

        // 不计总代
        if (isset($c['top_agent']) && $c['top_agent']) {
            $query->where('top_id', '!=', 0);
        }

        // amount 帐变金额
        if (isset($c['amount_min'], $c['amount_max']) && $c['amount_min'] && $c['amount_max']) {
            $query->whereBetween('amount', [$c['amount_min'], $c['amount_max']]);
        }

        // is_tester
        if (isset($c['is_tester'])) {
            $query->where('is_tester', $c['is_tester']);
        }

        // 开始时间
        // 结束时间
//        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {
//            $query->whereBetween('process_time',[strtotime($c['start_time']), strtotime($c['end_time'])]);
//        }else{
//            $query->whereBetween('process_time',[$timeNow,$timeFuture]);
//        }

        $types     = AccountChangeType::getDataListFromCache();
        $totalDatas = $query->get();

        $totalAmount = 0;
        foreach ($totalDatas as $key => $value) {
            if(($types[$value->type_sign]['type']) == 1){
                $totalAmount += $value->amount;
            } else {
                $totalAmount -= $value->amount;
            }
        }

        $total     = $query->count();
        $menus     = $query->skip($offset)->take($pageSize)->get();


        return ['data' => $menus, 'total' => $total,'totalAmount'=>number4($totalAmount), 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 查询 注单　的帐变合计
     * @param $item
     * @param $day
     * @return mixed
     */
    static function getProjectSumBySign($item, $day) {
        $typeSignArr = [
            "he_return",
            "bonus_challenge_reduce",
            "bonus_limit_reduce",
            "bet_cost",
            "trace_cost",
            "cancel_order",
            "cancel_trace_order",
            "game_bonus",
            "commission_from_bet",
            "commission_from_child"
        ];

        $R = db()->table('account_change_report')->select(
            'account_change_report.type_sign',
            'account_change_report.user_id',
            db()->raw('SUM(account_change_report.amount) as amount')
        )->leftJoin('lottery_projects', 'lottery_projects.id', '=', 'account_change_report.project_id');

        $R->where("account_change_report.partner_sign", $item->partner_sign);
        $R->where("account_change_report.user_id",      $item->user_id);
        $R->whereIn("account_change_report.type_sign",  $typeSignArr);


        $startTime = strtotime($day);
        $endTime   = $startTime + 86400;

        $R->whereBetween("lottery_projects.time_bought", [$startTime, $endTime]);

        $R->groupBy("account_change_report.type_sign");

        return $R->get();

    }

    /**
     * 提现详情表和审核表数据
     * @param $playerId
     * @param $c
     * @return mixed
     */
    static function getProfitList($playerId, $c)
    {
        $query = self::select(
            DB::raw('SUM(amount) as amount'),
            DB::raw('type_sign')
        )
            ->where('partner_sign', $c['partner_sign'])
            ->where('user_id', $playerId)
            ->groupBy('type_sign');
        // 默认显示第一次充值和最后一个了充值时间
        $recharge_first     = date('YmdHi',strtotime(Carbon ::today('PRC')));
        $recharge_last      = date('YmdHi',strtotime(Carbon::tomorrow('PRC')));
        if (isset($recharge_first,$recharge_last) && $recharge_first && $recharge_last) {
            $query -> whereBetween('account_change_report.day_m', [$recharge_first, $recharge_last]);
        }

        // 日期 开始
        if (isset($c['start_time']) && $c['start_time']) {
            $query -> where('account_change_report.day_m', ">=", date('YmdHi', strtotime($c['start_time'])));
        }

        // 日期 结束
        if (isset($c['end_time']) && $c['end_time']) {
            $query -> where('account_change_report.day_m', "<=", date('YmdHi', strtotime($c['end_time'])));
        }

        $data = $query->get();
        foreach ($data as $items){
            $items[$items['type_sign']] = $items['amount'];
            unset($items['amount']);
            unset($items['type_sign']);
        }

        $params = [];
        foreach ($data->toarray() as $_data){
            foreach ($_data as $key=>$value){
                $params[$key]=$value;
            }
        }

        $items = [
            'recharge_online'         => isset($params['recharge'])?number4($params['recharge']):0,                               // 在线充值
            'transfer_from_parent'    => isset($params['transfer_from_parent'])?number4($params['transfer_from_parent']):0,       // 转入
            'withdraw_amount'         => isset($params['withdraw_finish'])?number4($params['withdraw_finish']):0,                 // 提现
            'bets'                    => isset($params['bet_cost'])?number4($params['bet_cost']):0,                               // 投注额
            'bonus'                   => isset($params['game_bonus'])?number4($params['game_bonus']):0,                           // 奖金派送
            'commission_from_bet'     => isset($params['commission_from_bet'])?number4($params['commission_from_bet']):0,         // 投注返点
            'commission_from_child'   => isset($params['commission_from_child'])?number4($params['commission_from_child']):0,     // 下级返点
            'salary'                  => isset($params['day_salary'])?number4($params['day_salary']):0,                           // 日工资
            'dividend'                => isset($params['dividend_to_child'])?number4($params['dividend_to_child']):0,             // 分红
            'system_transfer_add'     => isset($params['system_transfer_add'])?number4($params['system_transfer_add']):0,         // 理赔充值
            'gift'                    => isset($params['gift'])?number4($params['gift']):0,                                       // 活动礼金
            'cancel'                  => isset($params['cancel_fee'])?number4($params['cancel_fee']):0,                           // 活动礼金
            'bonus_limit_reduce'      => isset($params['bonus_limit_reduce'])?number4($params['bonus_limit_reduce']):0,           // 奖金限额扣除
            'recharge_withdraw_ratio' => isset($params['recharge_withdraw_ratio'])?$params['recharge_withdraw_ratio']:0,          // 充提比例
        ];

        if ($items) {
            // 投注和充值比率
            // 投注额
            $items['total_bet']      = number4(moneyUnitTransferIn($items['bets']+ $items['cancel']));
            $items['total_recharge'] = number4(moneyUnitTransferIn($items['system_transfer_add'] + $items['gift'] + $items['recharge_online']));
            //***********************************************************
            $recharge_amount1 = moneyUnitTransferOut($items['recharge_online']);
            $bets             = $items['bets'];
            if (!$recharge_amount1 && $bets){
                $recharge_withdraw_ratio = round($bets,1).':1';
            }elseif(!$recharge_amount1 && !$bets){
                $recharge_withdraw_ratio = '1:1';
            }elseif($recharge_amount1 && !$bets){
                $recharge_withdraw_ratio = round($recharge_amount1,1).':1';
            }else{
                $recharge_withdraw_ratio = round($bets/$recharge_amount1,1).':1';
            }
            $items['recharge_withdraw_ratio'] = $recharge_withdraw_ratio;
            //************************************************************

            $items['recharge_first'] = empty($recharge_first)?'':date("Y-m-d H:i:s", strtotime($recharge_first));
            $items['recharge_last']  = empty($recharge_last)?'':date("Y-m-d H:i:s", strtotime($recharge_last));

            // 1.充值合计=在线充值+充值理赔
            $recharge_claim = $query->where([
                ['mode','=',3],
                ['type_sign','=','system_transfer_add']
            ])->sum('amount');
            $items['recharge_claim']  = moneyUnitTransferOut($recharge_claim);                                                     // 充值理赔
            $items['recharge_amount'] = number4(moneyUnitTransferIn($items['recharge_online'] + $items['recharge_claim'])); // 充值合计

            // 2.提现扣减
            // 提现=提现成功-提现扣减
            $withdraw_reduce = $query->where([
                ['mode','=',5],
                ['type_sign','=','system_transfer_reduce']
            ])->sum('amount');
            $items['withdraw_amount'] = $items['withdraw_amount'] + moneyUnitTransferOut($withdraw_reduce);

            // 3.游戏盈亏
            // 游戏盈亏 = 派奖-投注
            $items['profit']         = number4(moneyUnitTransferIn($items['bonus'] - $items['bets']));

            // 4.分红 = 系统派发的分红 + 分红理赔
            // 分红理赔
            $dividend_claim = $query->where([
                ['mode','=',2],
                ['type_sign','=','system_transfer_add']
            ])->sum('amount');
            $items['dividend'] = $items['dividend'] + moneyUnitTransferOut($dividend_claim);

            // 5.红包理赔
            $red_envelope_claim = $query->where([
                ['mode','=',4],
                ['type_sign','=','system_transfer_add']
            ])->sum('amount');
            $items['red_envelope_claim'] = moneyUnitTransferOut($red_envelope_claim);

            // 6.活动礼金
            $gift = $query->where([
                ['mode','=',5],
                ['type_sign','=','system_transfer_add']
            ])->sum('amount');
            $items['gift'] = moneyUnitTransferOut($gift);
        }
        $item = json_decode(json_encode($items));
        return $item;
    }

    /**
     * 获取一期的投注数据和
     * @param $lottery
     * @param $issueNo
     * @return array
     */
    static function getJackpotBetCount($lottery, $issueNo) {
        $typeSignArr = [
            "bet_cost",
            "trace_cost",
            "cancel_order",
            "cancel_trace_order",
        ];

        $R = db()->table('account_change_report')->select(
            'type_sign',
            db()->raw('SUM(amount) as amount')
        );

        $R->where("partner_sign",     $lottery->partner_sign);
        $R->where("lottery_sign",     $lottery->lottery_sign);
        $R->where("issue",            $issueNo);
        $R->whereIn("type_sign",      $typeSignArr);
        $R->groupBy("type_sign");

        $res    =  $R->get();

        $data   = [
            'bet_cost'              => 0,
            'trace_cost'            => 0,
            'cancel_order'          => 0,
            'cancel_trace_order'    => 0,
        ];

        foreach ($res as $item) {
            $data[$item->type_sign] = $item->amount;
        }

        return $data;
    }

    /**
     * 获取一期的奖金数据　合计
     * @param $lottery
     * @param $issueNo
     * @return array
     */
    static function getJackpotBonusCount($lottery, $issueNo) {
        $typeSignArr = [
            "bonus_challenge_reduce",
            "bonus_limit_reduce",
            "game_bonus",
        ];

        $R = db()->table('account_change_report')->select(
            'type_sign',
            db()->raw('SUM(amount) as amount')
        );

        $R->where("partner_sign",     $lottery->partner_sign);
        $R->where("lottery_sign",     $lottery->lottery_sign);
        $R->where("issue",            $issueNo);
        $R->whereIn("type_sign",      $typeSignArr);
        $R->groupBy("type_sign");

        $res    =  $R->get();

        $data   = [
            'bonus_challenge_reduce'    => 0,
            'bonus_limit_reduce'        => 0,
            'game_bonus'                => 0,
        ];

        foreach ($res as $item) {
            $data[$item->type_sign] = $item->amount;
        }

        return $data;
    }
}
