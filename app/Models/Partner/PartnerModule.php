<?php

namespace App\Models\Partner;

use App\Models\Admin\AdminModule;
use App\Models\Template\Template;
use Illuminate\Database\Eloquent\Model;

class PartnerModule extends Model
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table    = 'partner_modules';
    public    $errorMsg = '';
    public $rules = [
    ];

    public static function initPartnerModule($partnerSign)
    {
        $methodList = AdminModule::where("status", 1)->get();
        $Template   = Template::all();

        $data = [];
        foreach ($Template as $templateItem) {
            $partnerSignArr = explode(',', $templateItem->partner_sign);
            foreach ($methodList as $method) {
                $data[] = [
                    'partner_sign' => $partnerSign,
                    'name'         => $method->name,
                    'm_name'       => $method->m_name,
                    'sign'         => $method->sign,
                    'template_sign' => $templateItem->sign,
                    'route'        => $method->route,
                    'param'        => $method->param,
                    'num_max'      => $method->num_max,
                    'style'        => $method->style,
                    'status'       => in_array($partnerSign, $partnerSignArr) ? 1 : 0,
                ];
            }
        }

        self::insert($data);
    }

    public function saveItem($c, $partnerSign, $id) {
        $validator  = \Illuminate\Support\Facades\Validator::make($c, $this->rules);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        if (intval($id) === 0) {
            $thisModel = $this;
        } else {
            $thisModel = self::find($id);
        }
        if (!$thisModel) {
            return false;
        }

        if ( ! empty($c['name'])) {
            $thisModel->name = $c['name'];
        }
        if ( ! empty($c['m_name'])) {
            $thisModel->m_name = $c['m_name'];
        }
        if ( ! empty($c['route'])) {
            $thisModel->route = $c['route'];
        }
        if ( ! empty($c['num_max'])) {
            $thisModel->num_max = $c['num_max'];
        }
        if ( ! empty($c['style'])) {
            $thisModel->style = $c['style'];
        }
        if (isset($c['status'])) {
            if (!$thisModel->status && self::where('partner_sign', $partnerSign)->where('m_name', 'hotGame')->where('status', 1)->count() >= 3) {
                $this->errorMsg = '首页显示最多4个模块';
                return false;
            }
//            $thisModel->status = $thisModel->status ? 0 : 1;
        }

        $thisModel->partner_sign = $partnerSign;

        $thisModel->save();

        return true;
    }
}
