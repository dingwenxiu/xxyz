<?php

namespace App\Http\Controllers\Api;

use App\Lib\Help;
use App\Lib\Logic\Cache\ApiCache;
use App\Models\Account\Account;
use App\Models\Account\AccountChangeReport;
use App\Models\Admin\SysBank;
use App\Models\Admin\SysCity;
use App\Models\Finance\Withdraw;
use App\Models\Game\LotteryProject;
use App\Models\Game\LotteryTrace;
use App\Models\Partner\PartnerConfigure;
use App\Models\Partner\PartnerMessage;
use App\Models\Player\PlayerAvatarImg;
use App\Models\Player\PlayerCard;
use App\Models\Player\Player;
use App\Models\Report\ReportUserSalary;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\Account\AccountChangeType;
use App\Models\Player\PlayerTransferRecords;
use App\Models\Player\PlayerIp;
use App\Models\Player\PlayerVipConfig;

/**
 * 前端用户接口
 * Class ApiPlayerController
 * @package App\Http\Controllers\Api
 */
class ApiPlayerController extends ApiBaseController
{

    /**
     * 玩家详情
     * @return JsonResponse
     */
    public function detail()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 站内信 未读条数统计
		$message    = PartnerMessage::where('partner_sign',$user->partner_sign)->pluck('user_config');
		$c['username']      = $user->username;

		$count=0;
		foreach ($message as $item) {
			$userAll = unserialize($item);
			foreach ($userAll as $key => $value){
				if (isset($c['username']) && $c['username'] == $key && $value ==0) {
					$count++;
				}
			}
		}


        $levelInfo = PlayerVipConfig::where('vip_level', $user->vip_level)->where('partner_sign', $user->partner_sign)->first();
        $account = $user->account();
        $todayWithdrew = Withdraw::todayWithdrew($user->id);   // 今日提现次数
		// vip图片是否展示
		$partnerConfigure = PartnerConfigure::where('partner_sign',$user->partner_sign)->where('sign', 'player_vip_img_dispaly')->first();
		if ($partnerConfigure->value == 1) {
			$vip_show = true;
		}else{
			$vip_show = false;
		}
        $data = [
            'user_id'                   => $user->id,
            'username'                  => $user->username,
            'vip_level'                 => $levelInfo->show_name ?? '',
            'vip_img'                   => $levelInfo->icon ?? '',
			'vip_display'				=> $vip_show,
            'max_prize_group'           => (int)$user->prize_group,
            'min_prize_group'           => (int)partnerConfigure($user->partner_sign,"player_register_min_prize_group", "1700"),
            'user_type'                 => $user->type,
            'is_tester'                 => $user->is_tester,
            'last_login_time'           => date("Y-m-d H:i:s", $user->last_login_time),
            'levels'                    => $user->vip_level,
            'user_icon'                 => $user->user_icon,
            'can_withdraw'              => $user->frozen_type > 0 ? false : true,
            'today_withdraw'            => $todayWithdrew,
            'salary_percentage'         => $user->salary_percentage,
            'bonus_percentage'          => $user->bonus_percentage,
            'allowed_transfer'          => $user->allowed_transfer,
            'register_min_prize_group'  => (int)partnerConfigure($user->partner_sign,"player_register_min_prize_group", "1700"),
            'register_max_prize_group'  => (int)$user->prize_group,
            'balance'                   => number4($account->balance),
            'frozen'                    => number4($account->frozen),
            'fund_password'             => $user->fund_password ? true : false,
            'download_url'              => partnerConfigure($user->partner_sign,"system_app_download_url", "http://www.lottery.com/api/download") . "/" . $user->invite_code,
            'version'                   => partnerConfigure($user->partner_sign,"system_app_version", "1.0"),
            'hot_lottery'               => config("game.main.hot_lottery", []),

            'max_profit_bonus'          => partnerConfigure($user->partner_sign,'game_max_bonus', 200000),
        ];

