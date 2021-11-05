<?php

namespace App\Models\Admin;

use App\Lib\T;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Tom 2019
 * Class AdminUser
 * @package App\Models\Admin
 */
class AdminUser extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    public $rules = [
        'username'          => 'required|min:4|max:32',
        'email'             => 'required|email',
        'password'          => 'required',
        'fund_password'     => 'required',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'admin_users';

    /** ============== JWT 实现 ================ */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    // 后端用户列表
    public function getAdminSel($admin_id){
        if($admin_id){
            $res = self::select('id', 'username')->where("id", $admin_id)->first();
            return $res['username'];
        }
        return self::select('id', 'username')->where("status", 1)->get();
    }

    /**
     * 获取与用户关联的电话号码
     */
    public function group()
    {
        return AdminGroup::find($this->group_id);
    }

    static function getAdminUserList($condition, $group, $pageSize = 20) {
        $query = self::select(
            DB::raw('admin_users.*'),
            DB::raw('admin_groups.name as group_name')
        )->leftJoin('admin_groups', 'admin_users.group_id', '=', 'admin_groups.id')
            ->where("admin_groups.rid", "like" , "{$group->rid}%")
            ->orderBy('id', 'desc');


        $currentPage    = isset($condition['page_index']) ? intval($condition['page_index']) : 1;
        $pageSize       = isset($condition['page_size']) ? intval($condition['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 获得直接下级组
    public function getChildGroup() {
        if ($this->id == 1) {
            $groups = AdminGroup::where("pid", 1)->get();
        } else {
            $groups = AdminGroup::where("pid", $this->group_id)->get();
        }

        $data = [];
        if ($groups) {
            foreach($groups as $g) {
                $data[$g->id] = $g->name;
            }
        }

        return $data;
    }

    // 活动所有下级
    public function getChildGroupAll() {
        $groups = AdminGroup::where("rid", 'like', $this->group_id  ."|%")->get();
        $_l = substr_count($this->group_id, '|');
        $data = [];
        if ($groups) {
            foreach($groups as $g) {
                $_k = substr_count($g->rid, '|');
                $_i = $_k - $_l - 1;
                $str = "";
                if ($_i > 0) {
                    for($j = 0; $j < $_i; $j ++) {
                        $str .= "&nbsp;&nbsp;&nbsp;";
                    }
                    for($j = 0; $j < $_i; $j ++) {
                        $str .= "--";
                    }
                }
                $data[$g->id] = $str . $g->name;
            }
        }

        return $data;
    }

    public function saveItem($data) {

		if (empty($data['id'])) {
			$adminM = $this;
			$from = 6;
			$msg = '新增管理员:';
		} else {
			$adminM = $this->find($data['id']);
			$from = 7;
			$msg = '被编辑管理员:';
		}

		$validator  = Validator::make($data, $this->rules);

		if ($validator->fails()) {
			return $validator->errors()->first();
		}

		$adminUser      = auth()->guard('admin_api')->user();

		$adminM->username       = $data['username'];
		$adminM->email          = $data['email'];
		$adminM->group_id       = $data['group_id'];
		$adminM->password       = bcrypt($data['password']);
		$adminM->fund_password  = bcrypt($data['fund_password']);
		$adminM->status         = 1;
		$adminM->add_admin_id   = $adminUser->id;
		$adminM->register_ip    = real_ip();
		$adminM->save();

		// 添加telegram提现推送消息
		$fromConfig = config("admin.main.admin_behavior_type");

		$text  = "<b>用户{$adminUser -> username}(id:{$adminUser->id}),在" . date('Y-m-d H:i:s', time()) . ",使用了 {$fromConfig[$from]} 功能," . $msg . $adminM->username . '</b>';
		telegramSend('send_admin_behavior',$text);
		return true;
    }

    /**
     * 密码检测
     * @param $password
     * @return bool|string
     */
    static function checkPassword($password) {
        if (!preg_match("/^[0-9a-zA-Z_]{6,16}$/i", $password) || preg_match("/^[0-9]+$/", $password) || preg_match("/^[a-zA-Z]+$/i", $password) || preg_match("/(.)\\1{2,}/i", $password)) {
            return "对不起, 密码输入格式不正确!";
        } else {
            return true;
        }
    }

    /**
     * 密码检测
     * @param $password
     * @return bool|string
     */
    static function checkFundPassword($password) {
        if (!preg_match("/^[0-9a-zA-Z]{6,16}$/", $password)) {
            return "对不起, 资金密码格式不正确!";
        } else {
            return true;
        }
    }

    // 获取所有后台管理用户
    static function getAdminUserOptions() {
        $users = self::all();
        $options = [];
        foreach ($users as $user) {
            $options[$user->id] = $user->username;
        }
        return $options;
    }
}
