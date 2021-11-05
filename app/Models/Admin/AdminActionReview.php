<?php

namespace App\Models\Admin;

use App\Models\Partner\PartnerConfigure;
use App\Models\Partner\PartnerDomain;
use Illuminate\Database\Eloquent\Model;
use App\Models\Partner\PartnerAdminUser;
use Illuminate\Support\Facades\Hash;

class AdminActionReview extends Model
{
    protected $table = 'admin_action_review';

    static $status = [
        0 => "待审核",
        1 => "审核中",
        2 => "审核成功",
        -2 => "人工失败",
        -3 => "条件失败",
    ];

	/**
     * 获取日志列表
     * @param $c
     * @return mixed
     */
    static function getList($c)
    {
        $query = self::orderBy('id', 'DESC');

        // 用户名
        if (isset($c['type']) && $c['type'] && $c['type'] != "all") {
            $query->where('type', $c['type']);
        }

        // 路由
        if (isset($c['review_admin_name']) && $c['review_admin_name']) {
            $query->where('review_admin_name', $c['review_admin_name']);
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $items = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 处理
    public function process($adminUser, $desc)
    {
        $allTypes = config("admin.main.review_type", []);
        // 类型是否存在
        if (!array_key_exists($this->type, $allTypes)) {
            return "对不起, 无效的审核类型!";
        }


        if ($this->type == 'login_password' || $this->type == 'fund_password') {
			$admin = PartnerAdminUser::find($this->partner_admin_id);
			if (!$admin) {
				return "对不起, 不存在的玩家!";
			}
		}

        $status = 2;
        $failReason = "";
        switch ($this->type) {
            case 'login_password':
            	$password = Hash::make($this->password);
				$res = PartnerAdminUser::where('id', $admin->id)->update(['password' => $password]);
				if ($res != true) {
					return '对不起,修改密码失败';
				}
                break;
            case 'fund_password':
				$fundPassword = Hash::make($this->password);
				$res = PartnerAdminUser::where('id', $admin->id)->update(['password' => $fundPassword]);
				if ($res != true) {
					return '对不起,修改密码失败';
				}
                break;
			case 'player_register_min_prize_group':
				$partnerConfig = PartnerConfigure::find($this->config_id);
				$partnerConfig->id  = $this->config_id;
				$partnerConfig->pid  = $this->config_pid;
				$partnerConfig->name = $this->config_name;
				$partnerConfig->sign = $this->config_sign;

				$partnerConfig->value        = $this->config_value;
				$partnerConfig->description  = $this->config_description;
				$partnerConfig->can_show     = $this->config_partner_show;
				$partnerConfig->can_edit     = $this->config_partner_edit;
				$partnerConfig->partner_sign = $this->config_partner_sign;

				$partnerConfig->save();
				break;
            case 'partner_domain_del':
                $ids = explode(',', $this->value);
                $res = PartnerDomain::whereIn('id', $ids)->delete();
                if ($res != true) {
                    return '对不起,域名删除失败';
                }
                break;
            default:
                $failReason = "对不起, 无效的处理类型!";
                $status = -3;
        }

        // 处理
		$this->review_admin_id    = $adminUser->id;
		$this->review_admin_name  = $adminUser->username;
		$this->review_ip          = real_ip();
		$this->review_time        = time();
		$this->status             = $status;
		$this->review_fail_reason = $failReason;
		$this->process_desc		  = $desc;
        $this->save();
        return $status == 2 ? true : $failReason;
    }

    // 添加
    static function addReview($c, $type, $adminUser, $partner = null)
    {

        $allTypes = config("admin.main.review_type");
        // 类型是否存在
        if (!array_key_exists($type, $allTypes)) {
            return "对不起, 无效的审核类型!";
        }

        if (isset($c['id']) && $c['id']) {
            $desc = $c['description'] ?? '';
            $partners = $c['partner_sign'] ?? '';
            $show = $c['partner_show'] ?? '';
            $edit = $c['partner_show'] ?? '';
            $config =
                "配置id:".$c['id'].",".
                "配置父id:".$c['pid'].",".
                "标示:".$c['sign'].",".
                "值:".$c['value'].",".
                "描述:".$desc.",".
                "名称:".$c['name'].",".
                "商户标示:".$partners.",".
                "商户是否可展示:".$show.",".
                "商户是否可编辑:".$edit;
        } else {
            $adminID = $partner->id ?? '';
            $adminName = $partner->username ?? '';

            if (isset($c['config'])) {
                $value = $c['config'];
            } else {
                $value     = $c['values'] ?? '';
            }

            $config = "管理员ID:".$adminID.",". "管理员名:".$adminName.",". "内容:".$value;
        }

        $query = new self();
        // 添加修改配置 相关
		$query->config_id    = $c['id'] ?? '';
		$query->config_pid   = $c['pid'] ?? '';
		$query->config_name  = $c['name'] ?? '';
		$query->config_sign  = $c['sign'] ?? '';
		$query->config_value = $c['value'] ?? '';
		$query->config_description  = $c['description'] ?? '';
		$query->config_partner_show = $c['partner_show'] ?? '';
		$query->config_partner_edit = $c['partner_edit'] ?? '';
		$query->config_is_edit_pid  = $c['is_edit_pid'] ?? '';
		$query->config_partner_sign = $c['partner_sign'] ?? '';

		// 商户管理员密码修改相关
		$query->partner_admin_id    = $partner->id ?? '';
		$query->partner_admin_name  = $partner->username ?? '';
		$query->value               = $c['values'] ?? '';

		// 通用
        $query->request_desc       = $c['request_desc'];
        $query->process_config     = $config;
		$query->type         = $type;
		$query->request_ip   = real_ip();
		$query->request_time = time();

		$query->request_admin_id   = $adminUser->id;
		$query->request_admin_name = $adminUser->username;

        $query->save();
        return true;
    }

    /*
     * 用於總後台新增審核
     * 審核 ： 修改密碼
     * */
    static function adminAddReview($partner, $type, $config, $adminUser)
    {
        $allTypes = config("admin.main.review_type", []);
        // 类型是否存在
        if (!array_key_exists($type, $allTypes)) {
            return "对不起, 无效的审核类型!";
        }

    }

}
