<?php

namespace App\Http\Controllers\PartnerApi\System;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Lib\Logic\Cache\PartnerCache;
use App\Models\Admin\AdminAccessLog;
use App\Models\Admin\AdminUser;
use App\Models\Admin\Cache;
use App\Models\Admin\Configure;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminBehavior;
use App\Models\Partner\PartnerConfigure;
use App\Models\Partner\PartnerHome;
use App\Models\Partner\PartnerMenu;
use App\Models\Partner\PartnerModule;
use App\Models\Player\Player;
use GeoIP;
use Illuminate\Support\Facades\DB;

/**
 * 商户系统配置
 * Class ApiSystemController
 * @package App\Http\Controllers\PartnerApi\Partner
 */
class ApiSystemController extends ApiBaseController
{
    // 商户菜单
    public function menu() {
        // 获取权限数据
        $routes     = PartnerMenu::getApiAllRoute();
        return Help::returnApiJson('获取菜单成功', 1, $routes);
    }

    //在线人数
    public function online()
    {
        $adminUser = $this->partnerAdminUser;
        $players = Player::where('partner_sign',$adminUser->partner_sign)->pluck('id')->toArray();
        $onlineArray=[];
        foreach ($players as $value) {
           array_push($onlineArray,$adminUser->partner_sign.'_'.$value);
        }

        $count=0;
        if(count($onlineArray)){
            $_onlineArray = PartnerCache::onlineArray($onlineArray);
            
            foreach ($_onlineArray as $value) {
                if(!is_null($value))
                {
                    $count+=1;
                }
            }
        }
        
        $location   = GeoIP::getLocation(real_ip());
        $area       = $location['country'].'|'.$location['city'];
		$avatar	    = $adminUser->avatar;

        return Help::returnApiJson('获取在线人数成功', 1, ['count'=>$count,'ip'=>real_ip(),'area'=>$area, 'avatar'=>$avatar]);
    }

