<?php

namespace App\Http\Controllers\Api;

use App\Lib\Help;
use App\Models\Admin\SysCity;
use App\Models\Partner\PartnerModule;
use Exception;

class ApiPlatformController extends ApiBaseController
{
    /**
     * 获取网站配置
     * @return mixed
     * @throws Exception
     */
    public function baseConfig()
    {
        $data = $this->partner->getSiteConfig();
        $data['system_pic_base_url'] = configure("system_pic_base_url");
        $data['tws'] = configure('system_tws');
        return Help::returnApiJson('获取网站配置成功', 1, $data);
    }

    /**
     * 获取流行玩法
     * @return mixed
     */
    public function popularMethods()
    {
        $data = $this->partner->getPopularMethods();
        return Help::returnApiJson('获取流行玩法成功', 1, $data);
    }

    /**
     * 获取热门彩票
     * @return mixed
     */
    public function hotLotteryList()
    {
        $data = $this->partner->getPopularLotterys();
        return Help::returnApiJson('获取热门彩票成功', 1, $data);
    }

    /**
     * 获取排行
     * @return mixed
     */
    public function ranking()
    {
        $data = $this->partner->getPartnerRanking(request('partner_sign'));
        return Help::returnApiJson('获取排行成功', 1, $data);
    }

    /**
     * 获取城市
     * @return mixed
     */
    public function city()
    {
        $provinceId = request("region_parent_id");
        $cityList = SysCity::getCityList($provinceId);
        return Help::returnApiJson('获取城市成功', 1, $cityList);
    }

    /**
     * 开启加密
     * @return mixed
     */
    public function openEncryption()
    {
        $open = configure("system_open_encryption", 1);
        $open = $open ? 1 : 0;
        return Help::returnApiJson('开启加密数据成功', 1, [$open]);
    }

    /**
     * 获取游戏列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function openList()
    {
        $data = $this->partner->getOpenList();
        return Help::returnApiJson('获取游戏列表成功!', 1, $data);
    }
}
