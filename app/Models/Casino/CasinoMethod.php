<?php
namespace App\Models\Casino;

use App\Lib\BaseCache;
use App\Lib\Help;
use Illuminate\Support\Facades\DB;

/**
 * Class CasinoGameList
 * @package App\Models\Casino
 */
class CasinoMethod extends BaseCasinoModel
{
    protected $table    = 'partner_casino_methods';

    /**
     * @param array $data
     * @param string $partner_sign
     * @return int
     */
    public function saveItemAll(array $data, string $partner_sign)
    {
        $ossUrl = 'https://yx-casino.oss-cn-hongkong.aliyuncs.com/game/';
        foreach ($data as $dataItem) {
            $dataItem['partner_sign'] = $partner_sign;

            // 有需要更新的数据，根据 id获取数据信息
            $getDataItemOfId = self::where('partner_sign', $partner_sign)->where('main_game_plat_code', $dataItem['main_game_plat_code'])->where(function ($query) use ($dataItem) {
                $query->where('pc_game_code', $dataItem['pc_game_code'])->orWhere('mobile_game_code', $dataItem['mobile_game_code']);
            })->first();
            // 根据id判断是否有此数据
            if ($getDataItemOfId) {
                $id = $dataItem['id'];
                unset($dataItem['id']);
                $dataItem['img'] = $dataItem['img'] ? $ossUrl . $dataItem['img'] : '';
                self::where('id', $id)->where('partner_sign', $partner_sign)->update($dataItem);
            } else {
                $dataItem['img'] = $dataItem['img'] ? $ossUrl . $dataItem['img'] : '';
                unset($dataItem['id']);
                self::insert($dataItem);
            }
        }
        return 1;
    }

    /**
     * @param integer $id ID.
     * @return integer
     */
    public function getItem(int $id)
    {
        $listData = self::where('id', $id)->first();
        if (!$listData) {
            $this->errMsg = '数据不存在';
            return 1;
        }
        return $listData;
    }

    /**
     * 获取游戏列表
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $partner)
    {
        $query = self::orderBy('id', 'desc');

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 20;
        $offset         = ($currentPage - 1) * $pageSize;

        if (!empty($partner->sign)) {
            $query = $query->where('partner_sign', $partner->sign);
        }

        if (!empty($c['main_game_plat_code'])) {
            $query = $query->where('main_game_plat_code', $c['main_game_plat_code']);
        }

        if (!empty($c['category'])) {
            $query = $query->where('category', $c['category']);
        }

        if (!empty($c['cn_name'])) {
            $query = $query->where('cn_name', 'like', '%'. $c['cn_name'] . '%');
        }


        $total  = $query->count();
        $data  = $query->skip($offset)->take($pageSize)->get();
        $datas = [];
        foreach ($data as $item) {
            $imgs = substr($item['img'], -32,-19);
            if (!empty($item->img) && $imgs == 'CasinoGameImg') {
                $img = lotteryIcon($item->img);
            } else {
                $img = $item->img;
            }
            $datas[] = [
                'id'                       => $item->id,
                'main_game_plat_code'      => $item->main_game_plat_code,
                'cn_name'                  => $item->cn_name,
                'pc_game_code'             => $item->pc_game_code,
                'pc_game_deputy_code'      => $item->pc_game_deputy_code,
                'mobile_game_code'         => $item->mobile_game_code,
                'mobile_game_deputy_code'  => $item->mobile_game_deputy_code,
                'record_match_code'        => $item->record_match_code,
                'record_match_deputy_code' => $item->record_match_deputy_code,
                'img'                      => $img,
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
        $data['data'] = $datas;

        return Help::returnApiJson('成功获取数据', 1, ['data' => $data['data'], 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))]);
    }

    /**
     * 设置游戏首页显示
     * @param $c
     *
     * @return int
     */
    static function setHomeShow($c)
    {
        $gameId = intval($c['game_id']) ?? 0;
        if ($gameId == 0) {
            return 0;
        }
        $gameOneM   = self::find($gameId);
        $homeStatus = $gameOneM['home'] ? 0 : 1;

        self::where('id', $gameId)->update(['home' => $homeStatus]);
        return 1;
    }
}
