<?php

namespace App\Models\Partner;

use App\Models\BaseCache;
use Illuminate\Support\Facades\Validator;

class HelpMenu extends Base
{
    use BaseCache;
    protected $table = "help_center_menu";

    public $rules = [
        'name'                 => 'required|min:2|max:128',
    ];


    /**
     * 获取 分类列表
     * @param $partner_sign
     * @return mixed
     */
    static function getMenuList($partner_sign) {

        $menu = self::select('help_center_menu.*');

        if (empty($partner_sign)) {
            $menu->orderBy('help_center_menu.id', 'desc')->get();
        } else {
            $menu->where('help_center_menu.partner_sign',$partner_sign)->orderBy('help_center_menu.id', 'desc')->get();
        }

        $data   = $menu->get();

        return $data;
    }


    // 删除帮助分类
    public function helpMenuDel($id)
    {

        // 子
        $helpMenus = HelpCenter::where('pid',$id)->pluck('id')->toArray();

        if($helpMenus)
        {
            HelpCenter::whereIn('id',$helpMenus)->delete();
        }

        HelpMenu::where('id',$id)->delete();

        return true;
    }


    // 保存
    public function saveItem($data, $partnerSign, $partnerAdminUser = null) {
        $validator  = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // 如果是编辑 是否有权限
        if ($this->id && $partnerSign != $this->partner_sign) {
            return "对不起, 您没有操作的权限!";
        }

        // 不能编辑的字段
        if (!$this->id) {
            $this->partner_sign             = $partnerSign;
            $this->add_partner_admin_id     = $partnerAdminUser ? $partnerAdminUser->id : 0;
        }

        $this->name = $data['name'];
        $this->save();

        return true;
    }

    /**
     * @param $partnerSign
     * @return mixed
     * @throws \Exception
     */
    static function getDataFromCache($partnerSign) {
        if (self::_hasCache('help_' . $partnerSign)) {
            return self::_getCacheData('help_' . $partnerSign);
        } else {
            $allCache = self::getMenuList();
            if ($allCache) {
                self::_saveCacheData('help_' . $partnerSign, $allCache);
            }

            return $allCache;
        }
    }

}