    // 获取玩家列表
    public function adminLogList()
    {
        $c          = request()->all();
        $data       = AdminAccessLog::getList($c);

        $data['admin_user']     = AdminUser::getAdminUserOptions();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** ============================= 配置列表============================= */

    // 获取配置列表
    public function configureList()
    {
        $c      = request()->all();
        $c['partner_sign']  = $this->partnerSign;

        $allData   = PartnerConfigure::getConfigList($c);

        $data['data']           = array_values($allData);
       // $data['parent_option']  = Configure::getConfigParentOption();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }


    // 获取颜色配置列表
	public function colorConfigureList () {
        $partnerM = Partner::where('sign', $this->partnerSign)->first();
        $templateSign = $partnerM->template_sign ?? '';

    	 $color = PartnerModule::where([
             'partner_sign' => $this->partnerSign,
             'sign' => 'templateColor',
             'template_sign' => $templateSign,
         ])->first();
    	 if (is_null($color)) {
			 return Help::returnApiJson('数据获取失败!',  0);
		 }
		$dataC = PartnerHome::leftjoin('template_colors', 'partner_homes.other_id', '=', 'template_colors.id')
			->where('partner_homes.partner_sign', $this->partnerSign)
			->where('partner_homes.module_id', $color->id)->orderBy('partner_homes.order', 'desc')
			->get(['partner_homes.*', 'template_colors.name as lottery_name']);
		 return Help::returnApiJson('数据获取成功!',  1, $dataC);
	}

	// 修改颜色配置
	public function colorConfigureEdit () {
		$id   = request('id');
		$sign = request('sign');
		$value = request('color');

		if (!$id || !$sign || !$value) {
			return Help::returnApiJson('对不起,错误信息', 0);
		}

		PartnerHome::where('partner_sign', $this->partnerSign)->where('id', $id)->where('partner_sign', $sign)->update(['value' => $value]);

		return Help::returnApiJson('颜色设置成功!', 1);
	}


	// 删除颜色
	public function colorConfigureDelete () {
		$id   = request('id');
		$sign = request('sign');

		if (!$id || !$sign) {
			return Help::returnApiJson('对不起,错误信息', 0);
		}

		PartnerHome::where('id', $id)->where('partner_sign', $sign)->update(['value' => '']);

		return Help::returnApiJson('颜色删除成功!', 1);
	}


    // 获取配置详情
    public function configureDetail($id = 0)
    {
        $data = [];
        // 获取model
        $config = Configure::find($id);
        if ($config) {
            $data['config']    = $config;
        }

        return Help::returnApiJson("恭喜, 获取详情数据成功！", 1, $data);
    }

    // 添加配置
    public function configureAdd()
    {
        $c      = request()->all();

        $id    = $c['id'];
        $sign  = $c['sign'];
        $value = $c['value'];
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

        $partnerConfigure = PartnerConfigure::where('partner_sign',$this->partnerSign)->where('id', $id)->first();
        if(!$partnerConfigure)
        {
           return Help::returnApiJson('配置不存在!', 0);
        }

		$min = PartnerConfigure::where('sign', 'player_register_min_prize_group')->where('partner_sign',$this->partnerSign)->first();
		$max = PartnerConfigure::where('sign', 'player_register_max_prize_group')->where('partner_sign',$this->partnerSign)->first();
		// 注册默认奖金组
		if ($partnerConfigure->sign == 'player_open_register_default_group') {
			if (isset($value) && $value < $min->value){
				return Help::returnApiJson("对不起,最小值不能小于{$min->value}",0);
			} else if (isset($value) && $value > $max->value) {
				return Help::returnApiJson("对不起,最大值不能大于{$max->value}",0);
			}
		}
		// 注册最小奖金组
		if ($partnerConfigure->sign == 'player_register_min_prize_group') {
			$mins = DB::table('users')->where('partner_sign', $this->partnerSign)->min('prize_group');
			if (isset($value) && $value > $max->value) {
				return Help::returnApiJson("对不起,最小值不能大于{$max->value}",0);
			}
			// 如果小于默认奖金组
			if (isset($value) &&  $value > $mins) {
				return Help::returnApiJson("对不起,最小值不能大于当前用户用最低注册值{$mins}",0);
			}
		}
		// 注册最大奖金组
		if ($partnerConfigure->sign == 'player_register_max_prize_group') {
			$max = DB::table('users')->where('partner_sign', $this->partnerSign)->max('prize_group');
			if (isset($value) && $value < $min->value) {
				return Help::returnApiJson("对不起,最大值不能小余{$min->value}",0);
			}
			// 如果小于当前用户最大奖金组
			if (isset($value) &&  $value < $max) {
				return Help::returnApiJson("对不起,最大值不能小于当前用户用最高注册值{$max}",0);
			}
		}


        $partnerConfigures = PartnerConfigure::where('partner_sign', $this->partnerSign)
            ->where('id', $id)
            ->first(['sign', 'name', 'value', 'status', 'description'])
            ->toArray();
        // 管理员行为记录
        PartnerAdminBehavior::saveItem($this->partnerAdminUser, 'configure', $partnerConfigures);
        
        // 保存
        $partnerConfigure->saveItem($c,$this->partnerSign, $this->partnerAdminUser);

        if($partnerConfigure->sign=="player_salary_rate_max")
        {
            $ids = Player::where('partner_sign',$this->partnerSign)->where('salary_percentage','>',$partnerConfigure->value)->pluck('id')->toArray();
            if(count($ids))
            {
                Player::where('partner_sign',$this->partnerSign)->whereIn('id',$ids)->update(['salary_percentage'=>$partnerConfigure->value]);
            }
        }

        if($partnerConfigure->sign=="player_bonus_rate_max")
        {
            $ids = Player::where('partner_sign',$this->partnerSign)->where('bonus_percentage','>',$partnerConfigure->value)->pluck('id')->toArray();
            if(count($ids))
            {
                Player::where('partner_sign',$this->partnerSign)->whereIn('id',$ids)->update(['bonus_percentage'=>$partnerConfigure->value]);
            }
        }
        
        ConfigureCache::clearPartnerConfigureCache($this->partnerSign);
        return Help::returnApiJson("恭喜, 配置更新成功！", 1); 
    }

    // 配置状态
    public function configureStatus($id)
    {
        $config = PartnerConfigure::find($id);
        // 获取配置
        if (!$config) {
            return Help::returnApiJson("对不起, 无效的配置id！", 0);
        }

        $config->status = $config->status ? 0 : 1;
        $config->save();
        
         ConfigureCache::clearPartnerConfigureCache($this->partnerSign);
        return Help::returnApiJson("恭喜, 修改状态成功！", 1);
    }

    /**
     * 刷新商户缓存
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function configureFlush() {
        ConfigureCache::clearSystemConfigureCache();
        ConfigureCache::clearPartnerConfigureCache($this->partnerSign);
        return Help::returnApiJson("恭喜, 刷新缓存成功！", 1);
    }

    /** ============================= 缓存管理============================= */

    // 缓存列表
    public function cacheList()
    {
        $config = config("web.main.cache");

        $data['data']       = Cache::getList();
        $data['config']     = $config;
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 清空缓存
    public function cacheFlush($key)
    {
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

	/** ============================= 服务器时间============================= */
    //服务器时间
	public function getTimeNow () {
		list($microsecond , $time) = explode(' ', microtime()); //' '中间是一个空格

		$time =  (float)sprintf('%.0f',(floatval($microsecond)+floatval($time))*1000);


		return Help::returnApiJson('获取时间戳成功',1, $time);
	}

}
