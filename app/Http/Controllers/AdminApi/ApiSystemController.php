<?php

namespace App\Http\Controllers\AdminApi;

use App\Lib\Logic\Cache\AdminCache;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Models\Admin\AdminActionReview;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerConfigure;
use App\Models\Player\Player;
use App\Lib\Help;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Models\Admin\AdminAccessLog;
use App\Models\Admin\AdminUser;
use App\Models\Admin\Cache;
use App\Models\Admin\Configure;
use App\Models\Partner\PartnerNotice;
use App\Models\System\SysTelegramChannel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ApiSystemController extends ApiBaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function encode()
    {
        $params = request()->all();
        return IssueLogic::officialEncode($params);
    }

    // 获取玩家列表
    public function adminLogList()
    {
        $c = request()->all();
        $data = AdminAccessLog::getList($c);

        $data['admin_user'] = AdminUser::getAdminUserOptions();

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /** ============================= 配置列表============================= */

    // 获取配置列表
    public function configureList()
    {
        $c = request()->all();
        !isset($c['pid']) ? $c['pid'] = 0 : "";
        $data = Configure::getList($c);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    public function getSystemConfigureList()
    {

        $c = request()->all();

        $allData = Configure::getAllConfigureList($c);

        $data['data'] = array_values($allData);
        // $data['parent_option']  = Configure::getConfigParentOption();

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }


    /**
     * 配置详情
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function configureDetail($id = 0)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 是否存在
        $config = Configure::find($id);
        if (!$config) {
            return Help::returnApiJson("对不起, 无效的配置！", 0);
        }

        return Help::returnApiJson("恭喜, 获取详情数据成功！", 1, $config);
    }

    /**
     * 添加配置
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function configureAdd($id = 0)
    {

        // 是编辑
        if ($id) {
            $config = Configure::find($id);
            if (!$config) {
                return Help::returnApiJson("对不起, 配置ID对应数据不存在", 0);
            }
        } else {
            $config = new Configure();
        }

        // 奖金组设置 限定
		$value = request('value');
		$min = Configure::where('sign', 'player_register_min_prize_group')->first();
		$max = Configure::where('sign', 'player_register_max_prize_group')->first();
		// 注册默认奖金组
        if ($config->sign == 'player_open_register_default_group') {
			if (isset($value) && $value < $min->value){
				return Help::returnApiJson("对不起,最小值不能小于{$min->value}",0);
			} else if (isset($value) && $value > $max->value) {
				return Help::returnApiJson("对不起,最大值不能大于{$max->value}",0);
			}
		}
		// 注册最小奖金组
        if ($config->sign == 'player_register_min_prize_group') {
			$mins = DB::table('users')->min('prize_group');
			if (isset($value) && $value > $max->value) {
				return Help::returnApiJson("对不起,最小值不能大于{$max->value}",0);
			}
			// 如果小于默认奖金组
			if (isset($value) &&  $value > $mins) {
				return Help::returnApiJson("对不起,最小值不能大于当前用户最低值{$mins}",0);
			}
		}
        //  注册最大奖金组
        if ($config->sign == 'player_register_max_prize_group') {
			$max = DB::table('users')->max('prize_group');
			if (isset($value) && $value < $min->value) {
				return Help::returnApiJson("对不起,最大值不能小余{$min->value}",0);
			}
			// 如果小于当前用户最大奖金组
			if (isset($value) &&  $value < $max) {
				return Help::returnApiJson("对不起,最大值不能小于当前用户用最高注册值{$max}",0);
			}
		}


        // 存在上级  這裡傳入pid 還是 千位數
        $pid = request("pid", 0);
        if ($pid && $pid > 0) {

            if ($id && $id > 0) {
                $configParent = Configure::find($pid);
                if (!$configParent) {
                    return Help::returnApiJson("对不起, 配置上级ID对应数据不存在", 0);
                }
            } else {
                $configParent = Configure::find($pid);
            }
        } else {
            // 不存在上级
            $configParent = '';
        }

        $params = request()->all();
        $res = $config->saveItem($configParent, $params);

        if ($res === true) {
            $msg = $id ? '编辑' : '添加';
            return Help::returnApiJson("恭喜, {$msg}配置成功!!", 1);
        }

        ConfigureCache::clearSystemConfigureCache();
        return Help::returnApiJson($res, 0);
    }

    // 配置状态
    public function configureStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $config = Configure::find($id);
        if (!$config) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        $config->status = $config->status ? 0 : 1;
        $config->save();

        if ($config->pid == 0 && $config->status == 0) {
            $ids = substr($id,'0','1');
            Configure::where('pid', $ids)->update(['status' => 0]);
        }

        if ($config->pid != 0 && $config->status == 1) {
            $id = $config->pid * 1000;
            Configure::where('id', $id)->update(['status' => 1]);
        }


        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    // 配置状态
    public function configureFlush()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        ConfigureCache::clearSystemConfigureCache();

        return Help::returnApiJson("恭喜, 刷新成功！", 1);
    }

	/** ============================= 商户配置============================= */
	// 获取配置列表
	public function partnerConfigureList()
	{
		$c      = request()->all();
		$c['partner_sign']  = request('partner_sign');
		if (!$c['partner_sign']){
			$partner = Partner::where('status', 1)->first();
			$c['partner_sign'] = $partner->sign;
		}else {
			$partner = Partner::where('sign',$c['partner_sign'])->where('status', 1)->first();
		}

		$allData   = PartnerConfigure::getConfigList($c);
        // 是否需要审核
        $config = config("admin.main.review_type");
        $_data = PartnerConfigure::where('partner_sign',$c['partner_sign'])->get();

        $datas = [];
        foreach ($_data as $value) {
            $value[$value->sign] = 0;
            if (array_key_exists($value->sign, $config)) {
                $value[$value->sign] = 1;
            }
            $datas [] = [$value->sign => $value[$value->sign]];
        }

        $datas = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($datas)), true);

        $data['data']  = array_values($allData);
		$data['partner_option']     = Partner::getOptions();
        $data['process_option']     = $datas;


		$data['partner_name'] = $partner->name;
		return Help::returnApiJson('获取数据成功!', 1,  $data);
	}

	// 获取配置详情
	public function partnerConfigureDetail($id)
	{
		$adminUser = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}
		
		$config = PartnerConfigure::find($id);
		if (!$config) {
			return Help::returnApiJson("对不起, 无效的配置！", 0);
		}

		return Help::returnApiJson("恭喜, 获取详情数据成功！", 1, $config);
	}

	// 添加配置
	public function partnerConfigureAdd()
	{
		$adminUser = auth()->guard('admin_api')->user();
		if (!$adminUser) {
			return Help::returnApiJson("对不起, 用户不存在！", 0);
		}

		$c      = request()->all();

		$id     = $c['id'];
		$sign   = $c['sign'];
		$value  = $c['value'];
		$partnerSign = $c['partner_sign'];

        if (!$sign) {
            return "对不起, Sign不存在!!";
        }

        // 3. sign 不能重复
        if (isset($id) && $id) {
            $_config = PartnerConfigure::where("partner_sign", $partnerSign)->where('id', '!=', $id)->where('sign', $sign)->first();
            if ($_config) {
                return "对不起, Sign已经存在!!";
            }
        }

        // 4. 名称 不能重复
        if (isset($c['name']) && $c['name']) {
            $_config = PartnerConfigure::where("partner_sign", $partnerSign)->where('id', '!=', $id)->where('name', $c["name"])->first();
            if ($_config) {
                return Help::returnApiJson('对不起,名称重复', 0);
            }
        }



		switch ($sign) {
			case "player_default_type":
				if ($value != 2 && $value != 3) {
					return Help::returnApiJson("配置值只能为2 3！", 0);
				}
				break;
			case 'player_vip_img_display':
				if ($value != 1 && $value != 0) {
					return Help::returnApiJson("配置值只能为0 1！", 0);
				}
				break;
		}

		$partnerConfigure = PartnerConfigure::where('partner_sign',$partnerSign)->where('id', $id)->first();
		if(!$partnerConfigure)
		{
			return Help::returnApiJson('配置不存在!', 0);
		}

		$min = PartnerConfigure::where('sign', 'player_register_min_prize_group')->where('partner_sign',$partnerSign)->first();
		$max = PartnerConfigure::where('sign', 'player_register_max_prize_group')->where('partner_sign',$partnerSign)->first();
		if ($partnerConfigure->sign == 'player_open_register_default_group') {
			if (isset($value) && $value < $min->value){
				return Help::returnApiJson("对不起,最小值不能小于{$min->value}",0);
			} else if (isset($value) && $value > $max->value) {
				return Help::returnApiJson("对不起,最大值不能大于{$max->value}",0);
			}
		}
		if ($partnerConfigure->sign == 'player_register_min_prize_group') {
			$mins = DB::table('users')->where('partner_sign', $partnerSign)->min('prize_group');
			if (isset($value) && $value > $max->value) {
				return Help::returnApiJson("对不起,最小值不能大于{$max->value}",0);
			}
			// 如果小于默认奖金组
			if (isset($value) &&  $value > $mins) {
				return Help::returnApiJson("对不起,最小值不能大于当前用户用最低注册值{$mins}",0);
			}
		}
		if ($partnerConfigure->sign == 'player_register_max_prize_group') {
			$max = DB::table('users')->where('partner_sign', $partnerSign)->max('prize_group');
			if (isset($value) && $value < $min->value) {
				return Help::returnApiJson("对不起,最大值不能小余{$min->value}",0);
			}
			// 如果小于当前用户最大奖金组
			if (isset($value) &&  $value < $max) {
				return Help::returnApiJson("对不起,最大值不能小于当前用户用最高注册值{$max}",0);
			}
		}

		// 是否需要审核
		$config = config("admin.main.review_type");
		$type = $sign;
		// 需要审核
		if (array_key_exists($type, $config)) {
            // 审核描述
		    $c['request_desc'] = request('request_desc');
		    if (!$c['request_desc']) {
		        return Help::returnApiJson('对不起,请输入审核描述',0);
            }
			$res = AdminActionReview::addReview($c , $type, $adminUser);
			if ($res !== true) {
				return Help::returnApiJson($res, 0);
			}
			return Help::returnApiJson("恭喜, 操作已提交, 等待风控审核!", 1);
		}


        if (isset($c["can_edit"]) && $c["can_edit"] == 1) {
            $c["can_show"] = 1;
        }

        if ($partnerConfigure->pid != 0 && (isset($c['can_edit']) && $c['can_edit'] == 1 )|| (isset($c['can_show']) && $c['can_show'] == 1)) {

            PartnerConfigure::where('id', $partnerConfigure->pid)->update(['can_show' => 1]);
        }

        // 保存
		$partnerConfigure->saveItem($c,$partnerSign, $adminUser);

		if($partnerConfigure->sign=="player_salary_rate_max")
		{
			$ids = Player::where('partner_sign',$partnerSign)->where('salary_percentage','>',$partnerConfigure->value)->pluck('id')->toArray();
			if(count($ids))
			{
				Player::where('partner_sign',$partnerSign)->whereIn('id',$ids)->update(['salary_percentage'=>$partnerConfigure->value]);
			}
		}

		if($partnerConfigure->sign=="player_bonus_rate_max")
		{
			$ids = Player::where('partner_sign',$partnerSign)->where('bonus_percentage','>',$partnerConfigure->value)->pluck('id')->toArray();
			if(count($ids))
			{
				Player::where('partner_sign',$partnerSign)->whereIn('id',$ids)->update(['bonus_percentage'=>$partnerConfigure->value]);
			}
		}

		ConfigureCache::clearPartnerConfigureCache($partnerSign);
		return Help::returnApiJson("恭喜, 配置更新成功！", 1);
	}

	// 配置状态
	public function partnerConfigureStatus($id)
	{
		$config = PartnerConfigure::find($id);
		// 获取配置
		if (!$config) {
			return Help::returnApiJson("对不起, 无效的配置id！", 0);
		}

		$config->status = $config->status ? 0 : 1;
		$config->save();

        if ($config->pid == 0 && $config->status == 0 ) {
            PartnerConfigure::where('pid', $id)->update(['status' => 0]);
        }

        if ($config->pid != 0 && $config->status == 1 ) {
            PartnerConfigure::where('id', $config->pid)->where('pid', 0)->update(['status' => 1]);
        }


        ConfigureCache::clearPartnerConfigureCache($config->partner_sign);
		return Help::returnApiJson("恭喜, 修改状态成功！", 1);
	}

	/**
	 * 刷新商户缓存
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function partnerConfigureFlush() {
		$partnerSign = request('partner_sign');
		if (!$partnerSign) {
			return Help::returnApiJson('请填写商户标示',0);
		}
		$res = Partner::where('sign',$partnerSign)->first();
		if (!$res) {
			return Help::returnApiJson('错误商户标示',0);
		}
		ConfigureCache::clearSystemConfigureCache();
		ConfigureCache::clearPartnerConfigureCache($partnerSign);
		return Help::returnApiJson("恭喜, 刷新缓存成功！", 1);
	}

    /** ============================= 频道管理============================= */
    /**
     * 小飞机列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function telegramChannelList()
    {
        $c = request()->all();
        $data = SysTelegramChannel::getList($c);

        $partnerOptions = Partner::getOptions();

        $_partnerOption = ['system' => "系统"];
        foreach ($partnerOptions as $sign => $name) {
            $_partnerOption[$sign] = $name;
        }

        foreach ($data['data'] as $k => $item) {
            $item->partner_sign = isset($_partnerOption[$item->partner_sign]) ? $_partnerOption[$item->partner_sign] : '';
        }
        $data['partner_option'] = $_partnerOption;
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 编辑名称
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function telegramChannelEdit($id)
    {
        $channelName = request('channel_group_name', '');

        $channel = SysTelegramChannel::find($id);
        if (!$channel) {
            return Help::returnApiJson('无效的ID!', 0);
        }

        // 不能重复　和　必须下划线开头
        if ($channelName != $channel->channel_group_name) {
            $count = SysTelegramChannel::where('channel_group_name', $channelName)->count();
            if ($count > 1) {
                return Help::returnApiJson('对不起, 渠道名称不能重复!', 0);
            }

            $channelNameArr = explode("_", $channelName);
            if ($channelNameArr[0] != $channel->partner_sign) {
                return Help::returnApiJson('对不起, 渠道名称必须以标识和下划线开头!', 0);
            }
        }
        $channel->channel_group_name = $channelName;
        $channel->save();

        return Help::returnApiJson('操作成功!', 1);

    }

    /**
     * 添加/编辑频道F
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function telegramChannelAdd($id)
    {
        $params = request()->all();
        if ($id) {
            $model = SysTelegramChannel::find($id);
            if (!$model) {
                return Help::returnApiJson("对不起, ID对应数据不存在", 0);
            }
        } else {
            $validator = Validator::make($params, ['channel_group_name' => 'required', 'channel_sign' => 'required']);
            if ($validator->fails()) {
                return $validator->errors()->first();
            }
            $params['partner_sign'] = 'system';
            $params['channel_id'] = '';
            $params['status'] = 1;
            $model = new SysTelegramChannel();
        }
        if (isset($params['channel_group_name'])) {
            $count = SysTelegramChannel::where('channel_group_name', $params['channel_group_name'])->count();
            if ($count > 1) {
                return Help::returnApiJson('对不起, 渠道名称不能重复!', 0);
            }
        }

        if (isset($params['channel_sign'])) {

            //編輯
            if ($id) {
                $partner = Partner::findPartnerBySign($model->partner_sign);
                $count = SysTelegramChannel::where('channel_sign', $params['channel_sign'])
                    ->where('partner_sign',  $model->partner_sign)
                    ->where('channel_group_name',$params['channel_group_name'])
                    ->count();
                if($count == 1){ //自己修改自己的數值相同不影響
                    $count =0;
                }elseif ($count == 0){
                    $count = SysTelegramChannel::where('channel_sign', $params['channel_sign'])
                        ->where('channel_group_name',$params['channel_group_name'])
                        ->count();
                }
            } else { //新增
                $count = SysTelegramChannel::where('channel_sign', $params['channel_sign'])->count();

            }

        }
        if ($count >= 1) {
            return Help::returnApiJson('对不起, 渠道标识不能重复!', 0);
        }

        $res = $model->systemSaveItem($params);
        if ($res === true) {
            $msg = $id > 0 ? "编辑数据" : "添加数据";
            return Help::returnApiJson("恭喜, {$msg}频道成功!!", 1);
        }

        return Help::returnApiJson($res, 0);
    }

    // 配置状态
    public function telegramChannelStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取用户
        $model = SysTelegramChannel::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的id！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->save();

        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    /**
     * 频道删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function telegramChannelDel($id)
    {
        $model = SysTelegramChannel::find($id);
        if (!$model) {
            return Help::returnApiJson("对不去, ID对应数据不存在", 0);
        }

        $model->delete();

        return Help::returnApiJson("删除成功!", 1);
    }

    /**
     * 生成ID
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function telegramChannelGenId($id)
    {
        $model = SysTelegramChannel::find($id);
        if (!$model) {
            return Help::returnApiJson("对不去, ID对应数据不存在", 0);
        }

        $res = $model->updateChannelId();
        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson("更新 組Id 成功!", 1);
    }

    /** ============================= 缓存管理============================= */

    // 缓存列表
    public function cacheList()
    {
        $config = config("web.main.cache");

        $data['data'] = Cache::getList();
        $data['config'] = $config;
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 清空缓存
    public function cacheFlush($key)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        if ('all' == $key) {
            $config = config("web.main.cache");
            foreach ($config as $_key => $_c) {
                Cache::_flushCache($_key);
            }
        } else {
            if (!Cache::_hasCache($key)) {
                return Help::returnApiJson("对不起, 不存在的缓存！", 0);
            }
            Cache::_flushCache($key);
        }

        return Help::returnApiJson("恭喜, 刷新缓存成功！", 1);
    }

    /** ============================= 公告列表============================= */

    // 公告列表
    public function noticeList()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $c = request()->all();
        $data = PartnerNotice::getList($c);
        $partnerOptions = Partner::getOptions();
        foreach ($partnerOptions as $sign => $name) {
            $_partnerOption[$sign] = $name;
        }
        $data['partner_option'] = $_partnerOption;
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }


    // 获取公告详情
    public function noticeDetail($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data = [];
        // 获取model
        $model = PartnerNotice::find($id);
        if ($model) {
            $data['notice'] = $model;
        }

        return Help::returnApiJson("恭喜, 获取详情数据成功！", 1, $data);
    }


    // 刷新公告
    public function noticeFlush()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        PartnerNotice::flushCache("notice");

        return Help::returnApiJson("恭喜, 刷新缓存成功！", 1);
    }

    /** ============================== 在线人数 ============================ */
    //在线人数
    public function online()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $_onlineArray = AdminCache::onlineArray();

        $count = 0;
        foreach ($_onlineArray as $value) {
            if (!is_null($value))
                $count = 0;
            {
                $count += 1;
            }
        }
        $location = GeoIP::getLocation(real_ip());
        $area = $location['country'] . '|' . $location['city'];

        return Help::returnApiJson('获取在线人数成功', 1, ['count' => $count, 'ip' => real_ip(), 'area' => $area]);
    }


	function getTime($server){
		$data  = "HEAD / HTTP/1.1\r\n";
		$data .= "Host: $server\r\n";
		$data .= "Connection: Close\r\n\r\n";
		$fp = fsockopen($server, 80);
		fputs($fp, $data); $resp = '';
		while ($fp && !feof($fp)) $resp .= fread($fp, 1024);
		preg_match('/^Date: (.*)$/mi',$resp,$matches);
		return strtotime($matches[1]);
	}
}
