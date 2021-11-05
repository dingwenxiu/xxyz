<?php
namespace App\Models\Casino;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CasinoPlatform extends BaseCasinoModel
{
    protected $table    = 'partner_casino_platforms';
    public    $rules = [
        "main_game_plat_name"   => "required|min:2|max:64",
    ];

    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;

        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();


        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 设置游戏首页显示
     * @param $c
     *
     * @return int
     */
    static function setHomeShow($c)
    {
        $platId = intval($c['plat_id']) ?? 0;
        if ($platId == 0) {
            return 0;
        }
        $gameOneM   = self::find($platId);
        $homeStatus = $gameOneM->home ? 0 : 1;

        self::where('id', $platId)->update(['status' => $homeStatus]);
        return 1;
    }



    // 保存
    public function saveItem($data, $admin = null) {
        $validator  = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        // Sign 不能重复
        if (!$this->id) {
            $count = self::where('main_game_plat_code', '=', $data['main_game_plat_code'])->count();
            if ($count > 0) {
                return "对不起, 标识(code)已经存在!!";
            }
        } else {
            $count = self::where('main_game_plat_code', '=', $data['main_game_plat_code'])->where("id", "<>", $this->id)->count();
            if ($count > 0) {
                return "对不起, 标识(code)已经存在!!";
            }
        }

        $this->main_game_plat_name     = $data['main_game_plat_name'];
        $this->main_game_plat_code     = $data['main_game_plat_code'];
        $this->status                  = $data['status'] ? 1 : 0;
        $this->add_admin_id            = $admin ? $admin->id : '999999';
        $this->save();

        return true;
    }

    static function getOptions($partner) {
        $options = [];
        $platforms = self::where('partner_sign', $partner->partner_sign)->get();

        foreach ($platforms as $platform) {
            $options[] = [
                'id'   => $platform->id,
                'home' => $partner->home,
                'image' => $platform->image,
                "name" => $platform->main_game_plat_name,
                "code" => $platform->main_game_plat_code,
            ];
        }

        return $options;
    }

    /**
     * @param array $data Data.
     * @return string
     */
    public function saveItemAll(array $data, $partner_sign)
    {
        DB::beginTransaction();
        foreach ($data as $item) {
            $item['partner_sign'] = $partner_sign;
            if (self::where('partner_sign', $partner_sign)->where('main_game_plat_code', $item['main_game_plat_code'])->first() !== null) {
                unset($item['id']);
                self::where('partner_sign', $partner_sign)->where('main_game_plat_code', $item['main_game_plat_code'])->update($item);
            } else {
                unset($item['id']);
                self::insert($item);
            }
        }
        DB::commit();
        return 1;
    }
}
