<?php

namespace App\Lib\Activity;


use App\Lib\Activity\Core\BaseActivity;
use App\Models\Account\Account;
use App\Models\Activity\ActivityRecord;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Models\Partner\PartnerActivityLog;
use App\Models\Partner\PartnerActivityRule;
use Illuminate\Support\Facades\DB;

class RedPackRainHandler extends BaseActivity
{
    public $player = '';
    public $type = '';
    public $partner_sign = '';
    public $params = '';
    public $errorMsg = '';
    public $moneyUnit = '';
    public $successData = '';

    public function __construct($player, $type, $partner_sign, $params = '')
    {
        $this->player       = $player;
        $this->type         = $type;
        $this->partner_sign = $partner_sign;
        $this->params       = $params;
        $this->moneyUnit       = config('game.main.money_unit');
    }

    // 获取记录
    public function getActLog()
    {
        $c['type'] = $this->type;
        $c['user_id'] = $this->player->id;
        $partnerActivityLog = PartnerActivityLog::getList($c);
        return $partnerActivityLog;
    }

    // 红包雨

    /**
    红包总数, 多少个红包有钱, 每个红包最大金额, 红包雨时间, 需要达到条件, 条件值 奖品 奖品数量 领取方式  是否需要审核  参与人员  首页轮播图

    [
    [
    'total_num'     => 100,'winning_num'   => 70,    'max_money'     => 2,    'red_pack_time' => 15,    'possible'     => 1,    'possible_val' => 1,    'prize'        => 1,
    'prize_value'  => 11,    'obtain_type'  => 1,    'check'        => 1,    'participants' => 1,    'home'         => 1,    ],
    [
    'total_num'     => 100,    'winning_num'   => 70,    'max_money'     => 2,    'red_pack_time' => 15,    'possible'     => 1,    'possible_val' => 1,
    'prize'        => 1,    'prize_value'  => 11,    'obtain_type'  => 1,    'check'        => 1,    'participants' => 1,    'home'         => 1,    ],
    ]
     *
     * @return bool
     */
    public function joinAct()
    {
//        $a = [3=>[1,2,3,4], 2 => [2,3,45]];
//        var_dump(json_encode($a));die;
//        $A =     [
//            'cells' => 3,   // 红包雨中有几个奖品种类
//            'turn_num' => [[1, 1000, 'red_pack_time' => 10], [1, 2000, 'red_pack_time' => 2]],  // 条件类型 条件值 红包雨时间
//            'prize'  => [
//                ['prize' => 3 , 'prize_val' => 10, 'total_num' => 100,'winning_num'   => 70,'max_money'     => 2],   // 奖品类型 奖品值 红包总数 多少个红包有钱 每个红包最大金额
//                ['prize' => 3 , 'prize_val' => 10, 'total_num' => 100, 'winning_num'   => 70,'max_money'     => 2],
//                ['prize' => 3 , 'prize_val' => 10, 'total_num' => 100, 'winning_num'   => 70,'max_money'     => 2],
//                ],
//            'obtain_type' => 1,  // 领取方式
//            'participants' => 1, // 参与人员
//            'check' => 1,			// 是否需要审核
//        ];
//
//        var_dump(json_encode($A));
//        die;

//        $conditionKey = 'red-pack-condition-' . $this->partner_sign . '-' . $this->player->username;  // 用户是否可以参加
//
//        if (empty(cache()->get($conditionKey))) {
//            $this->errorMsg = '网络错误,数据失效,请重新参与!';
//            return false;
//        }

        $redPackParam = $this->params['red_pack_id_arr'] ?? 0;  // 红包id [1,2,3,4,5,6]


        $turnNum      =  $this->params['turn_num'] ?? -1;  // 活动类型
        if (!$redPackParam || $turnNum < 0) {
            $this->errorMsg = '充值参数不存在';
            return false;
        }

        $key = "1234567898882222";
        $iv  = '8NONwyJtHesysWpM';
//        $data = '[{"prize":[{"red_id":"5dd518f633e13","type":"3","red_prize":0.81},{"red_id":"5dd518f633e24","type":"3","red_prize":0.62},{"red_id":"5dd518f633e2f","type":"3","red_prize":0},{"red_id":"5dd518f633e42","type":"3","red_prize":0.39},{"red_id":"5dd518f633e56","type":"3","red_prize":0},{"red_id":"5dd518f633e7e","type":"3","red_prize":0},{"red_id":"5dd518f633f1b","type":"2","red_prize":0},{"red_id":"5dd518f633f24","type":"2","red_prize":0.71},{"red_id":"5dd518f633f35","type":"2","red_prize":0},{"red_id":"5dd518f633f57","type":"2","red_prize":0},{"red_id":"5dd518f633f76","type":"2","red_prize":0},{"red_id":"5dd518f633f93","type":"2","red_prize":0.97},{"red_id":"5dd518f633fa3","type":"2","red_prize":0},{"red_id":"5dd518f63402a","type":"1","red_prize":0},{"red_id":"5dd518f634035","type":"1","red_prize":0.78},{"red_id":"5dd518f63404d","type":"1","red_prize":0.86},{"red_id":"5dd518f63405f","type":"1","red_prize":0.43},{"red_id":"5dd518f63406d","type":"1","red_prize":0.31},{"red_id":"5dd518f634074","type":"1","red_prize":0},{"red_id":"5dd518f63408f","type":"1","red_prize":0.84},{"red_id":"5dd518f6340af","type":"1","red_prize":0.82}]},{"time":1574246669668}]';
//        $encode = base64_encode(openssl_encrypt($data,"AES-128-CBC",$key,true,$iv));
//        var_dump($encode);die;
        $decode = openssl_decrypt(base64_decode($redPackParam),"AES-128-CBC",$key,true,$iv);

        if (!$decode) {
            $this->errorMsg = '参数错误';
            return false;
        }
        $redPackArr = json_decode($decode, 1);
        if (time() - $redPackArr[1]['time'] >= 20) {
            $this->errorMsg = '参数时长失效';
            return false;
        }
        $redPackIdArr = [];
        $redPackPrizeArr = $redPackArr[0]['prize'];


        foreach ($redPackPrizeArr as $key => $item) {
            if (isset($redPackIdArr[$item['type']]['red_prize'])) {
                $redPackIdArr[$item['type']]['red_prize'] = sprintf("%.2f", $item['red_prize'] + $redPackIdArr[$item['type']]['red_prize']);
            } else {
                $redPackIdArr[$item['type']]['red_prize'] = sprintf("%.2f", $item['red_prize']);
            }
        }

        // 1 获取签到规则
        $activeM = PartnerActivityRule::where('partner_sign', $this->partner_sign)->where('type', $this->type)->first();

        // 2. 活动有效性
        if ($activeM === null) {
            $this->errorMsg = '此活动不存在';
            return false;
        }
        // 时间
        $start_time = $activeM->start_time;
        $end_time   = $activeM->end_time;
        $now_time   = date('Y-m-d H:i:s');
        if ($now_time < $start_time || $now_time > $end_time ) {
            $this->errorMsg = '此活动已经结束';
            return false;
        }
        // 参与要求
        $possibleStatus = false;
        $params         = json_decode($activeM->params, 1);
        $prizeArr       = $params['prize'] ?? 0;
        $paramsTurnNum  = $params['turn_num'][$turnNum] ?? 0;
        $participants   = !empty($params['participants']) ? explode(',', $params['participants']) : [];
        $obtainType = $params['obtain_type'] ?? 0;
        $check      = $params['check'] ?? 0;   // 是否需要审核

        if (!$check || !$prizeArr || !$paramsTurnNum || !$participants) {
            $this->errorMsg = '此活动参数错误';
            return false;
        }

        $date           = date("Y-m-d");
        $firstday       = date('Y-m-01 00:00:00', strtotime($date));  //本月第一天

        // 符合要求 进行转盘帐变
        // 1. 本月是否领取过
        if (!$possibleStatus) {
            switch ($paramsTurnNum['possible']) {
                // 投注量
                case 1:
                    $partnerActivityCount = PartnerActivityLog::where('partner_sign', $this->partner_sign)->where('user_id', $this->player->id)->where('possible', 1)->where('possible_val', $paramsTurnNum['possible_val'])->where('type', $this->type)->where('created_at', '>=', $firstday)->count('id');

                    if ($partnerActivityCount >= 1) {
                        $this->errorMsg = '已参与过';
                        return false;
                    } else {
                        $avtiveLogData['possible']     = $paramsTurnNum['possible'];  // 领取类型
                        $avtiveLogData['possible_val'] = $paramsTurnNum['possible_val'];  // 领取类型
                        $possibleStatus = true;
                    }
                    break;

                // 充值
                case 2:
                    $partnerActivityCount = PartnerActivityLog::where('partner_sign', $this->partner_sign)->where('user_id', $this->player->id)->where('possible', 2)->where('possible_val', $paramsTurnNum['possible_val'])->where('type', $this->type)->where('created_at', '>=', $firstday)->count('id');

                    if ($partnerActivityCount >= 1) {
                        $this->errorMsg = '已参与过';
                        return false;
                    } else {
                        $avtiveLogData['possible']     = $paramsTurnNum['possible'];  // 领取类型
                        $avtiveLogData['possible_val'] = $paramsTurnNum['possible_val'];  // 领取类型
                        $possibleStatus = true;
                    }

                    break;

            }
        }

        $prizeConfigArr = config('active.prize');
        $winData        = [];

        db()->beginTransaction();
        // 多个奖品参与
        foreach ($redPackIdArr as $prizeKey => $radPackItem) {
            // 匹配金额
            if ($radPackItem['red_prize'] == 0) {
                continue;
            }
            $balanceWin = $radPackItem['red_prize'] ;
            $winData[] = ['prize' => $prizeKey,    'prize_val' => $balanceWin];

            // 3. 帐变 添加log
            $avtiveLogData['partner_sign']
                                           = $this->partner_sign;  // 合作者标记
            $avtiveLogData['type']         = $this->type;  // 活动类型
            $avtiveLogData['type_name']    = $activeM->name;  // 活动类型
            $avtiveLogData['type_child']   = 'redPack' . $turnNum;  // 活动类型
            $avtiveLogData['active_id']    = $activeM->id;  // 活动id
            $avtiveLogData['obtain_type']  = $obtainType;  // 领取类型
            $avtiveLogData['check']        = $check;  // 领取类型
            $avtiveLogData['user_id']      = $this->player->id;  // 会员id
            $avtiveLogData['username']
                                           = $this->player->username;  // 会员名称
            $avtiveLogData['top_id']
                                           = $this->player->top_id;  // 顶级id
            $avtiveLogData['parent_id']
                                           = $this->player->parent_id;  // 顶级id
            $avtiveLogData['rid']
                                           = $this->player->rid;  // 所有上级id
            $avtiveLogData['created_at']   = now();

            // 活动礼品类型  10 每日签到无奖励 连续签到几天才有奖励
            // 礼品金额
            $avtiveLogData['prize']     = $prizeKey;
            $avtiveLogData['prize_val'] = $balanceWin;

            // 是否立即发放
            if ($obtainType == 1 && $check == 2) {
                // 1. 获取账户锁
                $accountLocker = new AccountLocker(
                    $this->player->id, "active-amount-red-pack" . $this->player->id
                );
                if ( ! $accountLocker->getLock()) {
                    $accountLocker->release();
                    $this->errorMsg = "对不起, 获取账户锁失败, 请稍后再试1!";
                    return false;
                }

                $avtiveLogData['status'] = 1;
                // 1. 如果金品类型为1 则添加余额 帐变
                switch ($avtiveLogData['prize']) {
                    case 3:  // 金额


                        // 2 . 帐变处理
                        $accountChange = new AccountChange();
                        // 3. 真实扣款
                        $account = Account::findAccountByUserId($this->player->id);
                        if ( ! $account) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = "对不起, 账户信息不存在, 请稍后再试2!";
                            return false;
                        }

                        $params = [
                            'user_id'              => $this->player,
                            'amount'               => $avtiveLogData['prize_val'] * $this->moneyUnit,
                            'activity_sign'        => $this->type,
                            'desc'                 => $activeM->name,
                        ];

                        $res = $accountChange->change(
                            $account, 'active_amount', $params,
                            $this->player->is_robot
                        );
                        if ($res !== true) {
                            $accountLocker->release();
                            db()->rollback();
                            $this->errorMsg = $res;
                            return false;
                        }

                        break;
                    case 2: // 积分
                        if ($avtiveLogData['prize_val']) {
                            DB::table('user_accounts')->where('user_id', $this->player->id)->increment(
                                'score', $avtiveLogData['prize_val']
                            );
                        }
                        break;
                    case 1: // 礼金
                        if ($avtiveLogData['prize_val']) {
                            DB::table('user_accounts')->where('user_id', $this->player->id)->increment(
                                'gift', $avtiveLogData['prize_val']
                            );
                        }
                        break;
                    default:
                        break;
                }
            } else {
                $avtiveLogData['status'] = 2;
            }

            // 2. 添加活动记录
            PartnerActivityLog::insert($avtiveLogData);
        }

