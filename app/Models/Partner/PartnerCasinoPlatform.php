<?php

namespace App\Models\Partner;

use App\Models\Casino\CasinoPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PartnerCasinoPlatform extends Model
{
    protected $table = 'partner_casino_platforms';

    public $rules = [
        'partner_sign'      => 'required|min:2|max:64',
        'platform_code'     => 'required|min:2|max:32',
    ];

    /**
     * 获取列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
//        $query = self::select(
////            DB::raw('partner_casino_platforms.*')
////        )->leftJoin('partner_casino_platforms', 'partner_casino_platforms.main_game_plat_code', '=', 'partner_casino_platforms.main_game_plat_code')->orderBy('partner_casino_platforms.id', 'desc');
////
////        // 平台
////        if (isset($c['partner_sign']) && $c['partner_sign']) {
////            $query->where('partner_casino_platforms.partner_sign', $c['partner_sign']);
////        }

        $query = self::where('partner_sign', $c['partner_sign'])->orderBy('id', 'desc');

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取商户对应开放的平台
     * @param $signArr
     * @return array
     */
    static function getAllPlatformBySign($signArr) {
//        $query = self::select(
//            DB::raw('partner_casino_platforms.partner_sign'),
//            DB::raw('partner_casino_platforms.*')
//        )->leftJoin('partner_casino_platforms', 'partner_casino_platforms.main_game_plat_code', '=', 'partner_casino_platforms.main_game_plat_code')->orderBy('partner_casino_platforms.id', 'desc');
//
//        $items  = $query->whereIn('partner_casino_platforms.partner_sign', $signArr)->get();
        $items  = self::whereIn('partner_casino_platforms.partner_sign', $signArr)->orderBy('id', 'desc')->get();

        $data = [];
        foreach ($items as $item) {
            if (!isset($data[$item->partner_sign])) {
                $data[$item->partner_sign] = [];
            }

            $data[$item->partner_sign][] = [
                'name' => $item->platform_name,
                'code' => $item->platform_code,
            ];
        }

        return $data;
    }

    // 初始化商户 娱乐城玩法
    static function initCasinoPlatform($partnerSign) {
        $platforms      = CasinoPlatform::where("status", 1)->get();
        $data           = [];
        foreach ($platforms as $p) {
            $data[] = [
                'partner_sign'          => $partnerSign,
                'main_game_plat_code'   => $p->main_game_plat_code,
                'status'                => 1,
            ];
        }

        self::insert($data);

        // 更新玩法表
        PartnerCasinoMethod::initCasinoMethods($partnerSign);
    }
}
