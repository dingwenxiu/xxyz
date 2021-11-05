<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartnerAdminGroup extends Model
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'partner_admin_groups';

    public $rules = [
        'name'              => 'required|min:2|max:32',
        'remark'            => 'required|min:2|max:128',
        'partner_sign'      => 'required|exists:partners,sign',
    ];

    public $msg = [
        'name.required'         => '分组名称必须填写',
        'name.min'              => '分组名称长度必须大于1',
        'name.max'              => '分组名称长度必须小于33',
        'partner_sign.exists'   => '商户标志不正确',
        'partner_sign.required' => '商户标志必须填写',
    ];

    /**
     * 后去用户可以配置的下级
     * @param string $c
     * @param int $pageSize
     * @return array
     */
    static function getAdminGroupList($c, $pageSize = 20) {


        $query = '';

        if(isset($c['partner_sign']))
        {
            $query = self::where(['partner_sign'=>$c['partner_sign']])->orderBy('id', 'DESC');
        }
        else
        {
            $query = self::orderBy('id', 'DESC');
        }

        // 是否是超管 不是超管 只查看当前组
        if (isset($c['group_name']) && !empty($c['group_name'])) {
            $query = self::where('id', $c['id'])->orderBy('id', 'DESC');
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();
        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    public function adminGroupDel($id,$partnerAdminUser)
    {

        $partnerAdminUsers = PartnerAdminUser::where('group_id',$id)->pluck('id')->toArray();
        $superAdmin        = self::where(['level'=>1,'partner_sign'=>$partnerAdminUser->partner_sign])->first();

        if($partnerAdminUsers)
        {
            PartnerAdminUser::whereIn('id',$partnerAdminUsers)->update(['group_id'=>$superAdmin->id]);
        }

        PartnerAdminGroupUser::where('id',$id)->delete();
        
        return true;
    }

    /**
     * 创建商户的时候　初始化　管理组
     * @param $partnerSign
     * @param $adminUser
     * @return PartnerAdminGroup
     */
    static function initSuperGroup($partnerSign, $adminUser = null) {
        $query = new self();
        $query->name             = "超级管理";
        $query->partner_sign     = $partnerSign;
        $query->remark           = "预设－超级管理";
        $query->acl              = '*';
        $query->level            = 1;
        $query->add_admin_id     = $adminUser ? $adminUser->id : 99999;
        $query->status           = 1;
        $query->save();

        return $query;
    }

    /**
     * 添加一个管理员到制定的组上
     * @param $email
     * @param $password
     * @param $fundPassword
     * @param null $adminUser
     * @return bool
     */
    public function addSuperAdminUser($email, $password, $fundPassword, $adminUser = null) {
        $data = [
            'email'         => $email,
            'username'      => strtolower($this->partner_sign) . "_super",
            'password'      => $password,
            'group_id'      => $this->id,
            'fund_password' => $fundPassword,
            'partner_sign'  => $this->partner_sign,
        ];

        $model = new PartnerAdminUser();
        $partnerAdminUser = $model->saveItem($data, $adminUser);
        if (!is_object($partnerAdminUser)) {
            return $partnerAdminUser;
        }

        return $partnerAdminUser;
    }

    /**
     * 获取商户组
     * @param $partnerAdminId
     * @return mixed
     */
    static function getGroups($partnerAdminId) {
        $query = PartnerAdminGroupUser::select(
                DB::raw('partner_admin_groups.*')
            )->leftJoin('partner_admin_groups', 'partner_admin_groups.id', '=', 'partner_admin_group_users.group_id')
                ->where("partner_admin_group_users.partner_admin_id", $partnerAdminId);
        return $query->get();
    }

    // 获取组包含的权限
    public function getAcl() {
        $acl = $this->acl;

        if (!$acl) {
            return [];
        }

        if ($acl == '*') {
            $menus = PartnerMenu::where('partner_sign', $this->partner_sign)->pluck("menu_id");
            $menuIds = $menus->toArray();
        } else {
            $menuIds = unserialize($acl);
        }

        $allMenus = PartnerMenu::getPartnerBindMenu($this->partner_sign, $menuIds);

        return $allMenus;
    }

    // 获取组包含的权限
    public function getPartnerAcl() {
        $acl = $this->acl;
        if ($acl == '*') {
            $menus = PartnerMenu::where('partner_sign', $this->partner_sign)->pluck("menu_id");
            $menuIds = $menus->toArray();
        } else if(empty($acl)) {
            $menuIds = [];
        } else {
            $menuIds = unserialize($acl);
        }

        $allMenus = PartnerMenu::getPartnerBindMenu($this->partner_sign, $menuIds);
        return $allMenus;
    }

    static function getAkkPartnerAcl($acl,$partner_sign){
        if ($acl == '*') {
            $menus = PartnerMenu::where('partner_sign',$partner_sign)->pluck("menu_id");
            $menuIds = $menus->toArray();
        } else if(empty($acl)) {
            $menuIds = [];
        } else {
            $menuIds = unserialize($acl);
        }

        $allMenus = PartnerMenu::getPartnerBindMenu($partner_sign, $menuIds);
        return $allMenus;

    }

    /**
     * 设置权限
     * @param $ids
     */
    public function setAcl($ids) {
        $data = [];
        foreach ($ids as $menuId) {
            $data[] = [
                'partner_sign'  => $this->partner_sign,
                'menu_id'       => $menuId,
                'status'        => 1,
                'created_at'    => now()
            ];
        }

        self::insert($data);
    }

    //设置权限
    public function setPartnerAcl($ids) {
        $data = [
            'acl'           => serialize($ids),
            'updated_at'    => now()
        ];

        self::where('id',$this->id)->update($data);
        
        return true;
    }


    /**
     * 添加管理组
     * @param $data
     * @param null $adminUser
     * @return bool|string
     */
    public function saveItem($data, $adminUser = null) {
        $validator  = Validator::make($data, $this->rules,$this->msg);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // 邮箱 不能重复
        if (!$this->id) {
            $count = self::where('name', '=', $data['name'])->where("partner_sign", $data['partner_sign'])->first();
            if ($count) {
                return "对不起, 组名(name)已经存在!!";
            }
        } else {
            $count = self::where('name', '=', $data['name'])->where("partner_sign", $data['partner_sign'])->where("id", "!=", $this->id)->first();
			if ($count) {
                return "对不起, 组名(name)已经存在!!";
            }
        }

        $this->name             = $data['name'];
        $this->partner_sign     = $data['partner_sign'];
        $this->remark           = $data['remark'];
        if($adminUser && isset($adminUser->partner_sign))
        {
            $partnerAdminGroup = PartnerAdminGroup::where('id',$adminUser->group_id)->first();
            $this->level = $partnerAdminGroup->level + 1;
            $this->add_admin_id     = $adminUser->id ;
            $this->acl              = '';
        }
        else
        {
            $this->add_admin_id     = 99999;
            $this->acl              = '*';
        }

        $this->save();

        return true;
    }

    /**
     * 检查是不是某一个平台的下级
     * @param $pid
     * @return bool
     */
    public function isChildGroup($pid) {
        $parentArr = explode('|', $this->rid);
        if(in_array($pid, $parentArr)) {
            return true;
        }
        return false;
    }

    /**
     * 获取管理组选项
     * @param int $pid
     * @return array
     */
    static function getGroupOptions($pid = 0) {
        $groupList = PartnerAdminGroup::where("id", $pid)->get();
        $options = [];
        foreach ($groupList as $group) {
            $options[$group->id] = $group->name;
        }

        return $options;
    }

    static function findBySign($sign){
        return self::where('partner_sign',$sign)->first();
    }
}
