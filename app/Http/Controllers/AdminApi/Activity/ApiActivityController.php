<?php namespace App\Http\Controllers\AdminApi\Activity;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Activity\ActivityList;
use App\Models\Activity\ActivityRule;
use App\Models\Partner\PartnerActivityRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

/**
 * version 1.0
 * 活动 总预览
 * Class ApiActivityController
 * @package App\Http\Controllers\AdminApi\Activity
 */
class ApiActivityController extends ApiBaseController
{
    // 获取活动列表信息
    public function activityList() {
        $c          = request()->all();
        $ActivityRule = ActivityRule::get(['id', 'type', 'open_partner', 'name']);

        foreach ($ActivityRule as $activeKey => $activeItem) {
            $PartnerActivityRuleOne = in_array($c['partner_sign'], explode(',', $activeItem['open_partner'])) ;

            if ($PartnerActivityRuleOne) {
                $activeItem['open'] = 1;
            } else {
                $activeItem['open'] = 0;
            }
        }

        return Help::returnApiJson('获取数据成功!', 1,  $ActivityRule);
    }

    // 配置商户的活动
    public function activityAdd() {
        $cAll        = request()->all();
        $addId       = $cAll['add_id'];
        $lessId      = $cAll['less_id'];
        $partnerSign = $cAll['partner_sign'];

        $activeAddIdArr = explode(',', $addId);
        $activeLessIdArr = explode(',', $lessId);

        db()->beginTransaction();

        try {
            // 1 清空商户数据
            foreach ($activeLessIdArr as $item) {
                $ActivityRule = ActivityRule::where('id', $item)->first(['id', 'type', 'open_partner', 'name']);

                if (!is_null($ActivityRule)) {

                    $openParnter = explode(',', $ActivityRule->open_partner);
                    if (in_array($partnerSign, $openParnter)) {
                        $unsetKey = array_keys($openParnter,"$partnerSign")[0];
                        unset($openParnter[$unsetKey]);
                        $openParnterStr = implode(',', $openParnter);
                        ActivityRule::where('id', $item)->update(['open_partner' => $openParnterStr]);
                    }
                }
            }


            foreach ($activeAddIdArr as $item) {
                $ActivityRule = ActivityRule::where('id', $item)->first(['id', 'type', 'open_partner', 'name']);

                if (!is_null($ActivityRule)) {
                    $openParnter = explode(',', $ActivityRule->open_partner);
                    if (!in_array($partnerSign, $openParnter)) {
                        $openParnter[]  = $partnerSign;
                        $openParnterStr = implode(',', $openParnter);
                        ActivityRule::where('id', $item)->update(['open_partner' => $openParnterStr]);
                    }
                }

            }
            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            Clog::activeLog("添加活動异常:" . $e->getMessage() . "-" . $e->getLine());
        }

        return Help::returnApiJson('添加数据成功!', 1);

    }

}
