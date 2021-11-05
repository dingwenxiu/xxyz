<?php

namespace App\Models\Partner;

use App\Models\Activity\ActivityPrize;
use App\Models\Base;
use Illuminate\Support\Facades\Validator;

class PartnerActivityPrize extends Base {

    public $errorMsg = '';
    public $models = '';

    public $rules = [
    ];

    public $msg = [
    ];

    public  $rulesGetList = [
    ];

    public $msgGetList = [
    ];


    /**
     * 初始化奖品
     * @param $partnerSign
     */
    static function initPartnerActivityPrize($partnerSign) {
        $activityPrizeList = ActivityPrize::where("status", 1)->get();

        $data = [];
        foreach ($activityPrizeList as $prize) {
            $data[] = [
                'partner_sign' => $partnerSign,
                'type'         => $prize->type,
                'name'         => $prize->name,
                'img'          => $prize->img,
                'status'       => 1,
            ];
        }

        self::insert($data);
    }


    static function getList($c, $pageSize = 30)
    {
        $query  = self::orderBy('id','desc');

        if (isset($c['partner_sign'])) {
            $query->where('partner_sign', $c['partner_sign']);
        }
        //规则名称
        if(isset($c['name'])) {
            $query->where('name', $c['name']);
        }

        //活动id
        if(isset($c['sign'])) {
            $query->where('sign', $c['sign']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get()->makeHidden('rule_details');

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    public function delItem($c, $id)
    {

    }

    public function saveItem($data, $id)
    {
        $saveId = [1, 2, 3];
        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            $this->errorMsg = $validator->errors();
            return false;
        }

        if (in_array($id, $saveId)) {
            $this->errorMsg = '此值不能被修改';
            return false;
        }

        $avtive = self::find($id);
        if ($avtive) {
            $this->models = $avtive;
        } else {
            $this->models = $this;
        }

        $this->models->partner_sign = $data['partner_sign'];
        $this->models->type         = $data['type'];
        $this->models->name         = $data['name'] ?? '';
        $this->models->img          = $data['img'] ?? '';
        $this->models->status       = $data['status'] ?? '';

        $this->models->save();

        return true;
    }
}
