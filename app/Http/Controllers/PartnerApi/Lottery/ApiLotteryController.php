<?php

namespace App\Http\Controllers\PartnerApi\Lottery;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Common\ImageArrange;
use App\Lib\Help;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Cache\PartnerCache;
use App\Models\Casino\CasinoCategorie;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryIssueRule;
use App\Lib\Logic\Lottery\ProjectLogic;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminBehavior;
use App\Models\Partner\PartnerLottery;
use App\Models\Partner\PartnerMethod;
use Illuminate\Support\Facades\Validator;


class ApiLotteryController extends ApiBaseController
{
    // 获取游戏列表
    public function lotteryList()
    {
        $c                  = request()->all();
        $c['partner_sign']  = $this->partnerSign;

        $data   = PartnerLottery::getList($c);
        $partner = Partner::where('sign', $this->partnerSign)->first();

        $seriesList = config('game.main.series');

        $_data = [];
        foreach ($data["data"] as $item) {
            
            if(!empty($item->icon_path)&& substr($item->icon_path, 0,7)=='lottery')
            {
                $imgs = strtolower($this->partnerSign).'/'.$item->icon_path;
            }
            else
            {
                $imgs = $item->icon_path;
            }

            $_data[] = [
                "id"                    => $item->lottery_sign,
                "img"                   => $imgs,
                "number_id"             => $item->id,
                "en_name"               => $item->lottery_sign,
                "cn_name"               => $item->lottery_name,
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
                "system_min_prize_group"=> $item->min_prize_group,
                "system_max_prize_group"=> $item->max_prize_group,
                "valid_modes"           => $item->formatModes(),
                "valid_price"           => $item->formatPrice(),
                'is_hot'                => $item->is_hot,
                "status"                => $item->status,
                "rate"                  => $item->rate,
                "rate_open"             => $item->rate_open,
            ];
        }

        $data['data']           = $_data;
        $data['seriesList']     = $seriesList;
        $data['issueBet_open'] = $partner->rate_open;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 彩票编辑
    public function lotteryInfoSet($lotterySign)
    {
        // 是否存在
        $model   = PartnerLottery::findBySign($this->partnerSign, $lotterySign);
        if (!$model) {
            return Help::returnApiJson("对不起, 彩种对象不存在！", 0);
        }

        // 有没有权限操作
        if ($model->partner_sign != $this->partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
        }

        $c                  = request()->all();
        $c['valid_price']   = request('valid_price','1,2');

        if ($model->setLottery($c) === true) {
            $data = PartnerLottery::where('partner_sign', $this->partnerSign)
                ->where('lottery_sign', $lotterySign)
                ->first([
                    'valid_modes',
                    'min_prize_group',
                    'max_prize_group',
                    'diff_prize_group',
                    'max_prize_per_issue',
                    'lottery_name',
                    'icon_path',
                    'ad_img',
                    'is_hot',
                    'valid_price',
                ])
                ->toArray();
            // 管理员行为记录
            PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'lottery_set', $data);
            return Help::returnApiJson('修改数据成功!', 1,  []);
        }
        return Help::returnApiJson('修改数据失败!', 0,  []);
    }

    public function lotterySetRate($lotterySign)
    {
        // 是否存在
        $model   = PartnerLottery::findBySign($this->partnerSign, $lotterySign);
        if (!$model) {
            return Help::returnApiJson("对不起, 彩种对象不存在！", 0);
        }

        // 有没有权限操作
        if ($model->partner_sign != $this->partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
        }

        $rate       = request("rate");
        $maxRate    = configure("partner_lottery_day_rate_max", 10);
        $minRate    = 0;

        if ($rate > $maxRate || $rate < $minRate) {
            return Help::returnApiJson("对不起, 无效的设置范围[{$minRate} - {$maxRate}]！", 0);
        }

        // 修改
        if ($model->setLottery(['rate' => intval($rate)]) === true) {
            LotteryCache::flushPartnerLottery($lotterySign, $model->partner_sign);
            return Help::returnApiJson('修改数据成功!', 1,  []);
        }

        return Help::returnApiJson('修改数据失败!', 0,  []);
    }

    /**
     * 热门彩种设置
     * @param $lotterySign
     * @return mixed
     */
    public function lotterySet($lotterySign)
    {

        // 是否存在
        $model   = PartnerLottery::findBySign($this->partnerSign, $lotterySign);
        if (!$model) {
            return Help::returnApiJson("对不起, 彩种对象不存在！", 0);
        }

        // 有没有权限操作
        if ($model->partner_sign != $this->partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
        }

        // 修改
        $model->changeStatus();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }


