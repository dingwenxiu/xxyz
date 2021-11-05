<?php

namespace App\Http\Controllers\Api;

use App\Jobs\User\Behavior;
use App\Lib\Help;
use App\Lib\Logic\Cache\ApiCache;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Models\Account\Account;
use App\Models\Player\Player;
use App\Models\Player\PlayerInviteLink;
use App\Models\Admin\SysBank;
use App\Models\Player\PlayerCard;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use App\Models\Report\ReportStatUserDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Psr\SimpleCache\InvalidArgumentException;

class ApiProxyController extends ApiBaseController
{
    /**
     * 代理首页
     */
    public function proxyMain() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 1. 需要有权限
        if($user->type == 3 ) {
            return Help::returnApiJson('您好，您不是代理无权操作!', 0);
        }

        // 2, 开始时间
        $startDay   = request("start_day");
        $startDay   = date("Y-m-d", strtotime($startDay));
        if (!isDateDay($startDay)) {
            return Help::returnApiJson('对不起, 开始日期格式不对!', 0);
        } else {
            $startDay = date("Ymd", strtotime($startDay));
        }

        // 3, 结束时间
        $endDay     = request("end_day");
        $endDay     = date('Y-m-d', strtotime($endDay));
        if (!isDateDay($endDay)) {
            return Help::returnApiJson('对不起, 结束日期格式不对!', 0);
        } else {
            $endDay = date("Ymd", strtotime($endDay));
        }


        $item   = ReportStatUserDay::getPlayerProxyData($user, $startDay, $endDay);

        $_data = [
            'child_count'                       => $item->child_count,                      // 团队人数
            "user_id"                           => $item->user_id,
            "username"                          => $item->username,                         // 用户名

            "profit"                            => $item->profit,                           // 净盈亏
            "salary"                            => $item->salary,                           // 日工资
            "team_salary"                       => $item->team_salary + $item->salary,      // 團隊日工资
            "first_recharge_count"              => $item->first_recharge_count,             // 是否首冲
            "repeat_recharge_count"             => $item->repeat_recharge_count,            // 是否复冲
            "have_bet"                          => $item->have_bet,                         // 投注人数
            "first_register"                    => $item->first_register,                   // 新注册/团队人数

            "team_commission_from_child"        => $item->team_commission_from_child + $item->commission_from_child,       // 代理返点
            "team_commission_from_bet"          => $item->team_commission_from_bet + $item->commission_from_bet,         // 投注返点
            "team_bonus"                        => $item->team_bonus + $item->bonus,          // 派奖总额
            "team_bets"                         => number_format($item->team_bets - $item->team_cancel + $item->bets - $item->cancel, 4),   // 投注总额

            "team_withdraw_count"               => $item->team_withdraw_count + $item->withdraw_count,       	    // 提现金额
            "team_recharge_count"               => $item->team_recharge_count,                                      // 充值次数
            "team_recharge_amount"              => $item->team_recharge_amount +$item->recharge_amount,             // 团队充值金额

            "team_gift"                         => $item->team_gift + $item->gift,                        // 活动礼金
            "team_have_bet"                     => $item->team_have_bet,                    // 投注人数
            "team_first_register"               => $item->team_first_register,              // 新注册团队人数

            "team_first_recharge_count"         => $item->team_first_recharge_count,        // 首冲
            "team_repeat_recharge_count"        => $item->recharge_preson,                  // 复冲　
        ];
        foreach ($_data as $key => $value) {
            if(is_null($value)){
                $_data[$key] = 0;
            }

        }

