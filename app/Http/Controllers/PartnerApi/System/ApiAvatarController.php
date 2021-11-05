<?php

namespace App\Http\Controllers\PartnerApi\System;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminUser;
use Illuminate\Support\Facades\Validator;
use App\Models\Player\PlayerAvatarImg;

/**
 * 玩家头上传
 * Class ApiAvatarController
 * @package App\Http\Controllers\PartnerApi\System
 */
class ApiAvatarController extends ApiBaseController
{

    /**
     * 头像设置
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAvatar () {
        $c = request()->all();
        $c["partner_sign"] = $this->partnerSign;

        $playerAvatarconfig = new PlayerAvatarImg();
        $msg = $playerAvatarconfig->saveItem($c);
        if($msg!==true)
        {
            return Help::returnApiJson($msg, 0);
        }

        return Help::returnApiJson('设置成功!', 1);
    }


    /**
     * 头像列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function playerAvatarList()
    {
        $partnerSign = $this->partnerSign;
        $data = PlayerAvatarImg::where('partner_sign',$partnerSign)->orderBy('id','asc')->get();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 删除头像
     * @return \Illuminate\Http\JsonResponse
     */
    public function avatarImgDel () {
        $id = request('id');
        if (!$id) {
            return Help::returnApiJson('对不起, 头像ID错误', 0);
        }

        $img = PlayerAvatarImg::find($id);
        if (!$img) {
            return Help::returnApiJson('对不起, 头像信息错误', 0);
        }

        PlayerAvatarImg::where('id', $id)->delete();
        return Help::returnApiJson('删除成功!', 1);
    }

	/**
	 * 商户管理员头像上传
	 * @param $id
	 * @return mixed
	 */
	public function adminAvatarImgUpload($id)
	{
		// 获取用户
		$user = PartnerAdminUser::find($id);
		if (!$user) {
			return Help::returnApiJson("对不起, 无效的用户id！", 0);
		}

		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr =[
			'partner_sign' => $user->partner_sign,
			'directory'    => 'AdminUserAvatar',
			'filename'     => $user->username
		];

		$icoArr = $imageObj->uploadImage($image, $arr);

		if ($icoArr['success']) {
			$path   = $icoArr['data']['path'];
			PartnerAdminUser::where('partner_sign', $user->partner_sign)
				->where('id', $id)
				->update(['avatar' => $path]);
			return Help::returnApiJson('恭喜,头像上传成功!', 1, ['path' => $path]);
		}

		return Help::returnApiJson('对不起,图片上传失败', 0);
	}

	/**
	 * 商戶Logo展示
	 */
	public function getPartnerLogo (){

		$res = Partner::where('sign', $this->partnerSign)->first();
		if (!$res) {
			return Help::returnApiJson('无效商户信息',0);
		}

		$logo = $res->logo_image_partner;
		return Help::returnApiJson('恭喜,获取商户logo成功', 1, $logo);
	}

	/**
	 * 管理员头像删除
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function adminAvatarImgDel ($id){
		$res = PartnerAdminUser::where('partner_sign', $this->partnerSign)->where('id',$id)->first();
		if (!$res) {
			return Help::returnApiJson('无效商户信息',0);
		}

		$logo = PartnerAdminUser::where('partner_sign', $this->partnerSign)->where('id',$id)->update(['avatar' => '']);
		if (true != $logo){
			return Help::returnApiJson('头像删除失败',0);
		}
		return Help::returnApiJson('恭喜,删除头像成功', 1);
	}

}
