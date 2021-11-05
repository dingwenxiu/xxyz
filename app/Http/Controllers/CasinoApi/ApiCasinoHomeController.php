<?php
namespace App\Http\Controllers\CasinoApi;

use App\Http\Controllers\Api\ApiBaseController;
use App\Lib\BaseCache;
use App\Lib\Help;
use App\Models\Casino\CasinoApiLog;
use App\Models\Casino\CasinoMethod;
use App\Models\Casino\CasinoPlatform;
use App\Models\Partner\PartnerCasinoPlatform;

class ApiCasinoHomeController extends ApiBaseController
{
    public $userInfo    = '';  // 会员信息
    public $secretkey   = '';  // 娱乐城代理key
    public $apiUrl      = '';  // 娱乐城API
    public $username    = '';   // 娱乐城代理账号
    public $signTime    = 0;   // 加密超时时间
    protected $model    = '';

    public function __construct() {
        parent::__construct();

        $this->userInfo = auth()->guard('api')->user();
        if (!$this->userInfo) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $this->secretkey = configure('secretkey')   ?? 'c518ae8a59bdb2fa89a943c7ab920669';
        $this->apiUrl    = configure('apiUrl')      ?? 'http://52.69.242.200';
        $this->username  = configure('username')    ?? 'xuanwu';
        $this->signTime  = configure('signTime')    ?? 30;
        $this->model = new CasinoApiLog();

    }

    /**
     * 视讯
     * @return \Illuminate\Http\JsonResponse
     */
    public function liveList()
    {
        $fishData = CasinoMethod::where('category', 'live')->whereIn('main_game_plat_code', ['bbin'])->get();
        return Help::returnApiJson('获取数据成功', 1, $fishData);
    }

    /***
     * 捕鱼
     * @return \Illuminate\Http\JsonResponse
     */
    public function fishingList()
    {
        $fishData = CasinoMethod::where('type', 'fishing')->get();
        return Help::returnApiJson('获取数据成功', 1, $fishData);
    }

    /**
     * 游戏列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function gameList()
    {
        $c         = request()->all();
        /**
         * 通过游戏类型 / 平台 获取所有游戏
         */
        $page     = $c['page_index']     ?? 1;
        $pageSize = $c['page_size'] ?? 20;
        $offset   = ($page - 1) * $pageSize;
        $platCode = $c['platCode'] ?? 'pt';
        $categorie = $c['categorie'] ?? 'e-game';

        $data = CasinoMethod::where('partner_sign', $this->partner->sign)->where('status', 1);

        if (isset($c['cn_name']) && $c['cn_name']) {
            $data->where('cn_name', 'like', '%'. $c['cn_name'] .'%');
        }

        switch ($categorie) {
            case 'fishing':
                $data = $data->where('type', $categorie);
                break;
            default:
                $data = $data->where('category', $categorie)->where('main_game_plat_code', $platCode);
                break;
        }

        switch ($platCode) {
            case 'ky':
                $data = $data->where('cn_name', '!=', '大厅')->select('*', 'pc_game_deputy_code as pc_game_code')->groupBy('pc_game_deputy_code');
                break;
        }

        $dataList['count'] = $data->count();
        $dataList['data']  = $data->skip($offset)->take($pageSize)->get();

        $data = [];
        foreach ($dataList['data'] as $item) {
            $imgs = substr($item['img'], -32,-19);
            if (!empty($item->img) && $imgs == 'CasinoGameImg') {
                $img = lotteryIcon($item->img);
            } else {
                $img = $item->img;
            }
            // 娱乐城平台logo
            $platLogo = CasinoPlatform::where('partner_sign', $this->partner->sign)->where('main_game_plat_code', $item->main_game_plat_code)->first();
            $data[] = [
                'main_game_plat_code'      => $item->main_game_plat_code,
                'cn_name'                  => $item->cn_name,
                'pc_game_code'             => $item->pc_game_code,
                'pc_game_deputy_code'      => $item->pc_game_deputy_code,
                'mobile_game_code'         => $item->mobile_game_code,
                'mobile_game_deputy_code'  => $item->mobile_game_deputy_code,
                'record_match_code'        => $item->record_match_code,
                'record_match_deputy_code' => $item->record_match_deputy_code,
                'img'                      => $img,
				'plat_logo'				   => $platLogo->image,
                'type'                     => $item->type,
                'category'                 => $item->category,
                'line_num'                 => $item->line_num,
                'bonus_pool'               => $item->bonus_pool,
                'status'                   => $item->status,
                'able_demo'                => $item->able_demo,
                'able_recommend'           => $item->able_recommend,
                'home'                     => $item->home,
                'add_admin_id'             => $item->add_admin_id,
            ];
        }
        $dataList['data'] = $data;

        return Help::returnApiJson('获取数据成功', 1, $dataList);
    }

    public function gamePlat() {
        $datas = PartnerCasinoPlatform::where('partner_sign', $this->partner->sign)->get();
        return Help::returnApiJson('获取数据成功', 1, $datas);
    }

    public function searchGame() {
        $c          = request()->all();

        $platCode   = $c['platCode'] ?? 'pt';
        $categorie  = $c['categorie'] ?? 'e-game';
        $gameCode   = $c['gameCode'] ?? '';
        $page     = $c['page'] ?? 1;
        $pageSize = $c['pageSize'] ?? 20;
        $offset   = ($page - 1) * $pageSize;

        $data = CasinoMethod::where('partner_sign', $this->partner->sign)->where('main_game_plat_code', $platCode)->where('category', $categorie)->where('cn_name', 'like', '%' .$gameCode . '%');
        $dataList['data']  = $data->skip($offset)->take($pageSize)->get();
        $dataList['count'] = ceil($data->count() / $pageSize);

        return Help::returnApiJson('获取数据成功', 1, $dataList);
    }

    public function suggestGame() {
        $cacheKey = 'casino_popular_web';
        $datas = BaseCache::_getCacheData($cacheKey);

        if (empty($datas)) {
            $datas = CasinoMethod::webCasinoCache($cacheKey);
        }
        return Help::returnApiJson('获取数据成功', 1, $datas);
    }
}
