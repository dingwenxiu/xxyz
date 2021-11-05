<?php

namespace App\Http\Controllers\Api;

use App\Lib\Help;
use App\Lib\Logic\Player\DividendLogic;
use App\Models\Player\Player;
use App\Models\Report\ReportStatUserDay;
use App\Models\Report\ReportUserDividend;
use App\Models\Report\ReportUserSalary;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * 报表接口
 * Class ApiReportController
 * @package App\Http\Controllers\Api
 */
class ApiReportController extends ApiBaseController {

    /**
     * 获取薪水列表
     * @return JsonResponse
     */
    public function salaryList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        //获取薪水列表
        $data   = ReportUserSalary::getList($c);

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }

    public function playerSalaryList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        $c              = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        $c['parent_id']     = $user->id;

        $selfData  = ReportUserSalary::where('user_id', $user->id)->first();
        $_data = [];
        $_data['username']       = $selfData['username'];       //用户名
        $_data['self_bets']     = $selfData['self_bets'];      // 用户投注额
        $_data['team_bets']     = $selfData['team_bets'];      // 团队投注额
        $_data['team_he_return'] = $selfData['team_he_return']; // 团队返点
        $_data['team_real_bets'] = $selfData['team_real_bets']; // 团队有效投注额
        $_data['rate']        = $selfData['rate'];           // 日工资比例
        $_data['total_salary']  = $selfData['total_salary'];   // 团队日工资合计
        $_data['child_salary']  = $selfData['child_salary'];  //用户结算工资

        $data   = ReportUserSalary::getList($c);
        $dataSelf = [];
        foreach ($data["data"] as $item) {
            $dataSelf[] = [
                'username'       => $item->username,       //用户名
                'self_bets'      => $item->self_bets,      // 用户投注额
                'team_bets'      => $item->team_bets,      // 团队投注额
                'team_he_return' => $item->team_he_return, // 团队返点
                'team_real_bets' => $item->team_real_bets, // 团队有效投注额
                'rate'           => $item->rate,           // 日工资比例
                'total_salary'   => $item->total_salary,   // 团队日工资合计
                'child_salary'   => $item->child_salary,   //用户结算工资
            ];
        }
        $data["data"] = $dataSelf;

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, ['child' => $data, 'self' => $_data]);
    }

    /**
     * 玩家分红列表
     * @return JsonResponse
     */
    public function playerDividendList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        $data   = ReportUserDividend::getList($c);

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }

    /**
     * 玩家分红
     * @return JsonResponse
     * @throws Exception
     */
    public function playerDividendSend() {

        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $ids    = request('hash_id', []);
        $idNew = [];
        foreach ($ids as $id) {
            $idNew[]    = hashId_decode($id);
        }
        
        $items  = ReportUserDividend::whereIn('user_id', $idNew)->get();
        if (count($items) <= 0) {
            return Help::returnApiJson('对不起, 无效的id!', 0);
        }

        foreach ($items as $item) {
            if (!$item) {
                return Help::returnApiJson('对不起, 无效的数据!', 0);
            }

            if ($item->partner_sign != $user->partner_sign) {
                return Help::returnApiJson("对不起, 存在一些用户您没有权限!", 0);
            }

            if ($item->status == ReportUserDividend::STATUS_SEND) {
                return Help::returnApiJson("对不起, 用户{$item->username}已经发放!", 0);
            }

            if ($item->parent_id != $user->id) {
                return Help::returnApiJson("对不起, 存在一些用户您不是他上级!", 0);
            }
        }

        $res = DividendLogic::sendBonus($items);

        if (!$res['status'] && $res['total_player'] != $res['fail_count']) {
            return Help::returnApiJson('对不起, 部分完成!', 0, $res);
        } else if (!$res['status'] ) {
            return Help::returnApiJson('对不起, 发放分行失败!', 0, $res);
        } else {
            return Help::returnApiJson('恭喜! 发放分红成功', 1, []);
        }
    }

    /**
     * 玩家利润列表
     * @return JsonResponse
     */
    public function playerProfitList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        $data   = ReportUserSalary::getList($c);

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }

    /**
     * 获取统计列表
     * @return JsonResponse
     */
    public function playerStatList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        if($user->type == 3 )
        {
            return Help::returnApiJson('您好，您不是代理无权操作!', 0);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;

        // 下级校验
        if (isset($c['parent_id']) && $c['parent_id'] != $user->id) {
            if (!$user->hasChild($c['parent_id'])) {
                return Help::returnApiJson('对不起, 无效的下级用户!', 0, ['reason_code' => 999]);
            }
        }  else if (isset($c['username']) && $c['username']){
            $child = Player::where('username', $c['username'])->first();
            $c['parent_id'] = $child->parent_id;
        } else {
            $c['parent_id'] = $user->id;
        }


        $data   = ReportStatUserDay::getStatDataForFrontend($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                'child_count'                => $item->child_count,
                "user_id"                    => $item->user_id,
                "username"                   => $item->username,                  // 用户名
                "team_gift"                  => $item->team_gift,                 // 活动礼金
                "profit"                     => $item->profit,                    // 净盈亏
                "salary"                     => $item->salary,                    // 日工资
                "first_recharge_count"       => $item->first_recharge_count,      // 是否首冲
                "repeat_recharge_count"      => $item->repeat_recharge_count,     // 是否复冲
                "have_bet"                   => $item->have_bet,                  // 投注人数
                "first_register"             => $item->first_register,            // 新注册/团队人数

                "team_commission_from_child" => $item->team_commission_from_child,// 代理返点
                "team_commission_from_bet"   => $item->team_commission_from_bet,  // 投注返点
                "team_bonus"                 => $item->team_bonus,                // 派奖总额
                "team_bets"                  => $item->team_bets,                 // 投注总额
                "balance"                    => $item->balance,                   // 团队余额
                "team_withdraw_count"        => $item->team_withdraw_count,       // 提现金额
                "team_recharge_count"        => $item->team_recharge_count,       // 充值金额
                "team_recharge_amount"       => $item->team_recharge_amount,      // 首冲/复充人数

                "team_have_bet"              => $item->team_have_bet,             // 投注人数
                "team_first_register"        => $item->team_first_register,       // 新注册/团队人数

                "team_first_recharge_count"         => $item->team_first_recharge_count,        // 首冲
                "team_repeat_recharge_count"        => $item->team_repeat_recharge_count,       // 复冲　

            ];
        }

        $data['data'] = $_data;

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }

    /**
     * 获取团队薪水列表
     * @return JsonResponse
     */
    public function teamSalaryList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        $hashId     = request('parent_id');
        if ($hashId) {
            $c['parent_id'] = hashId_decode($hashId);
        } else {
            $c['parent_id'] = $user->id;
        }

        //获取薪水列表
        $data         = ReportUserSalary::getList($c);
        $dataSon = [];
        foreach ($data["data"] as $item) {

            $fd = $item->self_commission_from_bet + $item->self_commission_from_child + $item->team_commission_from_child + $item->team_commission_from_bet;

            $dataSon[] = [
                'id'             => hashId()->encode($item->id),
                'day'            => $item->day,                                                         // 日工资的时间
                'username'       => $item->username,                                                    // 用户名
                'self_bets'      => number4($item->self_bets),                                          // 用户投注额
                'team_bets'      => number4($item->team_bets)+number4($item->self_bets),                // 团队投注额
                'team_cancel'    => number4($item->team_cancel),                                        // 团队撤單
                'team_he_return' => number4($fd),                                                       // 团队返点
                'team_real_bets' => number4($item->team_real_bet) + number4($item->self_real_bet),      // 团队有效投注额
                'rate'           => $item->rate,                                                        // 日工资比例
                'total_salary'   => number4($item->total_salary),                                       // 团队日工资合计
                'child_salary'   => number4($item->self_salary),                                       // 用户结算工资
            ];
        }

        $data["data"] = $dataSon;

        $m                  = request()->all();
        $m['partner_sign']  = $user->partner_sign;

        $data['self'] = ReportUserSalary::getCount($user->id,$m);
        $datas = [];

        $selffd = $data['self']['self_commission_from_bet'] + $data['self']['self_commission_from_child'] + $data['self']['team_commission_from_child'] + $data['self']['team_commission_from_bet'];

        $datas['self_bets'] = number4($data['self']['self_bets']);
        $datas['self_real_bet'] = number4($data['self']['self_real_bet']);
        $datas['team_bets'] = number4($data['self']['team_bets']);
        $datas['team_cancel'] = number4($data['self']['team_cancel']);
        $datas['team_he_return'] =  number4($selffd);                  
        $datas['team_real_bet'] = number4($data['self']['team_real_bet']);
        $datas['total_salary'] = number4($data['self']['total_salary']);
        $datas['child_salary'] = number4($data['self']['child_salary']);
        $datas['self_salary'] = number4($data['self']['self_salary']);
        $datas['real_salary'] = number4($data['self']['real_salary']);
        $datas['rate'] = $data['self']['rate']==null?$user->salary_percentage:$data['self']['rate'];

        $data['self'] = $datas;

        $data['user'] = ['username'=>$user->username,'salary_percentage'=>$user->salary_percentage,'bonus_percentage'=>$user->bonus_percentage];

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }

    /**
     * 团队分红列表
     * @return JsonResponse
     */
    public function teamDividendList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        $c['parent_id']     = $user->id;
        $data   = ReportUserDividend::getList($c);
        $_data = [];
        foreach ($data["data"] as $item) {
            $t = $item->profit - $item->total_dividend;
            $_data[] = [
                'hash_id'            => hashId()->encode($item->user_id),                                               //用户HASHID
                'username'          => $item->username,                                                                // 用户名
                'total_bets'        => number4($item->total_bets),                                                     // 投注总额
                'total_bonus'       => number4($item->total_bonus),                                                    // 派奖总额
                'total_commsission' => number4($item->total_commission_from_bet + $item->total_commission_from_child), // 返点总额
                'total_gift'        => number4($item->total_gift),                                                     // 促销红利
                'total_salary'      => number4($item->total_salary),                                                   //用户日工资
                'profit'            => number4($t),                                                                    // 净盈亏
                'rate'              => $item->rate,                                                                    //分红比例
                'real_amount'       => number4($item->amount),                                                        // 分红金额
            ];
        }
        $data["child"] = $_data;

        $m = request()->all();
        $m['partner_sign']  = $user->partner_sign;
        $m['username'] = $user->username;
        $data['self'] = ReportUserDividend::getCount($m);

        $datas = [];
        $datas['username'] = $data['self']['username'];
        $datas['profit'] = number4($data['self']['profit']);                                          //净盈亏
        $datas['total_salary'] = number4($data['self']['total_salary']);                              //日工资
        $datas['total_gift'] = number4($data['self']['total_gift']);                                  //促销红利
        $datas['total_commission_from_child'] = number4($data['self']['total_commission_from_child'])+number4($data['self']['total_commission_from_bet']);                                                             //返点总额
        $datas['total_dividend'] = number4($data['self']['amount']);                                  //已分红奖金
        $datas['total_bonus'] = number4($data['self']['total_bonus']);                                //派奖总额
        $datas['total_bets'] = number4($data['self']['total_bets']);                                  //投注总额
        $datas['total_commission_from_bet'] = number4($data['self']['total_commission_from_bet']);    //返点总额
        $datas['rate'] = is_null($data['self']['rate'])?$user->bonus_percentage :$data['self']['rate'];                                                       //分红比例

        $data['self'] = $datas;

        $data['user'] = ['username'=>$user->username,'salary_percentage'=>$user->salary_percentage,'bonus_percentage'=>$user->bonus_percentage];

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }

    /**
     * 娱乐城 盈亏
     * @return JsonResponse
     * @throws Exception
     */
    public function teamCasinoProfitList()
    {
        $player   = auth()->guard('api')->user();
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $player->partner_sign;

        if (isset($c['username']) && $c['username']) {
            $targetPlayer = Player::findByUsername($c['username'],$player->partner_sign);
            if (!$targetPlayer) {
                return Help::returnApiJson('对不起, 不存在的用户(0x001)!', 0);
            }

            if (!$player->hasChild($targetPlayer->id)) {
                return Help::returnApiJson('对不起, 不存在用户(0x002)!', 0);
            }
        }

        // 是否是当前用户的下级
        if (isset($c['parent_id']) && $c['parent_id']) {
            if (!$player->hasChild($c['parent_id'])) {
                return Help::returnApiJson('对不起, 不存在用户(0x003)!', 0);
            }
        } else if (!isset($c['username'])) {
            $c['parent_id']     = $player->id;
        }

        if (isset($c['parent_id']) && $c['parent_id']) {
            $playerOne = Player::where('id', $c['parent_id'])->first();
            if (!$playerOne->hasChild($c['parent_id'])) {
                return Help::returnApiJson('对不起, 不存在用户(0x003)!', 0);
            }
            $teamData   = ReportStatUserDay::getCasinoTeamProfitList($c);
            $partData   = ReportStatUserDay::getCasinoPartProfitList($player->username, $c);
            $selfData   = ReportStatUserDay::getSelfCasinoProfitList($player->username, $c);
            return Help::returnApiJson('恭喜, 获取数据成功!', 1, ['self' => $selfData, 'child' => $teamData, 'part' => $partData]);
        }

        $teamData   = ReportStatUserDay::getCasinoTeamProfitList($c);
        $partData   = ReportStatUserDay::getCasinoPartProfitList($player->username, $c);
        $selfData   = ReportStatUserDay::getSelfCasinoProfitList($player->username, $c);

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, ['self' => $selfData, 'child' => $teamData, 'part' => $partData]);

    }

    /**
     * 团队盈亏
     * @return JsonResponse
     * @throws Exception
     */
    public function teamProfitList()
    {
        $player   = auth()->guard('api')->user();
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $player->partner_sign;

        if (isset($c['username']) && $c['username']) {
            $targetPlayer = Player::findByUsername($c['username'],$player->partner_sign);
            if (!$targetPlayer) {
                return Help::returnApiJson('对不起, 不存在的用户(0x001)!', 0);
            }

            if (!$player->hasChild($targetPlayer->id)) {
                return Help::returnApiJson('对不起, 不存在用户(0x002)!', 0);
            }

            $playerIds = $targetPlayer->id;
            $c['parent_id']   =  $targetPlayer->id;
			$c['username']    = '';
        } else {
			$playerIds = $player->id;
		}

        // 是否是当前用户的下级
        if (isset($c['parent_id']) && $c['parent_id']) {
            if (!$player->hasChild($c['parent_id'])) {
                return Help::returnApiJson('对不起, 不存在用户(0x003)!', 0);
            }
        } else if (!isset($c['username'])) {
            $c['parent_id']     = $player->id;
        }

        if (isset($c['parent_id']) && $c['parent_id']) {
            $playerOne = Player::where('id', $c['parent_id'])->first();
            if (!$playerOne->hasChild($c['parent_id'])) {
                return Help::returnApiJson('对不起, 不存在用户(0x003)!', 0);
            }
            // 区间合计 统计自己和下级
			$partId   = $c['parent_id'];
			$playerId = $c['parent_id'];
            $teamData   = ReportStatUserDay::getTeamProfitList($c);
            $partData   = ReportStatUserDay::getPartProfitList($partId, $c);
            $selfData   = ReportStatUserDay::getSelfProfitList($playerId, $c);
            return Help::returnApiJson('恭喜, 获取数据成功!', 1, ['self' => $selfData, 'child' => $teamData, 'part' => $partData]);
        }
        $teamData   = ReportStatUserDay::getTeamProfitList($c);
        $partData   = ReportStatUserDay::getPartProfitList($playerIds, $c);
        $selfData   = ReportStatUserDay::getSelfProfitList($playerIds, $c);

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, ['self' => $selfData, 'child' => $teamData, 'part' => $partData]);
    }

    /**
     * 获取团队统计列表
     * @return JsonResponse
     */
    public function teamStatList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c                  = request()->all();
        $c['partner_sign']  = $user->partner_sign;

        // 下级校验
        if (isset($c['parent_id']) && $c['parent_id'] != $user->id) {
            if (!$user->hasChild($c['parent_id'])) {
                return Help::returnApiJson('对不起, 无效的下级用户!', 0, ['reason_code' => 999]);
            }
        } else {
            $c['parent_id'] = $user->id;
        }

        $data   = ReportStatUserDay::getStatDataForFrontend($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                'child_count'                => $item->child_count,               // 下级人数
                "user_id"                    => $item->user_id,                   // 用户id
                "username"                   => $item->username,                  // 用户名
                "team_gift"                  => $item->team_gift,                 // 活动礼金
                "profit"                     => $item->profit,                    // 净盈亏
                "salary"                     => $item->salary,                    // 日工资
                "first_recharge_count"       => $item->first_recharge_count,      // 是否首冲
                "repeat_recharge_count"      => $item->repeat_recharge_count,     // 是否复冲
                "have_bet"                   => $item->have_bet,                  // 投注人数
                "first_register"             => $item->first_register,            // 新注册/团队人数

                "team_commission_from_child" => $item->team_commission_from_child,// 代理返点
                "team_commission_from_bet"   => $item->team_commission_from_bet,  // 投注返点
                "team_bonus"                 => $item->team_bonus,                // 派奖总额
                "team_bets"                  => $item->team_bets,                 // 投注总额
                "balance"                    => $item->balance,                   // 团队余额
                "team_withdraw_count"        => $item->team_withdraw_count,       // 提现金额
                "team_recharge_count"        => $item->team_recharge_count,       // 充值金额
                "team_recharge_amount"       => $item->team_recharge_amount,      // 首冲/复充人数

                "team_have_bet"              => $item->team_have_bet,             // 投注人数
                "team_first_register"        => $item->team_first_register,       // 新注册/团队人数

                "team_first_recharge_count"         => $item->team_first_recharge_count,        // 首冲
                "team_repeat_recharge_count"        => $item->team_repeat_recharge_count,       // 复冲　

            ];
        }

        $data['data'] = $_data;

        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }

}
