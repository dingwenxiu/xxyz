<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminGroup extends Model
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'admin_groups';

    /**
     * 后去用户可以配置的下级
     * @param string $adminUser
     * @return array
     */
    static function getAdminGroupList($adminUser = '') {
        $query          = self::orderBy('id', 'desc');

        if (!$adminUser) {
            $adminUser  = auth()->guard('admin_api')->user();
        }

        $groupIds[] = $adminUser->group_id;

        $return = [];

        foreach ($groupIds as $key => $groupId) {

            // 当前登录
            $query->where('pid', $groupId);
            $data   = $query->get();
            foreach ($data as $item) {
                $return[$item->id] = [
                    'id'            => $item->id,
                    'pid'           => $item->pid,
                    'name'          => $item->name,
                    'total_childs'  => $item->total_childs,
                    'created_at'    => $item->created_at,
                    'level'         => 1,
                    'child'         => [],
                ];

                $_child = self::orderBy('id', 'desc')->where('pid', $item->id)->get();

                foreach ($_child as $_item) {
                    $return[$item->id]['child'][$_item->id] = [
                        'id'            => $_item->id,
                        'pid'           => $_item->pid,
                        'name'          => $_item->name,
                        'created_at'    => $_item->created_at,
                        'total_childs'  => $_item->total_childs,
                        'level'         => 2,
                        'child'         => [],
                    ];

                    $__child = self::orderBy('id', 'desc')->where('pid', $_item->id);;
                    foreach ($__child as $__item) {
                        $return[$item->id]['child'][$_item->id]['child'][$__item->id] = [
                            'id'            => $__item->id,
                            'pid'           => $__item->pid,
                            'name'          => $__item->name,
                            'created_at'    => $__item->created_at,
                            'total_childs'  => $__item->total_childs,
                            'level'         => 3,
                            'child'         => [],
                        ];
                    }
                }
            }
        }

        return $return;
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
        $groupList = AdminGroup::where("pid", $pid)->get();
        $options = [];
        foreach ($groupList as $group) {
            $options[$group->id] = $group->name;
        }

        return $options;
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

    // 获取组包含的权限
    public function getAcl() {
        $acl = $this->acl;

        if (!$acl) {
            return [];
        }

        if ($acl == '*') {
            $menus = AdminMenu::pluck("pid");
            $menuIds = $menus->toArray();
        } else if(empty($acl)) {
            $menuIds = [];
        } else {
            $menuIds = unserialize($acl);
        }

        $allMenus = AdminMenu::getAdminBindMenu($menuIds);

        return $allMenus;
    }

    /**
     * 后去用户可以配置的下级
     * @param string $c
     * @param string $adminUser
     * @return array
     */
    static function getAdminGroupDetail($c,$adminUser = '') {
        $query          = self::orderBy('id', 'desc');

        if (!$adminUser) {
            $adminUser  = auth()->guard('admin_api')->user();
        }

        $groupIds[] = $adminUser->group_id;

        $return = [];

        foreach ($groupIds as $key => $groupId) {

            // 当前登录
            $query->where('pid', $groupId);
            $data   = $query->get();
            foreach ($data as $item) {
                $return[$item->id] = [
                    'id'            => $item->id,
                    'pid'           => $item->pid,
                    'name'          => $item->name,
                    'total_childs'  => $item->total_childs,
                    'created_at'    => $item->created_at,
                    'level'         => 1,
                    'child'         => [],
                ];

                $_child = self::orderBy('id', 'desc')->where('pid', $item->id)->get();

                foreach ($_child as $_item) {
                    $return[$item->id]['child'][$_item->id] = [
                        'id'            => $_item->id,
                        'pid'           => $_item->pid,
                        'name'          => $_item->name,
                        'created_at'    => $_item->created_at,
                        'total_childs'  => $_item->total_childs,
                        'level'         => 2,
                        'child'         => [],
                    ];

                    $__child = self::orderBy('id', 'desc')->where('pid', $_item->id);;
                    foreach ($__child as $__item) {
                        $return[$item->id]['child'][$_item->id]['child'][$__item->id] = [
                            'id'            => $__item->id,
                            'pid'           => $__item->pid,
                            'name'          => $__item->name,
                            'created_at'    => $__item->created_at,
                            'total_childs'  => $__item->total_childs,
                            'level'         => 3,
                            'child'         => [],
                        ];
                    }
                }
            }
        }

        return $return;
    }

    //设置权限
    public function setAcl($ids) {
        $data = [
            'acl'           => serialize($ids),
            'updated_at'    => now()
        ];

        self::where('id',$this->id)->update($data);

        return true;
    }
}
