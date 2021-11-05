<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;

class PartnerHome extends Model
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'partner_homes';
    public    $error = '';

    public $rules = [
        'module_id' => 'required',
        'order'     => 'required',
        'other_id'  => 'required',
    ];


    public function saveItem($c, $partnerSign, $id)
    {
        try{
            $validator  = \Illuminate\Support\Facades\Validator::make($c, $this->rules);
            if ($validator->fails()) {
                $this->error = $validator->errors()->first();
                return false;
            }

            if (intval($id) === 0) {
                $thisModel = $this;

                if (self::where('module_id', $c['module_id'])->where('other_id', $c['other_id'])->where('partner_sign', $partnerSign)->first() !== null) {
                    $this->error = '此模块数据重复';
                    return false;
                }

                // 获取模型个数
                $a = PartnerModule::find($c['module_id']);
                if (self::where('module_id', $c['module_id'])->where('partner_sign', $partnerSign)->count() >= $a['num_max']) {
                    $this->error = '此模块数据量已满'.'最多只能设置'.$a['num_max'].'个模块';
                    return false;
                }

            } else {
                $thisModel = self::find($id);
            }

            if ($thisModel === null) {
                $this->error = 'thisModel为空';
                return false;
            }

            $thisModel->module_id     = $c['module_id'];
            $thisModel->other_id      = $c['other_id'];
            $thisModel->value         = $c['value'] ?? '';
            $thisModel->status        = 1;
            $thisModel->template_sign = $c['template_sign'] ?? 'youxia';
            $thisModel->order         = $c['order'];
            $thisModel->partner_sign  = $partnerSign;

            $thisModel->save();

            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }


    public function delNavigetion($id)
    {
        self::where('id', $id)->delete();
    }
}