		$data['un_read'] = $count;
        return Help::returnApiJson('获取数据成功', 1, $data);
    }


    /**
     * 帐变类型列表
     * @return JsonResponse
     */
    public function accountChangeTypeList()
    {
        $data   = AccountChangeType::getAll();

        $_data=[];
        $add = [
            'recharge',
            'commission_from_bet',
            'commission_from_child',
            'game_bonus',
            'cancel_order',
            'he_return',
            'cancel_trace_order',
            'withdraw_un_frozen',
            'active_amount',
            'transfer_from_parent',
            'system_transfer_add',
            'day_salary',
            'dividend_from_parent',
            'casino_transfer_in'
        ];
        foreach ($data["data"]->toArray()  as  $item) {


            if(in_array($item['sign'],$add))
            {
                $item['add']=1;
            }
            else
                {
                    $item['add']=0;
                }
            $_data[] = $item;
        }
        return Help::returnApiJson('获取数据成功!', 1,  $_data);
    }

    /**
     * 获取余额
     * @return JsonResponse
     */
    public function balance()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $account = $user->account();
        return Help::returnApiJson('获取数据成功!', 1, ['balance' => number4($account->balance), 'frozen' => number4($account->frozen)]);
    }

    /**
     * 设置昵称
     * @return JsonResponse
     */
    public function setNickname()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 昵称
        $nickName = request('nickname', '');
        if ($nickName) {
            $res = Player::checkNickname($nickName);
            if (true !== $res) {
                return Help::returnApiJson('对不起, 昵称不合法, 1到12个字符!', 0);
            }
        }

        if (!$nickName) {
            return Help::returnApiJson('对不起, 参数不能全为空!', 0);
        } else {
            $user->nickname = $nickName;
            $user->save();
        }

        return Help::returnApiJson('恭喜, 修改昵称成功!', 1);
    }

    /**
     * 获取银行列表
     * @return JsonResponse
     */
    public function bankList()
    {
        $bankList = config("web.banks");

        $data = [];
        foreach ($bankList as $code => $name) {
            $data[] = [
                'code' => $code,
                'title' => $name,
            ];
        }

        return Help::returnApiJson('恭喜, 获取省份成功!', 1, $data);
    }


    /**
     * 获取省份列表
     * @return JsonResponse
     */
    public function provinceList()
    {
        $province = SysCity::getProvinceList();

        return Help::returnApiJson('恭喜, 获取省份成功!', 1, $province);
    }

    /**
     * 获取城市列表
     * @return JsonResponse
     */
    public function cityList()
    {
        $parentId = request("region_id");

        if (!$parentId) {
            return Help::returnApiJson('恭喜, 获取城市成功!', 1, []);
        }

        $cityList = SysCity::getCityList($parentId);

        return Help::returnApiJson('恭喜, 获取城市成功!', 1, $cityList);
    }

    /**
     * 用户绑定卡
     * @return JsonResponse
     */
    public function bindCard()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 资金密码
        if ($user->fund_password == null) {
            return Help::returnApiJson('对不起, 请先设置资金密码!', 0);
        }

        // 2. 持卡人姓名格式检测
        $ownerName = request('owner_name');
        if (!$ownerName || !PlayerCard::checkCardRealName($ownerName)) {
            return Help::returnApiJson("对不起, 持卡人姓名不正确!", 0);
        }

        // 3. 会员不能以多个持卡人绑卡
        $userCard = PlayerCard::where('partner_sign', $user->partner_sign)->where('status',1)->where("user_id", $user->id)->first();

        if($userCard && $userCard->owner_name != request('owner_name'))
        {
            return Help::returnApiJson("只能增加同持卡人姓名的银行卡!!!", 0);
        }

        // 1. 最大可绑卡量
        $max = partnerConfigure($user->partner_sign,'finance_card_max_bind', 5);
        $cards = PlayerCard::where('user_id', '=', $user->id)->where('status', '=', 1)->get()->toArray();
        if (count($cards) >= $max) {
            return Help::returnApiJson("对不起, 不能绑定超过{$max}张卡!", 0);
        }

        $tmpCards = [];
        foreach ($cards as $card) {
            $tmpCards[$card['id']] = $card;
        }

        // 2. 银行是否存在
        $bankSign = request('bank_sign');
        if (empty($bankSign)) {
            return Help::returnApiJson("对不起, 银行标识不能为空!", 0);
        }

        $banks = SysBank::getOption();
        if (!isset($banks[$bankSign])) {
            return Help::returnApiJson("对不起, 无效的银行标识!", 0);
        }

        // 3. 省份
        $province = intval(request('province_id'));
        if (!PlayerCard::isHaveProvince($province)) {
            return Help::returnApiJson("对不起, 不存在的省份!", 0);
        }

        $city = intval(request('city_id'));
        if (!PlayerCard::isHaveCity($province, $city)) {
            return Help::returnApiJson("对不起, 不存在的城市!", 0);
        }

        // 4. 卡号
        $cardCode = request('card_number');
        $cardCode = str_replace(' ', '', trim($cardCode));

        if (empty($cardCode)) {
            return Help::returnApiJson("对不起, 卡号不能为空!", 0);
        }

        if (!PlayerCard::checkCardCode($cardCode)) {
            return Help::returnApiJson("对不起, 卡号格式不正确!", 0);
        }
        // 5. 校验资金密码
