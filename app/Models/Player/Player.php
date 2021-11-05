<?php

namespace App\Models\Player;

use App\Lib\CC;
use App\Lib\Clog;
use App\Lib\Help;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\Cache\PlayerCache;
use App\Models\Account\Account;
use App\Models\Admin\Configure;
use App\Models\Partner\PartnerAdminGroup;
use App\Models\Partner\PartnerAdminTransferRecords;
use App\Models\Finance\Withdraw;
use App\Models\Partner\Partner;
use App\Models\Report\ReportStatStack;
use App\Models\Report\ReportStatUser;
use App\Models\Report\ReportStatUserDay;
use App\Models\Talk\MsgInits;
use App\Observers\PlayerObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * version 1.0
 * Class Player
 * @package App\Models\Player
 */
class Player extends Authenticatable implements JWTSubject
{
    protected $table = 'users';
    public    $errMsg = '';
    public const INVITE_CODE_INIT = 2689;

    // 冻结类型
    static $frozenType = [
        0 => "未冻结",
        1 => '禁止登录',
        2 => '禁止投注',
        3 => '禁止提现',
        4 => '禁止转账',
        5 => '禁止资金'
    ];

    public const PLAYER_TYPE_TOP = 1;
    public const PLAYER_TYPE_PROXY = 2;
    public const PLAYER_TYPE_PLAYER = 3;

    public const FROZEN_TYPE_DISABLE_BET = 1;
    public const FROZEN_TYPE_DISABLE_RECHARGE = 2;
    public const FROZEN_TYPE_DISABLE_WITHDRAW = 3;

    public static $types = [
        1 => '直属',
        2 => '代理',
        3 => '会员'
    ];

    // 观察者
    protected static function boot()
    {
        parent ::boot();
        static ::observe(new PlayerObserver());
    }

    /** ============== JWT 实现 ================ */
    public function getJWTIdentifier()
    {
        return $this -> getKey();
    }

    /**
     * JWT 专用
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 获取当前账户
     */
    public function account()
    {
        $account = Account ::where("user_id", $this -> id) -> first();
        return $account;
    }


    /**
     * @param $userId
     * @return array
     * @throws \Exception
     */
    static function findByCache($userId)
    {
        try {
            $user = PlayerCache ::getUser($userId);
            if (!$user) {
                $user = self ::find($userId);
            }
            return $user;
        } catch (\Exception $e) {
            Clog ::userCache($e -> getMessage() . "|" . $e -> getFile() . "|" . $e -> getLine());
        }

        return [];
    }

    /**
     * @param $username
     * @return array
     * @throws \Exception
     */
    static function findByUsername($username,$partner_sign=0)
    {
       if($partner_sign)
       {
          return self ::where('username', $username)->where('partner_sign',$partner_sign)->first();
       }
       else
       {
          return self ::where('username', $username)->first();
       }

    }

    /**
     * 获取用户的扩展信息
     * @return mixed
     */
    public function getExtendInfo()
    {
        $info = PlayerExtendInfo ::where("partner_sign", $this -> partner_sign) -> where("user_id", $this -> id) -> first();
        return $info;
    }

    /**
     * 获取格式化的用户
     * @param $id
     * @return bool
     */
    static function findFormatUserById($id)
    {
        $user = self ::select('frozen_type','mark','username','is_tester','partner_sign')->find($id);
        $user -> register_time = $user -> last_login_time ? date("Y-m-d H:i:s", $user -> last_login_time) : "";
        $user -> last_login_time = $user -> last_login_time ? date("Y-m-d H:i:s", $user -> last_login_time) : "";
        $user -> frozen_type = self ::$frozenType[$user -> frozen_type];
        return $user ? $user : false;
    }

    static function getList($c)
    {
        $query = Player ::select(
            DB ::raw('users.*'),
            DB ::raw('user_accounts.balance'),
            DB ::raw('user_accounts.gift'),
            DB ::raw('user_accounts.frozen')
        ) -> leftJoin('user_accounts', 'user_accounts.user_id', '=', 'users.id')-> orderBy('users.id', 'desc');

        // 查询所有下级 不包含自己
        if (isset($c['parent_id']) && $c['parent_id'] == 'user.id') {
            $query ->where('users.rid', 'like', '%'.$c['parent_id'].'%')->where('users.id', '!=', $c['parent_id']);
        }

        // id
        if (isset($c['id']) && $c['id']) {
            $query -> where('users.id', $c['id']);
        }

        // 总代id
        if (isset($c['top_id']) && $c['top_id']) {
            $query -> where('users.top_id', $c['top_id']);
        }

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']!='all') {
            $query -> where('users.partner_sign', $c['partner_sign']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query -> where('users.username', $c['username']);
        }

        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query -> where('users.parent_id', $c['parent_id']);
        }