    /**
     * 彩种详情
     * @param $lotterySign
     * @return mixed
     */
    public function lotteryDetail($lotterySign)
    {
        if ($lotterySign) {
            $model   = PartnerLottery::findBySign($this->partnerSign, $lotterySign);
            if (!$model) {
                return Help::returnApiJson("对不起, 无效的彩种！", 0);
            }

            // 有没有权限操作
            if ($model->partner_sign != $this->partnerAdminUser->partner_sign) {
                return Help::returnApiJson("对不起, 您没有操作权限！", 0);
            }

            $data['lottery']            = $model->toArray();
            $data['lottery']['icon_path'] = substr($data['lottery']['icon_path'], 0,7)=='lottery' ? strtolower($this->partnerAdminUser->partner_sign).'/'.$data['lottery']['icon_path'] : $data['lottery']['icon_path'];
            $data['lottery']['ad_img'] = substr($data['lottery']['ad_img'], 0,7)=='lottery' ? strtolower($this->partnerAdminUser->partner_sign).'/'.$data['lottery']['ad_img'] : $data['lottery']['ad_img'];
        }

        $data['series_options']         = config("game.main.series");
        $data['mode_options']           = config("game.main.modes");
        $data['issue_type_options']     = config("game.main.issue_type");
        $data['valid_code_options']     = config("game.main.valid_code");
        $data['positions_options']      = config("game.main.positions");
        $data['system_pic_base_url']    = configure("system_pic_base_url");

        return Help::returnApiJson("恭喜, 获取彩种详情成功！", 1, $data);
    }

    /**
     * @param $lotterySign
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function lotteryStatus($lotterySign)
    {
        // 获取彩种
        $model   = PartnerLottery::findBySign($this->partnerSign, $lotterySign);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的彩种id！", 0);
        }

        // 有没有权限操作
        if ($model->partner_sign != $this->partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
        }

        if($model->is_admin_stop)
        {
             return Help::returnApiJson("对不起, 此游戏厂商已关闭,您无法开启！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->save();

        LotteryCache::flushPartnerLottery($lotterySign,$this->partnerSign);
        LotteryCache::flushPartnerAll($this->partnerSign);

        $partnerSign = $this->partnerSign;
        $templateSign = $this->partner->template_sign;
        // 中奖 热门 导航  热门电游
        PartnerCache::partnerAdminCacheClean($partnerSign, $templateSign);


        return Help::returnApiJson("恭喜, 修改彩种状态成功！", 1, ['status' => $model->status]);
    }

    /**
     * 修改游戏状态
     * @param $lotterySign
     * @return mixed
     */
    public function lotteryPopular($lotterySign)
    {
        // 获取彩种
        $model   = PartnerLottery::findBySign($this->partnerSign, $lotterySign);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的彩种id！", 0);
        }

        $model->is_hot = $model->is_hot ? 0 : 1;
        $model->save();

