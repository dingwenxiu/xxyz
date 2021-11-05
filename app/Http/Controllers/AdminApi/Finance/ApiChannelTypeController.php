<?php

namespace App\Http\Controllers\AdminApi\Finance;

use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Lib\Oss\OssTrait;
use Illuminate\Support\Facades\Validator;
use App\Models\Finance\FinanceChannelType;
use App\Http\Controllers\AdminApi\ApiBaseController;

/**
 * version 1.0
 * 支付种类表
 * Class ApiChannelTypeController
 * @package App\Http\Controllers\AdminApi\Finance
 */
class ApiChannelTypeController extends ApiBaseController
{
    use OssTrait;

    /**
     * 获取支付种类表信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        //获取前台传入参数
        $c = request() -> all();

        //获取列表信息
        $data = FinanceChannelType ::getList($c);
        return Help ::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function del($id)
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        $model = FinanceChannelType ::find($id);
        if (!$model) {
            return Help ::returnApiJson("对不起, 无效的id！", 0);
        }

        $model -> delete();

        return Help ::returnApiJson('删除成功!', 1, []);
    }

    /**
     * 添加/编辑
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($id = 0)
    {
        $adminUser = auth("admin_api") -> user();
        if (!$adminUser) {
            return Help ::returnApiJson("对不起, 商户未登录！", 0);
        }

        //获取前台传入参数
        $data = request() -> all();

        // $id>0是编辑
        if ($id) {
            $model = FinanceChannelType ::find($id);
            if (!$model) {
                return Help ::returnApiJson("对不起, 目标对象不存在", 0);
            }
        } else {
            $model = new FinanceChannelType(); //添加
        }

        $res = $model -> saveItem($data);
        if (true !== $res) {
            return Help ::returnApiJson($res, 0);
        }

        $msg = $id > 0 ? "编辑数据" : "添加数据";
        return Help ::returnApiJson("恭喜, {$msg}成功！", 1);
    }

    /**
     * 上传图片
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function channelTypeUploadImg() {
		$adminUser = auth("admin_api") -> user();
		if (!$adminUser) {
			return Help ::returnApiJson("对不起, 商户未登录！", 0);
		}
		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr =[
			'partner_sign' => 'system',
			'directory'    => 'finance'
		];
		$icoArr = $imageObj->uploadImage($image, $arr);

        if ($icoArr['success']) {
            $path   = $icoArr['data']['path'];
            $name   = $icoArr['data']['name'];
            return Help::returnApiJson("恭喜, 保存成功!", 1, ['name' => $name, 'path' => configure("system_pic_base_url").'/'.$path]);
        } else {
            return Help::returnApiJson("对不起,保存失败", 0);
        }
    }
}