        // 状态
        if (isset($c['status']) && $c['status'] && $c['status'] != 'all') {
            $query -> where('users.status', $c['status']);
        }

        // 冻结类型
        if (isset($c['frozen_type']) && $c['frozen_type'] && $c['frozen_type'] != 'all') {
            $query -> where('users.frozen_type', $c['frozen_type']);
        }

        // 类型
        if (isset($c['type']) && $c['type'] && $c['type'] != 'all') {
            $query -> where('users.type', $c['type']);
        }

        // 开始时间
        if (isset($c['startTime']) && $c['startTime']) {
            $query -> where('users.created_at', '>=', $c['startTime']);
        }

        // 结束时间
        if (isset($c['endTime']) && $c['endTime']) {
            $query -> where('users.created_at', '<=', $c['endTime']);
        }

        // 是否测试
        if (isset($c['is_tester']) && $c['is_tester'] != 'all') {
            $query -> where('users.is_tester', $c['is_tester']);
        }

        //奖金组
		if (isset($c['min_prize']) && $c['min_prize']) {
			$query->where('users.prize_group', '>=', $c['min_prize']);
		}

		if (isset($c['max_prize']) && $c['max_prize']) {
			$query->where('users.prize_group', '<=', $c['max_prize']);
		}


        if (isset($c['min']) && $c['min'] && $c['min'] >= 0) {
            $query->where('user_accounts.balance', '>=', $c['min'] * 10000);
        }

        if (isset($c['max']) && $c['max'] && $c['max'] > 0) {
            $query->where('user_accounts.balance', '<=', $c['max'] * 10000);
        }

