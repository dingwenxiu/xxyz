<?php

namespace App\Models\Player;


use App\Models\Finance\Recharge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Models\Partner\PartnerAdminActionReview;

class PlayerVipConfig extends Model
{
    protected $table = 'partner_player_vip_config';

    public $rules = [
        'vip_level'   => 'required|integer|min:1',
        'name'        => 'required|min:2|max:32',
        'show_name'   => 'required|min:2|max:32',
        'icon'        => 'required|max:254',
    ];

    public $msg = [
        'vip_level.required' => '会员等级必须填写',
        'vip_level.integer'  => '会员等级必须为整数',
        'vip_level.min'      => '会员等级必须大于等于1',
        'name.required'      => '会员等级名称必须填写',
        'name.min'           => '会员等级名称长度必须大于1个字符',
        'name.max'           => '会员等级名称长度必须小于33个字符',
        'show_name.required' => '会员等级显示名称必须填写',
        'show_name.min'      => '会员等级显示名称长度必须大于1个字符',
        'show_name.max'      => '会员等级显示名称长度必须小于33个字符',
        'icon.required'      => '会员等级图标不能为空',
        'icon.max'           => '会员等级图标长度必须小于255个字符',
    ];
    
    public function saveItem($params, $partnerSign)
    {
        $validator  = Validator::make($params, $this->rules,$this->msg);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        
        $exist = self::where('vip_level',$params['vip_level'])->where('partner_sign', $partnerSign)->first();
        if($this->id)
        {
            if($exist && $exist->id != $this->id)
            {
                return '会员等级已存在';
            }
        }
        else
        {
            if($exist)
            {
                return '会员等级已存在';
            }
        }

        $this->vip_level               = $params['vip_level'];
        $this->name                    = $params['name'];
        $this->show_name               = $params['show_name'];
        $this->partner_sign            = $params['partner_sign'];
        $this->icon                    = $params['icon'];
        $this->recharge_total          = $params['recharge_total'];
        $this->save();

        return true;
    }

    /**
     * 获取充值等级
     * @param $partner_sign
     * @param $userId
     * @param $realMoney
     * @return mixed
     */
    static function getUserLevel($partner_sign,$userId,$realMoney) {
        // 理赔上分
        $partnerAdminActionReview     = PartnerAdminActionReview::where('player_id',$userId)->select('process_config')->get();
        if (!isset($partnerAdminActionReview) || empty($partnerAdminActionReview) || !count($partnerAdminActionReview)){
            $total_review = 0;
        }else{
            $total_review = 0;
            foreach ($partnerAdminActionReview as $item){
                if ($item->process_config){
                    $item->process_config = unserialize($item->process_config);
                    if(isset($item->process_config['amount'])){
                        $total_review        += moneyUnitTransferOut($item->process_config['amount'])??0;
                    }
                }
            }
        }

        // 充值上分
        $recharge_total = moneyUnitTransferOut(Recharge::getTotalRecharge($userId));
        $recharge_total = $recharge_total + $total_review;
        $data           = self::where('partner_sign',$partner_sign)->select('recharge_total','vip_level')->get();
        if (!isset($data) || empty($data) || !count($data)){
            return $vip_level=1;
        }

        $options = [];
        foreach ($data as $item) {
            $options[$item['recharge_total']] = $item['vip_level']-1;
        }

        $vip_level = 1;
        foreach ($options as $key => $vip_level)
        {
            if ($recharge_total < $key)
            {
                if ($vip_level == 0){
                    return 1;
                }
                return $vip_level;
            }
        }
        return $vip_level;
    }
}
