<?php

namespace App\Http\Controllers\PartnerApi\Casino;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Casino\CasinoApi;
use App\Lib\Common\ImageArrange;
use App\Models\Account\AccountChangeReport;
use App\Models\Casino\CasinoApiLog;
use App\Models\Casino\CasinoCategorie;
use App\Models\Casino\CasinoMethod;
use App\Models\Casino\CasinoPlatform;
use App\Lib\Help;
use App\Models\Casino\CasinoPlayerBet;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerCasinoPlatform;


class ApiCasinoController extends ApiBaseController
{
    /**
     * 获取游戏列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function callGameList()
    {
        $player     = null;
        $partner    = Partner::where('sign', $this->partnerAdminUser->partner_sign)->first();
        $CasinoApi  = new CasinoApi($player, $partner);
        return $CasinoApi->callGameList();
    }

    /**
     * 获取游戏平台
     * @return \Illuminate\Http\JsonResponse
     */
    public function seriesLists()
    {
        $player     = null;
        $partner    = Partner::where('sign', $this->partnerAdminUser->partner_sign)->first();
        $CasinoApi  = new CasinoApi($player, $partner);
        return $CasinoApi->seriesLists();
    }

    /**
     * 游戏列表
     * @return mixed
     */
    public function getGameList()
    {
        $c       = request()->all();
        $partner = Partner::where('sign', $this->partnerAdminUser->partner_sign)->first();

        return CasinoMethod::getList($c, $partner);
    }

    /**
     * 修改游戏状态
     */
    public function gameControl () {
        $id = request('id');
        if (!$id || !CasinoMethod::find($id)) {
            return Help::returnApiJson('游戏信息错误', 0);
        }

        $game = CasinoMethod::find($id);
        $game->status = $game->status ? 0 : 1;
        $game->save();

        return Help::returnApiJson("恭喜, 修改游戏状态成功！", 1);
    }


    /**
     * 获取游戏平台跟类型
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlatType()
    {
        $CasinoPlatform     = CasinoPlatform::getOptions($this->partnerAdminUser);
        $CasinoCategories   = CasinoCategorie::getOptions();

        return Help::returnApiJson('成功获取平台', 1, ['plat_form' => $CasinoPlatform, 'categories' => $CasinoCategories]);
    }

    /**
     * 设置首页显示游戏
     * @return \Illuminate\Http\JsonResponse
     */
    public function setHomeGame()
    {
        $c = request()->all();
        if (CasinoMethod::setHomeShow($c)) {
            return Help::returnApiJson('设置成功', 1, []);
        }

        return Help::returnApiJson('设置失败--可能此款游戏不存在', 0, []);
    }

    /**
     * 设置首页显示类型
     * @return \Illuminate\Http\JsonResponse
     */
    public function setHomePlat()
    {
        $c = request()->all();
        if (CasinoPlatform::setHomeShow($c)) {
            return Help::returnApiJson('设置成功', 1, []);
        }

        return Help::returnApiJson('设置失败--可能此平台不存在', 0, []);
    }

    public function setHomeCategories()
    {
        $c = request()->all();
        if (CasinoCategorie::setHomeShow($c)) {
            return Help::returnApiJson('设置成功', 1, []);
        }

        return Help::returnApiJson('设置失败--可能此平台不存在', 0, []);
    }