//        $codeOne = base64_decode(request("fund_password"));
//        $codeTwo = substr($codeOne, 0, -4);
//        $final = base64_decode($codeTwo);
//        $fundPassword = substr($final, 5, 37);
//        if (!$fundPassword || !Hash::check($fundPassword, $user->fund_password)) {
//            return Help::returnApiJson("对不起, 资金密码不正确!", 0);
//        }

        // 6. 是否存在同样卡号的卡
        $hasCard = PlayerCard::where('card_number', '=', $cardCode)->where('partner_sign', $user->partner_sign)->where('status',1)->count();
        if ($hasCard > 0) {
            return Help::returnApiJson("对不起, 卡号已经存在!", 0);
        }

        // 7. 支行校验
        $branch_name = request('branch');
        $branch_name = trim($branch_name);
        if (!PlayerCard::checkBranchName($branch_name)) {
            return Help::returnApiJson("对不起, 支行名称输入不合法!", 0);
        }

        // 8. 开户人
        $real_name = request('owner_name');
        $real_name = trim($real_name);
        if (!PlayerCard::checkCardRealName($real_name)) {
            return Help::returnApiJson("对不起, 持卡人姓名不合法!", 0);
        }

        // 9. 是否可以重名
        if (!partnerConfigure($user->partner_sign,'finance_card_can_same_owner', 0)) {
            $sameNameCard = PlayerCard::where('owner_name', '=', $real_name)->where('status', '=', 1)->where('user_id','!=',$user->id)->count();
            if ($sameNameCard > 0) {
                return Help::returnApiJson("对不起, 已经存在同持卡人姓名的永航卡!", 0);
            }
        }

        $card = new PlayerCard;
        $data = [
            'bank_name' => $banks[$bankSign],
            'bank_sign' => $bankSign,
            'province_id' => $province,
            'city_id' => $city,
            'owner_name' => $real_name,
            'card_number' => $cardCode,
            'branch' => $branch_name,
        ];

        $res = $card->saveItem($data, $user);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        $cards = PlayerCard::getCards($user->id);

        return Help::returnApiJson("恭喜, 绑卡成功!", 1, ['cards' => $cards]);
    }

     /**
     * 资金密码校验
     * @return JsonResponse
     */
    public function bindCardCheck() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 资金密码
        if ($user->fund_password == null) {
            return Help::returnApiJson('对不起, 请先设置资金密码!', 0);
        }

        // 1. 校验资金密码
        $codeOne = base64_decode(request("fund_password"));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);
        if (!$fundPassword || !Hash::check($fundPassword, $user->fund_password)) {
            return Help::returnApiJson("对不起, 资金密码不正确!", 0);
        }

        // 2. 持卡人姓名格式检测
        $ownerName = request('owner_name');
        if (!$ownerName || !PlayerCard::checkCardRealName($ownerName)) {
            return Help::returnApiJson("对不起, 持卡人姓名不正确!", 0);
        }

        // 校验
        $cardCount = PlayerCard::where('partner_sign', $user->partner_sign)->where('status',1)->where("user_id", $user->id)->count();
        $max = partnerConfigure($user->partner_sign,'finance_card_max_bind', 5);
        if ($cardCount > $max) {
            return Help::returnApiJson("对不起, 绑定的卡不能超过{$max}张!", 0);
        }

        // 校验持卡人姓名
        $userCard = PlayerCard::where('partner_sign', $user->partner_sign)->where('status',1)->where("user_id", $user->id)->first();

        if($userCard->owner_name != request('owner_name'))
        {
            return Help::returnApiJson("只能增加同持卡人姓名的银行卡!!!", 0);
        }

        return Help::returnApiJson("恭喜, 设置成功!", 1);
    }


    /**
     * 获取银行卡列表
     * @return JsonResponse
     */
    public function cardList()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        //获取加工过银行卡列表
        $cards = PlayerCard::getCardForApi($user);

        $bankList = SysBank::getList();
        $province = SysCity::getProvinceList();

        return Help::returnApiJson('获取数据成功!', 1, ['cards' => $cards, 'bank_list' => $bankList, 'province' => $province]);
    }


    /**
     * 修改登录密码
     * @return JsonResponse
     */
    public function changeLoginPassword()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $codeOne = base64_decode(request("old_password", ''));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $oldPassword = substr($final, 5, 37);

        $codeO = base64_decode(request("password", ''));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        $codeA = base64_decode(request("password_confirm", ''));
        $codeB = substr($codeA, 0, -4);
        $finalC = base64_decode($codeB);
        $pwd_confirm = substr($finalC, 5, 37);


        if (empty($oldPassword)) {
            return Help::returnApiJson("对不起, 请输入旧密码!", 0);
        }

        if (empty($password)) {
            return Help::returnApiJson("对不起, 密码不能为空!", 0);
        }

        if ($password != $pwd_confirm) {
            return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
        }

        $passRet = Player::checkPassword($password);
        if (true !== $passRet) {
            return Help::returnApiJson("对不起, 密码输入格式不正确!", 0);
        }

        if (!Hash::check($oldPassword, $user->password)) {
            return Help::returnApiJson("对不起, 旧密码输入错误!", 0);
        }

        $user->password = bcrypt($password);
        $user->save();

        // 行为
        Help::savePlayerBehavior("change_login_password", $user->id, ['old' => $oldPassword, 'new' => $password]);

        return Help::returnApiJson("恭喜, 修改密码成功!", 1);
    }

    /**
     * 设置新资金密码
     */
    public function setFundPassword()
    {

        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 如果已经设置过了
        if ($user->fund_password) {
            return Help::returnApiJson("对不起, 资金密码已经设置!", 0);
        }


        $codeOne = base64_decode(request("password"));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $password = substr($final, 5, 37);

        $codeO = base64_decode(request("password_confirm"));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $pwd_confirm = substr($fina, 5, 37);

        if (empty($password)) {
            return Help::returnApiJson("对不起, 密码输入为空!", 0);
        }

        if ($password != $pwd_confirm) {
            return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
        }

        $passRet = Player::checkFundPassword($password);
        if (true !== $passRet) {
            return Help::returnApiJson("对不起, 资金密码格式不正确!", 0);
        }

        if (Hash::check($password, $user->password)) {
            return Help::returnApiJson("对不起, 资金密码不能和登录密码一致!", 0);
        }

        $user->fund_password = bcrypt($password);
        $user->save();

        // 行为
        Help::savePlayerBehavior("set_fund_password", $user->id, ['old' => "", 'new' => $password]);

        return Help::returnApiJson("恭喜, 修改资金密码成功!", 1);
    }

    /**
     * 修改资金密码
     */
    public function changeFundPassword()
    {

        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 如果没有设定资金密码
        if (empty($user->fund_password)) {
            return Help::returnApiJson('对不起, 资金密码未设定!', 0, ['reason_code' => 901]);
        }

        $codeOne = base64_decode(request("old_password"));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $oldPassword = substr($final, 5, 37);

        $codeO = base64_decode(request("password"));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        $codeA = base64_decode(request("password_confirm"));
        $codeB = substr($codeA, 0, -4);
        $finalC = base64_decode($codeB);
        $pwd_confirm = substr($finalC, 5, 37);


        if (empty($oldPassword)) {
            return Help::returnApiJson("对不起, 请输入旧密码!", 0);
        }

        if (empty($password)) {
            return Help::returnApiJson("对不起, 请输入新密码!", 0);
        }

        // 两次密码输入是否一直
        if ($password != $pwd_confirm) {
            return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
        }

        // 检查格式
        $passRet = Player::checkFundPassword($password);
        if (true !== $passRet) {
            return Help::returnApiJson("对不起, 资金密码格式不正确!", 0);
        }

        // 检查旧密码是否正确
        if (!Hash::check($oldPassword, $user->fund_password)) {
            return Help::returnApiJson("对不起, 旧密码输入不正确!", 0);
        }

        // 不能和账户密码一样
        if (Hash::check($password, $user->password)) {
            return Help::returnApiJson("对不起, 资金密码不能和登录密码一致!", 0);
        }

        $user->fund_password = bcrypt($password);
        $user->save();

        // 行为
        Help::savePlayerBehavior("change_fund_password", $user->id, ['old' => $oldPassword, 'new' => $password]);

        return Help::returnApiJson("恭喜, 修改资金密码成功!", 1);
    }

    // 获取下级
    public function childList()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c = request()->all();
        $c['parent_id'] = $user->id;

        $data = Player::getList($c);

        foreach ($data['data'] as $index => $item) {
            $_tmp = [
                'nick_name' => $item->nickname,
                'val' => number4($item->balance),
            ];
            $data['data'][$index] = $_tmp;
        }
        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data);
    }

    // 获取上下级分红比例
    public function childsDividend (){
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $id = request('hash_id');
        $id = hashId_decode($id);
        $users = Player::find($id);
        if (!$user) {
            return Help::returnApiJson('对不起,无效用户信息', 0);
        }

        // 上级级分红比例
        $dividendFather = Player::where('id', $users->parent_id)->first();
        if ($dividendFather) {
            $data['father_bonus'] = $dividendFather['bonus_percentage'];
            $data['father_name']  = $dividendFather['username'];
            // 下级工资比例
            $data['father_salary'] = $dividendFather['salary_percentage'];
        }

        // 下级分红比例
        $dividendSon = Player::where('parent_id', $users->id)->orderBy('bonus_percentage')->get();
        $dataSon = $dividendSon->last();
        if ($dataSon) {
            $data['son_bouns'] = $dataSon->bonus_percentage;
            $data['son_name']  = $dataSon->username;
        }

        // 下级工资比例
        $salarySon = Player::where('parent_id', $users->id)->orderBy('salary_percentage')->get();
        $salarySons = $salarySon->last();
        if ($salarySons) {
            $data['son_salary'] = $salarySons->salary_percentage;
        }

        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data);
    }

    /**
     * 发起转账
     */
    public function transfer()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 资金密码未设置
        if (!$user->fund_password) {
            return Help::returnApiJson("对不起, 您还没有设置资金密码!", 0);
        }

        // 资金密码
        $codeOne = base64_decode(request("fund_password",''));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $password = substr($final, 5, 37);

        if (!$password || !Hash::check($password, $user->fund_password)) {
            return Help::returnApiJson("对不起, 资金密码不正确!", 0);
        }

        // 目标用户
        $toUserId = request('to_id', 0);
        if (!$toUserId) {
            return Help::returnApiJson("对不起, 请输入目标用户Id!", 0);
        }

        $toUser = Player::find($toUserId);
        if (!$toUser) {
            return Help::returnApiJson("对不起, 目标对象不存在!", 0);
        }

        // 金额
        $amount = request('amount', 0);
        $amount = intval($amount);

        if (!$amount) {
            return Help::returnApiJson("对不起, 请输入转账金额!", 0);
        }

        // 原因
        $msg = request('msg', "");
        if ($msg && mb_strlen($msg) > 64) {
            return Help::returnApiJson("对不起, 转账原因输入过长!", 0);
        }

        $memKey = "send_transfer_" . $user->id;
        if (!ApiCache::saveMemKey($memKey, 1, 5)) {
            return Help::returnApiJson("对不起, 发送中!", 0);
        }

        $res = $user->transfer($toUserId, $amount, $msg);
        if (true !== $res) {
            ApiCache::cleanMemKey($memKey);
            return Help::returnApiJson($res, 0);
        }

        // 行为
        Help::savePlayerBehavior("transfer", $user->id, ['amount' => $amount, 'to_user_id' => $toUserId]);

        ApiCache::cleanMemKey($memKey);
        return Help::returnApiJson("恭喜, 转账成功!", 1);
    }

    // 帐变列表
    public function accountChangeList()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c = request()->all();
        $userId = $user->id;
        $c['user_id'] = request('user_id') ?? $user->id;
        $c['partner_sign'] = $user->partner_sign;

        // 直属下级玩家列表
        $child = Player::where('parent_id',  $userId)->get();
        $_child = [];
        foreach ($child as $index => $item) {
            $_tmp = [
                'id' => $item->id,
                'username' => $item->username,
            ];
            $_child[] = $_tmp;
        }

        $userName = request('username');
        if ($userName) {
            $child = Player::where('username',  $userName)->first();
            if (!$child) {
                return Help::returnApiJson('对不起, 无效下级信息!', 0);
            }
            // 是否是下级
            if(!$user->hasChild($child->id)) {
                return Help::returnApiJson('对不起, 无效目标用户!', 0);
            }
            $d['rid'] = $child->id;
            $data = AccountChangeReport::getList($d);
        } else {
            $data = AccountChangeReport::getList($c);
        }

        $_data = [];
        foreach ($data['data'] as $index => $item) {
            $_tmp = [
                'hash_id'               => hashId()->encode($item->id),
                'username'              => $item->username,
                'casino_platform_sign'   => $item->casino_platform_sign,
                'type_name'             => $item->type_name,
                'amount'                => number4($item->amount),
                'before_balance'        => number4($item->before_balance),
                'balance'               => number4($item->balance),
                'before_frozen_balance' => number4($item->before_frozen_balance),
                'frozen_balance'        => number4($item->frozen_balance),
                'in_out'                => $item->before_balance >= $item->balance ? 0 : 1,
                'lottery_name'          => empty($item->lottery_name) ? $item->desc : $item->lottery_name,
                'method_name'           => $item->method_name,
                'from_id'               => $item->from_id,
                'process_time'          => $item->process_time,
            ];
            $_data[] = $_tmp;
        }

        $data['data'] = $_data;
        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data, ['self' => $userId, 'child' => $_child]);
    }


    // 帐变详情
    public function accountChangeDetail() {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $id = request('hash_id',0);
        if (!$id) {
            return Help::returnApiJson('对不起, 账号信息错误!', 0);
        }

        $c = request()->all();
        $accountChangeId = hashId()->decode($id);
        $realId = $accountChangeId[0];
        $dataNew = AccountChangeReport::where('id', $realId)->where('partner_sign', $user->partner_sign)->first();
        if (!$dataNew){
            return Help::returnApiJson('对不起, 账号信息错误!', 0);
        }
        switch ($dataNew->type_sign) {
            // 撤单返款
            case 'cancel_order':
                // 撤单返款
            case 'cancel_trace_order':
                //撤单手续费
            case 'cancel_fee':
                // 游戏奖金 投注
            case 'game_bonus':
                // 下级返点
            case 'commission_from_child':
                //个人返点
            case 'commission_from_self':
                //投注返点
            case 'commission_from_bet':
                // 投注扣款
            case 'bet_cost':
                // 和局返款
            case 'he_return':
                $dataNews = LotteryProject::where('id', $dataNew->project_id)->where('partner_sign', $user->partner_sign)->first();
                $modeArr   = config("game.main.modes");
                // 注单号
                $data['hash_id'] = hashId()->encode($dataNews->id);
                //用户名
                $data['username'] = $dataNews->username;
                // 模式
                $data['mode'] = $modeArr[$dataNews->mode]['title'];
                // 彩票名
                $data['lottery_name'] = $dataNews->lottery_name;
                // 玩法
                $data['method_name'] = $dataNews->method_name;
                // 是不是单挑
                $data['is_challenge'] = $dataNews->is_challenge;
                // 期号
                $data['issue'] = $dataNews->issue;
                // 倍数
                $data['times'] = $dataNews->times;
                // 投注金额
                $data['total_cost'] = number4($dataNews->total_cost);
                // 奖金
                $data['bonus'] = number4($dataNews->bonus);
                //注数
				$data['count'] = $dataNews->count;
                // 投注时间
                $data['time_bought'] = date("Y-m-d H:i:s", $dataNews->time_bought);
                // 单价
                $data['price'] = $dataNews->price;
                // 下注号码
                $data['bet_number'] = $dataNews->bet_number;
                // 下注号码预览
                $data['bet_number_view'] = $dataNews->bet_number_view;
                // 开奖号
                $data['open_number'] = $dataNews->open_number;
                // 是否赢
                $data['is_win'] = $dataNews->is_win;
                $data['time_open'] = date("Y-m-d H:i:s", $dataNews->time_open);
                $data['time_send'] = date("Y-m-d H:i:s", $dataNews->time_send);
                $data['time_commission'] = date("Y-m-d H:i:s", $dataNews->time_commission);
                $data['status'] = $dataNews->getStatus();
                $data['can_cancel'] = $dataNews->getStatus();
                unset($dataNews->id);
                break;
            // 用户提现
            case 'withdraw_finish':
                //充值
            case 'recharge':
                //日工资
            case 'day_salary':
                //解冻
            case 'withdraw_un_frozen':
                //冻结
            case 'withdraw_frozen':
                //系统扣减
            case 'system_transfer_reduce':
                //系统理赔
            case 'system_transfer_add':
                // 单挑
            case 'bonus_challenge_reduce':
                // 娱乐城转入转出
            case 'casino_transfer_out':
            case 'casino_transfer_in':
                //活动礼金
            case 'active_amount':
                // 撤销派奖
            case 'cancel_bonus':
                $data = null;
                break;
            // 真实扣款
            case 'real_cost':
                // 活动礼金
            case 'gift':
                $data = Account::where('id', $dataNew->project_id)->where('partner_sign', $user->partner_sign)->first();
                break;
            // 下级转账
            case 'transfer_to_child':
                // 上级转账
            case 'transfer_from_parent':
                $data = PlayerTransferRecords::where('id', $dataNew->project_id)->where('partner_sign', $user->partner_sign)->first();
                break;
            // 分红给下级
            case 'dividend_to_child':
                // 奖金限额扣除
            case 'bonus_limit_reduce':
                // 上级分红
            case 'dividend_from_parent':
                $data = ReportUserSalary::where('project_id', $dataNew->project_id)->where('partner_sign', $user->partner_sign)->first();
                break;
            // 追号扣款
            case 'trace_cost':
                $dataOld = LotteryTrace::where('id', $dataNew->project_id)->where('partner_sign', $user->partner_sign)->first();
                $modeArr   = config("game.main.modes");
                $data = [];
                // 注单号
                $data['id'] = hashId()->encode($dataOld->id);
                // 用户名
                $data['username'] = $dataOld->username;
                // 模式
                $data['mode'] = $modeArr[$dataOld->mode]['title'];
                // 彩票名
                $data['lottery_name'] = $dataOld->lottery_name;
                // 玩法
                $data['method_name'] = $dataOld->method_name;
                // 开始期号
                $data['start_issue'] = $dataOld->start_issue;
                // 追的总期数
                $data['total_issues'] = $dataOld->total_issues;
                // 完成期数
                $data['finished_issues'] = $dataOld->finished_issues;
                // 完成金额
                $data['finished_amount'] = $dataOld->finished_amount;
                // 追停
                $data['win_stop'] = $dataOld->win_stop;
                // 状态
                $data['status'] = $dataOld->status;
                // 取消期数
                $data['canceled_issues'] = $dataOld->canceled_issues;
                // 奖金
                $data['finished_bonus'] = number4($dataOld->finished_bonus);
                $data['canceled_amount'] = number4($dataOld->canceled_amount);
                // 投注金额
                $data['total_price'] = number4($dataOld->trace_total_cost);
                // 投注时间
                $data['created_at'] = date("Y-m-d H:i:s", $dataOld->time_bought);
                // 单价
                $data['price'] = $dataOld->price;
                // 下注号码
                $data['bet_number'] = $dataOld->bet_number;
                // 下注号码预览
                $data['bet_number_view'] = $dataOld->bet_number_view;
                // 奖金组
                $data['bet_prize_group'] = $dataOld->bet_prize_group;
                break;
        }

        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data);
    }


    /**
     * 用户信息
     * @return JsonResponse
     */
    public function info()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $extendInfo = $user->getExtendInfo();
        $user = Player::find($user->id);
        $lastLogin = PlayerIp::where('user_id', $user->id)->first();

        $levelInfo = PlayerVipConfig::where('vip_level', $user->vip_level)->where('partner_sign', $user->partner_sign)->first();
		$partnerConfigure = PartnerConfigure::where('partner_sign',$user->partner_sign)->where('sign', 'player_vip_img_dispaly')->first();
		if ($partnerConfigure->value == 1) {
			$vip_show = true;
		}else{
			$vip_show = false;
		}
        $infoData = [
            'login_ip'          => $lastLogin?$lastLogin->ip:'0.0.0.0',
            'real_name'         => $extendInfo->real_name,
            'vip_level'         => $levelInfo->show_name ?? '',
            'vip_img'           => $levelInfo->icon ?? '',
			'vip_display'		=> $vip_show,
            'address'           => $extendInfo->address,
            'email'             => $extendInfo->email,
            'mobile'            => $extendInfo->phone,
            'user_icon'         => $user->user_icon,
            'nickname'          => $user->nickname,
            'last_login_time'   => $user->last_login_time ? date("Y-m-d H:i:s", $user->last_login_time) : "----",
            'register_time'     => $user->register_time ? date("Y-m-d H:i:s", $user->register_time) : "----",
            'zip_code'          => $extendInfo->zip_code,
        ];

        // 头像选项
        $newAvatar = PlayerAvatarImg::where('partner_sign',$user->partner_sign)->orderBy('id','asc')->get();
        $avatarData = [];
        foreach ($newAvatar as $id => $path) {
            $avatarData[] = [
                'id'    => $id,
                'path'  => $path->avatar
            ];
        }

        $iconArr = config('user.main.user_icon', []);
        $iconData = [];
        foreach ($iconArr as $id => $path) {
            $iconData[] = [
                'id'    => $id,
                'path'  => "system/avatar/" . $path
            ];
        }

        $data = [
            'info'          => $infoData,
            'avatar_option' => $iconData,
            'new_avatar'    => $avatarData
        ];

        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data);
    }


    /**
     * 设置用户头像
     * @return JsonResponse
     */
    public function setAvatar() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $avatar     = request("avatar");

		Player::where('id', $user->id)->update(['user_icon' => $avatar]);

        return Help::returnApiJson("恭喜, 设置头像成功!", 1);
    }


    /**
     * 设置用户消息
     * @return JsonResponse
     */
    public function setInfo() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $extendInfo = $user->getExtendInfo();

        $params     = request()->all();

        $res = $extendInfo->setInfo($params);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 设置数据成功!", 1);
    }

    /**
     * 获取银行列表
     * @return JsonResponse
     */
    public function optionData()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }
        $bankList = config("web.banks");
        $bankData = [];
        foreach ($bankList as $code => $name) {
            $bankData[] = [
                'code' => $code,
                'title' => $name,
            ];
        }

        // 省份数据
        $province = SysCity::getProvinceList();
        // 用户图标数据
        $iconArr = config('user.main.user_icon', []);
        $iconData = [];
        foreach ($iconArr as $id => $path) {
            $iconData[] = [
                'id' => $id,
                'path' => playerIcon("system/avatar/" . $path)
            ];
        }


        $data = [
            'bank_list' => $bankData,
            'province_list' => $province,
            'player_icon' => $iconData
        ];

        return Help::returnApiJson('恭喜, 获取银行列表成功!', 1, $data);
    }

    public function getMessageList()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c = request()->all();
        $c['partner_sign']  = $user->partner_sign;
        $data       = PartnerMessage::getList($c);
		$c['username']      = $user->username;

		// 站内信 未读条数统计
		$message    = PartnerMessage::where('partner_sign',$user->partner_sign)->pluck('user_config');
		$c['username']      = $user->username;

		$count=0;
		foreach ($message as $item) {
			$userAll = unserialize($item);
			foreach ($userAll as $key => $value){
				if (isset($c['username']) && $c['username'] == $key && $value ==0) {
					$count++;
				}
			}
		}

		$datas = [];
        foreach ($data['data'] as $key=> $item) {
			$res = unserialize($item->user_config);
			if (isset($res[$c['username']])){
				$datas[] = [
					'id'         => $item->id,
					'username'   => $c['username'],
					'title'      => $item->title,
					'user_type'  => $item->user_type,
					'content'    => $item->content,
					'read'       => $res[$c['username']],
					'created_at' => date("Y-m-d H:i:s", strtotime($item->created_at)),
				];
			}
		}

		$currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
		$pageSize    = isset($c['page_size']) ? intval($c['page_size']) : 15;
		$total       = count($datas);

        return Help::returnApiJson('获取数据成功!', 1,  ['data'=>$datas,'un_read' => $count,'total' => $total,  'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))]);
    }


	// 用户阅读站内信
    public function readMessage () {
		$user = auth()->guard('api')->user();
		if (!$user) {
			return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
		}

		$id   = request('id');
		if (!$id){
			return Help::returnApiJson( 'id不能为空',0);
		}

		$data = PartnerMessage::where('id', $id)->first();
		if (!$data) {
			return Help::returnApiJson('信息错误', 0);
		}

		$res = unserialize($data->user_config);

		foreach ($res as $key => $v) {
			if ($user->username == $key) {
				$res[$key] = 1;
			}
		}

		PartnerMessage::where('id', $id)->update(['user_config' => serialize($res)]);

		return Help::returnApiJson('站内信已读', 1);
	}


	// 用户删除站内信
	public function  deleteMessage () {
		$user = auth()->guard('api')->user();
		if (!$user) {
			return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
		}

		$id   = request('id');
		if (!$id){
			return Help::returnApiJson( 'id不能为空',0);
		}

		$data = PartnerMessage::where('id', $id)->first();
		if (!$data) {
			return Help::returnApiJson('信息错误', 0);
		}

		$res = unserialize($data->user_config);
		foreach ($res as $key => $v) {
			if ($user->username == $key) {
				unset($res[$key]);
			}
		}

		PartnerMessage::where('id', $id)->update(['user_config' => serialize($res)]);

		return Help::returnApiJson('恭喜删除成功', 1);
	}
}
