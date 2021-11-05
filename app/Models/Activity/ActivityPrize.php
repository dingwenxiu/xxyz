<?php

namespace App\Models\Activity;

use App\Models\Base;

class ActivityPrize extends Base {

    public $rules = [
    ];

    public $msg = [
    ];

    public  $rulesGetList = [
        'activity_id' => 'required|exists:activity_infos,id',//活动id
    ];

    public $msgGetList = [
        'activity_id.required' => '活动ID必须填写',
        'activity_id.exists' => '该活动不存在',
    ];

    static function getList($c, $pageSize = 15)
    {
        $query  = self::orderBy('id','desc');

        //规则名称
        if(isset($c['name'])) {
            $query->where('name', $c['name']);
        }
        //活动id
        if(isset($c['sign'])) {
            $query->where('sign', $c['sign']);
        }

        $menus  = $query->get();

        return $menus;
    }
}