        $_data['team_balance'] = number4(Account::where('rid', 'like', $user->rid . "%")->sum('balance')); // 团队余额

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $_data);
    }

    /**
     * @todo 用户需要开启下级转帐
     * 代理转账到下级
     * @return JsonResponse
     */
    public function transferToChild()
    {
        $amount         = request('amount',0);
        $bankCardId     = request('bank_card_id','');
        $bankCardNumber = request('bank_card_number','');
        $codeOne = base64_decode(request("fund_password",''));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);
        $userId         = hashId_decode(request('user_id',''));

        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 代理是否绑定了银行
        $userCard = PlayerCard::where('user_id', $user->id)->first();
        if (!$userCard) {
            return Help::returnApiJson('对不起, 请先绑定银行卡!', 0);
        }

        // 是否开启转账
        if (!$user->allowed_transfer) {
            return Help::returnApiJson('对不起, 您没有开通转账,请联系上级!', 0);
        }

        
        // 是否是下级
        if(!$user->hasChild($userId)) {
            return Help::returnApiJson('对不起, 无效目标用户!', 0);
        }

        $player = Player::find($userId);

        $playerCards = PlayerCard::where('user_id', $player->id)->first();
        if (!$playerCards) {
            return Help::returnApiJson('对不起, 下级用户：'.$player->username.'未绑定银行卡!', 0);
        }

        $playCard       = PlayerCard::find($bankCardId);
        if(!$playCard) {
            return Help::returnApiJson('对不起, 银行卡不存在!', 0);
        }

        // 银行卡是否是本人持有
        if($playCard->user_id != $user->id) {
            return Help::returnApiJson('对不起, 无效的银行卡!', 0);
        }

        // 验证卡号
        if($playCard->card_number != $bankCardNumber) {
            return Help::returnApiJson('对不起, 银行卡输入不正确!', 0);
        }

        // 金额
        $amount = intval($amount);
        if($amount < 0) {
            return Help::returnApiJson('对不起, 金额格式不正确!', 0);
        }

        $minTransfer = partnerConfigure($user->partner_sign, 'player_transfer_child_min', 1);
        $maxTransfer = partnerConfigure($user->partner_sign, 'player_transfer_child_max', 200);

        if ($amount < $minTransfer || $amount > $maxTransfer) {
            return Help::returnApiJson('对不起, 转账额度范围'.$minTransfer.'-'.$maxTransfer.'!', 0);
        }

        // 资金密码验证
        if(!$fundPassword) {
            return Help::returnApiJson('对不起, 资金密码不能为空!', 0);
        }

        if (!Hash::check($fundPassword, $user->fund_password)) {
            return Help::returnApiJson("对不起, 资金密码不正确!", 0);
        }

        // 发起转帐
        $res = $user->transferToChild($player, $amount);
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson('转帐成功!', 1);
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function childList()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c = request()->all();
        $c['parent_id'] = request('parent_id');
        if (isset($c['parent_id']) && $c['parent_id']) {
            $c['parent_id'] = hashId_decode($c['parent_id']);
        }

        // 下级校验
        if (isset($c['parent_id']) && $c['parent_id'] != $user->id) {
            if (!$user->hasChild($c['parent_id'])) {
                return Help::returnApiJson('对不起, 无效的下级用户!', 0, ['reason_code' => 999]);
            }
        } else if (isset($c['username']) && $c['username']){
            $child = Player::where('username', $c['username'])->first();
            if (!$child){
                return Help::returnApiJson('无效的下级用户名!', 0);
            } else {
                $c['parent_id'] = $child->parent_id;
            }
        } else {
            $c['parent_id'] = $user->id;
        }

        if (isset($c['min_prize']) && $c['min_prize'] < 1700) {
        	return Help::returnApiJson('对不起,最小搜索范围不能小于1700', 0);
		}
        if (isset($c['max_prize']) && $c['max_prize'] > 1980) {
			return Help::returnApiJson('对不起,最小搜索范围不能大于1980', 0);
		}

        // 获取数据
        $data = Player::getList($c);

        $_data = [];
        $minPrizeGroup          = partnerConfigure($user->partner_sign, 'player_register_min_prize_group');

        //是否允许转帐
        $data['allowed_transfer']   = $user->allowed_transfer;
        $data['salary_percentage']   = $user->salary_percentage;
        $data['bonus_percentage']   = $user->bonus_percentage;
        $userTypes      = config('user.main.type');

        foreach ($data["data"] as $item) {
            $isOnline = ConfigureCache::get($item->partner_sign.'_'.$item->id);

            $childMaxPrizeGroup = Player::where("parent_id", $item->id)->max('prize_group');

            $childMaxSalaryPercentage = Player::where("parent_id", $item->id)->max('salary_percentage');
            $childMaxBonusPercentage = Player::where("parent_id", $item->id)->max('bonus_percentage');
            
            $isbandcard =  PlayerCard::where('user_id', $item->id)->first();

            $_data[] = [
//                "id"                => $item->id,
                "hash_id"           => hashId()->encode($item->id),
                "balance"           => number4($item->balance),
                "frozen_balance"    => number4($item->frozen),
                "username"          => $item->username,
                "nickname"          => $item->nickname,
                "type"              => $item->type,
                "type_desc"         => $userTypes[$item->type],
                "vip_level"         => $item->vip_level,
                "is_tester"         => $item->is_tester,
                "frozen_type"       => $item->frozen_type,
                "prize_group"       => $item->prize_group,
                "salary_percentage" => $item->salary_percentage,
                "bonus_percentage"  => $item->bonus_percentage,
                "register_ip"       => $item->register_ip,
                "register_time"     => ! $item->register_time
                    ? "---"
                    : date(
                        'Y-m-d H:i:s', $item->register_time
                    ),
                "last_login_time"   => ! $item->last_login_time
                    ? "---"
                    : date(
                        'Y-m-d H:i:s', $item->last_login_time
                    ),
                "last_login_ip"         => $item->last_login_ip,
                "direct_child_count"    => $item->direct_child_count,
                "child_count"           => $item->child_count,
                "status"                => $item->status,
                "is_online"             => is_null($isOnline)?0:1,
                "childMaxSalaryPercentage" => $childMaxSalaryPercentage,
                "childMaxBonusPercentage"  => $childMaxBonusPercentage,
                "childMaxPrizeGroup"    => $minPrizeGroup < $childMaxPrizeGroup ? $childMaxPrizeGroup : $minPrizeGroup,
                "allowed_transfer"      => $isbandcard && $data['allowed_transfer']?1:0
            ];

        }
        
        //处理余额区间查询
    /*     if (isset($c['min_team_balance']) && $c['min_team_balance'] < 0) {
            return Help::returnApiJson('对不起,团队余额搜索范围不能小于0', 0);
        }
        if (isset($c['max_team_balance']) && $c['max_team_balance'] <0) {
            return Help::returnApiJson('对不起,团队余额搜索范围不能小于0', 0);
        }

        $_childIdArr = [];

        foreach ($_data as $second) {
                $_childIdArr[]=hashId_decode($second['hash_id']);
        }

        if (isset($c['start_time'], $c['end_time']) && $c['start_time'] && $c['end_time'] && $c['end_time'] >= $c['start_time']){
            $startTime = strtotime($c['start_time']);
            $endTime   = strtotime($c['end_time']);
            $childArr   = Player::whereIn('id', $_childIdArr)
                ->whereBetween('users.register_time', [$startTime, $endTime])
                ->get();
        } else {
            $childArr   = Player::whereIn('id', $_childIdArr)->get();
        }

        $a=$childArr->toArray();
        
        $cdata = [];
        foreach ($childArr as $cchild) {

            // 缓存30S
            $key = "team_total_balance_" . $cchild->id;
            if (cache()->has($key)) {
                $totalBalance = cache()->get($key);
            } else {
                $totalBalance = Account::where("top_id", $cchild->top_id)->where('rid', 'like', $cchild->rid . "%")->sum('balance');
                cache()->put($key, $totalBalance, now()->addSeconds(30));
            }

            if(isset($c['min_team_balance'])&&$c['min_team_balance']>number4($totalBalance)){
                continue;
            }

            if(isset($c['max_team_balance'])&&$c['max_team_balance']<number4($totalBalance)){
                continue;
            }
            $cdata[hashId()->encode($cchild->id)] = number4($totalBalance);
        }
       
       $pdata=[];
       foreach ($_data as $key => $second) {
            if(isset($cdata[$second['hash_id']])){
                $pdata[]=$second;
            }
        }

        //处理余额区间查询结束
    */
        $data['data'] = $_data;

        $data['sysbanks'] = SysBank::getList();

        $data['maxbonusgroup']  = $user->prize_group;
       
        $data['minbonusgroup']  = $minPrizeGroup;

        // 是否可以设置工资
        $data['can_set_salary']     = $user->salary_percentage > 0 ? 1 : 0;

        // 是否可以设置分红
        $data['can_set_bonus']      = $user->bonus_percentage > 0 ? 1 : 0;

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function childTeamBalance() {
        $player = auth()->guard('api')->user();
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        if($player->type == 3 )
        {
            return Help::returnApiJson('您好，您不是代理无权操作!', 0);
        }

        $c             = request()->all();
        $childIdArr    = request('id', []);
//        $childIdArr = json_decode($childIdArr);
        if (!is_array($childIdArr) || count($childIdArr) > 15) {
            return Help::returnApiJson("对不起, 您需要传递一个数组！", 0);
        }

        $_childIdArr = [];
        foreach ($childIdArr as $_hashId) {
            $_id = hashId_decode($_hashId);
            if ($_id) {
                $_childIdArr[] = $_id;
            }
        }

        if (isset($c['start_time'], $c['end_time']) && $c['start_time'] && $c['end_time'] && $c['end_time'] >= $c['start_time']){
            $startTime = strtotime($c['start_time']);
            $endTime   = strtotime($c['end_time']);
            $childArr   = Player::whereIn('id', $_childIdArr)
                ->whereBetween('users.register_time', [$startTime, $endTime])
                ->get();
        } else {
            $childArr   = Player::whereIn('id', $_childIdArr)->get();
        }

        $data = [];
        foreach ($childArr as $child) {
            if (!$child) {
                return Help::returnApiJson("对不起, 无效的id！", 0);
            }

            // 是不是有这个下级
            if (!$player->hasChild($child->id)) {
                return Help::returnApiJson("对不起, 您只能操作您的下级！", 0);
            }

            // 缓存30S
            $totalBalance = ApiCache::save30($child);

            $data[hashId()->encode($child->id)] = number4($totalBalance);
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 设置日工资
    public function salarySet()
    {
        $player = auth()->guard('api')->user();
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 本人是否可以设置
        if (!$player->salary_percentage) {
            return Help::returnApiJson("对不起, 您无法设置下级日工资, 请联系上级开通！", 0);
        }

        // 获取用户ID
        $id = request('id', 0);
        $id = hashId_decode($id);
        if (!$id) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        // 获取用户
        $model = Player::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        if($model->type == 3 )
        {
            return Help::returnApiJson('您好，会员不能设置日工资!', 0);
        }

        // 是否有权限操作
        if ($model->partner_sign != $player->partner_sign) {
            return Help::returnApiJson("对不起, 无效的操作(0x008！", 0);
        }

        // 是否直接下级
        if ($model->parent_id != $player->id) {
            return Help::returnApiJson("对不起, 目标用户不是您的下级", 0);
        }

        $rate = request('rate', 0);
        
        // 不能设置超过自己
        if ($rate > $player->salary_percentage) {
            return Help::returnApiJson("对不起, 您下级日工资比列不能超过您本身", 0);
        }

        $model->setSalaryRate($rate, 'parent', $player);
        $model->save();

        return Help::returnApiJson("恭喜, 设置日工资成功！", 1);
    }
    
    // 设置分红
    public function bonusSet()
    {
        $player = auth()->guard('api')->user();
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 本人是否可以设置
        if (!$player->bonus_percentage) {
            return Help::returnApiJson("对不起, 您无法设置下级分红, 请联系上级开通！", 0);
        }

        // 获取用户ID
        $id = request('id', 0);
        $id = hashId_decode($id);
        if (!$id) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        // 获取用户
        $model = Player::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }
        
        if($model->type == 3 )
        {
            return Help::returnApiJson('您好，会台不能设置日工资!', 0);
        }

        // 是否有权限操作
        if ($model->partner_sign != $player->partner_sign) {
            return Help::returnApiJson("对不起, 无效的操作(0x008！", 0);
        }

        // 是否直接下级
        if ($model->parent_id != $player->id) {
            return Help::returnApiJson("对不起, 目标用户不是您的下级", 0);
        }

        $rate = request('rate', 0);

        if ($rate != intval($rate)) {
            return Help::returnApiJson("对不起, 分红比例不正确", 0);
        }

        // 不能设置超过自己
        if ($rate > $player->bonus_percentage) {
            return Help::returnApiJson("对不起, 您下级的分红比列不能超过您自己", 0);
        }

        $model->setBonusRate($rate, 'parent', $player);
        $model->save();

        return Help::returnApiJson("恭喜, 设置分红比列成功！", 1);
    }

    // 设置奖金组
    public function prizeGroupSet()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $id = request('id', 0);
        $id = hashId_decode($id);
        
        $child = Player::find($id);
        if(!$child) {
            return Help::returnApiJson('用户不存在!', 0);
        }

        // 是否直接下级
        if ($child->parent_id != $user->id) {
            return Help::returnApiJson("对不起, 目标用户不是您的下级", 0);
        }

        // 奖金组格式
        $prize_group = request('prize_group','');
        if(empty(trim($prize_group)) || !is_int($prize_group)) {
            return Help::returnApiJson('奖金组格式不正确!', 0);
        }

        // 范围
        $minPrizeGroup      = partnerConfigure($user->partner_sign, 'player_register_min_prize_group', 1800);
        $childMaxPrizeGroup = Player::where("parent_id", $child->id)->max('prize_group');
        $minPrizeGroup      = $minPrizeGroup < $childMaxPrizeGroup ? $childMaxPrizeGroup : $minPrizeGroup;

        if($prize_group > $user->prize_group || $prize_group < $minPrizeGroup) {
            return Help::returnApiJson('奖金组只能在!' . $minPrizeGroup . '至' . $user->prize_group. '之间', 0);
        }

        $child->prize_group = $prize_group;
        $child->save();

       return Help::returnApiJson('奖金组设置成功!', 1);
    }

    /**
     * 添加下级
     * @return mixed
     */
    public function addChild()
    {

        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 会员不能添加下级
        if ($user->type == Player::PLAYER_TYPE_PLAYER) {
            return Help::returnApiJson('对不起, 会员不能添加下级!', 0);
        }

        // 总层级不能超过20
        $maxLevel = configure("player_child_max_level", 20);
        if ($user->user_level >= $maxLevel) {
            return Help::returnApiJson('对不起, 您已经达到代理层级的最大值, 请联系客服!', 0);
        }

        $username   = request('username', '');
        $codeO = base64_decode(request("password", ''));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);
        $prizeGroup = request('prize_group', '');
        $type       = request('user_type', '');

        $child = $user->addChild($username, $password, $type, $prizeGroup, $user->is_test);
        if (!is_object($child)) {
            return Help::returnApiJson("{$child}", 0);
        }

        return Help::returnApiJson('恭喜, 添加下级成功!', 1);
    }


    /**
     * 邀请连接地址
     * @return mixed
     */
    public function inviteLinkList()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $params = request()->all();
        $linkData = PlayerInviteLink::getRegisterLinkForFrontend($params, $user, 15);

        // 开户奖金组
        $minGroup = partnerConfigure($user->partner_sign,"player_register_min_prize_group", 1700);
        $maxGroup = partnerConfigure($user->partner_sign,"player_register_max_prize_group", 1960);
        $maxGroup = $maxGroup > $user->prize_group ? $user->prize_group : $maxGroup;

        $data['min_child_prize_group'] = $minGroup;

        $data['max_child_prize_group'] = $user->prize_group;
        $data['expire_option'] = config("user.main.register_expire_options");
        $data['links'] = $linkData;
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 添加链接地址
     * @return mixed
     */
    public function addInviteLink()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        // 玩家邀请链接
        $model = new PlayerInviteLink();
        $data = request()->all();
        $res = $model->saveItem($user, $data);

        if (true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加数据成功！", 1);
    }

    /**
     * 删除链接地址
     * @return mixed
     */
    public function delInviteLink()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 玩家邀请链接
        $link = PlayerInviteLink::find(request('id'));
        if ($link->user_id != $user->id) {
            return Help::returnApiJson("恭喜, 删除链接成功.", 1);
        }

        $link->delete();

        return Help::returnApiJson("恭喜, 删除链接成功！", 1);
    }

    /**
     * 注册通过注册链接
     * @return mixed
     */
    public function registerByLink()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $codeFix = partnerConfigure($user->partner_sign,"user_invite_link_code_fix", 118020);
        $code = request("code");
        $code = $code - $codeFix;

        $link = PlayerInviteLink::find($code);
        if (!$link) {
            return Help::returnApiJson("对不起, 无效的推广链接", 0);
        }

        if ($link->expired_at < time()) {
            return Help::returnApiJson("对不起, 链接已经过期", 0);
        }

        $player = Player::find($link->user_id);
        if (!$player) {
            return Help::returnApiJson("对不起, 无效的推广用户", 0);
        }

        $username = request('username');
        $password = request('password');

        $prizeGroup = $player->prize_group > $link->prize_group ? $link->prize_group : $player->prize_group;

        $res = $player->addChild($username, $password, Player::PLAYER_TYPE_PROXY, $prizeGroup);
        if (!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }

        // 关联ID
        $res->link_id = $link->id;
        $res->save();
        // 链接
        $link->total_register = $link->total_register + 1;
        $link->save();

        // 记录日志
        $logData = [
            'user_id' => $res->id,
            'username' => $res->username,
            'parent_id' => $player->id,
            'parent_name' => $player->username,

            'agent' => \Browser::userAgent(),
            'device_type' => \Browser::deviceFamily() . "|" . \Browser::deviceModel(),
            'platform_type' => \Browser::platformName(),
            'platform_version' => \Browser::platformVersion(),
            'browser_type' => \Browser::browserName(),
            'browser_version' => \Browser::browserVersion(),
            'link_id' => $link->id,
            'ip' => real_ip(),
        ];
        jtq(new Behavior('link_register', $logData), 'user_common');

        return Help::returnApiJson("恭喜, 添加数据成功！", 1);
    }
}
