<?php

namespace App\Http\Controllers\AdminApi\Player;

use App\Lib\Help;
use App\Models\Player\Player;
use App\Models\Partner\Partner;
use App\Models\Finance\Withdraw;
use App\Models\Finance\Recharge;
use App\Models\Player\PlayerCard;
use App\Models\Report\ReportStatUser;
use App\Http\Controllers\AdminApi\ApiBaseController;

class ApiPlayerController extends ApiBaseController
{
    /**
     * 获取玩家列表
     * @return mixed
     */
    public function playerList()
    {
        $adminUser      = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        $c              = request()->all();
        // 获取数据
        $data           = Player::getList($c);

        $c['parent_id'] = request('parent_id');
        if (isset($c['parent_id']) && $c['parent_id']) {
            // 获取上级用户名
            $users      = Player::where('id', $c['parent_id'])->first();
            if (!$users){
                return Help::returnApiJson(0);
            }
            $ids        = explode('|', $users->rid);
            $parent     = Player::whereIn('id', $ids)->where('id','!=', $users->id)->get();
            if (!$parent) {
                return Help::returnApiJson("对不起, 该玩家没有上级！", 0);
            }
            $parentData = [];
            foreach ($parent as $item) {
                $parentData[] = [
                    'topParent_id'   => $item->id,
                    'topParent_name' => $item->username
                ];
            }
            $data['parent'] = $parentData;
        }


        $frozenTypes    = config('user.main.frozen_type');
        $userTypes      = config('user.main.type');
        $partnerSign    = '';
        $topOption      = Player::getTopUserOption($partnerSign);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                    => $item->id,
                "parent_id"             => $item->parent_id,
                "partner_sign"          => isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'',
                "top_username"          => isset($topOption[$item->top_id]) ? $topOption[$item->top_id] : "",
                "balance"               => number4($item->balance),
                "frozen_balance"        => number4($item->frozen),
                "username"              => $item->username,
                "nickname"              => $item->nickname,
                "type_desc"             => $userTypes[$item->type],
                "type"                  => $item->type,
                "mark"                  => $item->mark,
                "vip_level"             => $item->vip_level,
                "user_level"            => $item->user_level,
                "is_tester"             => $item->is_tester,
                "frozen_type"           => $frozenTypes[$item->frozen_type],
                "prize_group"           => $item->prize_group,
                "bonus_percentage"      => $item->bonus_percentage,
                "salary_percentage"     => $item->salary_percentage,
                "allowed_transfer"      => $item->allowed_transfer,
                "register_ip"           => $item->register_ip,
                "last_login_time"       => !$item->last_login_time ? "---" : date('Y-m-d H:i:s', $item->last_login_time),
                "subordinate_count"     => $item->subordinate_count,
                "register_time"         => date('Y-m-d H:i:s', $item->register_time),
                "last_login_ip"         => $item->last_login_ip,
                "direct_child_count"    => $item->direct_child_count,
                "child_count"           => $item->child_count,
                "status"                => $item->status,
            ];
        }

        $data['data']                   = $_data;
        $data['type_options']           = $userTypes;
        $data['frozen_type_options']    = $frozenTypes;
        $data["partner_option"]         = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加总代
    public function playerAddTop()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        $partnerSign        = request("partnerSign");
        $username           = request("username");
        $password           = request("password");
        $fundPassword       = request("fund_password");
        $isTester           = request("is_tester");
        $prizeGroup         = request("prize_group");
        $phoneNumber        = request('phone','');

        $res = Player::addTop( $partnerSign, $username, $password, $fundPassword, $prizeGroup, $isTester, $phoneNumber);
        if(!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加总代成功!!", 1);
    }

    // 获取　玩家　详情
    public function playerDetail($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $player = Player::find($id);
        if (!$player) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $deviceType = config("user.main.device");
        // 获取用户
        $data['player'] = [];
        if ($player) {
            $account    = $player->account();
            $stat       = ReportStatUser::where("user_id", $id)->first();

            $account->balance           = number4($account->balance);
            $account->frozen_balance    = number4($account->frozen_balance);

            $player->type               = Player::$types[$player->type];
            $player->frozen_type        = Player::$frozenType[$player->frozen_type];

            $player->register_device    = $deviceType[$player->register_device];
            $player->register_time      = $player->register_time ? date("Y-m-d H:i:s", $player->register_time) : "-----";
            $player->last_login_time    = $player->last_login_time ? date("Y-m-d H:i:s", $player->last_login_time) : "-----";

            $data['player']             = $player;
            $data['account']            = $account;
            $data['stat']               = $stat;

            // 总提款次数
            $player->totalRechargeCount     = Recharge::where("user_id", $id)->whereIn('status', [2,3])->count();
            $player->totalRechargeAmount    = Recharge::where("user_id", $id)->whereIn('status', [2,3])->sum("real_amount");
            $player->totalRechargeAmount    = number4($player->totalRechargeAmount);
            // 总取款次数
            $player->totalWithdrawCount     = Withdraw::where("user_id", $id)->whereIn('status', [4,5])->count();
            $player->totalWithdrawAmount    = Withdraw::where("user_id", $id)->whereIn('status', [4,5])->sum("real_amount");
            $player->totalWithdrawAmount    = number4($player->totalWithdrawAmount);
        }

        // 绑定的银行卡
        $cards = PlayerCard::getCards($id);
        $data['cards']                  = array_values($cards);
        $data['last10RechargeOrder']    = Recharge::getOrders($id, 10);
        $data['last10WithdrawOrder']    = Withdraw::getOrders($id, 10);

        $data['frozen_type_options']    = config('user.main.frozen_type');
        $data['user_type_options']      = config('user.main.type');

        $data['parentSet']              = $player->getRidStr();

        return Help::returnApiJson("恭喜, 获取用户详情成功！", 1, $data);
    }


    // 设置玩家状态
    public function playerStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 获取用户
        $model = Player::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        $model->status = $model->status == 1 ? 0 : 1;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }


    /** ==================================== 日工资配置　================================== */



    // 分红配置
    public function dividendConfigList()
    {
        $c          = request()->all();

        // 获取数据
        $data       = PlayerCard::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                => $item->id,
                "user_id"           => $item->user_id,
                "username"          => $item->username,
                "bank_sign"         => $item->bank_sign,
                "bank_name"         => $item->bank_name,
                "owner_name"        => $item->owner_name,
                "card_number"       => $item->card_number,
                "province_id"       => $item->province_id,
                "city_id"           => $item->city_id,
                "branch"            => $item->branch,
                "admin_id"          => $item->admin_id,
                "status"            => $item->status,
            ];
        }

        $data['data'] = $_data;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 分红契约配置
    public function dividendConfigDetail()
    {
        $c          = request()->all();

        // 获取数据
        $data       = PlayerCard::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                => $item->id,
                "user_id"           => $item->user_id,
                "username"          => $item->username,
                "bank_sign"         => $item->bank_sign,
                "bank_name"         => $item->bank_name,
                "owner_name"        => $item->owner_name,
                "card_number"       => $item->card_number,
                "province_id"       => $item->province_id,
                "city_id"           => $item->city_id,
                "branch"            => $item->branch,
                "admin_id"          => $item->admin_id,
                "status"            => $item->status,
            ];
        }

        $data['data'] = $_data;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

}
