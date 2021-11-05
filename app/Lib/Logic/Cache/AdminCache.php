<?php namespace App\Lib\Logic\Cache;

use App\Models\Casino\CasinoCategorie;
use App\Models\Partner\Partner;
use App\Models\Player\Player;

/**
 * Tom 2019 整理 用户缓存
 * Class UserCache
 * @package App\Lib
 */
class AdminCache
{

    public static $prefix   = "admin_";
    public static $expired  = 12;
    public static $store    = "redis";

    /**
     * 在线人数
     * @return mixed
     * @throws \Exception
     */
    static function onlineArray()
    {
        $onlineArray  = [];
        $_onlineArray = [];
        $players = Player::pluck('id')->toArray();
        foreach ($players as $value) {
            array_push($onlineArray, $value);
        }
        if(count($onlineArray)) {
            $_onlineArray = cache()->many($onlineArray);
        }
        
        return $_onlineArray;
    }

    /**
     * 清除彩种缓存
     * @param $sign
     *
     * @throws \Exception
     */
    static function cleanLotteryAll($sign)
    {
        $partners = Partner::all();
        foreach ($partners as $partner) {

            cache()->forget('lottery_all_'.$partner->sign);
            cache()->forget('lottery_all_'.$partner->sign.'_'.$sign);
        }

    }

}
