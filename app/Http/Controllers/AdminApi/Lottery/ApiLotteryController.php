<?php

namespace App\Http\Controllers\AdminApi\Lottery;

use App\Lib\Help;
use App\Lib\Logic\Cache\AdminCache;
use App\Lib\Logic\Cache\LotteryCache;
use App\Models\Game\Lottery;
use App\Models\Partner\Partner;
use App\Lib\Common\ImageArrange;
use App\Models\Partner\PartnerLottery;
use App\Http\Controllers\AdminApi\ApiBaseController;

/**
 * version 1.0
 * 彩票
 * Class ApiLotteryController
 * @package App\Http\Controllers\AdminApi\Lottery
 */
class ApiLotteryController extends ApiBaseController
{
    public $rules = [
        'cn_name'           => 'required|min:4|max:32',
        'en_name'           => 'required|min:4|max:32',
        'series_id'         => 'required|min:2|max:32',
        'max_trace_number'  => 'required|min:1|max:32',
        'issue_format'      => 'required|min:2|max:32',
    ];

    // 获取游戏列表
    public function lotteryList()
    {
        $c      = request()->all();
        $data   = Lottery::getList($c);

        $seriesList     = config('game.main.series');
        $issueType = config('game.main.issue_type');
        $partnerOption  = Partner::getOptions();
        $partnerOption['system'] = '系統自建';
        $validModes = config('game.main.modes');

        $selfOpenLottery = config("game.self_open_lottery.lottery");
        $_data = [];
        foreach ($data["data"] as $item) {
            $img = lotteryIcon($item->lottery_icon);

            $_data[] = [
                "id"                    => $item->id,
                "img"                   => $img,
                "number_id"             => $item->id,
                "en_name"               => $item->en_name,
                "partner_name"          => isset($partnerOption[$item->partner_sign]) ? $partnerOption[$item->partner_sign] : '官方',
                "cn_name"               => $item->cn_name,
                "series_id"             => $item->series_id,
                "series_name"           => $seriesList[$item->series_id],
                "is_fast"               => $item->is_fast,
                "auto_open"             => $item->auto_open,
                "max_trace_number"      => $item->max_trace_number,
                "day_issue"             => $item->day_issue,
                "issue_format"          => $item->issue_format,
                "issue_type"            => $item->issue_type,
                "diff_prize_group"      => $item->diff_prize_group,
                "max_prize_per_code"    => $item->max_prize_per_code,
                "max_prize_per_issue"   => $item->max_prize_per_issue,
                "valid_modes"           => $item->formatModes(),
                "valid_price"           => $item->formatPrice(),
                'is_hot'                => $item->is_hot,
                "status"                => $item->status,
            ];
        }
        $data['issue_format']       = ['Ymd|N2','Ymd|N3','Ymd|N4','C6','C7','C9','y|T3'];
        $data['data']               = $_data;
        $data['series_option']      = $seriesList;
        $data['partner_option']     = $partnerOption;
        $data['valid_modes']        = $validModes;
        $data['selfOpenLottery']    = $selfOpenLottery;
        $data['valid_code_options']     = config("game.main.valid_code");
        $data['positions_options']      = config("game.main.positions");
        $data['system_pic_base_url']    = configure("system_pic_base_url");
        $data['valid_price_options'] = config('game.main.price');
        $data['issue_type'] = $issueType;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }


    // 添加彩种
    public function lotteryAdd()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        $data       = request()->all();
        $lottery = new Lottery();
        $res = $lottery->lotteryAdd($data);

        if(!$res['res']) {
            return Help::returnApiJson($res, 0,$res['msg']);
        }

