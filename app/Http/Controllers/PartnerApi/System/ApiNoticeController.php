<?php

namespace App\Http\Controllers\PartnerApi\System;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerNotice;
use Illuminate\Support\Facades\Validator;
use App\Lib\Oss\OssTrait;

/**
 * 公告
 * Class ApiNoticeController
 * @package App\Http\Controllers\PartnerApi\Partner
 */
class ApiNoticeController extends ApiBaseController
{
    use OssTrait;

    /** ============================= 公告列表============================= */

    // 公告列表
    public function noticeList()
    {
        $c                  = request()->all();
        $c["partner_sign"]  = $this->partnerSign;
        $data   = PartnerNotice::getList($c);

        $data['type_option']        = PartnerNotice::$types;
        $data['device_type_option'] = PartnerNotice::$deviceTypes;

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 添加公告
    public function noticeAdd($id = 0)
    {
        $partnerAdminUser = $this->partnerAdminUser;

        if ($id) {
            $model  = PartnerNotice::find($id);
            if (!$model) {
                return Help::returnApiJson("对不起, 目标对象不存在！", 0);
            }

            // 是否可以操作
            if ($model->partner_sign != $this->partnerSign) {
                return Help::returnApiJson("对不起, 您没有权限操作！", 0);
            }

        } else {
            $model  = new PartnerNotice();
        }

        // 获取选项
        $action     = request('action', 'process');
        if ($action == 'option') {
            if ($model->id > 0) {
                $data['model'] = $model;
            }
            $data['type_options'] = PartnerNotice::$types;
            return Help::returnApiJson("恭喜, 添加数据成功！", 1, $data);
        }

        $params     = request()->all();
        $res = $model->saveItem($params, $this->partnerSign, $partnerAdminUser);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("恭喜, 添加数据成功！", 1);
    }

    // 修改公告状态
    public function noticeStatus($id)
    {
        // 是否存在
        $model = PartnerNotice::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 数据不存在！", 0);
        }

        // 是否可以操作
        if ($model->partner_sign != $this->partnerSign) {
            return Help::returnApiJson("对不起, 您没有权限操作！", 0);
        }

        $model->status  = $model->status ? 0 : 1;
        // 修改
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1, ['status' => $model->status]);
    }

    // 置顶公告
    public function noticeTop($id)
    {
        // 是否存在
        $model = PartnerNotice::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 数据不存在！", 0);
        }

        // 是否可以操作
        if ($model->partner_sign != $this->partnerSign) {
            return Help::returnApiJson("对不起, 您没有权限操作！", 0);
        }

        $model->setTop();

        return Help::returnApiJson("恭喜, 公告置顶成功！", 1);
    }

    // 刷新公告
    public function noticeFlush()
    {
        PartnerNotice::_flushCache("notice_" . $this->partnerSign);

        return Help::returnApiJson("恭喜, 刷新缓存成功！", 1);
    }

    // 删除公告
    public function noticeDel () {

        $id = request('id');
        $notice = PartnerNotice::find($id);
        if (!$notice) {
            return Help::returnApiJson("对不起, 无效的公告id！", 0);
        }

        $notice->delete();

        return Help::returnApiJson("恭喜, 删除数据成功！", 1);
    }


    // LOGO图片
    public function logoUpLoadImg()
    {
        //获取前台传入参数
        $c = request()->all();

        $file            = $c['file'];

        $data['partner_sign'] = $this->partnerSign;
        $data['filename']     = $c['type'];
        $data['directory']    = 'logo';
        $ImageArrange = new ImageArrange();
        $ImageArrangeM = $ImageArrange->uploadImage($file, $data);

        try {
            if (!$ImageArrangeM['success']) {
                return Help::returnApiJson($ImageArrangeM['msg'], 0);
            }
            $filename = $ImageArrangeM['data']['path'];
            
            //执行添加
            Partner::where('sign', $this->partnerSign)->update([$c['type'] => $filename]);

            return Help::returnApiJson('保存成功!', 1, ['path' => $filename]);
        } catch (\Exception $e) {
            //删除上传成功的图片
            return Help::returnApiJson('保存失败!' . $e->getMessage() . $e->getLine() . $e->getFile(), 0, []);
        }
    }


    //获取LOGO
    public function logoImage() {
        $partner = Partner::where('sign', $this->partnerSign)->first();
        if ($partner === null) {
            return Help::returnApiJson('获取成功!', 1, []);
        }
        $data = [];
        $data['logo_image_pc_1'] = $partner->logo_image_pc_1 ? $partner->logo_image_pc_1 : '' ;
        $data['logo_image_pc_2'] = $partner->logo_image_pc_2 ? $partner->logo_image_pc_2 : '' ;
        $data['logo_image_h5_1'] = $partner->logo_image_h5_1 ? $partner->logo_image_h5_1 : '' ;
        $data['logo_image_h5_2'] = $partner->logo_image_h5_2 ? $partner->logo_image_h5_2 : '' ;
        $data['logo_icon']       = $partner->logo_icon ? $partner->logo_icon : '' ;
        return Help::returnApiJson('获取成功!', 1, $data);
    }

    // 删除LOGO
    public function logoDel () {
        $type = request('type');

        $partner = Partner::where('sign', $this->partnerSign)->update([$type => '']);
        return Help::returnApiJson('删除成功!', 1, $partner);
    }
}
