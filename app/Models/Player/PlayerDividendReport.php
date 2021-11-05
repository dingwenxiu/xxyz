<?php

namespace App\Models\Player;

use App\Models\Admin\Province;
use App\Models\Base;
use Illuminate\Support\Facades\Validator;

class PlayerDividendReport extends Base
{
    public $rules = [
        'owner_name'        => 'required|min:2|max:128',
        'card_number'       => 'required|integer',
        'province'          => 'required|integer',
        'city'              => 'required|integer',
        'branch'            => 'required|min:4|max:128',
    ];

    protected $table = 'user_dividend_report';

    static function getList($c, $pageSize = 10) {
        $query = self::orderBy('id', 'desc');

        // 用户名
        if (isset($c['owner_name'])) {
            $query->where('owner_name', $c['owner_name']);
        }

        // 上级
        if (isset($c['user_id'])) {
            $query->where('user_id', $c['user_id']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    public function saveItem($adminId = 0) {
        $data       = request()->all();
        $validator  = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $user = Player::where('username', $data['username'])->first();
        if (!$user->id) {
            return "无效的用户!";
        }

        // 卡号
        if (strlen($data['card_number']) < 15 || strlen($data['card_number']) > 19) {
            return "银行卡号只能是15位和19位之间!";
        }

        // 银行
        $banks = config("web.banks");
        if (!isset($data['bank_sign']) || !isset($banks[$data['bank_sign']])) {
            return "无效的开户行!";
        }

        // 省份
        $provinceList = Province::getProvince();
        if (!isset($data['province']) || !isset($provinceList[$data['province']])) {
            return "无效的省份!";
        }

        // 市区
        $cityList = $provinceList[$data['province']]['city'];
        if (!isset($data['city']) || !isset($cityList[$data['city']])) {
            return "无效的市区!";
        }

        $this->username             = $data['username'];
        $this->user_id              = $user->id;
        $this->bank_sign            = $data['bank_sign'];
        $this->card_number          = $data['card_number'];
        $this->branch               = $data['branch'];
        $this->owner_name           = $data['owner_name'];
        $this->province             = $provinceList[$data['province']]['name'];
        $this->city                 = $cityList[$data['city']];
        $this->admin_id             = $adminId;
        $this->save();
        return true;
    }
}
