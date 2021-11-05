<?php
namespace App\Http\Controllers\PartnerApi\Player;

use App\Lib\Help;
use App\Lib\Logic\Cache\PartnerCache;
use App\Models\Partner\PartnerAdminActionReview;
use App\Models\Partner\PartnerAdminGroup;
use App\Models\Partner\PartnerMessage;
use App\Models\Partner\PartnerReviewFlow;
use App\Models\Player\Player;
use App\Models\Player\PlayerIp;
use App\Models\Finance\Withdraw;
use App\Models\Finance\Recharge;
use App\Models\Player\PlayerLog;
use App\Models\Player\PlayerCard;
use Illuminate\Support\Facades\Hash;
use App\Models\Report\ReportStatUser;
use App\Models\Player\PlayerVipConfig;
use App\Models\Partner\PartnerAdminBehavior;
use App\Http\Controllers\PartnerApi\ApiBaseController;

/**
 * 玩家管理接口
 * Class ApiPlayerController
 * @package App\Http\Controllers\PartnerApi\Player
 */
class ApiPlayerController extends ApiBaseController
{
    /**
     * 获取玩家等级设置列表
     * @return mixed
     */
    public function playerVipConfig()
    {
        $partnerSign = $this->partnerSign;
        $data['data'] = PlayerVipConfig::where('partner_sign',$partnerSign)->orderBy('vip_level','asc')->get();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 编辑或新增玩家等级设置
     * @param int $id
     * @return mixed
     */
    public function addPlayerVipConfig($id=0)
    {
        $c = request()->all();
        $c["partner_sign"] = $this->partnerSign;

        if($id)
        {
            $playerVipconfig = PlayerVipConfig::find($id);

            if(!$playerVipconfig)
            {
                return Help::returnApiJson('此配置不存在!', 0);
            }

            if($playerVipconfig->partner_sign != $this->partnerSign)
            {
                return Help::returnApiJson('对不起，您没有操作权限!', 0);
            }
        }
        else
        {
            $playerVipconfig = new PlayerVipConfig();
        }

        if($id)
        {
            $nextVip = PlayerVipConfig::where('vip_level','>',$c['vip_level'])->where('id','!=',$id)->orderBy('vip_level','asc')->first();
            $prevVip = PlayerVipConfig::where('vip_level','<',$c['vip_level'])->where('id','!=',$id)->orderBy('vip_level','desc')->first();
        }
        else
        {
            $nextVip = PlayerVipConfig::where('vip_level','>',$c['vip_level'])->orderBy('vip_level','asc')->first();
            $prevVip = PlayerVipConfig::where('vip_level','<',$c['vip_level'])->orderBy('vip_level','desc')->first();
        }
        
        
        if($nextVip && $nextVip->recharge_total<=$c['recharge_total'])
        {
            return Help::returnApiJson('对不起，充值金额必须小于上一级!', 0);
        }
        elseif($prevVip && $c['recharge_total']<= $prevVip->recharge_total)
        {
            return Help::returnApiJson('对不起，充值金额必须大于下一级!', 0);
        }

        $msg = $playerVipconfig->saveItem($c, $this->partnerSign);
        if($msg!==true)
        {
            return Help::returnApiJson($msg, 0);
        }

        return Help::returnApiJson('编辑数据成功!', 1);
    }

    /**
     * 获取玩家等级设置详情
     * @param $id
     * @return mixed
     */
    public function playerVipConfigDetail($id)
    {
        $playerVipconfig = PlayerVipConfig::find($id);
        if(!$playerVipconfig)
        {
            return Help::returnApiJson('此配置不存在!', 0);
        }

        if($playerVipconfig->partner_sign != $this->partnerSign)
        {
            return Help::returnApiJson('对不起，您没有操作权限!', 0);
        }

        return Help::returnApiJson('获取数据成功!', 1, $playerVipconfig);
    }

    /**
     * 玩家VIP等级设置
    */
    public function setPlayerVipLevel () {
        $id = request('id');
        $c['vipLevel'] = request('vip_level');

        $user = Player::where('id', $id)->where('partner_sign', $this->partnerSign)->first();
        if (!$user) {
            return Help::returnApiJson('错误的玩家信息!', 0);
        }

        $level = PlayerVipConfig::where('vip_level', $c['vipLevel'])->first();
        if (!$level) {
            return Help::returnApiJson('请先设置玩家等级信息!', 0);
        }

        Player::where('id',$id)->update(['vip_level' => $c['vipLevel']]);

        return Help::returnApiJson('玩家等级设置成功!', 1);
    }

    /**
     * 获取玩家列表
     * @return mixed
     */
    public function playerList()
    {
        $c      = request()->all();
        $c["partner_sign"] = $this->partnerSign;

        // 获取数据
        $data   = Player::getList($c);

        $c['parent_id']    = request('parent_id');
        if (isset($c['parent_id']) && $c['parent_id']) {
            // 获取上级用户名
            $users = Player::where('id', $c['parent_id'])->first();
            if (!$users){
                return Help::returnApiJson(0);
            }
            $ids = explode('|', $users->rid);
            $parent = Player::whereIn('id', $ids)->where('id','!=', $users->id)->get();
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

        $topOption = Player::getTopUserOption($this->partnerSign);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                    => $item->id,
                "parent_id"             => $item->parent_id,
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
                "unfrozen"              => $item->unfrozen,
                "prize_group"           => $item->prize_group,
                "bonus_percentage"      => $item->bonus_percentage,
                "salary_percentage"     => number_format($item->salary_percentage,1),
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

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
    
    // 冻结所有包括自已
    public function frozenAll($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;
        $frozenType       = request("frozen_type","");
        if($frozenType!=1 && $frozenType!=2 && $frozenType!=3 && $frozenType!=4 && $frozenType!=5)
        {
            return Help::returnApiJson("对不起, 冻结类型不正确！", 0);
        }

        $player = Player::find($id);
        if(!$player)
        {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($player->partner_sign != $partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
        }
        
        if($frozenType==4)
        {
            $frozenType=0;
        }
        Player::where('rid','like','%'.$player->id.'%')->update(['frozen_type'=>$frozenType]);

        // 加入缓存 强制踢线
        PartnerCache::kickLine($player);
        return Help::returnApiJson("恭喜, 操作成功!!", 1);
    }

    //用户备注
    public function playerMark($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;
        $player = Player::find($id);
        if(!$player)
        {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        if ($player->partner_sign != $partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
        }

        $player->mark     = request("mark","");
        $player->save();
        
        return Help::returnApiJson("恭喜, 操作成功!!", 1);
    }

    // 获取　玩家　详情
    public function playerDetail($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        $player = Player::find($id);
        if ($player->partner_sign != $partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
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

    // 添加总代
    public function playerAddTop()
    {
        $partnerSign        = $this->partnerSign;
        $username           = request("username");
        $phoneNumber        = request('phone','');

        $codeOne = base64_decode(request("fund_password"));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);

        $codeO = base64_decode(request("password"));
        $codeT = substr($codeO, 0, -4);
        $fina = base64_decode($codeT);
        $password = substr($fina, 5, 37);

        $isTester           = request("is_test");
        $prizeGroup         = request("prize_group");

        // 密码和资金密码不能一致
        if ($password == $fundPassword) {
            return Help::returnApiJson("对不起, 登录密码和资金密码不能一致！", 0);
        }

        $res = Player::addTop($partnerSign, $username, $password, $fundPassword, $prizeGroup, $isTester, $phoneNumber);
        if(!is_object($res)) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加总代成功!!", 1);
    }

    // 获取　玩家　密码
    public function playerPassword($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取用户
        $player = Player::find($id);
        if (!$player) {
            return Help::returnApiJson("对不起, 无效的玩家id！", 0);
        }

        $partnerSign = $player->partner_sign;

        // 检测操作者资金密码
        $codeOne = base64_decode(request("admin_fund_password"));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);
        if (!$fundPassword || !Hash::check($fundPassword, $adminUser->fund_password)) {
            return Help::returnApiJson('对不起, 无效的管理员资金密码!', 0);
        }

        // 模型
        $mode   = request("mode");

        if ("login" == $mode) {

            // 直接设置新没密
            $codeO = base64_decode(request("password"));
            $codeT = substr($codeO, 0, -4);
            $fina = base64_decode($codeT);
            $password = substr($fina, 5, 37);
            $res        = Player::checkPassword($password);
            if ($res !== true) {
                return Help::returnApiJson($res, 0);
            }

            $codeO = base64_decode(request("confirm_password"));
            $codeT = substr($codeO, 0, -4);
            $fina = base64_decode($codeT);
            $confirmPassword = substr($fina, 5, 37);
            if ($confirmPassword != $password) {
                return Help::returnApiJson("对不起, 两次密码输入不一致!", 0);
            }

            // 操作描述
            $desc = request("desc", '');
            if (!$desc) {
                return Help::returnApiJson("对不起, 请输入操作描述!", 0);
            }

            // 等待审核
            $config = [
                'old_password' => "",
                'new_password' => $password,
                "desc"         => $desc,
            ];

			// 是否需要审核
			$res = PartnerReviewFlow::where('partner_sign', $this->partnerSign)->where('type', 'password')->first();
			if ($res == null) {
				$passwordHash = Hash::make($password);
				$ress = Player::where('id', $id)->update(['password' => $passwordHash]);
				if (true == $ress) {
					return Help::returnApiJson('恭喜,登录密码修改成功', 1);
				}
				$res = PartnerAdminActionReview::addReviewSelf($player, 'login_password', 0, $config, $adminUser, $partnerSign);
				if ($res !== true) {
					return Help::returnApiJson($res, 0);
				}
			}

            $res = PartnerAdminActionReview::addReview($player, 'login_password', 0, $config, $adminUser, $partnerSign);
            if ($res !== true) {
                return Help::returnApiJson($res, 0);
            }

            return Help::returnApiJson("恭喜, 操作已经记录, 等待审核!", 1);
        } else {
            // 资金密码
            $codeO = base64_decode(request("password"));
            $codeT = substr($codeO, 0, -4);
            $fina = base64_decode($codeT);
            $password = substr($fina, 5, 37);
            $res        = Player::checkFundPassword($password);
            if ($res !== true) {
                return Help::returnApiJson($res, 0);
            }

            $codeOne = base64_decode(request("confirm_password"));
            $codeTwo = substr($codeOne, 0, -4);
            $final = base64_decode($codeTwo);
            $confirmPassword = substr($final, 5, 37);
            if ($confirmPassword != $password) {
                return Help::returnApiJson("对不起, 密码输入不一致!", 0);
            }

            // 操作描述
            $desc = request("desc", '');
            if (!$desc) {
                return Help::returnApiJson("对不起, 请输入操作描述!", 0);
            }

            // 等待审核
            $config = [
                'old_password' => "",
                'new_password' => $password,
                "desc"         => $desc,
            ];

			// 是否需要审核
			$res = PartnerReviewFlow::where('partner_sign', $this->partnerSign)->where('type', 'fund_password')->first();
			if ($res == null) {
				$passwordHash = Hash::make($password);
				$ress = Player::where('id', $id)->update(['fund_password' => $passwordHash]);
				if (true == $ress){
					return Help::returnApiJson('恭喜,资金密码修改成功', 1);
				}
				$res = PartnerAdminActionReview::addReviewSelf($player, 'fund_password', 0, $config, $adminUser, $partnerSign);
				if ($res !== true) {
					return Help::returnApiJson($res, 0);
				}
			}
            $res = PartnerAdminActionReview::addReview($player, 'fund_password', 0, $config, $adminUser, $partnerSign);
            if ($res !== true) {
                return Help::returnJson($res, 0);
            }
            return Help::returnApiJson("恭喜, 操作已经记录, 等待审核!", 1);
        }
    }

    // 资金审核
    public function playerTransfer($id) {
        $adminUser = $this->partnerAdminUser;

        // 获取用户
        $player = Player::find($id);
        if (!$player) {
            return Help::returnApiJson("对不起, 无效的玩家id！", 0);
        }

        $partnerSign  = $player->partner_sign;
        if ($partnerSign != $this->partnerSign){
        	return Help::returnApiJson('对不起,错误信息',0);
		}

        $action       = request("action", 'process');
        if ('option' == $action) {
            $account = $player->account();

            $data['username']                   = $player->username;
            $data['balance']                    = number4($account->balance);
            $data['transfer_add_options']       = config("user.main.transfer_add");
            $data['transfer_reduce_options']    = config("user.main.transfer_reduce");

            return Help::returnApiJson("恭喜, 获取成功", 1, $data);
        } else {
            $mode       = request("mode");
            $type       = request("type");
            $amount     = request("amount");
            $reason     = request("reason");

            $codeOne = base64_decode(request("password"));
            $codeTwo = substr($codeOne, 0, -4);
            $final = base64_decode($codeTwo);
            $password = substr($final, 5, 37);

            // 资金密码
            if (!$password || !Hash::check($password, $adminUser->fund_password)) {
                return Help::returnApiJson('对不起, 无效的资金密码!', 0);
            }
			// 类型
			$types = config('user.main.transfer_' . $mode);

            // 描述
            if (!$reason) {
                return Help::returnApiJson("对不起, 请输入操作描述!", 0);
            }

            $modes = config('user.main.transfer_mode');
            // 模式
            if (!array_key_exists($mode, $modes)) {
                return Help::returnApiJson("对不起, 无效的模式!", 0);
            }
            
            if (!array_key_exists($type, $types)) {
                return Help::returnApiJson("对不起, 无效的类型!", 0);
            }
            // 金额限制
            $min    = partnerConfigure($adminUser->partner_sign,"player_transfer_child_min", 1);
            $max    = partnerConfigure($adminUser->partner_sign,"player_transfer_child_max", 10000);

            $adminGroup = PartnerAdminGroup::where('id', $adminUser->group_id)->first();
            if ($adminUser && $adminGroup->name === '超级管理') {
                $max = partnerConfigure($adminUser->partner_sign,"player_transfer_max_super", 200000);
            }

            if ($amount > $max || $amount < $min) {
                return Help::returnApiJson("对不起, 理赔扣减额度范围{$min} - {$max}!", 0);
            }

            $needReview = partnerConfigure($adminUser->partner_sign,"finance_admin_transfer_need_review", 0);

            // 如果需要审核
            if ($needReview) {
                // 等待审核
                $config = [
                    'mode'      => $mode,
                    'type'      => $type,
                    'amount'    => $amount,
                    "desc"      => $reason,
                ];
                $typDetail = $type;

				// 检测是否需要审核
				$res = PartnerReviewFlow::where('partner_sign', $this->partnerSign)
					->where('type', 'system_transfer_add')
					->where('type_detail', $type)
					->first();

				$ress = PartnerReviewFlow::where('partner_sign', $this->partnerSign)
					->where('type', 'system_transfer_reduce')
					->where('type_detail', $type)
					->first();

				if ($res == null && $ress == null) {
					if ($mode === 'add') {
						$res = PartnerAdminActionReview::addReviewSelf($player, 'system_transfer_add', $typDetail, $config, $adminUser, $partnerSign);
						if ($res !== true) {
							return Help::returnApiJson($res, 0);
						}
						$result = $player->manualTransfer($mode, $type, $amount, $reason, $adminUser);
						if (false == $result) {
							return Help::returnApiJson('对不起,操作失败', 0);
						}
					} else {
						$res = PartnerAdminActionReview::addReviewSelf($player, 'system_transfer_reduce', $typDetail, $config, $adminUser, $partnerSign);
						if ($res !== true) {
							return Help::returnApiJson($res, 0);
						}
						$result = $player->manualTransfer($mode, $type, $amount, $reason, $adminUser);
						if (false == $result) {
							return Help::returnApiJson('对不起,操作失败', 0);
						}
					}
					return Help::returnApiJson('恭喜,操作已成功', 1);

				}

                if ($mode === 'add') {
                    $res = PartnerAdminActionReview::addReview($player, 'system_transfer_add', $typDetail, $config, $adminUser, $partnerSign);
                    if ($res !== true) {
                        return Help::returnApiJson($res, 0);
                    }
                } else {
                    $res = PartnerAdminActionReview::addReview($player, 'system_transfer_reduce', $typDetail, $config, $adminUser, $partnerSign);
                    if ($res !== true) {
                        return Help::returnApiJson($res, 0);
                    }
                }

                return Help::returnApiJson("恭喜, 操作已提交, 等待风控审核!", 1);
            }

            $ret = $player->manualTransfer($mode, $type, $amount, $reason, $adminUser);
            if (true === $ret) {
                return Help::returnApiJson("操作成功", 1);
            }

            return Help::returnApiJson($ret, 0);

        }
    }

    // 直接冻结玩家
    public function playerFrozen($id) {

        // 获取用户
        $player = Player::find($id);
        if (!$player) {
            return Help::returnApiJson("对不起, 无效的玩家id！", 0);
        }

        $frozenTypes = config("user.main.frozen_type");

        $action = request("action", 'process');
        if ('detail' == $action) {
            $data['username']               = $player->username;
            $data['frozen_type']            = $player->frozen_type;
            $data['frozen_name']            = $frozenTypes[$player->frozen_type];
            $data['frozen_options']         = $frozenTypes;

            return Help::returnApiJson("恭喜, 获取成功", 1, $data);
        } else {
            $frozen = request("frozen", 0);
            if (!array_key_exists($frozen, $frozenTypes)) {
                return Help::returnApiJson("对不起, 无效的冻结类型!!", 0);
            }

            // 操作描述
            $desc = request("desc", '');
            if (!$desc) {
                return Help::returnApiJson("对不起, 请输入操作描述!", 0);
            }

            // 直接冻结
            $type = request('type', 'frozen');
            // 管理员行为记录
            $data = Player::where('id', $id)->first(['frozen_type'])->toArray();
            if ($type == 'frozen') {
                Player::where('id', $player->id)->update(['frozen_type'=>$frozen, 'mark' => $desc]);
                PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'frozen_player', $data);
            } elseif ($type == 'frozenAll') {
                Player::where('rid','like','%'.$player->id.'%')->update(['frozen_type'=>$frozen, 'mark' => $desc]);
                PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'frozen__all_player', $data);
            }

            // 加入缓存 强制踢线
            PartnerCache::kickLine($player);

            return Help::returnApiJson("恭喜, 冻结成功!", 1);
        }
    }


    // 申请解除冻结 审核
    public function playerUnfrozen($id) {
        $adminUser = $this->partnerAdminUser;

        // 获取用户
        $player = Player::find($id);
        if (!$player) {
            return Help::returnApiJson("对不起, 无效的玩家id！", 0);
        }

        $partnerSign = $player->partner_sign;

        $frozenTypes = config("user.main.frozen_type");

        $action = request("action", 'process');
        if ('detail' == $action) {
            $data['username']               = $player->username;
            $data['frozen_type']            = $player->frozen_type;
            $data['frozen_name']            = $frozenTypes[$player->frozen_type];
            $data['frozen_options']         = $frozenTypes;

            return Help::returnApiJson("恭喜, 获取成功", 1, $data);
        } else {
            $frozen = $player->frozen_type;
            if (!array_key_exists($frozen, $frozenTypes)) {
                return Help::returnApiJson("对不起, 无效的冻结类型!!", 0);
            }

            // 操作描述
            $desc = request("desc", '');
            if (!$desc) {
                return Help::returnApiJson("对不起, 请输入操作描述!", 0);
            }

            $type = request('type', 'frozen');

            // 等待审核
            $config = [
                'mode' => $type,
                'type' => $frozen,
                "desc" => $desc,
            ];
            $typDetail = $frozen;

			// 是否需要审核
			if ($type == 'frozenAll') {
				$res = PartnerReviewFlow::where('partner_sign', $this->partnerSign)
					->where('type', 'frozenAll')
					->Where('type_detail', $frozen)
					->first();
				if ($res == null) {
					$ress = Player::where('rid','like','%'.$player->id.'%')->update(['frozen_type'=>0]);
					Player::where('id', $player->id)->update(['unfrozen' => $frozen]);
					if (true == $ress){
						return Help::returnApiJson('恭喜,解除冻结成功', 1);
					}
					$res = PartnerAdminActionReview::addReviewSelf($player, $type, $typDetail, $config, $adminUser, $partnerSign);
					if ($res !== true) {
						return Help::returnApiJson($res, 0);
					}
				}
			}else {
				$res = PartnerReviewFlow::where('partner_sign', $this->partnerSign)
					->where('type', 'frozen')
					->Where('type_detail', $frozen)
					->first();
				if ($res == null) {
					$ress = Player::where('id',$player->id)->update(['frozen_type'=>0]);
					Player::where('id', $player->id)->update(['unfrozen' => $frozen]);
					if (true == $ress){
						return Help::returnApiJson('恭喜,解除冻结成功', 1);
					}
					$res = PartnerAdminActionReview::addReviewSelf($player, $type, $typDetail, $config, $adminUser, $partnerSign);
					if ($res !== true) {
						return Help::returnApiJson($res, 0);
					}
				}
			}
            $res = PartnerAdminActionReview::addReview($player, $type, $typDetail, $config, $adminUser, $partnerSign);
            if ($res !== true) {
                return Help::returnApiJson($res, 0);
            }
            Player::where('id', $player->id)->update(['unfrozen' => $frozen]);

            return Help::returnApiJson("恭喜, 操作已提交, 等待风控审核!", 1);
        }
    }

    // 设置玩家状态
    public function playerStatus($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取用户
        $model = Player::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        // 是否有权限操作
        if ($model->partner_sign != $adminUser->partner_sign) {
            return Help::returnApiJson("对不起, 无效的操作(0x008！", 0);
        }

        $model->status = $model->status == 1 ? 0 : 1;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 开启转账
    public function allowedTransfer($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取用户
        $model = Player::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        if ($model->type == 3) {
            return Help::returnApiJson("该会员不支持转账", 0);
        }

        // 是否有权限操作
        if ($model->partner_sign != $adminUser->partner_sign) {
            return Help::returnApiJson("对不起, 无效的操作(0x008！", 0);
        }

        $model->allowed_transfer = $model->allowed_transfer == 1 ? 0 : 1;
        $model->save();

        // 管理员行为记录
        $data = Player::where('id', $id)->first(['status'])->toArray();
        PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'allowed_transfer', $data);

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 设置玩家日工资
    public function playerSetSalary($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        // 获取用户ID
        if (!$id) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        // 获取用户
        $model = Player::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        // 是否有权限操作
        if ($model->partner_sign != $partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 无效的操作(0x008！", 0);
        }

        $action = request('action', 'process');
        if ($action == 'option') {
            $data['parent']             = Player::find($model->parent_id);
            $data['salary_percentage']  = $model->salary_percentage > 0 ? $model->salary_percentage : 0;

            // 最大不能大于上级
            $data['max'] = partnerConfigure($model->partner_sign, "player_salary_rate_max", 10);
            if ($data['parent']) {
                $data['max'] = $data['max'] > $data['parent']->salary_percentage ? $data['parent']->salary_percentage : $data['max'];
            }

            // 最小 不能小于下级
            $data['min']    = partnerConfigure($model->partner_sign, "player_salary_rate_min", 10);
            $maxChildRate   = Player::where("parent_id", $model->id)->max("salary_percentage");
            if ($maxChildRate) {
                $data['min'] = $data['min'] < $maxChildRate ? $maxChildRate : $data['min'];
            }

            return Help::returnApiJson("恭喜,设置日工资成功！", 1, $data);
        }

        $rate = request('salary_percentage', 0);
        if ($rate != floatval($rate) || $rate < 0) {
            return Help::returnApiJson("对不起, 日工资比例不正确", 0);
        }

        $res = $model->setSalaryRate($rate, 'admin', $partnerAdminUser);
        if ($res !== true){
            return Help::returnApiJson($res, 0);
        };

        // 管理员行为记录
        $data = Player::where('id', $id)->first(['salary_percentage'])->toArray();
        PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'player_set_salary', $data);

        return Help::returnApiJson("恭喜,设置日工资成功！", 1);
    }

    // 设置玩家分红
    public function playerSetBonus($id)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        // 获取用户ID
        if (!$id) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        // 获取用户
        $model = Player::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        // 是否有权限操作
        if ($model->partner_sign != $partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 无效的操作(0x008！", 0);
        }

        $action = request('action', 'process');
        if ($action == 'option') {
            $data['parent'] = Player::find($model->parent_id);
            $data['bonus_percentage']  = $model->bonus_percentage > 0 ? $model->bonus_percentage : 0;
            $data['max']    = partnerConfigure($model->partner_sign, "player_bonus_rate_max", 10);

            // 最大
            if ($data['parent']) {
                $data['max'] = $data['max'] > $data['parent']->bonus_percentage ? $data['parent']->bonus_percentage : $data['max'];
            }

            // 最小
            $data['min']    = partnerConfigure($model->partner_sign, "player_bonus_rate_min", 0);
            $maxChildRate   = Player::where("parent_id", $model->id)->max("bonus_percentage");
            if ($maxChildRate) {
                $data['min'] = $data['min'] < $maxChildRate ? $maxChildRate : $data['min'];
            }

            return Help::returnApiJson("恭喜,设置日工资成功！", 1, $data);
        }

        $rate = request('bonus_percentage', 0);
        if ($rate != floatval($rate) || $rate < 0) {
            return Help::returnApiJson("对不起, 分红比列不正确", 0);
        }

        $res = $model->setBonusRate($rate, 'admin', $partnerAdminUser);
        if ($res !== true){
            return Help::returnApiJson($res, 0);
        }

        // 管理员行为记录
        $data = Player::where('id', $id)->first(['bonus_percentage'])->toArray();
        PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'player_set_bonus', $data);

        return Help::returnApiJson("恭喜, 设置分红成功！", 1);
    }

    // 设置奖金组
    public function prizeGroupSet()
    {
        $id = request('id', 0);

        $player = Player::find($id);
        if(!$player) {
            return Help::returnApiJson('用户不存在!', 0);
        }

        $parent = Player::where('id' ,$player->parent_id)->first();

        // 奖金组格式
        $prize_group = request('prize_group','');
        $prize_group = (int)$prize_group;
        if(empty(trim($prize_group)) || !is_int($prize_group)) {
            return Help::returnApiJson('奖金组格式不正确!', 0);
        }

        // 范围
        $minPrizeGroup      = partnerConfigure($parent->partner_sign, 'player_register_min_prize_group', 1800);
        $childMaxPrizeGroup = Player::where("parent_id", $player->id)->max('prize_group');

        $minPrizeGroup      = $minPrizeGroup < $childMaxPrizeGroup ? $childMaxPrizeGroup : $minPrizeGroup;
        if($prize_group > $parent->prize_group || $prize_group < $minPrizeGroup) {
            return Help::returnApiJson('奖金组只能在!' . $minPrizeGroup . '至' . $parent->prize_group. '之间', 0);
        }

        $player->prize_group = $prize_group;
        $player->save();

        // 管理员行为记录
        $data = Player::where('id', $id)->first(['prize_group'])->toArray();
        PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'prize_group_set', $data);

        return Help::returnApiJson('奖金组设置成功!', 1);
    }

    // 获取玩家ip日志
    public function userIpLogList()
    {
        $c          = request()->all();
        $c["partner_sign"]  = $this->partnerSign;
        $data       = PlayerIp::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取玩家ip详情
    public function userIp($userId)
    {
        if (!$userId) {
            return Help::returnApiJson("对不起, 无效id！", 0);
        }

        $data = PlayerIp::getIpListByUserId($userId);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取玩家 访问日志
    public function userPlayerLogList()
    {
        $c                  = request()->all();
        $c["partner_sign"]  = $this->partnerSign;
        $data               = PlayerLog::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 获取玩家访问日志详情
    public function userPlayerDetail($userId)
    {
        if (!$userId) {
            return Help::returnApiJson("对不起, 无效id！", 0);
        }

        $data = PlayerLog::getIpListByUserId($userId);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** =====================$$$$$$$$$$$$$$ 玩家银行卡 $$$$$$$$$$$$$$$$======================= */

    /**
     * 银行卡
     * @return \Illuminate\Http\JsonResponse
     */
    public function playerCardList()
    {
        $c                  = request()->all();
        $c["partner_sign"]  = $this->partnerSign;

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
                "add_time"          => date("m-d H:i", strtotime($item->created_at)),
                "update_time"       => date("m-d H:i", strtotime($item->updated_at)),
            ];
        }

        $data['data']               = $_data;
        $data['bank_list']          = config("web.banks");
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加银行卡
    public function playerCardAdd($id)
    {

        $adminUser = $this->partnerAdminUser;

        $model  = Player::find($id);

        if (!$model) {
            return Help::returnApiJson("对不起, 玩家不存在！", 0);
        }

        $data   = request()->all();

        $card   = new PlayerCard();
        $cardNmb = PlayerCard::where('card_number', $data['card_number'])->where('status', 1)->first();

        if (isset($cardNmb)){
            return Help::returnApiJson("对不起, 银行卡已经存在！", 0);
        }

        $cardOld = PlayerCard::where('user_id', $model->id)->where('status', 1)->first();
        if ($cardOld) {
            $ownerName = $cardOld->owner_name;
            if (isset($data['owner_name']) && $data['owner_name'] && $data['owner_name'] != $ownerName) {
                return Help::returnApiJson('对不起,持卡人姓名有误！', 0);
            }
        }

        // 绑定且激活的卡不能超过四张
        $cardNumb = PlayerCard::where('user_id', $id)->where('status', 1)->get();

        if ($cardNumb && count($cardNumb) > 4) {
            return Help::returnApiJson("对不起, 最多只能绑定四张银行卡！", 0);
        }

        $res    = $card->saveItem($data, $model, $adminUser->id);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加银行卡成功！", 1);
    }

    // 修改银行卡
    public function editPlayerCard($id)
    {
        $adminUser = $this->partnerAdminUser;

        if (!$id) {
            return Help::returnApiJson("对不起, 目标对象不存在！", 0);
        }

        // 资金密码
		$codeOne = base64_decode(trim(request("fund_password")));
		$codeTwo = substr($codeOne, 0, -4);
		$final = base64_decode($codeTwo);
		$password = substr($final, 5, 37);
        if (!$password || !Hash::check($password, $adminUser->fund_password)) {
            return Help::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        $model  = PlayerCard::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 银行卡信息错误！", 0);
        }

        $data   = request()->all();
        $number = request('card_number');
        $numberOld = PlayerCard::where('card_number', $number)->where('status', 1)->where('id', '!=' , $id)->first();
        if ($numberOld) {
            return Help::returnApiJson("对不起, 该卡号已经绑定！", 0);
        }

        $cardOld = PlayerCard::where('user_id', $model->id)->where('status', 1)->first();
        if ($cardOld) {
            $ownerName = $cardOld->owner_name;
            if (isset($data['owner_name']) && $data['owner_name'] && $data['owner_name'] != $ownerName) {
                return Help::returnApiJson('对不起,持卡人姓名有误！', 0);
            }
        }

        $res = $model->editItem($data, $model);
        if (true !== $res) {
            return Help::returnApiJson($res, 0);
        }


        // 管理员行为记录
        $data = Player::where('id', $id)
            ->first([
                'bank_sign',
                'bank_name',
                'owner_name',
                'card_number',
                'province_id',
                'city_id',
                'branch'
            ])->toArray();
        PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'edit_player_card', $data);

        return Help::returnApiJson("恭喜, 修改成功！", 1);
    }


    // 禁用或者启用银行卡
    public function cardStatus($id)
    {
        $adminUser = $this->partnerAdminUser;

        if (!$id) {
            return Help::returnApiJson("对不起, 银行卡信息错误！", 0);
        }

        $model = PlayerCard::where('id', $id)->first();

        if (!$model) {
            return Help::returnApiJson("对不起, 无效的银行卡id！", 0);
        }

        // 逻辑删除卡号
        PlayerCard::where('id', $id)->update(['status' => 0]);

        // 行为
        PartnerAdminBehavior::saveItem($adminUser, "cardStatus", "禁用或者启用");
        return Help::returnApiJson("恭喜, 银行卡删除成功！", 1);
    }


    // 获取卡详情
    public function playerCardDetail($id)
    {

        $data['card']  = [];

        // 获取用户银行卡
        $card = PlayerCard::find($id);
        if ($card) {
            $data['card']   = $card;
        }

        $data['bank_options']       = config("web.banks");

        $province = config("web.province");

        $options = [];
        foreach ($province as $pid => $item) {
            $_tmp = [
                'value'     => $pid,
                'label'     => $item['name'],
                'children'  => []
            ];

            if ($item['city']) {
                foreach ($item['city'] as $cid => $cName) {
                    $_tmp['children'][] = [
                        'value'     => $cid,
                        'label'     => $cName,
                        'isLeaf'    => true
                    ];
                }
            }

            $options[] = $_tmp;
        }

        $data['province_options']   = $options;

        return Help::returnApiJson("恭喜, 获取银行卡详情成功！", 1, $data);
    }

    /** ==================================== 日工资配置　================================== */

    // 日工资配置
    public function salaryConfigList()
    {
        $c                  = request()->all();
        $c["partner_sign"]  = $this->partnerSign;
        
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


    // 日工资契约详情
    public function salaryConfigDetail()
    {
        $c          = request()->all();
        $c["partner_sign"]  = $this->partnerSign;

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

    // 分红配置
    public function dividendConfigList()
    {
        $c          = request()->all();
        $c["partner_sign"]  = $this->partnerSign;

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
        $c["partner_sign"]  = $this->partnerSign;
        
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

    /** ==================================== 审核　================================== */
    // 审核列表
    public function reviewList()
    {
		$adminUser = $this->partnerAdminUser;
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}
        $c = request()->all();
        $c["partner_sign"] = $this->partnerSign;

        // 获取数据
        $data = PartnerAdminActionReview::getList($c);
        

        $allTypes   = config("user.main.frozen_type");
        $_data = [];
        foreach ($data["data"] as $item) {
            $detail = unserialize($item->process_config);
                $temp_data= [
                    "id"               => $item->id,
                    "player_username"  => $item->player_username,
                    "type"             => $item->type,
                    "type_detail"      => $item->type_detail,
					'desc'			   => $detail['desc'],
                    "process_amount"   => $detail['amount'] ?? '',
                    "process_desc"     => $item->process_desc,
                    "request_ip"       => $item->request_ip,
                    "review_ip"        => $item->review_ip,
                    "review_fail_reason" => $item->review_fail_reason,
                    "request_time"       => date("Y-m-d H:i:s", $item->request_time),
					"review_time"        => date("Y-m-d H:i:s", $item->review_time),
					"handle_admin_one"   => $item->handle_admin_one,
					"handle_admin_two"   => $item->handle_admin_two,
					"handle_admin_three" => $item->handle_admin_three,
					"request_admin_name" => $item->request_admin_name,
                    "review_admin_name"  => $item->review_admin_name,
                    "request_admin_id"   => $item->request_admin_id,
                    "status" => $item->status,
                ];
            //------------------------------------
			if (isset($item->type) && $item->type == 'login_password') {
				$item->type = 'password';
			}
            $partnerReviewFlows = PartnerReviewFlow::where('partner_sign',$this->partnerSign)
				->where('type',$item->type)
				->where('type_detail',$item->type_detail)
				->first();
			if ($partnerReviewFlows) {
				$groups = explode('|', $partnerReviewFlows->users);
				$arr=['handle_admin_one','handle_admin_two','handle_admin_three'];

				foreach ($groups as $key => $value) {
					if($temp_data[$arr[$key]] ==''){
						$user =explode(',',$groups[$key]);
						if(in_array($adminUser->username, $user)){
							$temp_data['enable']=1;
							break;
						} else {
							$temp_data['enable']=0;
						}
					}
				}
				//------------------------------------
				if (!isset($temp_data['enable'])) {
					$temp_data['enable']=0;
				}
				$_data[]=$temp_data;
			} else {
				$_data[] = $temp_data;
			}
        }

        $data['data'] = $_data;
        $data['type_options'] = $allTypes;
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }


    // 审核详情
    public function reviewDetail($id)
    {
        $adminUser = $this->partnerAdminUser;
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['detail']  = [];

        // 获取审核id
        $detail = PartnerAdminActionReview::find($id);
        if ($detail) {
            $allTypes = config("admin.main.review_type");
            $detail->type = $allTypes[$detail->type]['name'];
            $data = [
                'id'                 => $detail->id,
                'player_id'          => $detail->player_id,
                'player_username'    => $detail->player_username,
                'type'               => $detail->type,
                "process_config"     => unserialize($detail->process_config),
                'process_desc'       => $detail->process_desc,
                'request_ip'         => $detail->request_ip,
                'review_ip'          => $detail->review_ip,
                'request_time'       => $detail->request_time,
                'review_time'        => $detail->review_time,
                'request_admin_id'   => $detail->request_admin_id,
                'request_admin_name' => $detail->request_admin_name,
                'review_admin_id'    => $detail->review_admin_id,
                'review_admin_name'  => $detail->review_admin_name,
                'review_fail_reason' => $detail->review_fail_reason,
                'status'             => $detail->status,
                'created_at'         => $detail->created_at,
                'updated_at'         => $detail->updated_at,
            ];
        }

        return Help::returnApiJson("恭喜, 获取审核详情成功！", 1, $data);
    }


    // 处理审核
    public function reviewProcess($id)
    {
        $adminUser = $this->partnerAdminUser;
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 请先登录！", 0);
        }
        // 获取详情
        $detail = PartnerAdminActionReview::find($id);
        if (!$detail) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }
        // 审核通过后 无需再审核
        if ($detail->status == 2 || $detail->status == -2 || $detail->status == -3) {
            return Help::returnApiJson("审核已经处理！,请勿重新审核!", 0);
        }

        if (isset($detail->type) && $detail->type == 'login_password') {
			$detail->type = 'password';
		}
        // 获取管理员层级 和权限
        $handleAdmin = PartnerReviewFlow::where('type', $detail->type)
			->where('type_detail', $detail->type_detail)
			->first();
        if (!$handleAdmin) {
        	return Help::returnApiJson('审核管理组权限未明确', 0);
		}

        $flag =false;
        $index=0;
		$groups = explode('|', $handleAdmin->users);
		foreach ($groups as $key => $group){
			$users = explode(',', $group);
			if(in_array($adminUser->username,$users)){
				$flag  = true;
				$index = $key;
			}
		}
		if ($flag == true) {
			if ($index == count($groups) - 1) {
				PartnerAdminActionReview::where('id', $id)->update(['handle_admin_three' => $adminUser->username]);
			} else {
				if ($adminUser->username == $detail->handle_admin_one || $adminUser->username == $detail->handle_admin_two || $adminUser->username == $detail->handle_admin_three) {
					return Help::returnApiJson('您已审阅,请勿重复操作',0);
				}
				if ($index == 0) {
					PartnerAdminActionReview::where('id', $id)->update(['handle_admin_one' => $adminUser->username]);
					return Help::returnApiJson('已审阅,待上一级管理员确认', 1);
				} else if ($index == 1) {
					PartnerAdminActionReview::where('id', $id)->update(['handle_admin_two' => $adminUser->username]);
					return Help::returnApiJson('已审阅,待上一级管理员确认', 1);
				}
			}
		} else {
			return Help::returnApiJson('对不起,您无权限操作', 0);
		}


		if (isset($detail->type) && $detail->type == 'password') {
			$detail->type = 'login_password';
		}

        // 资金密码
        $codeOne = base64_decode(trim(request("fund_password")));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $password = substr($final, 5, 37);
        if (!$password || !Hash::check($password, $adminUser->fund_password)) {
            return Help::returnApiJson('对不起, 资金密码不正确!', 0);
        }

        $mode = trim(request("mode"));
        $desc = request('process_desc','');
        if ($mode === 'fail') {
            $fail = request('review_fail_reason');
            if (!$fail) {
                return Help::returnApiJson('对不起,　请输入拒绝原因', 0);
            }
            $detail->review_admin_id      = $adminUser->id;
            $detail->review_admin_name    = $adminUser->username;
            $detail->process_desc         = $desc;
            $detail->review_fail_reason   = $fail;
            $detail->review_ip            = real_ip();
            $detail->review_time          = time();
            $detail->status = -2;
            $detail->save();
            return Help::returnApiJson("审核已经拒绝！", 1, []);
        }

        // 处理
        $res = $detail->process($adminUser, $desc);
        if (true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 处理成功！", 1, []);
    }

    public function transferFrom($id)
    {
        $player = Player::find($id);
        if (!$player) {
            return Help::returnApiJson("对不起, 无效的玩家id！", 0);
        }

        // 用户是否绑定银行卡
        $playerCard = PlayerCard::where('user_id', $player->id)->first();
        if (!$playerCard) {
            return Help::returnApiJson("对不起, 用户未绑定银行卡！", 0);
        }

        $c      = request()->all();
        $playerM = new Player();
        $transferStatus = $playerM->transferFrom($c, $player);
        if ($transferStatus) {
            return Help::returnApiJson("转账成功", 1);
        }
        return Help::returnApiJson($playerM->errMsg, 0);
    }


    public function getMessageList()
    {
        $user = auth()->guard('api')->user();
        $c['partner_sign'] = $user->partner_sign;
        $c['user_type']    = $user->type;
        $data       = PartnerMessage::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