        db()->commit();

        if ($check == 1) {
            $this->errorMsg    = '需要客服审核后, 在领取';
        } else if ($obtainType != 1) {
            switch ($obtainType) {
                case 2:
                    $this->errorMsg    = '第二天赠送';
                    break;
                case 3:
                    $this->errorMsg    = '客服领取';
                    break;
            }
        } else {
            $this->errorMsg    = '成功';
        }
        $this->successData = ['obtain_type' => $obtainType, 'prize' => $winData];
        return true;//执行成功删除key并跳出循环
    }

    /**
     * 获取活动详情规则
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne()
    {
        $getRedPackData = $this->params['getRedPackData'] ?? '';
        if (!$getRedPackData) {
            $this->errorMsg = '参数不能为空';
            return false;
        }
        if ( $getRedPackData == 'getRedPackData') {
            $conditionKey = 'red-pack-condition-' . $this->partner_sign . '-' . $this->player->username;  // 用户是否可以参加
            $getRadPackData = $this->getRadPackData();
            $getRadPackData
                ? cache()->put($conditionKey, true, now()->addMinutes(10))
                : cache()->put($conditionKey, false, now()->addMinutes(10));

            return $getRadPackData;
        }
        $prizeActiveNew  = [];
        $radPackCount    = 0;
        $radPackCountArr = [];
        $possibleArr     = config('active.possible');

        $this->params['partner_sign'] = $this->partner_sign;
        $partnerActivityRule          = PartnerActivityRule::getOne(
            $this->params
        );

        if ($partnerActivityRule == null) {
            $this->errorMsg = '活动不存在';
            return false;
        }

        $partnerActivityRule = json_decode($partnerActivityRule->params, 1);
        $prizeActive         = $partnerActivityRule['prize'] ?? [];
        foreach ($prizeActive as $item) {
            $radPackCountArr[$item['prize']] = $item['total_num'];
        }
        foreach ($radPackCountArr as $item) {
            $radPackCount += $item;
        }

        $partnerActivityRule = $partnerActivityRule['turn_num'] ?? '';
        foreach ($partnerActivityRule as $key => $item2) {
            $prizeActiveNew['time'][] = [
                'id'            => $key,
                'possible'      => $possibleArr[$item2['possible']],
                'possible_val'  => $item2['possible_val'],
                'red_pack_time' => $item2['red_pack_time']
            ];
        }

        $prizeActiveNew['radPackCount'] = $radPackCount;
        $this->errorMsg                 = '获取成功';
        $this->successData              = $prizeActiveNew;

        return true;
    }

    /**
     * 获取红包雨数据
     * @return bool
     * @throws \Exception
     */
    public function getRadPackData()
    {
        $cacheKey     = 'red-pack-' . $this->partner_sign . '-' . $this->player->username;  // 红包缓存
        $turnNum      = $this->params['turn_num'] ?? -1;  // 活动类型

        if ($turnNum < 0) {
            $this->errorMsg = '参数不存在';
            return false;
        }

        if (!empty(cache()->get($cacheKey))) {
            $this->errorMsg    = '获取成功';
            $this->successData = cache()->get($cacheKey);
//            return true;
        }

        $this->params['partner_sign'] = $this->partner_sign;
        $partnerActivityRule          = PartnerActivityRule::getOne($this->params);

        if ($partnerActivityRule == null) {
            $this->errorMsg = '活动不存在';
            return false;
        }

        $partnerActivityRule = json_decode($partnerActivityRule->params, 1);
        $participants   = !empty($partnerActivityRule['participants']) ? explode(',', $partnerActivityRule['participants']) : [];
        $paramsTurnNum  = $partnerActivityRule['turn_num'][$turnNum] ?? 0;

        if (!$partnerActivityRule) {
            $this->errorMsg = '活动不存在';
            return false;
        }

        // 判断是否可以参与
        $firstday = date('Y-m-01 00:00:00');  //本月第一天


        switch ($paramsTurnNum['possible']) {
            // 投注量
            case 1:
                $possibleTotal = $this->getPossibleTotal($firstday);
                if ($paramsTurnNum['possible_val'] < $possibleTotal) {
                    $this->errorMsg = '当天投注数量不满足, 无法参与活动';
                    return false;
                }
                break;

            // 充值
            case 2:
                $possibleTotal = $this->getRechargeTotal($firstday);
                if ($paramsTurnNum['possible_val'] < $possibleTotal) {
                    $this->errorMsg = '当天充值数量不满足, 无法参与活动';
                    return false;
                }
                break;

        }
        // 1. 本月是否领取过
        switch ($paramsTurnNum['possible']) {
            // 投注量
            case 1:
                $partnerActivityCount = PartnerActivityLog::where(
                    'partner_sign', $this->partner_sign
                )->where('user_id', $this->player->id)->where('possible', 1)
                    ->where('possible_val', $paramsTurnNum['possible_val'])->where('type', $this->type)->where(
                    'created_at', '>=', $firstday
                )->count('id');
                if (0 && $partnerActivityCount >= 1) {
                    $this->errorMsg = '已参与过';
                    return false;
                }
                break;

            // 充值
            case 2:
                $partnerActivityCount = PartnerActivityLog::where(
                    'partner_sign', $this->partner_sign
                )->where('user_id', $this->player->id)->where('possible', 2)
                    ->where('possible_val', $paramsTurnNum['possible_val'])->where('type', $this->type)->where(
                        'created_at', '>=', $firstday
                    )->count('id');
                if (0 && $partnerActivityCount >= 1) {
                    $this->errorMsg = '已参与过';
                    return false;
                }

                break;
        }
        // 参与人员

        if (!in_array($this->player->type, $participants)) {
            $this->errorMsg = '此会员, 无法参与活动';
            return false;
        }

        $prizeActive = $partnerActivityRule['prize'];
        $redPackArr  = [];  // 盛放最后的红包
        $redPackWin  = [];  // 盛放红包有钱的key

        foreach ($prizeActive as $key => $item2) {
            $redPackWin                  = [];  // 盛放红包有钱的key
            // 随机产出n个红包有钱的数组
            for ($j = 0; $j < $item2['winning_num']; $j++) {
                $redPackWin[] = random_int(0, $item2['total_num']);
            }
            for ($i = 0; $i < $item2['total_num']; $i++) {
                $redPackArr[] = [
                        'type' => $item2['prize'],
                        'red_id' => uniqid(),
                        'red_prize' => in_array($i, $redPackWin) ? 0.00 : random_int(0, $item2['max_money'] * 100) / 100,
                ];
            }
        }

        cache()->put($cacheKey, $redPackArr, now()->addMinutes(10));

        $this->errorMsg    = '获取成功';
        $this->successData = $redPackArr;
        return true;
    }
}