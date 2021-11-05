<?php

namespace App\Models\Partner;

use App\Models\Base;
use Illuminate\Support\Facades\Validator;

class PartnerActivityRule extends Base {

    public $errorMsg = '';
    public $models = '';

    public $rules = [
        'name' => 'required|string',//规则名称
    ];

    public $msg = [
        'name.required'=>'规则名称必须填写',
        'name.string'=>'规则名称必须字符串',
        'description.string'=>'规则描述必须字符串',
        'activity_id.required'=>'活动id必须填写',
        'activity_id.exists'=>'活动id不存在',
        'rule_details.required'=>'活动规则详情必须填写',
        'rule_details.array'=>'活动规则详情必须一个二维数组',
        'rule_details.*.field.required' => '活动规则field值必须填写',
        'rule_details.*.field.string' => '活动规则field值必须是字符串',
        'rule_details.*.value.required' => '活动规则value值必须填写',
        'rule_details.*.value.numeric' => '活动规则value值必须数字',
        'rule_details.*.remark.required' => '活动规则remark值必须填写',
        'rule_details.*.remark.string' => '活动规则remark值必须字符串',
        'rule_sign.required'=>'活动规则标记必须填写',
        'rule_sign.string'=>'活动规则标记必须是字符串',
    ];

    public  $rulesGetList = [
        'activity_id' => 'required|exists:activity_infos,id',//活动id
    ];

    public $msgGetList = [
        'activity_id.required' => '活动ID必须填写',
        'activity_id.exists' => '该活动不存在',
    ];

    static function getOne($c)
    {
        $query  = self::orderBy('id','asc');

        if (isset($c['partner_sign'])) {
            $query->where('partner_sign', $c['partner_sign']);
        }
        //规则名称
        if(isset($c['name'])) {
            $query->where('name', $c['name']);
        }

        //活动id
        if(isset($c['type'])) {
            $query->where('type', $c['type']);
        }

        $menus  = $query->first();
        return $menus;
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
        if(isset($c['type'])) {
            $query->where('type', $c['type']);
        }

        if(isset($c['typeArr'])) {
            $query->whereIn('type', $c['typeArr']);
        }
        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get()->makeHidden('rule_details');

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }


    public function saveItem($data, $id)
    {
        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            $this->errorMsg = $validator->errors();
            return false;
        }

        $avtive = self::find($id);
        if ($avtive) {
            $this->models = $avtive;
        } else {

            // 判断此活动是否存在
            $activeM = self::where([
                'type' => $data['type'],
                'partner_sign' => $data['partner_sign'],
            ])->first();
            if (!is_null($activeM)) {
                $this->errorMsg = '此活动已存在';
                return false;
            }
            $this->models = $this;
        }

        $this->models->partner_sign = $data['partner_sign'];
        $this->models->home         = $data['params']['home'] ?? 2;
        $this->models->status       = $data['params']['status'] ?? 2;
        $this->models->login_show   = $data['params']['login_show'] ?? 2;
        $this->models->type         = $data['type'];
        $this->models->pc_desc      = $data['pc_desc'] ?? '';
        $this->models->h5_desc      = $data['h5_desc'] ?? '';
        $this->models->params       = json_encode($data['params']);
        $this->models->img_banner   = $data['img_banner'] ?? '';
        $this->models->img_list     = $data['img_list'] ?? '';
        $this->models->img_info     = $data['img_info'] ?? '';
        $this->models->start_time   = $data['start_time'];
        $this->models->end_time     = $data['end_time'];
        $this->models->name         = $data['name'];

        $this->models->save();

        return true;
    }
}
