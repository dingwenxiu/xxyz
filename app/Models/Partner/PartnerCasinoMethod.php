<?php
namespace App\Models\Partner;

use App\Models\Base;
use App\Models\Casino\CasinoMethod;

/**
 * 商户开放的玩法
 * Class PartnerCasinoMethod
 * @package App\Models\Partner
 */
class PartnerCasinoMethod extends Base {
    protected $table    = 'partner_casino_methods';

    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total          = $query->count();
        $data           = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 初始化玩法
    static function initCasinoMethods($partnerSign) {
        $methods    = CasinoMethod::where("status", 1)->get();
        $data       = [];
        foreach ($methods as $m) {
            $data[] = [
                'partner_sign'          => $partnerSign,
                'platform_code'         => $m->main_game_plat_code,
                'pc_game_code'          => $m->pc_game_code,
                'status'                => 1,
            ];
        }

        self::insert($data);
    }

}
