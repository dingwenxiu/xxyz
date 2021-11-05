<?php
namespace App\Http\Controllers\AdminApi\Template;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Admin\AdminModule;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerModule;
use App\Models\Template\Template;

class ApiTemplateController extends ApiBaseController
{

    public function addTemplate()
    {
        $c = request()->all();
        $Template = new Template();
        if ($Template->addItem($c)) {
            return Help::returnApiJson($Template->errorMsg, 1);
        }
        return Help::returnApiJson($Template->errorMsg, 0);
    }

    // 获取 模板列表
    public function getTemplateList()
    {
        $c = request()->all();
        $data['template'] = Template::getList($c);
        $data['partner']  = Partner::get();

        return Help::returnApiJson("", 1, $data);
    }

    // 获取模板的模型
    public function getTemplateModule()
    {
        $c = request()->all();
        $c['id'] = request('id',0);

        $data['template'] = Template::find($c['id']);
        $adminModuleM = AdminModule::all()->toArray();

        if ($data['template']){
            $name= explode(',', $data['template']->module_name);
            $_data = [];
            foreach ($adminModuleM as $item) {
                $sign = $item['name'];
                $select = 0;
                if (in_array($sign, $name)) {
                    $select = 1;
                }
                $_data[] = [
                    "id" => $item['id'],
                    "name" => $item['name'],
                    "sign" => $item['m_name'],
                    "selected" => $select,
                ];
            }
            $adminModuleM = $_data;
        }


        return Help::returnApiJson("", 1, $adminModuleM);
    }

    // 设置模板的模型
    public function setTemplateModule()
    {
        try {
            $c = request()->all();
            $templateSign   = $c['template_sign'];
            $addModuleSign  = $c['add_module_sign'];
            $lessModuleSign = $c['less_module_sign'];

            $moduleName = $c['module_name'];

            $addModuleSignArr  = explode(',', $addModuleSign);
            $lessModuleSignArr = explode(',', $lessModuleSign);

            db()->beginTransaction();
            // 先删除
            foreach ($lessModuleSignArr as $key => $item) {
                // 删除总module
                $AdminModule = AdminModule::where('sign', $item)->first(['id', 'sign', 'template_sign']);
                $templateSignStr = $AdminModule->template_sign ?? '';
                $templateSignArr = explode(',', $templateSignStr);
                if (in_array($templateSign, $templateSignArr)) {
                    $unsetKey = array_keys($templateSignArr, $templateSign)[0];
                    unset($templateSignArr[$unsetKey]);
                    $templateSignStr = implode(',', $templateSignArr);
                    AdminModule::where('sign', $item)->update(
                        ['template_sign' => $templateSignStr]
                    );
                }

                PartnerModule::where(['template_sign' => $templateSign, 'sign' => $item])->update(['status' => 0]);
            }

            // 添加
            foreach ($addModuleSignArr as $item) {
                // 添加总module
                $AdminModule = AdminModule::where('sign', $item)->first(['id', 'sign', 'template_sign']);
                $templateSignStr = $AdminModule->template_sign ?? '';
                $templateSignArr = explode(',', $templateSignStr);
                if ( ! in_array($templateSign, $templateSignArr)) {
                    $templateSignArr[] = $templateSign;
                    $templateSignStr = implode(',', $templateSignArr);
                    AdminModule::where('sign', $item)->update(
                        ['template_sign' => $templateSignStr]
                    );
                }

                PartnerModule::where(['template_sign' => $templateSign, 'sign' => $item])->update(['status' => 1]);
            }

            Template::where('sign', $templateSign)->update(['module_sign' => $addModuleSign, 'module_name' => $moduleName]);
            db()->commit();
            return Help::returnApiJson('添加数据成功!', 1);
        }catch (\Exception $exception) {
            db()->rollback();
            var_dump($exception->getLine() . '-' . $exception->getMessage());
        }
    }

    // 获取商户模板 同事同步 module数据
    public function getTemplateOfModule()
    {
        $c = request()->all();
        $partnerSign = $c['partner_sign'];
        $template = Template::where('partner_sign', '')->orWhere('partner_sign', $partnerSign)->get();
        foreach ($template as $item) {
            $partnerSignArr = explode(',', $item->partner_sign);
            $item['selected'] = 0;
            if (in_array($partnerSign, $partnerSignArr)) {
                $item['selected'] = 1;
            }
        }
        return Help::returnApiJson('获取数据成功!', 1, $template);
    }

    // 分配商户模板 同事同步 module数据
    public function setTemplateOfModule()
    {
        $c = request()->all();
        $partnerSign = $c['partner_sign'];
        $partnerName = $c['partner_name'] ?? '';

        $addTemplateSign = $c['add_template_sign'];

        // 获取模板信息
        $Template = Template::where('sign', $addTemplateSign)->first(['id', 'sign', 'partner_sign', 'module_sign']);

        if (is_null($Template)) {
            return Help::returnApiJson('模板不存在!', 0);
        }

        db()->beginTransaction();
        // 1. 清空商户所有模板的模块配置
        PartnerModule::where(['partner_sign' => $partnerSign])->update(['status' => 0]);

        // 2. 更新商户模块的对应模板信息
        $module_signArr = explode(',', $Template->module_sign);
        PartnerModule::where(['template_sign' => $addTemplateSign, 'partner_sign' => $partnerSign])->WhereIn('sign', $module_signArr)->update(['status' => 1]);

        // 商户修改模板
        Partner::where('sign', $partnerSign)->update(['template_sign' => $addTemplateSign]);
        // 模板更新 商户数据
        Template::where(['partner_sign' => $partnerSign])->update(['partner_sign' => '', 'partner_name' => '']);
        Template::where('sign', $addTemplateSign)->update(['partner_sign' => $partnerSign, 'partner_name' => $partnerName]);

        db()->commit();
        return Help::returnApiJson('添加数据成功!', 1);
    }

}
