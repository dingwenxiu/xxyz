<?php
namespace App\Http\Controllers\PartnerApi\Activity;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Lib\Logic\Cache\ActivityCache;
use App\Models\Activity\ActivityRule;
use App\Models\Partner\PartnerActivityRule;

class ApiActivityRuleController extends ApiBaseController
{
    public function getList()
    {
        $c = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $data = PartnerActivityRule::getList($c);
        return Help::returnApiJson('获取数据成功',1,$data);
    }

    public function ruleSet($id)
    {
        $data                 = request()->all();
        $data['partner_sign'] = $this->partnerSign;
        $partnerActivityRule  = new PartnerActivityRule();
        $validator            = $partnerActivityRule->saveItem($data, $id);
        if (!$validator) {
            return Help::returnApiJson($partnerActivityRule->errorMsg, 0, $partnerActivityRule);
        }
        ActivityCache::clearAll($this->partnerSign);
        return Help::returnApiJson('更新活动成功', 1, $partnerActivityRule);
    }

    public function ruleDel($id)
    {
        $partnerActiveStatus = PartnerActivityRule::where('id', $id)->delete();

        if ($partnerActiveStatus) {
            return Help::returnApiJson('删除成功',1);
        }else{
            return Help::returnApiJson('删除失败',0);
        }

    }

    //该活动规则列表不分页
    public function getRule()
    {
//        $activityRule = ActivityRule::whereRaw('FIND_IN_SET(?, open_partner)', [$this->partnerSign])->get();
        $activityRule = ActivityRule::all();
        foreach ($activityRule as $key => $item) {
            $activityRule[$key]['params'] = json_decode($item['params'], 1);
        }

        return Help::returnApiJson('获取成功',1, $activityRule);
    }

    // 上传图片
    public function activityUploadImg() {
		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr =[
			'partner_sign' => $this->partnerSign,
			'directory'    => 'activity'
		];
		$icoArr = $imageObj->uploadImage($image, $arr);
        if ($icoArr['success']) {
            $path   = $icoArr['data']['path'];
            $name   = $icoArr['data']['name'];
            return Help::returnApiJson("恭喜, 保存成功!", 1, ['name' => $name, 'path' => $path]);
        } else {
            return Help::returnApiJson("对不起, 保存失败1!", 0);
        }
    }
}
