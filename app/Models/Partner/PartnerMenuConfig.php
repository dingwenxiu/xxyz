<?php
namespace App\Models\Partner;

use App\Models\BaseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PartnerMenuConfig extends Model
{

    use BaseCache;

    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'partner_menu_config';

    public $rules = [
        "cn_name"       => "required|min:2,max:32",
        "route"         => "required|min:1,max:32",
        "type"          => "required|in:0,1",
    ];

    static $typeOptions = [
        1 => "链接",
        0 => "菜单"
    ];

    /**
     * @param $checkedMenu
     * @return array
     */
    static function getMenuList($checkedMenu = []) {
        $menus = self::orderBy('id', 'desc')->get();

        $parentSet = [];
        foreach ($menus as $menu) {
            if ($menu->pid > 0) {
                if (!isset($parentSet[$menu->pid]) ) {
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
     * 保存
     * @param $data
     * @param $parent
     * @param $adminUser
     * @return bool|string
     */
    public function saveItem($data, $parent, $adminUser = null) {
        $validator  = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $_menu = self::where('route', $data['route'])->where('pid','!=',0)->first();

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

        // 保存
        $this->pid              = $parent && $parent->id > 0 ? $parent->id : 0;
        $this->route            = $data['route'];
        $this->cn_name          = $data['cn_name'];
        $this->en_name          = $data['en_name'];
        $this->type             = $data['type'];
        $this->sort             = isset($data['sort']) ? intval($data['sort']) : 100;
        $this->api_path         = isset($data['api_path']) ? $data['api_path'] : '';
        $this->css_class        = $class;
        $this->add_admin_id     = $adminUser ? $adminUser->id : 999999;
        $this->save();

        $this->rid              = $parent && $parent->id > 0 ? $parent->rid . "|" . $this->id : $this->id;
        $this->save();

        return true;
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
    /*
     * 產生menu 上下級
     * @param int
     * @return array
     * */
    static function getMenuRidArr($pid){

        $menuStr = [];

        /*  根目錄 前端處理
        array_push($menuStr,[
            'key'=>0,
            'c_name'=>'根目錄'
        ]);
        */
        if($pid >0){

            $partnerMenu = PartnerMenuConfig::find($pid);
            if($partnerMenu){
                $rids = explode('|', $partnerMenu->rid);

                foreach ($rids as $rid){
                    $name = PartnerMenuConfig::find($rid)->cn_name;
                    array_push($menuStr,['key' => $rid,'c_name'=>$name]);
                }
            }
        }
        return $menuStr;
    }

    static function getSubMenuByParentId($id){
        return self::where('pid',$id)->get();
    }

    static function partnerMenuConfigDel($menu){

        db()->beginTransaction();
        try {
            $res = self::where('id',$menu->id)
                ->orWhere('rid','like','%|'.$menu->id.'|%')
                ->orWhere('rid','like',$menu->id.'|%')
                ->delete();


            db()->commit();
            return ['res'=>1,'msg'=>'刪除成功'];
        }catch (\Exception $e){

            db()->rollback();
            return ['res'=>0,'msg'=>$e->getMessage() . "-" . $e->getLine()];
        }
    }
}