        return Help::returnApiJson("恭喜, 修改彩种热门成功！", 1, ['status' => $model->status]);
    }

    /** =================================== 玩法 @ 相关 ===================================== */

    // 玩法列表
    public function methodList()
    {
        $c                  = request()->all();
        $c['partner_sign']  = $this->partnerSign;

        $data   = PartnerMethod::getList($c);
        $_data = [];

        $groupNameArr   = config("game.method.group_name");
        $rowNameArr     = config("game.method.row_name");

        $challengeTypeArr = config("game.challenge.type");

        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                    => $item->partner_methods_id,
                "series_id"             => $item->series_id,
                "lottery_name"          => $item->lottery_name,
                "method_name"           => $item->method_name,
                "method_sign"           => $item->method_sign,

                "method_group"          => $item->method_group,
                "method_group_name"     => $groupNameArr[$item->series_id][$item->method_group],
                "group_sort"            => $item->group_sort,

                "method_row"            => $item->method_row,
                "method_row_name"       => $rowNameArr[$item->series_id][$item->method_row],
                "tab_sort"              => $item->tab_sort,

                "method_sort"           => $item->method_sort,

                "challenge_type"        => $item->challenge_type,
                "challenge_type_desc"   => $item->challenge_type ? $challengeTypeArr[$item->challenge_type] : "无",
                "challenge_min_count"   => $item->challenge_min_count,
                "challenge_config"      => unserialize($item->challenge_config),
                "challenge_bonus"       => $item->challenge_bonus,

                "add_time"              => $item->created_at ? date("m-d H:i", strtotime($item->created_at)) :'',
                "update_time"           => $item->updated_at ? date("m-d H:i", strtotime($item->updated_at)) : '',

                "status"                => $item->partner_methods_status,
            ];
        }

        $data['data'] = $_data;
        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩法详情
    public function methodDetail($id)
    {
        $data['method'] = [];

        // 获取用户
        if ($id) {
            $method = PartnerMethod::find($id);
            if ($method) {
                $data['method'] = $method;
            }
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }


    // 玩法开启 / 关闭
    public function methodStatus($id)
    {
        // 获取玩法
        $model   = PartnerMethod::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的彩种id！", 0);
        }

        // 有没有权限操作
        if ($model->partner_sign != $this->partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！".$model->partner_sign.$this->partnerAdminUser->partner_sign, 0);
        }

        $model->modifyStatus();

        return Help::returnApiJson("恭喜, 修改玩法状态成功！", 1, ['status' => $model->status]);
    }

	/**
	 * 设置玩法
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
    public function methodSet($id) {

        $c = request()->all();

        // 获取玩法
        $model   = PartnerMethod::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的玩法id！", 0);
        }

        // 有没有权限操作
        if ($model->partner_sign != $this->partnerAdminUser->partner_sign) {
            return Help::returnApiJson("对不起, 您没有操作权限！", 0);
        }

        $action = request("action", "process");

        // 获取数据
        if ($action == 'option') {
            $data['method']             = $model;
            $data['challenge_type_option']     = config("game.challenge.type");

            return Help::returnApiJson("获取数据成功！", 1, $data);
        }

        foreach ($c as $key => $item) {
            $model->$key = $item;
        }
        $model->save();

        LotteryCache::flushPartnerAllLottery($this->partnerSign);

        return Help::returnApiJson("设置成功！", 1, []);
    }

    /** =================================== 奖期 @ 相关 ===================================== */

    // 奖期列表
    public function issueList()
    {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $data   = LotteryIssue::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                        => $item->id,
                "series_id"                 => $item->series_id,
                "issue"                     => $item->issue,
                "lottery_name"              => $item->lottery_name,
                "lottery_sign"              => $item->lottery_sign,
                "begin_time"                => date("Y-m-d H:i:s", $item->begin_time),
                "end_time"                  => date("Y-m-d H:i:s", $item->end_time),
                "allow_encode_time"         => date("Y-m-d H:i:s", $item->allow_encode_time),
                "official_code"             => $item->official_code,

                "time_open"                 => $item->time_open ? date("Y-m-d H:i:s", $item->time_open) : '',
                "time_send"                 => $item->time_send ? date("Y-m-d H:i:s", $item->time_send) : '',
                "time_trace"                => $item->time_trace ? date("Y-m-d H:i:s", $item->time_trace) : '',
                "time_commission"           => $item->time_commission ? date("Y-m-d H:i:s", $item->time_commission) : '',

                "time_end_open"             => $item->time_end_open ? date("Y-m-d H:i:s", $item->time_end_open) : '',
                "time_end_send"             => $item->time_end_send ? date("Y-m-d H:i:s", $item->time_end_send) : '',
                "time_end_trace"            => $item->time_end_trace ? date("Y-m-d H:i:s", $item->time_end_trace) : '',
                "time_end_commission"       => $item->time_end_commission ? date("Y-m-d H:i:s", $item->time_end_commission) : '',

                "status_process"            => $item->status_process,
                "status_commission"         => $item->status_commission,
                "status_trace"              => $item->status_trace,
                "can_encode"                => $item->status_process == 0 && $item->allow_encode_time > time() ? 1 : 0,
                "encode_username"           => $item->encode_username,
            ];
        }

        $data['data'] = $_data;

        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign, false);
        $data['series_options']     = PartnerLottery::getSeriesLotteryOptions($this->partnerSign);
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 奖期详情
    public function issueDetail($id)
    {
        $adminUser = $this->partnerAdminUser;
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['issue'] = [];

        // 获取用户
        if ($id) {
            $issue = LotteryIssue::find($id);
            if ($issue) {
                $data['issue']    = $issue;
            }
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /** =================================== 奖期规则 @ 相关 ===================================== */

    // 奖期规则 - 列表
    public function issueRuleList()
    {
        $c      = request()->all();
        $data   = LotteryIssueRule::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                => $item->id,
                "lottery_sign"      => $item->lottery_sign,
                "lottery_name"      => $item->lottery_name,
                "begin_time"        => $item->begin_time,
                "end_time"          => $item->end_time,
                "issue_seconds"     => $item->issue_seconds,
                "first_time"        => $item->first_time,
                "adjust_time"       => $item->adjust_time,
                "encode_time"       => $item->encode_time,
                "issue_count"       => $item->issue_count,
                "status"            => $item->status,
            ];
        }

        $data['data'] = $_data;
        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign, false);
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }


    // 彩票上传图片
    public function lotteryUploadImg() {

        $imageObj = new ImageArrange();
        $image  = request()->file('file');
        $arr =[
        	'partner_sign' => $this->partnerSign,
        	'directory'    => 'activity'
		];
        $icoArr = $imageObj->uploadImage($image, $arr);
        if ($icoArr['success']) {
            $path   = $icoArr['data']['path'];
            return Help::returnApiJson("恭喜, 保存成功!", 1, ['name' => $icoArr['data']['name'], 'path' => $path]);
        } else {
            return Help::returnApiJson("对不起, 保存失败!", 0);
        }
    }

    // 彩票广告图片上传
    public function lotteryAdImgUpload()
    {
		$c = request()->all();

		$file            = $c['file'];
		$data['partner_sign'] = $this->partnerSign;
		$data['directory']    = 'lottery';
		$ImageArrange = new ImageArrange();
		$ImageArrangeM = $ImageArrange->uploadImage($file, $data);

        $c['partner_sign'] = $this->partnerSign;
        $c['lottery_sign'] = request('lottery_sign');
        if (!$c['partner_sign'] || !$c['lottery_sign']) {
            return Help::returnApiJson('请输入正确信息', 0);
        }

		try {
			if (!$ImageArrangeM['success']) {
				return Help::returnApiJson($ImageArrangeM['msg'], 0);
			}
			$filename = $ImageArrangeM['data']['path'];

			//执行添加
			PartnerLottery::where('partner_sign', $this->partnerSign)
				->where('lottery_sign', $c['lottery_sign'])
				->update(['ad_img' => $filename]);

			return Help::returnApiJson('保存成功!', 1, ['path' => $filename]);
		} catch (\Exception $e) {
			//删除上传成功的图片
			return Help::returnApiJson('保存失败!' . $e->getMessage() . $e->getLine() . $e->getFile(), 0, []);
		}
    }


    // 彩票广告图片删除
    public function lotteryAdImgDelete()
    {
        //获取前台传入参数
        $c = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $c['lottery_sign'] = request('lottery_sign');

        if (!$c['lottery_sign']) {
            return Help::returnApiJson('请输入正确信息', 0);
        }

        PartnerLottery::where('partner_sign', $this->partnerSign)
            ->where('lottery_sign', $c['lottery_sign'])
            ->update(['ad_img' =>  '']);

        return Help::returnApiJson('恭喜,删除成功', 1);
    }

    // 彩票控水
    public function rateOpen (){
        $selfOpenLottery = config("game.self_open_lottery.lottery");
        $lotterySign = request('lottery_sign');
        if (!$lotterySign) {
            return Help::returnApiJson('请输入正确信息', 0);
        }

        $isOpen = Partner::where('sign', $this->partnerSign)->first();

        if ($isOpen->rate_open == 0) {
            return Help::returnApiJson('对不起,控水并未开启', 0);
        }

        $model = PartnerLottery::where('partner_sign', $this->partnerSign)->where('lottery_sign', $lotterySign)->first();
        if (!$model) {
            return Help::returnApiJson('对不起,无效彩种',0);
        }

        $selfOpen = [];
        foreach ($selfOpenLottery as $item) {
            $selfOpen[] = $item['cn_name'];
        }

        if (!in_array($model->lottery_name, $selfOpen)) {
            return Help::returnApiJson('对不起,此彩种不是自开菜',0);
        }

        $model->rate_open = $model->rate_open ? 0 : 1;
        $model->save();

        return Help::returnApiJson('恭喜,设置成功',1);
    }

}