        return Help::returnApiJson("恭喜, 添加数据成功！", 1);
    }


    public function lotteryEdit($id){
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        $data       = request()->all();
        $lottery = new Lottery();
        $res = $lottery->lotteryEdit($data, $id);

        if(!$res['res']) {
            return Help::returnApiJson($res, 0,$res['msg']);
        }

        return Help::returnApiJson("恭喜, ！" . $res['msg'], 1);
    }
    // 分配彩种
    public function lotteryAssign($sign)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }

        $model   = Lottery::findBySign($sign);
        if (!$model) {
            return Help::returnApiJson("对不起, 目标对象不存在！", 0);
        }

        if ($model->auto_open) {
            return Help::returnApiJson("对不起, 自开采不能分配！", 0);
        }

        // 彩种 已经存在的商户
        $lotteryArr     = PartnerLottery::where("lottery_sign", $sign)->get();
        $partnerOption  = Partner::getOptions();

        $lotteryOption = [];
        foreach ($lotteryArr as $lottery) {
            $lotteryOption[$lottery->partner_sign] = $partnerOption[$lottery->partner_sign];
        }

        $action = request("action", "process");
        if ($action == "option") {
            $data = [];
            foreach ($partnerOption as $sign => $name) {
                $data['partner_option'][] = [
                    'label'     => $name,
                    'value'     => $sign,
                    'checked'   => isset($lotteryOption[$sign])
                ];
            }

            $data['checked_option'] = array_keys($lotteryOption);
            return Help::returnApiJson("恭喜, 获取数据成功！", 1, $data);
        }

        // 分配
        $partnerSignArr = request("partner_sign", []);
        if (!$partnerSignArr) {
            return Help::returnApiJson("对不起, 无效的商户！", 0);
        }

        foreach ($partnerSignArr as $sign) {
            if (!isset($partnerOption[$sign])) {
                return Help::returnApiJson("对不起, 无效的商户标识{$sign}！", 0);
            }
        }

        // 分配
        $totalAssign = 0;
        foreach ($partnerSignArr as $sign) {
           if (!isset($lotteryOption[$sign])) {
                PartnerLottery::addLotteryToPartner($model, $sign);
               $totalAssign ++;
           }
        }

        return Help::returnApiJson("恭喜, 彩种分配成功{$totalAssign}个！", 1);
    }

    // 获取　游戏　详情
    public function lotteryDetail($sign)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['lottery'] = [];

        // 获取用户
        if ($sign) {
            $lottery = Lottery::findBySign($sign);
            if ($lottery) {
                $data['lottery']    = $lottery;
            }
        }

        $data['series_options']         = config("game.main.series");
        $data['mode_options']           = config("game.main.modes");
        $data['issue_type_options']     = config("game.main.issue_type");
        $data['valid_code_options']     = config("game.main.valid_code");
        $data['positions_options']      = config("game.main.positions");

        return Help::returnApiJson("恭喜, 获取彩种详情成功！", 1, $data);
    }

    // 游戏状态
    public function lotteryStatus($sign)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取彩种
        $lottery = Lottery::findBySign($sign);
        if (!$lottery) {
            return Help::returnApiJson("对不起, 无效的彩种id！", 0);
        }

        $lottery->status = $lottery->status ? 0 : 1;
        $lottery->save();
        if(!$lottery->status)
        {
            PartnerLottery::where('lottery_sign',$sign)->update(['status'=>0,'is_admin_stop'=>1]);
        }
        else
        {
            PartnerLottery::where(['lottery_sign'=>$sign,'is_admin_stop'=>1,'status'=>0])->update(['status'=>1,'is_admin_stop'=>0]);
        }

        AdminCache::cleanLotteryAll($sign);

        return Help::returnApiJson("恭喜, 修改彩种状态成功！", 1, ['status' => $lottery->status]);
    }

    /**
     * 刷新所有彩票缓存
     * @return bool|\Illuminate\Http\JsonResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function lotteryFlush() {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        $partner = Partner::getOptions();

        foreach ($partner as $partnerSign => $name) {
            // 所有彩票配置
            PartnerLottery::flushAllLotteryToFrontEnd($partnerSign);
            LotteryCache::flushPartnerAllLottery($partnerSign);
            Lottery::flushAllLotteryByCache();
            $lotteryArr = PartnerLottery::getOption($partnerSign);

            // 所有玩法配置
            foreach ($lotteryArr as $lotterySign => $lotteryName) {
                LotteryCache::flushPartnerLotteryAllMethodConfig($lotterySign, $partnerSign);
                LotteryCache::flushPartnerLottery($lotterySign, $partnerSign);
            }
        }

        return Help::returnApiJson("恭喜, 刷新成功！", 1);
    }

    /**
     * 游戏编辑
     * @param $lotterySign
     * @return \Illuminate\Http\JsonResponse
     */
    public function lotteryInfoSet($lotterySign)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }
        // 是否存在
        $model   = Lottery::findBySign($lotterySign);
        if (!$model) {
            return Help::returnApiJson("对不起, 彩种对象不存在！", 0);
        }

        $c                  = request()->all();
        $c['valid_price']   = request('valid_price','1,2');

        if ($model->setLottery($c) === true) {
            return Help::returnApiJson('修改数据成功!', 1,  []);
        }
        return Help::returnApiJson('修改数据失败!', 0,  []);
    }

    // 上传图片
    public function lotteryUploadImg() {
		$adminUser = auth("admin_api") -> user();
		if (!$adminUser) {
			return Help ::returnApiJson("对不起, 商户未登录！", 0);
		}
		$imageObj = new ImageArrange();
		$image  = request()->file('file');
		$arr =[
			'partner_sign' => 'system',
			'directory'    => 'lottery'
		];
		$icoArr = $imageObj->uploadImage($image, $arr);

        if ($icoArr['success']) {

            $path   = $icoArr['data']['path'];
            $name   = $icoArr['data']['name'];

            return Help::returnApiJson("恭喜, 保存成功!", 1, ['name' => $name, 'path' => $path]);
        } else {
            return Help::returnApiJson("对不起, 保存失败!", 0);
        }
    }
}
