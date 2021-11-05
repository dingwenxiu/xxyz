<?php

namespace App\Models\Partner;

//use App\Models\BaseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartnerMenu extends Model
{

    // use BaseCache;

    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'partner_menus';

    public $rules = [
        "title" => "required|min:2,max:32",
        "route" => "required|min:0,max:32",
        "type" => "required|in:0,1",
    ];

    public $add_rules = [
        "partner_sign" => "required|alpha",
        "menu_id" => "required|numeric",
        "sort" => "required|numeric",
        "status" => "required|in:0,1",
    ];

    public $edit_rules = [
        "partner_sign" => "alpha",
        "menu_id" => "numeric",
        "sort" => "numeric",
        "status" => "in:0,1",
    ];

    /**
     * @param array $checkedMenu
     * @param string $partnerSign
     * @return array
     */
    static function getPartnerMenuList($checkedMenu = [], $partnerSign = '')
    {

        $menu = self::select(
            DB::raw('partner_menu_config.id'),
            DB::raw('partner_menu_config.pid'),
            DB::raw('partner_menu_config.rid'),
            DB::raw('partner_menu_config.cn_name'),
            DB::raw('partner_menu_config.en_name'),
            DB::raw('partner_menu_config.route'),
            DB::raw('partner_menu_config.api_path'),
            DB::raw('partner_menu_config.sort'),
            DB::raw('partner_menu_config.css_class'),
            DB::raw('partner_menu_config.type'),
            DB::raw('partner_menu_config.level'),
            DB::raw('partner_menu_config.add_admin_id'),
            DB::raw('partner_menu_config.update_admin_id'),
            DB::raw('partner_menu_config.created_at'),
            DB::raw('partner_menu_config.updated_at'),
            DB::raw('partner_menus.partner_sign'),
            DB::raw('partner_menus.status')
        )->leftJoin('partner_menu_config', 'partner_menu_config.id', '=', 'partner_menus.menu_id');

        if (empty($partnerSign)) {
            $menus = $menu->orderBy('partner_menu_config.id', 'desc')->get();
        } else {
            $menus = $menu->where('partner_menus.partner_sign', $partnerSign)->orderBy('partner_menu_config.id', 'desc')->get();
        }

        $parentSet = [];
        foreach ($menus as $menu) {
            if ($menu->pid > 0) {
                if (!isset($parentSet[$menu->pid])) {
                    $parentSet[$menu->pid] = [];
                }
                $parentSet[$menu->pid][] = $menu;
            }

            // 是否被选中
            if (in_array($menu->id, $checkedMenu)) {
                $menu->is_checked = true;
            } else {
                $menu->is_checked = false;
            }
        }

        // 设置层级
        $data = [];
        foreach ($menus as &$menu) {
            if (!$menu->pid) {
                if (isset($parentSet[$menu->id])) {
                    $menu->childs = $parentSet[$menu->id];
                    foreach ($menu->childs as $index => $_menu) {
                        if (isset($parentSet[$_menu->id])) {
                            $_menu->childs = $parentSet[$_menu->id];
                        }
                    }
                }

                $data[] = $menu;
            }
        }

        return ['data' => $data, 'total' => count($data), 'currentPage' => 1, 'totalPage' => 1];
    }

    /**
     * @param $condition
     * @param $pageSize
     * @return array
     */
    static function getMenuList($condition, $pageSize = 20)
    {
        $query = self::select(
            DB::raw('partner_menus.*'),
            DB::raw('partner_menu_config.route as menu_route'),
            DB::raw('partner_menu_config.cn_name as menu_title'),
            DB::raw('partner_menu_config.api_path as menu_path'),
            DB::raw('partner_menu_config.type as menu_type')
        )->leftJoin('partner_menu_config', 'partner_menu_config.id', '=', 'partner_menus.menu_id')->orderBy('partner_menus.id', 'desc');

        // 菜单类型
        if (isset($condition['menu_type']) && in_array($condition['menu_type'], ['0', '1'])) {
            $query->where('partner_menu_config.type', $condition['menu_type']);
        }

        // 平台
        if (isset($condition['partner_sign']) && $condition['partner_sign']) {
            $query->where('partner_menus.partner_sign', $condition['partner_sign']);
        }

        if(isset($condition['pid'])) {
            $query->where('partner_menu_config.pid', $condition['pid']);
        }

        

        $currentPage = isset($condition['page_index']) ? intval($condition['page_index']) : 1;
        $pageSize = isset($condition['page_size']) ? intval($condition['page_size']) : $pageSize;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $menus = $query->skip($offset)->take($pageSize)->get();

        $typeOptions = PartnerMenuConfig::$typeOptions;
        foreach ($menus as $_menu) {
            $_menu->menu_type = isset($typeOptions[$_menu->menu_type]) ? $typeOptions[$_menu->menu_type] : $_menu->menu_type;
        }

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /* 總後台商戶菜單列表
     *
     *＠param $condition
     * 判斷 menu_type 菜单类型
     * 判斷 partner_sign 平台
     * 判斷 pid 父級
     *
     * @return array
     * */
    static function partnerGetMenu($condition, $pageSize = 20)
    {
        $query = self::select(
            DB::raw('partner_menus.*'),
            DB::raw('partner_menu_config.route as menu_route'),
            DB::raw('partner_menu_config.pid as pid'),
            DB::raw('partner_menu_config.rid as rid'),
            DB::raw('partner_menu_config.cn_name as menu_title'),
            DB::raw('partner_menu_config.api_path as menu_path'),
            DB::raw('partner_menu_config.type as menu_type')
        )->leftJoin('partner_menu_config', 'partner_menu_config.id', '=', 'partner_menus.menu_id')->orderBy('partner_menus.id', 'desc');

        // 菜单类型
        if (isset($condition['menu_type']) && in_array($condition['menu_type'], ['0', '1'])) {
            $query->where('partner_menu_config.type', $condition['menu_type']);
        }

        // 平台
        if (isset($condition['partner_sign']) && $condition['partner_sign']) {
            $query->where('partner_menus.partner_sign', $condition['partner_sign']);
        }

        $query->where('partner_menu_config.pid', $condition['pid']);

        $currentPage = isset($condition['page_index']) ? intval($condition['page_index']) : 1;
        $pageSize = isset($condition['page_size']) ? intval($condition['page_size']) : $pageSize;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $menus = $query->skip($offset)->take($pageSize)->get();

        $typeOptions = PartnerMenuConfig::$typeOptions;
        foreach ($menus as $_menu) {
            $_menu->menu_type = isset($typeOptions[$_menu->menu_type]) ? $typeOptions[$_menu->menu_type] : $_menu->menu_type;
        }
        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }


    /**
     * 获取商户对应开放的菜单
     * @param $signArr
     * @return array
     */
    static function getAllMenuBySign($signArr)
    {
        $query = self::select(
            DB::raw('partner_menus.menu_id'),
            DB::raw('partner_menus.partner_sign'),
            DB::raw('partner_menu_config.cn_name as title'),
            DB::raw('partner_menu_config.route as route')
        )->leftJoin('partner_menu_config', 'partner_menu_config.id', '=', 'partner_menus.menu_id')->orderBy('partner_menus.id', 'desc');

        $items = $query->whereIn('partner_menus.partner_sign', $signArr)->get();

        $data = [];
        foreach ($items as $item) {
            if (!isset($data[$item->partner_sign])) {
                $data[$item->partner_sign] = [];
            }

            $data[$item->partner_sign][] = [
                'menu_id' => $item->menu_id,
                'title' => $item->title,
            ];
        }

        return $data;
    }

    /**
     * 获取菜单层级关系
     * @param int $pid
     * @return mixed
     */
    static function getMenuRelated($pid)
    {
        $menu = self::find($pid);
        if (!$menu || !$menu->rid) {
            return [];
        }

        $ids = explode('|', $menu->rid);
        $menus = self::whereIn('id', $ids)->get();
        return $menus;
    }

    /**
     * 初始化平台的菜单
     * @param $sign
     */
    static function initPartnerMenu($sign)
    {
        $allMenus = PartnerMenuConfig::where('status', 1)->get();

        $data = [];
        foreach ($allMenus as $menu) {
            $data[] = [
                'partner_sign' => $sign,
                'menu_id' => $menu->id,
                'status' => 1,
                'sort' => $menu->sort,
                'created_at' => now()
            ];
        }

        self::insert($data);
    }

    /**
     * 获取商户绑定的菜单
     * @param $partnerSign
     * @param array $checkedMenu 已经绑定的
     * @return array
     */
    static function getPartnerBindMenu($partnerSign, $checkedMenu = [])
    {
        $query = self::select(
            DB::raw('partner_menus.partner_sign'),
            DB::raw('partner_menu_config.*')
        )->leftJoin('partner_menu_config', 'partner_menu_config.id', '=', 'partner_menus.menu_id')
            ->where("partner_menus.partner_sign", $partnerSign)->orderBy('partner_menus.id', 'desc');

        $menus = $query->get();
        $parentSet = [];
        foreach ($menus as $menu) {
            if ($menu->pid > 0) {
                if (!isset($parentSet[$menu->pid])) {
                    $parentSet[$menu->pid] = [];
                }
                $parentSet[$menu->pid][] = $menu;
            }

            // 是否会被选中
            if (in_array($menu->id, $checkedMenu)) {
                $menu['checked'] = true;
            } else {
                $menu['checked'] = false;
            }

            // 总后台是否会被选中
            if (in_array($menu->id, $checkedMenu)) {
                $menu['disabled'] = true;
            } else {
                $menu['disabled'] = false;
            }
        }

        // 设置层级
        $data = [];
        foreach ($menus as &$menu) {

            if (!$menu->pid) {

                if (isset($parentSet[$menu->id])) {
                    $menu->child = $parentSet[$menu->id];
                    foreach ($menu->child as $_menu) {
                        if (isset($parentSet[$_menu->id])) {
                            $_menu->child = $parentSet[$_menu->id];
                        }
                    }
                }

                $data[] = $menu;
            }
        }

        return $data;
    }

    /**
     * All Route
     * @return array
     */
    static function getApiAllRoute()
    {
        $partnerAdminUser = auth("partner_api")->user();
        $group = $partnerAdminUser->group();
        $allAcl = PartnerMenu::getAclIds([$group]);

        $menus = self::select(
            DB::raw('partner_menu_config.*')
        )->leftJoin('partner_menu_config', 'partner_menu_config.id', '=', 'partner_menus.menu_id')
            ->where("partner_menu_config.status", 1)
            ->where("partner_menus.status", 1)
            ->where("partner_menus.partner_sign", $partnerAdminUser->partner_sign)
            ->whereIn("partner_menus.menu_id", $allAcl)
            ->orderBy('partner_menus.sort', 'ASC')->get();

        $secondLevelMenu = [];
        $data = [];
        foreach ($menus as $m) {
            if (!$m->pid && $m->type != 1 && in_array($m->id, $allAcl)) {
                $data[$m->id] = [
                    'is_menu' => $m->type ? false : true,
                    'id' => $m->id,
                    'pid' => $m->pid,
                    'title' => $m->cn_name,
                    'en_name' => $m->cn_name,
                    'css_class' => $m->css_class,
                    'api_path' => "",
                    'api_route' => "",
                    "status" => $m->status,
                    'childs' => [],
                ];
            }

            if ($m->type == 1 && in_array($m->id, $allAcl)) {
                if (!isset($secondLevelMenu[$m->pid])) {
                    $secondLevelMenu[$m->pid] = [];
                }

                $secondLevelMenu[$m->pid][] = [
                    'is_menu' => $m->type ? false : true,
                    'id' => $m->id,
                    'pid' => $m->pid,
                    'title' => $m->cn_name,
                    'en_name' => $m->cn_name,
                    'css_class' => $m->css_class,
                    'api_path' => $m->api_path,
                    "api_route" => "partner-api/" . $m->route,
                    'status' => $m->route,
                    "display" => $m->status,
                    'childs' => [],
                ];
            }
        }

        //  二级菜单
        foreach ($menus as $m) {
            if ($m->pid && $m->type != 1 && in_array($m->id, $allAcl)) {
                $data[$m->pid]['childs'][] = [
                    'is_menu' => $m->type ? false : true,
                    'id' => $m->id,
                    'pid' => $m->pid,
                    'title' => $m->cn_name,
                    'en_name' => $m->cn_name,
                    'css_class' => $m->css_class,
                    'api_path' => $m->api_path,
                    "status" => $m->status,
                    "api_route" => "partner-api/" . $m->route,
                    'childs' => isset($secondLevelMenu[$m->id]) ?? [],
                ];
            }
        }

        return array_values($data);
    }


    /**
     * 多个组合并权限
     * 获取可用权限menu Id
     * @param $groups
     * @return array|mixed
     */
    static function getAclIds($groups)
    {
        foreach ($groups as $group) {
            if ($group->acl == "*") {
                $menus = self::where('status', '=', 1)->orderBy('id', 'ASC')->get();
                $allIds = [];
                foreach ($menus as $m) {
                    $allIds[] = $m->menu_id;
                }

                return $allIds;

            } else {
                $acl = $group->acl ? unserialize($group->acl) : [];
                $menus = self::select(
                    DB::raw('partner_menu_config.*')
                )->leftJoin('partner_menu_config', 'partner_menu_config.id', '=', 'partner_menus.menu_id')
                    ->where("partner_menu_config.status", 1)
                    ->where("partner_menus.status", 1)
                    ->whereIn("partner_menus.menu_id", $acl)
                    ->orderBy('partner_menus.sort', 'ASC')->get();

                $allIds = [];
                foreach ($menus as $m) {
                    $allIds[] = $m->id;
                    if ($m->rid) {
                        $ids = explode("|", $m->rid);
                        foreach ($ids as $id) {
                            if (!in_array($id, $allIds)) {
                                $allIds[] = $id;
                            }
                        }
                    }
                }
            }
        }

        return array_unique($allIds);
    }

    /**
     * 获取菜单路由
     * @param $ids
     * @return array
     */
    static function getAllMenuRoute($ids)
    {
        $menus = self::whereIn('id', $ids)->get();
        $data = [];
        foreach ($menus as $menu) {
            $data[] = $menu->route;
        }
        return $data;
    }

    /**
     * 获取权限
     * @param array $allIds
     * @return array
     */
    static function getAclMenus($allIds = [])
    {

        // 带上级的
        $allMenus = self::where('status', '=', 1)->whereIn('id', $allIds)->orderBy('sort', 'ASC')->get();

        $aclMenus = [];

        $parentMenus = [];
        foreach ($allMenus as $menu) {
            if (!$menu->pid) {
                if (in_array($menu->id, $allIds)) {
                    $aclMenus[$menu->id] = [
                        'title' => $menu->title,
                        'route' => $menu->route,
                        'child' => []
                    ];
                }
            }

            $aRid = explode('|', $menu->rid);

            if (count($aRid) == 3) {
                if (!isset($parentMenus[$menu->pid])) {
                    $parentMenus[$menu->pid] = [];
                }
                $parentMenus[$menu->pid][$menu->id] = [
                    'title' => $menu->title,
                    'route' => $menu->route,
                    'child' => []
                ];
            }
        }

        foreach ($allMenus as $_menu) {
            $aRid = explode('|', $_menu->rid);
            if (count($aRid) == 2) {
                $aclMenus[$_menu->pid]['child'][$_menu->id] = [
                    'title' => $_menu->title,
                    'route' => $_menu->route,
                    'child' => isset($parentMenus[$_menu->id]) ? $parentMenus[$_menu->id] : []
                ];
            }

        }


        return $aclMenus;
    }

    public function saveItem($data, $parent, $adminId)
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $_menu = self::where('route', $data['route'])->first();
        if (!$this->id && $_menu) {
            return "对不起, 路由不能重复!";
        }

        // 类 可以为空
        $class = isset($data['css_class']) ? $data['css_class'] : "";

        // id 的递增问题
        if ($parent && $parent->id > 0) {
            $sibling = self::where('pid', $parent->id)->orderBy('id', 'desc')->first();
            if ($sibling && !$this->id) {
                $this->id = $sibling->id + 1;
            }
        }

        $this->pid = $parent && $parent->id > 0 ? $parent->id : 0;
        $this->route = $data['route'];
        $this->c_name = $data['c_name'];
        $this->type = $data['type'];
        $this->sort = $data['sort'] ?? '';
        $this->api_path = $data['api_path'] ?? '';
        $this->css_class = $class;
        $this->admin_id = $adminId;
        $this->save();

        $this->rid = $parent ? $parent->rid . "|" . $this->id : $this->id;

        $this->save();

        return true;
    }

    /*
     *
     *
     *
     * */
    static function partnerBindMenuConfig($ids, $partner)
    {

        db()->beginTransaction();
        try {

            foreach ($ids as $id) {

                $partnerMenu = new self();
                $menu = PartnerMenuConfig::find(intval($id));
                if (!$menu){
                    db()->rollback();
                    return ['res' => 0, 'msg' => 'id : '.$id.'不存在'];
                }
                $existCount = self::where('partner_sign', $partner->partner_sign)->where('menu_id', $menu->id)->count();
                if ($menu && $existCount ==0 ) {

                    //一級菜單
                    if (intval($menu->pid) === 0) {
                        $partnerMenu->partner_sign = $partner->partner_sign;
                        $partnerMenu->menu_id = $menu->id;
                        $partnerMenu->sort = $menu->sort;
                        $partnerMenu->status = $menu->status;
                        $partnerMenu->add_admin_id = 0;
                        $partnerMenu->update_admin_id = 0;
                        $partnerMenu->add_partner_admin_id = 0;
                        $partnerMenu->save();

                    } else {
                        //二級菜單 鍊結自動加
                        if ($menu->type == 0) {
                            $partnerMenu->partner_sign = $partner->partner_sign;
                            $partnerMenu->menu_id = $menu->id;
                            $partnerMenu->sort = $menu->sort;
                            $partnerMenu->status = $menu->status;
                            $partnerMenu->add_admin_id = 0;
                            $partnerMenu->update_admin_id = 0;
                            $partnerMenu->add_partner_admin_id = 0;
                            $partnerMenu->save();

                            $subMenus = PartnerMenuConfig::getSubMenuByParentId($menu->id);

                            //底下的子菜單和鍊結加入
                            if ($subMenus) {
                                foreach ($subMenus as $subMenu) {

                                    $existCountSubMenu = self::where('partner_sign', $partner->partner_sign)->where('menu_id', $subMenu->id)->count();

                                    if ($existCountSubMenu == 0) {


                                        $subPartnerMenu = new self();
                                        $subPartnerMenu->partner_sign = $partner->partner_sign;
                                        $subPartnerMenu->menu_id = $subMenu->id;
                                        $subPartnerMenu->sort = $subMenu->sort;
                                        $subPartnerMenu->status = $subMenu->status;
                                        $subPartnerMenu->add_admin_id = 0;
                                        $subPartnerMenu->update_admin_id = 0;
                                        $subPartnerMenu->add_partner_admin_id = 0;
                                        $subPartnerMenu->save();

                                    }
                                }
                            }

                        }
                        //鍊結 以防後新增的鍊結菜單
                        if ($menu->type == 1) {

                            $partnerMenu->partner_sign = $partner->partner_sign;
                            $partnerMenu->menu_id = $menu->id;
                            $partnerMenu->sort = $menu->sort;
                            $partnerMenu->status = $menu->status;
                            $partnerMenu->add_admin_id = 0;
                            $partnerMenu->update_admin_id = 0;
                            $partnerMenu->add_partner_admin_id = 0;
                            $partnerMenu->save();

                        }

                    }
                }else{

                    db()->rollback();
                    return ['res' => 0, 'msg' => 'id : '.$id.'已經新增'];
                }

            }


            db()->commit();
            return ['res' => 1, 'msg' => "新增成功"];
        } catch (\Exception $e) {
            db()->rollback();
            return ['res' => 0, 'msg' => $e->getMessage()];
        }

    }

    static function getNoneCombineList($condition)
    {
        $query = self::where('partner_sign', $condition['partner_sign']);
        $ids = [];
        $res = $query->get();
        foreach ($res as $item) {
            array_push($ids, $item->menu_id);
        }
        $data = DB::table('partner_menu_config')->select('*')->whereNotIn('id', $ids)
            ->where('pid', $condition['pid'])
            ->get();

        return $data;
    }

    /**
     *  菜单添加
     * @param $data
     * @param $id
     * @param $adminId
     * @return bool|string
     */
    public function saveItems($data, $id, $adminId)
    {
        $menuId = self::where('menu_id', $data['menu_id'])->where('partner_sign', $data['partner_sign'])->count();
        if ($data['id'] == 0) {
            // 添加
            $validator = Validator::make($data, $this->add_rules);
            if ($menuId > 0) {
                return "对不起, menu_id不能重复!";
            }
        } else {
            // 编辑
            $validator = Validator::make($data, $this->edit_rules);
            if ($menuId > 1) {
                return "对不起, menu_id不能重复!";
            }
        }
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $this->partner_sign = $data['partner_sign'];
        $this->menu_id = $data['menu_id'];
        $this->sort = $data['sort'];
        $this->status = $data['status'];
        $this->add_admin_id = $id && $id > 0 ? 0 : $adminId;
        $this->add_partner_admin_id = $id && $id > 0 ? 0 : $adminId;
        $this->update_admin_id = $id && $id > 0 ? $adminId : 0;
        $this->update_partner_admin_id = $id && $id > 0 ? $adminId : 0;

        return $this->save();
    }
}
