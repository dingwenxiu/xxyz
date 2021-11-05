<?php

namespace App\Models\Partner;

use App\Lib\Help;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Models\Player\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * 商户管理员动作审核
 * Class PartnerAdminActionReview
 * @package App\Models\Partner
 */
class PartnerAdminActionReview extends Model
{
    protected $table = 'partner_admin_action_review';

    static $status = [
        0   => "待审核",
        1   => "审核中",
        2   => "审核成功",
        -2  => "审核拒绝",
        -3  => "审核失败",
    ];

    /**
     * 获取日志列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
        $query = self::orderBy('id', 'DESC');

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != "all") {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 类型搜索  admin_transfer_to_player 玩家理赔 admin_reduce_from_player 玩家扣减 fund_password 资金密码  login_password 登录密码 froen
        if (isset($c['type']) && $c['type'] && $c['type'] != "all") {
            $query->where('type', $c['type']);
        }

        // 类型详情
        if (isset($c['type_detail']) && $c['type_detail']) {
            $query->where('type_detail', $c['type_detail']);
        }

        // 用户名
        if (isset($c['player_username']) && $c['player_username']) {
            $query->where('player_username', $c['player_username']);
        }

        // 审核管理员
        if (isset($c['review_admin_name']) && $c['review_admin_name']) {
            $query->where('review_admin_name', $c['review_admin_name']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 处理
    public function process($adminUser, $desc) {
        $allTypes = config("user.main.review_type");

        // 类型是否存在
        if (!array_key_exists($this->type, $allTypes)) {
            return "对不起, 无效的审核类型!";
        }

        // 玩家
        $player = Player::find($this->player_id);
        if (!$player) {
            return "对不起, 不存在的玩家!";
        }
        $config = unserialize($this->process_config);
        $status         = 2;
        $failReason     = "";
        switch($this->type) {
            case 'login_password':
                $password = $config['new_password'];
                $passwordHash = Hash::make($password);
                Player::where('id', $this->player_id)->update(['password' => $passwordHash]);

                // 加入缓存 强制踢线
                ConfigureCache::put($player->partner_sign.'_'.$player->username.'_password',true);

                break;
            case 'fund_password':
                $password = $config['new_password'];
                $passwordHash = Hash::make($password);
                Player::where('id', $this->player_id)->update(['fund_password' => $passwordHash]);

                // 加入缓存 强制踢线
                ConfigureCache::put($player->partner_sign.'_'.$player->username.'_password',true);

                break;
            case 'system_transfer_add':
            case 'system_transfer_reduce':
                $_adminUser = PartnerAdminUser::find($this->request_admin_id);
                $res =  $player->manualTransfer($config['mode'], $config['type'], $config['amount'], $config['desc'], $_adminUser);
                if (true !== $res) {
                    $failReason = $res;
                    $status     = -3;
                }
                break;
            case 'frozen':
                Player::where('id', $player->id)->update(['frozen_type'=> 0]);
                break;
            case 'frozenAll':
                Player::where('rid','like','%'.$player->id.'%')->update(['frozen_type'=>0]);
                break;
            default:
                $failReason = "对不起, 无效的处理类型!";
                $status     = -3;
        }

        // 处理
        $this->review_admin_id      = $adminUser->id;
        $this->review_admin_name    = $adminUser->username;
        $this->review_ip            = real_ip();
        $this->review_time          = time();
        $this->status               = $status;
        $this->process_desc		    = $desc;
        $this->review_fail_reason   = $failReason;
        $this->save();
        return $status == 2 ? true : $failReason;
    }

    // 添加
    static function addReview($user,  $type, $typDetail, $config, $adminUser, $partnerSign) {

        $query = new self();
        $query->player_id           = $user->id;
        $query->player_username     = $user->username;
        $query->process_config      = serialize($config);
        $query->type                = $type;
        $query->type_detail         = $typDetail;
        $query->request_ip          = real_ip();
        $query->request_time        = time();

        $query->request_admin_id      = $adminUser->id;
        $query->request_admin_name    = $adminUser->username;
        $query->partner_sign          = $partnerSign;

        $query->save();
        return true;
    }

    // 自己审核自己
	static function addReviewSelf($user,  $type, $typDetail, $config, $adminUser, $partnerSign) {

		$query = new self();
		$query->player_id           = $user->id;
		$query->player_username     = $user->username;
		$query->process_config      = serialize($config);
		$query->type                = $type;
		$query->type_detail         = $typDetail;
		$query->request_ip          = real_ip();
		$query->request_time        = time();

		$query->review_admin_name   = $adminUser->username;
		$query->process_desc        = '自审';
		$query->review_ip           = real_ip();
		$query->review_time	        = $query->request_time;
		$query->handle_admin_three  = $adminUser->username;
		$query->status              = 2;

		$query->request_admin_id      = $adminUser->id;
		$query->request_admin_name    = $adminUser->username;
		$query->partner_sign          = $partnerSign;

		$query->save();
		return true;
	}
}