    /**
     * 投注记录
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBetLog() {
        $c       = request()->all();
        $partner = Partner::where('sign', $this->partnerAdminUser->partner_sign)->first();
        $data    = CasinoPlayerBet::getList($c, $partner);

        return Help::returnApiJson('成功获取平台', 1, $data);
    }

    /**
     * 接口记录
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApiLog()
    {
        $c       = request()->all();
        $partner = Partner::where('sign', $this->partnerAdminUser->partner_sign)->first();
        $data    = CasinoApiLog::getList($c, $partner);

        return Help::returnApiJson('成功获取平台', 1, $data);
    }

    /**
     * 数据表统计
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $c       = request()->all();
        $partner = Partner::where('sign', $this->partnerAdminUser->partner_sign)->first();
        $data    = CasinoPlayerBet::getStatistics($c, $partner);

        return Help::returnApiJson('成功获取平台', 1, $data);
    }

    // 上传平台logo图片
    public function uploadImage()
    {
        //获取前台传入参数
        $c = request()->all();
        $platforms = $c['platforms'] ?? '';

		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr =[
			'partner_sign' => $this->partnerSign,
			'directory'    => 'logo',
			'filename'	   => $platforms
		];

		$icoArr = $imageObj->uploadImage($image, $arr);

        if ($icoArr['success']) {
            $path   = $icoArr['data']['path'];

            //执行添加
            PartnerCasinoPlatform::where(['partner_sign' => $this->partnerSign, 'main_game_plat_code' => $platforms])->update(['image' => $path]);
            return Help::returnApiJson('编辑成功!', 1, ['path' => $path]);
        } else {
            return Help::returnApiJson('对不起, 保存失败', 0);
        }
    }


    // 删除平台图片

    public function deleteImage()
    {
        $c = request()->all();
        $platforms = $c['platforms'] ?? '';
        PartnerCasinoPlatform::where(['partner_sign' => $this->partnerSign, 'main_game_plat_code' => $platforms])->update(['image' => '']);
        return Help::returnApiJson('删除成功', 1, []);
    }

    public function setCasinoNavigation()
    {
        $c       = request()->all();
        $partner = Partner::where('sign', $this->partnerAdminUser->partner_sign)->first();
        $data    = CasinoApiLog::getList($c, $partner);

        return Help::returnApiJson('设置成功', 1, $data);
    }

    public function getTransfer()
    {
        $c                  = request()->all();
        $c['partner_sign'] = $this->partnerAdminUser->partner_sign;
        $c['type']         = ['casino_transfer_in', 'casino_transfer_out'];
        $data    = AccountChangeReport::getList($c);

        return Help::returnApiJson('成功获取平台', 1, $data);
    }


    // 娱乐城广告图上传
    public function adImgUpload()
    {
		//获取前台传入参数
		$c = request()->all();
		$c['platforms'] = request('platforms');
		$c['method_id'] = request('method_id');

		if (!$c['platforms'] || !$c['method_id']) {
			return Help::returnApiJson('请输入正确信息', 0);
		}

		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr =[
			'partner_sign' => $this->partnerSign,
			'directory'    => 'CasinoAdImg',
		];

		$icoArr = $imageObj->uploadImage($image, $arr);

        if ($icoArr['success']) {
            $path   = $icoArr['data']['path'];
                CasinoMethod::where('partner_sign', $this->partnerAdminUser->partner_sign)
                    ->where('main_game_plat_code', $c['platforms'])
                    ->where('id', $c['method_id'])
                    ->update(['ad_img' => $path]);
                return Help::returnApiJson('编辑成功!', 1, ['path' => $path]);
        }

        return Help::returnApiJson('对不起,图片上传失败', 0);
    }

    // 娱乐城广告图片删除
    public function adImgDelete()
    {
        //获取前台传入参数
        $c = request()->all();
        $c['id'] = request('id');

        if (!$c['id']) {
            return Help::returnApiJson('请输入正确信息', 0);
        }

        CasinoMethod::where('partner_sign', $this->partnerAdminUser->partner_sign)
            ->where('id', $c['id'])
            ->update(['ad_img' =>  '']);

        return Help::returnApiJson('恭喜,删除成功', 1);
    }


    // 娱乐城游戏图片上传
    public function casinoGameImgUpload()
    {
        //获取前台传入参数
		//获取前台传入参数
		$c = request()->all();
		$c['platforms'] = request('platforms');
		$c['method_id'] = request('method_id');

		if (!$c['platforms'] || !$c['method_id']) {
			return Help::returnApiJson('请输入正确信息', 0);
		}

		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr =[
			'partner_sign' => $this->partnerSign,
			'directory'    => 'CasinoGameImg',
		];

		$icoArr = $imageObj->uploadImage($image, $arr);

		if ($icoArr['success']) {
            $path   = $icoArr['data']['path'];
            if ($method = CasinoMethod::where('partner_sign', $this->partnerAdminUser->partner_sign)->where('main_game_plat_code', $c['platforms'])->where('id', $c['method_id'])->first()) {
                CasinoMethod::where('partner_sign', $this->partnerAdminUser->partner_sign)
                    ->where('main_game_plat_code', $c['platforms'])
                    ->where('id', $c['method_id'])
                    ->update(['img' => $path]);
                return Help::returnApiJson('编辑成功!', 1, ['path' => $path]);
            }else {
                return Help::returnApiJson('对不起,保存失败', 0);
            }
        }

        return Help::returnApiJson('恭喜,图片上传成功', 1);
    }


    // 娱乐城游戏图片删除
    public function casinoGameImgDelete()
    {
        //获取前台传入参数
        $c = request()->all();
        $c['id'] = request('id');

        if (!$c['id']) {
            return Help::returnApiJson('请输入正确信息', 0);
        }

        CasinoMethod::where('partner_sign', $this->partnerAdminUser->partner_sign)
            ->where('id', $c['id'])
            ->update(['img' =>  '']);

        return Help::returnApiJson('恭喜,删除成功', 1);
    }

}
