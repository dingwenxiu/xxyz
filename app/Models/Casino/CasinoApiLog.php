<?php
namespace App\Models\Casino;

use App\Models\Base;
use Illuminate\Support\Facades\Validator;

class CasinoApiLog extends BaseCasinoModel {
    protected $table    = 'partner_casino_api_logs';
    public    $rules = [
    ];

    /**
     * @param $condition
     * @param $pageSize
     * @return mixed
     */
    static function getList($c, $partner) {
        $query = self::orderBy('id', 'desc');

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 20;
        $offset         = ($currentPage - 1) * $pageSize;

        if (!empty($partner->sign)) {
            $query = $query->where('partner_sign', $partner->sign);
        }

        if (!empty($c['username'])) {
            $query = $query->where('username', 'like', $c['username']);
        }

        if (!empty($c['platform_sign'])) {
            $query = $query->where('platform_sign', $c['platform_sign']);
        }

        if (!empty($c['ip'])) {
            $query = $query->where('ip', $c['ip']);
        }

        $total  = $query->count();
        $data   = $query->skip($offset)->take($pageSize)->get();


        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }


    // 保存
    public function saveItem($data, $listId = 0) {
        $validator  = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            $this->errMsg = $validator->errors()->first();
            return 0;
        }

        return $this->saveBase($data, $listId);
    }

    public function getItem($id){
        $listData = self::where('id',$id)->first();
        if (!$listData){
            $this->errMsg = '数据不存在';
            return 1;
        }
        return $listData;
    }

}
