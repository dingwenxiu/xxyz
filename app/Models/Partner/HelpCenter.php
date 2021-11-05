<?php

namespace App\Models\Partner;

use App\Models\BaseCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HelpCenter extends Base
{
    use BaseCache;
    protected $table = "help_center";

    protected $fillable = ['help_image','title','content'];
    public $rules = [
        'title'                 => 'required|min:2|max:128',
        'content'               => 'required',
    ];

    /**
     * 添加过滤参数
     * @var array
     */
    public $create_rules = [
        'title' => 'required|string',
        'content' => 'required|string',
    ];

    /**
     * 添加过滤参数返回消息
     * @var array
     */
    public $create_messages = [
        'title.required' => '标题不能为空',
        'content.required' => '内容不能为空',
    ];

    /**
     * 添加过滤参数
     * @var array
     */
    public $edit_rules = [
        'id' => 'string|exists:partners,id',//商户ID
    ];



    /**
     * @param $c
     * @param $pageSize
     * @return mixed
     */
    static function getList($params) {
        $data = self::select(
            DB::raw('help_center_menu.id as menu_id'),
            DB::raw('help_center_menu.partner_sign'),
            DB::raw('help_center_menu.name'),
            DB::raw('help_center_menu.add_partner_admin_id'),
            DB::raw('help_center_menu.update_partner_admin_id'),
            DB::raw('help_center_menu.created_at'),
            DB::raw('help_center_menu.updated_at'),
            DB::raw('help_center.id'),
            DB::raw('help_center.pid'),
            DB::raw('help_center.title'),
            DB::raw('help_center.content'),
            DB::raw('help_center.help_image')
        )->leftJoin('help_center_menu', 'help_center.pid', '=', 'help_center_menu.id')->where('help_center_menu.partner_sign',$params['partner_sign'])->orderBy('help_center.id', 'desc')->get();

        $dataAll = [];
        foreach ($data as $item) {
            if(!isset($dataAll[$item->pid])) {
                [$item->pid => $item->name];
                $dataAll[$item->pid] = array_merge([$item->pid => $item->name],  [$item->pid =>$item]);
            } else {
                $dataAll[$item->pid] = array_merge($dataAll[$item->pid], [$item->pid =>$item]);
            }
        }

        return ['data' => $dataAll];
    }


    // 修改状态
    public function changeStatus() {
        $this->status = $this->status > 0 ? 0 : 1;
        $this->save();
        return true;
    }

    // 保存
    public function saveItem($data) {
        $validator  = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $this->pid                      = $data['pid'];
        $this->title                    = $data['title'];
        $this->content                  = $data['content'];
        $this->status                   = $data['status'];
        $this->add_partner_admin_id     = $data['add_partner_admin_id'];
        $this->save();

        return true;
    }

    /**
     * @param $partnerSign
     * @return mixed
     * @throws \Exception
     */
    static function getDataFromCache($partnerSign) {
        if (self::_hasCache('helpContent_' . $partnerSign)) {
            return self::_getCacheData('helpContent_' . $partnerSign);
        } else {
            $allCache = self::getList();
            if ($allCache) {
                self::_saveCacheData('helpContent_' . $partnerSign, $allCache);
            }

            return $allCache;
        }
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    static function getTipNotice() {
        $data = self::getDataFromCache();
        if (!$data) {
            return "";
        }

        foreach ($data as $item) {
            return $item['content'];
        }

        return "";
    }

    /**
     * 判断前端接收过滤参数
     * @param $c
     * @param $input
     * @return string
     */
    public function Validator($c, $input)
    {
        if ($input == 0) {
            $validator = Validator::make($c, $this->create_rules, $this->create_messages);
        } else {
            $validator = Validator::make($c, $this->edit_rules, $this->edit_messages);
        }
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        return true;
    }

    /**
     * 保存图片
     * @param $imageObj
     * @param $path
     * @param $picSource
     * @return mixed
     */
    private function savePic($imageObj, $path, $picSource)
    {
        $picSavePath = $imageObj->depositPath($path, 1, 1);
        $previewPic = $imageObj->uploadImg($picSource, $picSavePath);
        return $previewPic;
    }
}
