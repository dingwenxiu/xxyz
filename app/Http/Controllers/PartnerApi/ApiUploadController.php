<?php
namespace App\Http\Controllers\PartnerApi;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use Illuminate\Support\Facades\Validator;
use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Lib\Oss\OssTrait;
/**
* $type 类型 活动 1  彩票 2 配置 3
*
*
*/
//商户后台上传接口
class ApiUploadController extends ApiBaseController
{
    use OssTrait;
    
    public function uploadImg() {

        $params = request()->all();

        $imgNewName = request()->get('img_new_name','');
        if(!isset($params['type'])) {
        	return Help::returnApiJson('对不起,type不能为空,请输入文件的type类型',0);
        }
        
        $type        = $params['type'];

		$directory = '';
        switch ($type) {
			case '1':
				$directory = 'activity';
				break;
			case '2':
				$directory = 'lottery';
				break;
			case '3':
				$directory = 'configure';
				break;
			case '4':
				$directory = 'logo';
				break;
			case '5':
				$directory = 'finance';   // 支付图标
				break;
			case '6':
				$directory = 'notice';    // 公告
				break;
			case '7':
				$directory = 'avatar';    // 会员头像
				break;
			case '8':
				$directory = 'adImg';     // 彩票广告
				break;
			case '9':
				$directory = 'CasinoAdImg';  // 娱乐城广告
				break;
			case '10':
				$directory = 'CasinoGameImg';   // 娱乐城游戏图片
				break;
			case '11':
				$directory = 'partnerLogo';   // 娱乐城游戏图片
                break;
            default:
                break;
        }


		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr['partner_sign'] = $this->partnerSign;
		if (!empty($imgNewName)){
			$arr['filename'] = $imgNewName;
		}
		$arr['directory'] = $directory;

		$icoArr = $imageObj->uploadImage($image, $arr);


        if ($icoArr['success']) {

            $path   = $icoArr['data']['path'];
            $name   = $icoArr['data']['name'];

            return Help::returnApiJson("恭喜, 保存成功!", 1, ['name' => $name, 'path' => $path]);
        } else {
            return Help::returnApiJson("对不起,保存失败", 0);
        }
    }
}
