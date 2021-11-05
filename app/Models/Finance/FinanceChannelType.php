<?php

namespace App\Models\Finance;

use App\Lib\Help;
use App\Models\Base;
use Illuminate\Support\Facades\Validator;

class FinanceChannelType extends Base
{
    /**
     * 支付种类表列表
     * @var string
     */
    protected $table = 'finance_channel_type';

    /**
     * @var array
     */
    protected $fillable = ['type_name', 'type_sign', 'is_bank', 'icon'];

    /**
     * 添加过滤参数
     * @var array
     */
    protected $create_rules = [
        'type_name' => 'required|string',//支付方式种类名称
        'type_sign' => 'required|string',//支付方式种类标记
        'is_bank'   => 'required|in:0,1',//是否是银行 0 不是 1 是
        'icon'      => 'required|string',//支付方式图标
    ];

    /**
     * 添加过滤参数
     * @var array
     */
    protected $edit_rules = [
        'type_name' => 'string',//支付方式种类名称
        'type_sign' => 'string',//支付方式种类标记
        'is_bank'   => 'in:0,1',//是否是银行 0 不是 1 是
    ];

    /**
     * 获取支付种类表列表信息
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 20)
    {
        $query = self ::orderBy('id', 'desc');
        // 搜索是否是银行
        if (isset($c['is_bank']) && $c['is_bank'] !== null) {
            $query -> where('is_bank', $c['is_bank']);
        }

        // 搜索ID
        if (isset($c['id']) && $c['id'] !== null) {
            $query -> where('id', $c['id']);
        }

        $total = $query -> count();
        if ($total === 0) {
            $total = 1;
        }
        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize    = isset($c['page_size']) ? intval($c['page_size']) : $total;

        $offset      = ($currentPage - 1) * $pageSize;

        $data        = $query -> skip($offset) -> take($pageSize) -> get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
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
        $picSavePath = $imageObj -> depositPath($path, 1, 1);
        $previewPic  = $imageObj -> uploadImg($picSource, $picSavePath);
        return $previewPic;
    }

    /**
     * 保存
     * @param $params
     * @return bool
     */
    public function saveItem($params)
    {
        // 业务校验 编辑
        if ($this -> id > 0) {
            $validator = Validator ::make($params, $this -> edit_rules);
            if ($validator -> fails()) {
                return $validator -> errors() -> first();
            }
        } else {
            $validator = Validator ::make($params, $this -> create_rules);
            if ($validator -> fails()) {
                return $validator -> errors() -> first();
            }
        }

        //判断是否重复添加
        $isExist = $this -> isExist($params);
        if ($isExist == true) {
            return Help ::returnApiJson('对不起,请勿重复输入!', 0, []);
        }

        $this -> type_name = $params['type_name'] ?? $this->type_name;
        $this -> type_sign = $params['type_sign'] ?? $this->type_sign;
        $this -> is_bank   = $params['is_bank'] ?? $this  ->is_bank;
        $this -> icon      = $params['icon'] ?? $this     ->icon;

        $this -> save();
        return true;
    }

    /**
     * 判断添加标记和名称不能重复
     * @param $params
     * @return bool
     */
    public function isExist($params)
    {
        //0添加验证，1编辑验证
        $array = [
            ['type_name', '=', $params['type_name'] ?? $this -> type_name],
            ['type_sign', '=', $params['type_sign'] ?? $this -> type_sign],
        ];

        //判断自己是否可以修改
        if ($this -> id > 0) {
            $count = $this -> where($array) -> count();
            if ($count <= 1) {
                return false;
            } else {
                return true;
            }
        }

        $isExist = $this -> where($array)->exists();
        if ($isExist) {
            return $isExist;
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    static function getOptions()
    {
        $data    = self::all();
        $options = [];
        foreach ($data as $key => $item) {
            $options[$item['type_sign']] = $item['type_name'];
        }
        return $options;
    }
}
