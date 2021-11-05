<?php

namespace App\Models\Template;

use App\Models\Admin\AdminModule;
use App\Models\Base;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerModule;
use Illuminate\Support\Facades\Validator;

class Template extends Base
{
    public $errorMsg;
    public $rules = [
        'name' => 'required|string',//规则名称
        'sign' => 'required|string|unique:templates',//规则名称
    ];

    public $create_messages = [
        'name.required' => '名称不合法或则不存在',
        'sign.required' => '标记不合法或则不存在',
        'sign.unique' => '标记已存在',
    ];
    static public function getList($c)
    {
        $query  = self::orderBy('id','asc');

        if (isset($c['status']) && $c['status']) {
            $query -> where('status', $c['status']);
        }

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query -> where('partner_sign', $c['partner_sign']);
        }

        $data = $query->get();

        return $data;

    }

    public function addItem($param)
    {
        try{
            $validator = Validator::make($param, $this->rules, $this->create_messages);
            if ($validator->fails()) {
                $this->errorMsg = $validator->errors()->first();
                return false;
            }

            $this->name = $param['name'];
            $this->sign = $param['sign'];
            $this->partner_sign = $param['partner_sign'] ?? '';
            $this->module_sign = $param['module_sign'] ?? '';
            $this->status = $param['status'] ?? 1;
            $this->module_name = $param['module_name'] ?? '';
            $this->partner_name = $param['partner_name'] ?? '';

            if (!$this->save()) {
                $this->errorMsg = '添加失败';
                return false;
            }

            $partner     = Partner::get();

            $methodList = AdminModule::where("status", 1)->get();

            $data = [];
            foreach ($partner as $item) {
                foreach ($methodList as $method) {
                    $data[] = [
                        'partner_sign' => $item->sign,
                        'name'         => $method->name,
                        'm_name'       => $method->m_name,
                        'sign'         => $method->sign,
                        'template_sign' => $param['sign'],
                        'route'        => $method->route,
                        'param'        => $method->param,
                        'num_max'      => $method->num_max,
                        'style'        => $method->style,
                        'status'       => 0,
                    ];
                }
            }

            PartnerModule::insert($data);

            $this->errorMsg = '添加成功';
            return true;
        } catch (\Exception $exception) {
            $this->errorMsg = $exception->getMessage();
            return false;
        }
    }


}
