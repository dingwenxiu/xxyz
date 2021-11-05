<?php
namespace App\Models\Admin;

use App\Lib\Logic\Cache\ConfigureCache;
use App\Models\BaseCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class AdminMenu extends Model
{

    use BaseCache;

    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'admin_menus';

    public $rules = [
        "title"         => "required|min:2,max:32",
        "route"         => "required|min:0,max:32",
        "type"          => "required|in:0,1,2",
    ];

    /**
     * @param $condition
     * @param $pageSize
     * @return array
     */

    static function getMenuList($condition, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');
        if (isset($condition['pid'])) {
            $query->where('pid', $condition['pid']);
        } else {
            $query->where('pid', 0);
        }

        $currentPage    = isset($condition['pageIndex']) ? intval($condition['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    public function saveItem($data, $parent, $adminId) {
        $validator  = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $_menu = self::where('route', $data['route'])->first();
        if (!$this->id && $_menu) {
            return "对不起, 路由不能重复!";
        }

        // 类 可以为空
        $class  = isset($data['css_class']) ? $data['css_class'] : "";

        // id 的递增问题
        if (!$this->id) {
            if ($parent && $parent->id > 0) {
                $sibling = self::where('pid', $parent->id)->orderBy('id', 'desc')->first();
                if ($sibling && $sibling->id > 0) {
                    $deep       = $parent && $parent->id > 0 ? explode("|", $parent->rid) : 0;
                    $fixNumber  = self::getFixNumber(count($deep));
                    $this->id = $sibling->id + $fixNumber;

                } else {
                    $deep       = $parent && $parent->id > 0 ? explode("|", $parent->rid) : 0;
                    $fixNumber  = self::getFixNumber(count($deep));
                    $this->id   = $parent->id + $fixNumber;
                }
            } else {
                $sibling    = self::where('pid', 0)->orderBy('id', 'desc')->first();
                $fixNumber  = self::getFixNumber(0);
                $this->id   = $sibling->id + $fixNumber;

            }
        }

        $this->pid          = $parent && $parent->id > 0 ? $parent->id : 0;
        $this->route        = $data['route'];
        $this->title        = $data['title'];
        $this->type         = $data['type'];
        $this->sort         = $data['sort']??'';
        $this->api_path     = $data['api_path']??'';
        $this->css_class    = $class;
        $this->admin_id     = $adminId;
        $this->save();

        $this->rid  = $parent ? $parent->rid . "|" . $this->id : $this->id;

        $this->save();

        return true;
    }

    /**
     * 获取菜单层级关系
     * @param int $pid
     * @return mixed
     */
    static function getMenuRelated($pid) {
        $menu = self::find($pid);

        if (!$menu || !$menu->rid) {
            return [];
        }

        $ids    = explode('|', $menu->rid);
        $menus  = self::whereIn('id', $ids)->get();
        return $menus;
    }

    /**
     * @return array|\Illuminate\Contracts\Cache\Repository
     * @throws \Exception
     */
    static function getLeftMenus() {
        $adminUser  = \Auth::guard('admin')->user();
        $group      = $adminUser->group();

        $cacheKey = "menu_cache_" . $group->id;
        if (ConfigureCache::get($cacheKey)) {
            return ConfigureCache::get($cacheKey);
        }

        $allAcl     = AdminMenu::getAclIds($group);


        $menus  = self::where('status', '=', 1)->where('type', '=', 0)->orderBy('sort', 'ASC')->get();
        $data   = [];
        foreach ($menus as $m) {
            if ($m->pid == 0 && in_array($m->id, $allAcl)) {
                $data[$m->id]['title']      = $m->title;
                $data[$m->id]['css_class']  = $m->css_class;
            }
        }

        //  二级菜单
        foreach ($menus as $m) {
            if ($m->pid > 0 && in_array($m->id, $allAcl)) {
                $data[$m->pid]['child'][] = $m;
            }
        }

        ConfigureCache::forget($cacheKey, $data);

        return $data;
    }

    /**
     * All Route
     * @return array
     */
    static function getApiAllRoute() {
        $adminUser  = auth()->guard('admin_api')->user();
        $group      = $adminUser->group();
        $allAcl     = AdminMenu::getAclIds($group);

        $menus      = self::where('status', '=', 1)->orderBy('sort', 'ASC')->get();

        $secondLevelMenu = [];
        $data   = [];
        foreach ($menus as $m) {
            if (!$m->pid &&  $m->type != 1 && in_array($m->id, $allAcl)) {
                $data[$m->id] = [
                    'is_menu'       => $m->type ? false : true,
                    'id'            => $m->id,
                    'pid'           => $m->pid,
                    'title'         => $m->title,
                    'css_class'     => $m->css_class,
                    'api_path'      => "",
                    'api_route'     => "",
                    'childs'        => [],
                ];
            }

            if ($m->type == 1 && in_array($m->id, $allAcl)) {
                if (!isset($secondLevelMenu[$m->pid])) {
                    $secondLevelMenu[$m->pid] = [];
                }

                $secondLevelMenu[$m->pid][] = [
                    'is_menu'       => $m->type ? false : true,
                    'id'            => $m->id,
                    'pid'           => $m->pid,
                    'title'         => $m->title,
                    'css_class'     => $m->css_class,
                    'api_path'      => $m->api_path,
                    'api_route'     => "api/" . $m->route,
                    'childs'        => [],
                ];
            }
        }

        //  二级菜单
        foreach ($menus as $m) {
            if ($m->pid && $m->type != 1 && in_array($m->id, $allAcl)) {
                $data[$m->pid]['childs'][] = [
                    'is_menu'       => $m->type ? false : true,
                    'id'            => $m->id,
                    'pid'           => $m->pid,
                    'title'         => $m->title,
                    'css_class'     => $m->css_class,
                    'api_path'      => $m->api_path,
                    'api_route'     => "api/" . $m->route,
                    'childs'        => isset($secondLevelMenu[$m->id]) ??[],
                ];
            }
        }

        return array_values($data);
    }


    /**
     * 构建菜单 @TODO 权限
     * @param $buttons
     * @return array
     */
    static function buildButtons($buttons) {
        $routes = [];
        foreach($buttons as $button) {
            $routes[] = $button['route'];
        }

        $menus = self::whereIn('route', $routes)->get();
        $class = [];
        $ids   = [];
        foreach($menus as $m) {
            $class[$m->route] = ['class' => $m->css_class, 'title' => $m->title];
            $ids[] = $m->id;
        }

        $data = [];
        foreach($buttons as $button) {
            $data[] = [
                'url'       => route($button['route'], $button['params']),
                'class'     => $class[$button['route']]['class'],
                'title'     => $class[$button['route']]['title'],
                'type'      => isset($button['type']) ? $button['type'] : ''
            ];
        }
        return $data;
    }

    /**
     * 获取面包屑
     */
    static function getBreadcrumb() {
        $routeName  = Route::getCurrentRoute()->getName();

        $cacheKey = "breadcrumb_" . $routeName;
        if (ConfigureCache::get($cacheKey)) {
            return ConfigureCache::get($cacheKey);
        }

        $item   = self::where('route', $routeName)->first();
        if ($item && $item->rid) {
            $items = self::whereIn('id', explode('|', $item->rid))->orderBy('id', 'ASC')->get();
        } else {
            $items = $item ? [$item] : [];
        }

        $data = [];
        foreach ($items as $_route) {
            $data[] = [
                'route' => $_route->route,
                'title' => $_route->title,
            ];
        }

        ConfigureCache::forget($cacheKey, $data);

        return $data;
    }

    /**
     * 获取可用权限menu Id
     * @param $group
     * @param $hasRoute
     * @return array|mixed
     */
    static function getAclIds($group) {
        if ($group->acl == "*") {
            $menus  = self::where('status',  '=', 1)->orderBy('id',   'ASC')->get();
            $allIds    = [];
            foreach ($menus as $m) {
                $allIds[] = $m->id;
            }
        } else {
            $acl    =  $group->acl ? unserialize($group->acl) : [];
            $menus  = self::where('status',  '=', 1)->whereIn('id', $acl)->orderBy('id',   'ASC')->get();

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
        return $allIds;
    }

    /**
     * 获取菜单路由
     * @param $ids
     * @return array
     */
    static function getAllMenuRoute($ids) {
        $menus  = self::whereIn('id', $ids)->get();
        $data   = [];
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
    static function getAclMenus($allIds = []) {

        // 带上级的
        $allMenus   = self::where('status',  '=', 1)->whereIn('id', $allIds)->orderBy('sort',   'ASC')->get();

        $aclMenus   = [];

        $parentMenus = [];
        foreach ($allMenus as $menu) {
            if (!$menu->pid) {
                if(in_array($menu->id, $allIds)) {
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

    /**
     * 获取绑定的菜单
     * @param array $checkedMenu  已经绑定的
     * @return array
     */
    static function getAdminBindMenu($checkedMenu = []) {
        $query = self::orderBy('admin_menus.id', 'desc');

        $menus = $query->get();
        $parentSet = [];
        foreach ($menus as $menu) {
            if ($menu->pid > 0) {
                if (!isset($parentSet[$menu->pid]) ) {
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
    // 修整数据
    static function getFixNumber($deep) {
        if ($deep == 0) {
            return 10000;
        } else if ($deep == 1) {
            return 100;
        } else {
            return 1;
        }
    }

}