        // 余额区间
        if (isset($c['min'], $c['max']) && $c['min'] && $c['max'] && $c['max'] >= $c['min'] && $c['min'] >= 0 && $c['max'] >0) {
            $query->whereBetween('user_accounts.balance', [$c['min'] * 10000, $c['max'] * 10000]);
        }

        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('users.register_time', '>=', strtotime($c['start_time']));
        }

        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('users.register_time', '<=', strtotime($c['end_time']));
        }

        // 注册时间
        if (isset($c['start_time'], $c['end_time']) && $c['start_time'] && $c['end_time'] && $c['end_time'] >= $c['start_time']) {
            $query->whereBetween('users.register_time', [strtotime($c['start_time']), strtotime($c['end_time'])]);
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query -> count();
        $data = $query -> skip($offset) -> take($pageSize) -> get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

	/**
	 * 添加总代
	 * @param $partnerSign
	 * @param $username
	 * @param $password
	 * @param $phoneNumber
	 * @param $fundPassword
	 * @param $prizeGroup
	 * @param int $isTest
	 * @return Player|bool|string
	 * @throws \Exception
	 */
    static function addTop($partnerSign, $username, $password, $fundPassword, $prizeGroup, $isTest = 0, $phoneNumber = null)
    {
        $allPartner = Partner ::getOptions();
        if (!array_key_exists($partnerSign, $allPartner)) {
            return "对不起, 无效的商户标识";
        }

        $res = self ::checkPrizeGroup($prizeGroup,$partnerSign);
        if ($res !== true) {
            return $res;
        }

        $res = self ::checkUsername($username,$partnerSign);
        if ($res !== true) {
            return $res;
        }

        if ($phoneNumber != null) {
			$res = self ::checkPhoneNumber($phoneNumber,$partnerSign);
			if ($res !== true) {
				return $res;
			}
		}

        $res = self ::checkPassword($password);
        if ($res !== true) {
            return $res;
        }

        $res = self ::checkFundPassword($fundPassword);
        if ($res !== true) {
            return $res;
        }

        $top = self ::_addPlayer(null, $partnerSign, $username, $password, self::PLAYER_TYPE_TOP, $prizeGroup, $isTest, $phoneNumber);

        if (!is_object($top)) {
            return $top;
        }

        $top -> fund_password = Hash ::make($fundPassword);
        $top -> save();

        return $top;
    }

    // 添加下级
    public function addChild($username, $password, $type, $prizeGroup, $isTest = 0, $phoneNumber = null)
    {

        $res = self ::checkPrizeGroup($prizeGroup,$this->partner_sign);
        if ($res !== true) {
            return $res;
        }

        $res = self ::checkUsername($username,$this->partner_sign);
        if ($res !== true) {
            return $res;
        }

        $res = self ::checkPassword($password);
        if ($res !== true) {
            return $res;
        }

        if (!array_key_exists($type, self ::$types)) {
            return "对不起, 无效的用户类型!";
        }

        return self ::_addPlayer($this, $this -> partner_sign, $username, $password, $type, $prizeGroup, $isTest, $phoneNumber);
    }

    // 获取rid Array
    public function getRidArr() {
        $rid = $this->rid;
        if ($rid) {
            $idArr = explode("|", $rid);
            array_pop($idArr);
            return $idArr;
        }
        return [];
    }

    public function getRidStr()
    {
        $rid = $this->rid;
        $data = [];
        if ($rid) {
            $ids = explode("|", trim($rid, "|"));
            $users = Player::whereIn('id', $ids)->get();

            foreach ($users as $user) {
                $data[] = [
                    'id'    => $user->id,
                    'name'  => $user->username,
                ];
            }
        }
        return $data;
    }


	/**
	 * 添加下级
	 * @param $parent
	 * @param $partnerSign
	 * @param $username
	 * @param $password
	 * @param $phoneNumber
	 * @param $type
	 * @param $prizeGroup
	 * @param int $isTest
	 * @return Player|string
	 * @throws \Exception
	 */
    public static function _addPlayer($parent, $partnerSign, $username, $password, $type, $prizeGroup, $isTest = 0, $phoneNumber = null)
    {
        db() -> beginTransaction();
        try {
            $iconArr = CC ::getUserIcon($partnerSign);

            // 保存用户
            $item = new self();
            $item -> partner_sign = $partnerSign;
            $item -> username = $username;
            $item -> nickname = $username;
            $item -> password = Hash ::make($password);
            $item -> phone    = $phoneNumber;
            $item -> type = $type;
            $item -> user_icon = "/system/avatar/" . $iconArr[array_rand($iconArr)];
            $item -> user_level = $parent ? ($parent -> user_level + 1) : 1;
            $item -> prize_group = $prizeGroup;
            $item -> top_id = $parent ? ($parent -> top_id ? $parent -> top_id : $parent -> id) : 0;
            $item -> parent_id = $parent ? $parent -> id : 0;
            $item -> rid = $parent ? $parent -> rid : '';
            $item -> is_tester = $parent ? $parent->is_tester : intval($isTest);
            $item -> frozen_type = 0;
            //$item -> vip_level = 0;
            $item -> register_ip = real_ip();
            $item -> register_time = time();
            $item -> status = 1;
            $item -> save();

            if (!$parent || !$parent -> rid) {
                $item -> rid = $item -> id;
            } else {
                $item -> rid = $parent -> rid . "|" . $item -> id;
            }

            $item -> save();

            // 初始化账户
            Account ::initUserAccount($item);

            // 初始化统计数据
            ReportStatUserDay ::initUserStatData($item, true);

            // 初始化扩展信息
            PlayerExtendInfo ::initUserInfo($item);

            // 统计
            ReportStatStack::doRegister($item);

            db() -> commit();
        } catch (\Exception $e) {
            db() -> rollback();
            Clog ::userAddChild('添加下级-' . $e -> getMessage() . "-" . $e -> getFile() . "-" . $e -> getLine());
            return $e -> getMessage();
        }

        $res = $item->playerChildCountChange();

        //刷新用户关系缓存
        MsgInits::SetFriendCache($item);

        if ($res !== true) {
            return $res;
        }

        return $item;
    }

    /**
     * @param $mode
     * @param $type
     * @param $amount
     * @param $reason
     * @param $adminUser
     * @return bool|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function manualTransfer($mode, $type, $amount, $reason, $adminUser)
    {
        $min    = partnerConfigure($adminUser->partner_sign,"player_transfer_child_min", 1);
        $max    = partnerConfigure($adminUser->partner_sign,"player_transfer_child_max", 10000);

        $adminGroup = PartnerAdminGroup::where('id', $adminUser->group_id)->first();
        if ($adminUser && $adminGroup->name === '超级管理') {
            $max = partnerConfigure($adminUser->partner_sign,"player_transfer_max_super", 200000);
        }

        $account = $this -> account();
        if (!$account) {
            return "对不起, 账户不存在!";
        }

        if ('add' == $mode) {
            $addTypes = config("user.main.transfer_add");
            if (!array_key_exists($type, $addTypes)) {
                return "对不起, 无效的类型!";
            }

            if ($amount < $min || $amount > $max) {
                return "对不起, 无效的金额!";
            }

            if (!$reason) {
                return "对不起, 请输入描述!";
            }

            $accountLock = new AccountLocker($this -> id, "admin-transfer-");
            if (!$accountLock -> getLock()) {
                return "对不起, 获取账户锁失败, 请稍后再试!";
            }

            $transferConfig = $addTypes[$type];
            db() -> beginTransaction();
            try {
                // 帐变 - 中奖
                $params = [
                    'user_id' => $this -> id,
                    'amount' => moneyUnitTransferIn($amount),
                    'from_admin_id' => $adminUser -> id,
                    'desc' => $transferConfig['name'] . "|" . $reason
                ];

                $accountChange = new AccountChange();
                $res = $accountChange -> change($account, $transferConfig['change_type'], $params);
                if ($res !== true) {
                    $accountLock -> release();
                    db() -> rollback();
                    return $res;
                }

				// 保存记录 帐变备份
				PartnerAdminTransferRecords::addItem($this, $mode, $type, $params['amount'], $adminUser);
				db()->commit();
				

            } catch (\Exception $e) {
                $accountLock -> release();
                db() -> rollback();
                return $e -> getMessage() . "|" . $e -> getLine() . "|" . $e -> getFile();
            }

            $accountLock -> release();

            // 统计
        } else {

            $reduceTypes = config("user.main.transfer_reduce");
            if (!array_key_exists($type, $reduceTypes)) {
                return "对不起, 无效的类型!";
            }

            // 金额
            $amount = intval($amount);
            if ($amount < $min || $amount > $max) {
                return "对不起, 无效的金额!"."最小金额".$min."最大金额".$max."当前金额".$amount;
            }

            // 描述
            if (!$reason) {
                return "对不起, 请输入描述!";
            }

            // 价差余额
            if ($account -> balance < $amount * 10000) {
                return "对不起, 余额不足!";
            }

            $accountLock = new AccountLocker($this -> id, "admin-transfer-reduce");
            if (!$accountLock -> getLock()) {
                return "对不起, 获取账户锁失败, 请稍后再试!";
            }

            $transferConfig = $reduceTypes[$type];
            db() -> beginTransaction();
            try {
                // 帐变 - 中奖
                $params = [
                    'user_id' => $this -> id,
                    'amount' => $amount * 10000,
                    'from_admin_id' => $adminUser -> id,
                    'desc' => $transferConfig['name'] . "|" . $reason
                ];

                $accountChange = new AccountChange();
                $res = $accountChange -> change($account, $transferConfig['change_type'], $params);
                if ($res !== true) {
                    $accountLock -> release();
                    db() -> rollback();
                    return $res;
                }

                // 帐变备份
				PartnerAdminTransferRecords::addItem($this, $mode, $type, $params['amount'], $adminUser);
				db()->commit();

            } catch (\Exception $e) {
                $accountLock -> release();
                db() -> rollback();
                return $e -> getMessage() . "|" . $e -> getLine() . "|" . $e -> getFile();
            }

            $accountLock -> release();

            // 统计
        }

        return true;
    }

    /**
     * 用户名检测
     * @param $username
     * @return string
     */
    static function checkUsername($username,$partner_sign=0)
    {
        // 1. 长度是否合法
        if (!preg_match("/^[0-9a-zA-Z_]{6,16}$/i", $username)) {
            return "对不起,用户名不符合规则!";
        }
        
        $count = 0;

        if(!$partner_sign)
        {
            // 2. 是否存在重复用户名
            $count = self ::where('username', '=', $username) -> count();
        }
        else
        {
            // 2. 是否存在重复用户名
            $count = self ::where('username', '=', $username)->where('partner_sign',$partner_sign) -> count();
        }
       
        if ($count > 0) {
            return '对不起,该用户名已被注册，请选择其他用户名!';
        }

        return true;
    }

	/**
	 * 手机号检测
	 * @param $phoneNmber
	 * @param int $partner_sign
	 * @return string
	 */
	static function checkPhoneNumber($phoneNmber,$partner_sign=0)
	{
		// 1. 长度是否合法
		if (!preg_match("/^[1](([3][0-9])|([4][5-9])|([5][0-3,5-9])|([6][5,6])|([7][0-8])|([8][0-9])|([9][1,8,9]))[0-9]{8}$/", $phoneNmber)) {
			return "对不起,请输入正确手机号!";
		}

		$count = 0;

		if(!$partner_sign)
		{
			// 2. 是否存在重复号码
			$count = self ::where('phone', '=', $phoneNmber) -> count();
		}
		else
		{
			// 2. 是否存在重复号码
			$count = self ::where('phone', '=', $phoneNmber)->where('partner_sign',$partner_sign) -> count();
		}

		if ($count > 0) {
			return '对不起,该用手机号已被注册，请使用他手机号!';
		}

		return true;
	}

    /**
     * @param $nickname
     * @return bool
     */
    static function checkNickname($nickname)
    {
        if (mb_strlen($nickname, "UTF-8") >= 1 && mb_strlen($nickname, "UTF-8") <= 64) {
            return true;
        } else {
            return "对不起, 昵称不合法!!";
        }
    }

    /**
     * 密码检测
     * @param $password
     * @return bool|string
     */
    static function checkPassword($password)
    {
        if (!preg_match("/^[0-9a-zA-Z]{6,16}$/i", $password) || preg_match("/^[0-9]+$/", $password) || preg_match("/^[a-zA-Z]+$/i", $password) || preg_match("/(.)\\1{2,}/i", $password)) {
            return "对不起,密码输入不正确!";
        } else {
            return true;
        }
    }

    /**
     * 密码检测
     * @param $password
     * @return bool|string
     */
    static function checkFundPassword($password)
    {
        if (!preg_match("/^[0-9a-zA-Z]{6,16}$/i", $password) || preg_match("/^[0-9]+$/", $password) || preg_match("/^[a-zA-Z]+$/i", $password) || preg_match("/(.)\\1{2,}/i", $password)) {
            return "对不起,密码输入不正确!";
        } else {
            return true;
        }
    }

    // 检查奖金组
    static function checkPrizeGroup($prizeGroup,$partner_sign)
    {
        $minGroup = partnerConfigure($partner_sign,'player_register_min_prize_group', 1800);
        $maxGroup = partnerConfigure($partner_sign,'player_register_max_prize_group', 1980);

        if ($prizeGroup < $minGroup) {
            return "对不起,奖金组不能低于{$minGroup}!";
        }

        if ($prizeGroup > $maxGroup) {
            return "对不起,奖金组不能高于{$maxGroup}!";
        }

        return true;
    }

    // 检查用户类型
    static function checkUserType($userType)
    {
        if (!in_array($userType, array(self::PLAYER_TYPE_TOP, self::PLAYER_TYPE_PROXY, self::PLAYER_TYPE_PLAYER))) {
            return "无效的用户类型!";
        }
        return true;
    }

    // 获取今日提现数量
    public function getTodayDrawCount()
    {
        $todayStart = strtotime(date("Y-m-d 00:00:00"));
        $todayEnd   = strtotime(date("Y-m-d 23:59:59"));

        $count      = Withdraw ::where('user_id', $this -> id) -> whereIn('status', [4, 5]) -> where('process_time', ">=", $todayStart) -> where('process_time', "<=", $todayEnd) -> count();
        return $count;
    }

    /**
     * 冻结用户不能投注
     * @return bool
     */
    public function canBet()
    {
        if ($this -> frozen_type == self::FROZEN_TYPE_DISABLE_BET) {
            return false;
        }
        return true;
    }


    /**
     * 冻结用户不能提现
     * @return bool
     */
    public function canWithdraw()
    {

        if ($this -> frozen_type > 0) {
            return false;
        }

        return true;
    }

    /**
     * 提现是否需要审核
     * @return bool
     */
    public function needWithdrawCheck()
    {
        // 检查是否开启审核
        $isNeedCheck = configure('finance_withdraw_need_check', 1);
        if ($isNeedCheck != 1) {
            return false;
        }

        return true;
    }

    public function checkBetCondition($username)
    {
        // 1. 流水倍数获取
        $betTimes  = request('sign', 'finance_withdraw_bet_times');
        $configure = Configure ::findBySign($betTimes);

        // 2. 获取玩家的下注金额
        $bets      = ReportStatUserDay ::getTotalBets($username);

        // 3. 获取玩家的流水金额
        $cancel    = ReportStatUserDay ::getTotalCancel($username);

        // 4. 检测流水是否达到标准
//        if ($configure -> value * $bets < $cancel || $bets === 0 || $cancel === 0) {
//            return false;
//        }

        return true;
    }

    /**
     * 发起提现
     * @param $amount
     * @param $card
     * @param string $from
     * @return bool|string
     * @throws \Exception
     */
    public function requestWithdraw($amount, $card, $from)
    {
        $ret = Withdraw ::request($this, $amount, $card, $from);
        if (true !== $ret) {
            return $ret;
        }

        return true;
    }

    /**
     * 是否是下级
     * @param $childId
     * @return bool
     */
    public function hasChild($childId)
    {
        if (!$childId) {
            return false;
        }

        // 检测是否存在
        $child = Player ::find($childId);
        if (!$child) {
            return false;
        }

        // 不在之中
        $idArr = explode("|", trim($child -> rid, "|"));
        if (!$idArr || !in_array($this -> id, $idArr)) {
            return false;
        }

        return true;
    }

    // 获取上级信息
    public function getParents()
    {
        $data = [];

        if ($this -> rid) {
            $ids = explode("|", trim($this -> rid, "|"));
            array_pop($ids);

            if ($ids) {
                $parents = Player ::select(
                    DB ::raw('users.*'),
                    DB ::raw('user_accounts.balance'),
                    DB ::raw('user_accounts.frozen')
                ) -> leftJoin('user_accounts', 'user_accounts.user_id', '=', 'users.id') -> whereIn("users.id", $ids) -> orderBy('id', 'DESC');

                foreach ($parents as $parent) {
                    $data[] = [
                        'username' => $parent -> username,
                        'salary_percentage' => $parent -> salary_percentage,
                        'bonus_percentage' => $parent -> bonus_percentage,
                        "prize_group" => $parent -> prize_group,
                        "balance" => number4($parent -> balance),
                        "frozen" => number4($parent -> frozen),
                        "vip_level" => $parent -> vip_level,
                        "register_ip" => $parent -> register_ip,
                        "register_time" => date("Y-m-d H:i:s", $parent -> register_time),
                        "last_login_ip" => $parent -> last_login_ip,
                        "last_login_time" => date("Y-m-d H:i:s", $parent -> last_login_time),
                        "frozen_type" => date("Y-m-d H:i:s", $parent -> last_login_time),
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * 获取规定时间内注册数
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function getUserRegisterCount($startTime, $endTime)
    {
        $ip = real_ip();
        $count = self ::where("partner_sign", $this -> partner_sign) -> where("register_ip", $ip) -> where("register_time", ">=", $startTime) -> where("register_time", "<=", $endTime) -> count();
        return $count;
    }

    // 设置头像
    public function setAvatar($avatar)
    {
        $this -> user_icon = "player/" . $avatar;
        $this -> save();
        return true;
    }

    /*** =========================== 日工资 ========================== */

    /**
     * @param $rate
     * @param $fromType 'admin' / 'parent'
     * @param $fromUser
     * @return bool
     */
    public function setSalaryRate($rate, $fromType = 'admin',  $fromUser = null) {

        $parent = null;

        // 上级 设置
        if ($fromType == 'parent') {
            if (!$fromUser && $fromUser->id != $this->parent_id) {
                return'对不起, 您没有权限(0x001)!';
            }

            $parent = $fromUser;
        }

        // 商户管理员 设置
        if ($fromType == 'admin') {
            if ($fromUser->partner_sign != $this->partner_sign) {
                return '对不起, 您没有权限(0x002)!';
            }

            if ($this->type != Player::PLAYER_TYPE_TOP) {
                $parent = Player::find($this->parent_id);
                if (!$parent->salary_percentage) {
                    return '对不起, 用户的上级没有设置日工资!';
                }
            }
        }

        $maxRate = partnerConfigure($fromUser->partner_sign, "player_salary_rate_max", 10);
        // 不能大于上级
        if ($parent) {
            $maxRate = $maxRate > $parent->salary_percentage ? $parent->salary_percentage : $maxRate;
        }

        $minRate        = partnerConfigure($fromUser->partner_sign, "player_salary_rate_min", 0);
        $maxChildRate   = self::where("parent_id", $this->id)->max("salary_percentage");
        if ($maxChildRate) {
            $minRate = $minRate < $maxChildRate ? $maxChildRate : $minRate;
        }

        if ($rate > $maxRate || $rate < $minRate) {
            return "对不起, 无效日工资比例不正确, 最小{$minRate}, 最大{$maxRate}";
        }

        $this->salary_percentage = $rate;
        $this->save();
        return true;
    }

    /**
     * @param $rate
     * @param $fromType 'admin' / 'parent'
     * @param $fromUser
     * @return bool
     */
    public function setBonusRate($rate, $fromType = 'admin',  $fromUser = null) {
        $parent = null;

        // 上级 设置
        if ($fromType == 'parent') {
            if (!$fromUser && $fromUser->id != $this->parent_id) {
                return'对不起, 您没有权限(0x001)!';
            }

            $parent = $fromUser;
        }

        // 商户管理员 设置
        if ($fromType == 'admin') {
            if ($fromUser->partner_sign != $this->partner_sign) {
                return '对不起, 您没有权限(0x002)!';
            }

            if ($this->type != Player::PLAYER_TYPE_TOP) {
                $parent = Player::find($this->parent_id);
                if (!$parent->bonus_percentage) {
                    return '对不起, 用户的上级没有设置分红!';
                }
            }
        }

        // 范围
        $maxRate = partnerConfigure($fromUser->partner_sign, "player_bonus_rate_max", 50);

        // 不能大于上级
        if ($parent) {
            $maxRate = $maxRate > $parent->bonus_percentage ? $parent->bonus_percentage : $maxRate;
        }

        // 不能小于下级
        $minRate        = partnerConfigure($fromUser->partner_sign, "player_bonus_rate_min", 0);
        $maxChildRate   = self::where("parent_id", $this->id)->max("bonus_percentage");
        if ($maxChildRate) {
            $minRate = $minRate < $maxChildRate ? $maxChildRate : $minRate;
        }

        if ($rate > $maxRate || $rate < $minRate) {
            return "对不起, 无效日工资比例不正确, 最小{$minRate}, 最大{$maxRate}";
        }


        $this->bonus_percentage = $rate;
        $this->save();
        return true;
    }

	/**
	 * @param $username
	 * @param $password
	 * @param $phoneNumber
	 * @param $type
	 * @param $prizeGroup ?
	 * @param $partner_sign
	 * @param int $isTest
	 * @return Player|bool|string
	 * @throws \Exception
	 */
    public function addUser($username, $password, $type, $prizeGroup, $partner_sign, $isTest = 0, $phoneNumber = null)
    {

        $res = self ::checkPrizeGroup($prizeGroup,$partner_sign);
        if ($res !== true) {
            return $res;
        }

        $res = self ::checkUsername($username,$partner_sign);
        if ($res !== true) {
            return $res;
        }

        $res = self ::checkPassword($password);
        if ($res !== true) {
            return $res;
        }

        if (!array_key_exists($type, self ::$types)) {
            return "对不起, 无效的用户类型!";
        }
        return self ::_addPlayer('', $partner_sign, $username, $password, $type, $prizeGroup, $isTest, $phoneNumber);
    }

    /**
     * 更新团队会员奖金组 分红 日工资
     * @param $c
     *
     * @return bool
     */
    static public function saveItem($c)
    {
        $data = [];

        if (!empty($c['bonus_percentage'])) {
            $data['bonus_percentage'] = $c['bonus_percentage'];
        }
        if (!empty($c['salary_percentage'])) {
            $data['salary_percentage'] = $c['salary_percentage'];
        }
        if (!empty($c['prize_group'])) {
            $data['prize_group'] = $c['prize_group'];
        }
        if (!empty($data)) {
            self::where('id', $c['id'])->where('parent_id', $c['parent_id'])->update($data);
        }
        return true;
    }

    /**
     * 统计玩家的总下级和直属下级
     * @return bool|string
     */
    public function playerChildCountChange()
    {
        if ($this->parent_id <= 0) {
            return true;
        }

        // 直接下级
        $sql = "update `users` set `direct_child_count` = `direct_child_count` + 1 where `id` = {$this->parent_id}";
        $ret = db()->update($sql);
        if(!$ret) {
            return "update-stat-player-count-" . $sql;
        }

        // 间接下级
        $filter = array_filter(explode('|', $this->rid));
        array_pop($filter);
        if(count($filter) > 0) {
            $ids = implode("','", $filter);
            // 日 团队 更新
            if ($ids) {
                $sql = "update `users` set `child_count` = `child_count` + 1 where  `id` in ('{$ids}')";
                $ret = db()->update($sql);
                if(!$ret) {
                    return "update-stat-player-count-" . $sql;
                }
            }
        }

        return true;
    }

    /**
     * 转帐给下级
     * @param $childUser
     * @param $amount
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function transferToChild($childUser, $amount) {
        // 资金修正
        $moneyFixedTimes = partnerConfigure($childUser->partner_sign, 'system_finance_fixed_times');
        $amount = $moneyFixedTimes * $amount;

        $fromUserLocker = new AccountLocker($this->id);
        if(!$fromUserLocker->getLock()){
            return "对不起, 获取用户锁失败(0x001)!";
        }

        $toUserLocker = new AccountLocker($childUser->id);
        if(!$toUserLocker->getLock()){
            return "对不起, 获取用户锁失败(0x002)!";
        }

        db()->beginTransaction();

        try {
            /** ================= 上级转账到下级 ============== */
            $fromUserAccount = $this->account();
            if($fromUserAccount->balance < $amount)
            {
                return Help::returnApiJson('对不起, 您帐户余额不足!', 0);
            }

            // 为下级充值
            $formParams = [
                'user_id'       => $this->id,
                'to_id'         => $childUser->id,
                'amount'        => $amount,
                'desc'          => '为下级充值',
            ];

            $accountChange = new AccountChange();
            $fromRes = $accountChange->change($fromUserAccount, 'transfer_to_child',  $formParams);
            if ($fromRes !== true) {
                $fromUserLocker->release();
                db()->rollback();
                return $fromRes;
            }

            /** ================= 下级接收转账 ============== */
            $toUserAccount   = $childUser->account();
            $toParams = [
                'user_id'       => $childUser->id,
                'from_id'       => $this->id,
                'amount'        => $amount,
                'desc'          => '上级转帐',
            ];

            $toRes = $accountChange->change($toUserAccount, 'transfer_from_parent',  $toParams);
            if ($toRes !== true) {
                $toUserLocker->release();
                db()->rollback();
                return $toRes;
            }

            $toUserLocker->release();

            PlayerTransferRecords::addItem($this, $childUser,  $amount);
            ReportStatStack::doTransferToChild($this, $childUser, $amount);
            db()->commit();
        } catch (\Exception $e) {
            $fromUserLocker->release();
            $toUserLocker->release();
            db()->rollback();
            return  $e->getMessage();
        }

        return true;
    }

    /** =============== 辅助静态方法 ================= */

    /**
     * 总代的选项
     * @param $partnerSign
     * @return array
     */
    static function getTopUserOption($partnerSign) {
        $players = self::where("partner_sign", $partnerSign)->where("type", self::PLAYER_TYPE_TOP)->where("status", 1)->get();

        $data = [];
        foreach ($players as $player) {
            $data[$player->id] = $player->username;
        }

        return $data;
    }

    function transferFrom($c, $player)
    {
        $amount = $c['amount'] ?? 0;
        if ($amount < 50 || $amount > 1000) {
            $this->errMsg = '转账范围 大于50  小于 1000';
            return false;
        }
        $amount = $amount * config('game.main.money_unit');

        // 1. 获取上级信息
        $partnerM = Player::where('id', $player->parent_id)->first();
        if ($partnerM == null) {
            $this->errMsg = '无上级';
            return false;
        }
        // 2. 上级是否绑定了银行卡
        $parentCard = PlayerCard::where('user_id', $partnerM->id)->first();
        if (!$parentCard) {
            $this->errMsg = '上级未绑定银行卡';
            return false;
        }

        $userId    = $player->id;
        $partnerId = $partnerM->id;

        // 2. 帐变
        // 1. 获取账户锁
        $accountLockerChild = new AccountLocker(
            $userId, "transfer-to-child" . $userId
        );
        if ( ! $accountLockerChild->getLock()) {
            $accountLockerChild->release();
            $this->errMsg = '对不起, 获取账户锁失败, 请稍后再试, Child!';
            return false;
        }
        $accountLockerParent = new AccountLocker(
            $partnerId, "transfer-from-parent" . $partnerId
        );
        if ( ! $accountLockerParent->getLock()) {
            $accountLockerParent->release();
            $this->errMsg = '对不起, 获取账户锁失败, 请稍后再试, Parent!';
            return false;
        }

        // 帐变
        db()->beginTransaction();
        try {
            // 1帐变处理
            $accountChange = new AccountChange();
            // 真实扣款

            $account = Account::findAccountByUserId($partnerId);
            if ( ! $account) {
                $accountLockerChild->release();
                db()->rollback();

                return "对不起, 账户信息不存在, 请稍后再试2!";
            }

            // 为下级充值
            $params = [
                'user_id' => $partnerId,
                'amount'  => $amount,
                'to_id' => $userId,
            ];
            $res = $accountChange->change(
                $account, 'transfer_to_child', $params,
                $player->is_robot
            );
            if ($res !== true) {
                $accountLockerChild->release();
                db()->rollback();

                return $res;
            }


            $account = Account::findAccountByUserId($userId);
            if ( ! $account) {
                $accountLockerParent->release();
                db()->rollback();

                return "对不起, 账户信息不存在, 请稍后再试2!";
            }
            $params = [
                'user_id' => $userId,
                'amount'  => $amount,
                'from_id' => $partnerId,
            ];
            $res = $accountChange->change(
                $account, 'transfer_from_parent', $params,
                $partnerM->is_robot
            );

            if ($res !== true) {
                $accountLockerParent->release();
                db()->rollback();
                $this->errMsg = $res;
                return false;
            }
            $accountLockerParent->release();
            $accountLockerChild->release();
            db()->commit();
            return true;
        } catch (\Exception $e) {
            $accountLockerChild->release();
            $accountLockerParent->release();
            db()->rollback();
            $this->errMsg = '对不起, 访问超时Err';
            return false;
        }
    }
}
