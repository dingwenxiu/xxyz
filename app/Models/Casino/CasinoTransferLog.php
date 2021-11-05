<?php
namespace App\Models\Casino;

use App\Models\Base;
use Illuminate\Support\Facades\Validator;

class CasinoTransferLog extends Base{
    protected $table    = 'casino_transfer_log';
    public    $rules = [
    ];

    /**
     * @param $condition
     * @param $pageSize
     * @return mixed
     */
    static function getList($condition, $pageSize = 20) {
        $query = self::orderBy('id', 'desc');

        $currentPage    = isset($condition['pageIndex']) ? intval($condition['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

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

        return $this->saveItemBase($data, $listId);
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
