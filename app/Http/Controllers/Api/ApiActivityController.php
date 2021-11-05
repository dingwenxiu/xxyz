<?php

namespace App\Http\Controllers\Api;

use App\Lib\Help;
use App\Models\Activity\ActivityPrize;
use App\Models\Activity\ActivityRule;
use App\Models\Partner\PartnerActivityRule;

class ApiActivityController extends ApiBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取活动列表
    public function getLists()
    {
        $c['partner_sign'] = $this->partner->sign;
        $c['typeArr'] = ActivityRule::get('type');
//        $c['typeArr'] = ActivityRule::whereRaw('FIND_IN_SET(?, open_partner)', [$this->partner->sign])->get('type');
        $partnerActivityRule  = PartnerActivityRule::getList($c)['data'];
        $partnerActivityList  = [];
        foreach ($partnerActivityRule as $key => $item) {
            $partnerActivityList[$key]['content_text'] = $item['pc_desc'] ?? '';
            $partnerActivityList[$key]['type']         = $item['type'];
            $partnerActivityList[$key]['name']         = $item['name'];
            $partnerActivityList[$key]['home']         = $item['home'];
            $partnerActivityList[$key]['status']       = $item['status'];
            $partnerActivityList[$key]['login_show']   = $item['login_show'];
            $partnerActivityList[$key]['img']          = $item['img_banner'];
            $partnerActivityList[$key]['start_time']   = $item['start_time'];
            $partnerActivityList[$key]['end_time']     = $item['end_time'];
        }
        return Help::returnApiJson('获取成功', 1, $partnerActivityList);
    }

    public function getPrizes()
    {
        $c['partner_sign'] = $this->partner->sign;
        $data = ActivityPrize::getList($c);
        return Help::returnApiJson('获取成功', 1, $data);
    }

    // 获取详细的活动奖品
    public function getOne()
    {
        $player = auth()->guard('api')->user();

        $params         = request()->all();
        $activityType   = request()->get('type');
        $activityHander = $this->getActivityHandler($activityType, $player, $this->partner->sign, $params);

        if ( ! $activityHander) {
            return Help::returnApiJson('活动类型不存在', 0);
        }
        
        $joinRes = $activityHander->getOne();
        if ($joinRes) {
            return Help::returnApiJson($activityHander->errorMsg, 1, $activityHander->successData);
        }

        return Help::returnApiJson($activityHander->errorMsg, 0, $activityHander->successData);
    }

    //参加活动
    public function joinAct()
    {
        $params = request()->all();
        $player = auth()->guard('api')->user();
        if ( ! $player) {
            return Help::returnApiJson(
                '对不起, 用户未登录!', 0, ['reason_code' => 999],401
            );
        }
        $activityType   = request()->get('type');
        $activityHander = $this->getActivityHandler(
            $activityType, $player, $this->partner->sign, $params
        );

        if ( ! $activityHander) {
            return Help::returnApiJson('活动类型不存在', 0);
        }

        $joinRes = $activityHander->joinAct();
        if ($joinRes) {
            return Help::returnApiJson(
                $activityHander->errorMsg, 1, $activityHander->successData
            );
        }

        return Help::returnApiJson($activityHander->errorMsg, 0, []);
    }

    //获取活动记录
    public function getRecords()
    {
        $params = request()->all();
        $player = auth()->guard('api')->user();
        if ( ! $player) {
            return Help::returnApiJson(
                '对不起, 用户未登录!', 0, ['reason_code' => 999],401
            );
        }

        $activityType   = request()->get('type');
        $activityHander = $this->getActivityHandler(
            $activityType, $player, $this->partner->sign, $params
        );

        if ( ! $activityHander) {
            return Help::returnApiJson('活动类型不存在', 0);
        }

        $actLog = $activityHander->getActLog();

        return Help::returnApiJson('获取成功', 1, $actLog);
    }

    private function getActivityHandler($type, $player, $partner_sign, $params)
    {
        $typeArr = explode('_', str_replace(' ', '', $type));
        $typeNew = '';
        foreach ($typeArr as $item) {
            $typeNew .= ucfirst($item);
        }

        $class = 'App\Lib\Activity\\' . $typeNew . 'Handler';

        return new $class($player, $type, $partner_sign, $params);
    }
}
