<?php
namespace App\Models\Report;

use Carbon\Carbon;
use App\Models\Base;
use App\Models\Player\Player;
use App\Models\Finance\Recharge;
use App\Models\Partner\PartnerAdminTransferRecords;

class ReportStatStack extends Base {
    protected $table = 'report_stat_stack';

    /**
     * 获取列表
     * @param $c
     * @return array
     */
    static function getList($c) {
        $query = self::orderBy('bets', 'desc');

        // 商户
        if(isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 用户ID
        if(isset($c['user_id']) && $c['user_id']) {
            $query->where('user_id', $c['user_id']);
        }

        // 彩种
        if(isset($c['lottery_sign']) && $c['lottery_sign']) {
            $query->where('lottery_sign', $c['lottery_sign']);
        }

        // 日期 开始
        if(isset($c['start_day']) && $c['start_day']) {
            $query->where('day', ">=", $c['start_day']);
        } else {
            $query->where('day', ">=", Carbon::yesterday()->format("Ymd"));
        }

        // 日期 结束
        if(isset($c['end_day']) && $c['end_day']) {
            $query->where('day', "<=", $c['end_day']);
        } else {
            $query->where('day', "<=", Carbon::yesterday()->format("Ymd"));
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 充值
     * @param $player
     * @param $amount
     * @return bool
     */
    static function doRecharge($player, $amount) {
        $timeNow              = Carbon::today('PRC')->format("Ymd") . "0000";
        $timeFuture           = Carbon::tomorrow('PRC')->format("Ymd") . "0000";
        $now                  = Carbon::now('PRC')->format("YmdHi");

        // 1. 检查首充与复充
        $rechargeCount = Recharge::where("user_id", $player->id) ->whereIn('status', [2,3])->count('id');

        $isEmpty       = PartnerAdminTransferRecords::where('partner_sign',$player->partner_sign)->where('user_id',$player->id)->where('mode','add') ->where('type','3')->count('id');

        $is_first               =  1;
        $is_has                 =  0;
        if ($isEmpty || $rechargeCount){
            $is_first           =  0;
            $is_has             =  1;
        }

        // 2. 当日首充与复充
        $rechargeCountForDay        = Recharge::where("user_id", $player->id) ->whereIn('status', [2,3]) ->whereBetween('day_m', [$timeNow,$timeFuture]) ->count('id');

        $isEmptyForDay              = PartnerAdminTransferRecords::where('partner_sign',$player->partner_sign)->where('user_id',$player->id)->where('mode','add')->where('type','3')->whereBetween('day_m',[$timeNow,$timeFuture])->count('id');

        $isDayFirst             =  1;
        $isBeforeHas            =  0; 
        if ($rechargeCountForDay || $isEmptyForDay){
            $isDayFirst         =  0;
            $isBeforeHas        =  1; 
        }

        $isBeforeHas          = $rechargeCount == 2 ? 1 : 0;

        self::_saveItem($player, 'recharge', [
                'amount'            => $amount,                 // 金额
                'is_first'          => $is_first,               // 是否是首次冲
                'is_has'            => $is_has,                 // 是否是复冲
                'is_day_first'      => $isDayFirst,             // 是否是今天首冲
                'is_before_has'     => $isBeforeHas,            // 是否是复冲
            ]
        );

        return true;
    }

    /**
     * 提现
     * @param $player
     * @param $item
     * @return bool
     */
    static function doWithdraw($player, $item) {
        self::_saveItem($player, 'withdraw', ['amount' => moneyUnitTransferIn($item->real_amount)]);
        return true;
    }

    /**
     * 注册
     * @param $player
     * @return bool
     */
    static function doRegister($player) {
        self::_saveItem($player, 'register', ['amount' => 1]);
        return true;
    }

    /**
     * 转给下级
     * @param $player
     * @param $child
     * @param $amount
     * @return bool
     */
    static function doTransferToChild($player, $child, $amount) {
        self::_saveItem($child, 'transfer_from_parent', ['amount' => $amount]);
        self::_saveItem($player, 'transfer_to_child', ['amount' => $amount]);
        return true;
    }


    /**
     * 后台转账
     * @param $player
     * @param $type
     * @param $amount
     * @return bool
     */
    static function doSystemTransfer($player, $type, $amount) {
        if ($type == 'add') {
            self::_saveItem($player, 'system_transfer_add', ['amount' => $amount]);
            // 如果是增加 判定是否首冲
            self::doRecharge($player, $amount);
        } else {
            self::_saveItem($player, 'system_transfer_reduce', ['amount' => $amount]);
        }

        return true;
    }

    /**
     * 投注
     * @param $player
     * @param $totalProjectCount
     * @return bool
     * @throws \Exception
     */
    static function doFirstBet($player, $totalProjectCount) {
        $day = date("Ymd");

        $item = self::where("user_id", $player->id)->where("day", $day)->where('type_sign', 'first_bet')->first();
        if ($item) {
            $item->amount = $item->amount + $totalProjectCount;
            $item->save();
            return true;
        }

        // 保存
        self::_saveItem($player, 'first_bet', ['amount' => $totalProjectCount, 'is_first' => 1]);
        return true;
    }

    /**
     * 撤单
     * @param $project
     * @return bool
     * @throws \Exception
     */
    static function doCancel($project) {
        $day = date("Ymd");

        $item = self::where("user_id", $project->user_id)->where("day", $day)->where('type_sign', 'first_bet')->first();
        if ($item) {
            if ($item->amount == 1) {
                // 保存
                $player = Player::findByCache($project->user_id);
                self::_saveItem($player, 'cancel', ['amount' => 1, 'is_first' => 1]);
                return true;
            } else {
                $item->amount = $item->amount - 1;
                $item->save();
                return true;
            }
        }

    }

    /**
     * 保存记录
     * @param $player
     * @param $typeSign
     * @param array $params
     * @return bool
     */
    static function _saveItem($player, $typeSign, $params = []) {

        try{
            $query = new self();
            $query->user_id         = $player->id;
            $query->is_tester       = $player->is_tester;
            $query->username        = $player->username;
            $query->partner_sign    = $player->partner_sign;
            $query->top_id          = $player->top_id;
            $query->parent_id       = $player->parent_id;
            $query->rid             = $player->rid;
            $query->type_sign       = $typeSign;
            $query->amount          = isset($params['amount']) ? $params['amount'] : 0;
            $query->is_first        = isset($params['is_first']) ? $params['is_first'] : 0;
            $query->is_day_first    = isset($params['is_day_first']) ? $params['is_day_first'] : 0;
            $query->is_before_has   = isset($params['is_before_has']) ? $params['is_before_has'] : 0;

            $query->day             = date("Ymd");
            $query->day_m           = date("YmdHi");

            return $query->save();
        }catch (\Exception $exception) {
            $exception->getMessage();
        }


    }
}
