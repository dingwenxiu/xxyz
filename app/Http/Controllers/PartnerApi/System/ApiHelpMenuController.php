<?php

namespace App\Http\Controllers\PartnerApi\System;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Models\Partner\HelpCenter;
use App\Models\Partner\HelpMenu;
use Illuminate\Support\Facades\Validator;
/**
 * version 1.0
 * Class ApiPartnerMenuController
 * @package App\Http\Controllers\AdminApi\Partner
 */
class ApiHelpMenuController extends ApiBaseController
{
    /** =================================== 菜单 @ 相关 ===================================== */
    // 获取帮助中心内容
    public function helpMenuList()
    {
        $c                 = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $data       = HelpCenter::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 修改菜单状态
    public function partnerMenuStatus($id)
    {
        $adminUser = $this->partnerAdminUser;

        // 获取菜单状态
        $model = HelpCenter::where('id',$id)->first();
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的菜单id！", 0);
        }

        $model->status          = $model->status ? 0 : 1;
        $model->update_admin_id = $adminUser->id;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 删除帮助
    public function helpMenuDel($id)
    {
        // 获取用户
        $models = HelpCenter::where('pid', $id)->get()->toArray();
        if ($models) {
            return Help::returnApiJson("对不起, 请先删除下级帮助内容！", 0);
        }

        // 删除菜单
        $model = HelpMenu::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 菜单信息错误！", 0);
        }
        $model->delete();

        return Help::returnApiJson("恭喜, 删除数据成功！", 1);
    }

    // 删除帮助内容
    public function contentDel($id)
    {
        // 删除内容
        $model = HelpCenter::find($id);

        // 删除图片
        if (!empty($model->help_image) && isset($model->help_image)) {
            unlink(storage_path(). '/' . $model->help_image);
        }

        $model->delete();

        return Help::returnApiJson("恭喜, 删除数据成功！", 1);
    }

    /** =================================== 分类菜单 @ 相关 ===================================== */
    // 获取 菜单 列表
    public function helpMenu()
    {
        $data = HelpMenu::getMenuList($this->partnerSign);
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加 菜单 列表
    public function helpMenuAdd()
    {
        $adminUser = $this->partnerAdminUser;

        $model = new HelpMenu();

        $params = request()->all();

        $res    = $model->saveItem($params, $this->partnerSign, $adminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加分类菜单成功！", 1);
    }

    // 添加内容

    public function addHelpContent($pid)
    {
        $adminUser = $this->partnerAdminUser;

        if (!$pid) {
            return Help::returnApiJson("对不起, 请选择分类！", 0);
        }
        
        $title = request('title');
        if (empty($title)) {
            return Help::returnApiJson("对不起, 标题不能为空!", 0);
        }

        $content = request('content');
        if (empty($content)) {
            return Help::returnApiJson("对不起, 内容不能为空!", 0);
        }

        $helpContent = new HelpCenter();
        $data = [
            'pid'         => $pid,
            'title'       => $title,
            'content'     => $content,
            'status'      => 1,
            'add_partner_admin_id'  => $adminUser->id,
        ];

        $res = $helpContent->saveItem($data, $adminUser);

        return Help::returnApiJson("恭喜, 添加成功!", 1, ['cards' => $res]);
    }

    // 修改帮助内容
    public function editHelp($id)
    {
        if (!$id) {
            return Help::returnApiJson("对不起, 无效的内容ID!", 0);
        }
        $data = request()->all();
        $validator  = Validator::make($data, [
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $helpContent = HelpCenter::find($id);
        $helpContent->fill($data);
        $helpContent->save();

        return Help::returnApiJson('修改数据成功!', 1,  $helpContent);
    }

    
    // 帮助图片删除
    public function helpImgDel(){

        $id   = request('id');

        if (!$id) {
            return Help::returnApiJson('图片信息有误!', 0);
        }

        $partner = HelpCenter::where('id', $id)->first();
        if ($partner) {
            $partner->help_image = '';
        }

        $data = $partner->update();

        return Help::returnApiJson('删除成功!', 1, $data);
    }
}
