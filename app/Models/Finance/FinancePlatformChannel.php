<?php

namespace App\Models\Finance;

use App\Lib\Help;
use App\Models\Base;
use Illuminate\Support\Facades\Validator;


class FinancePlatformChannel extends Base
{
    public const DIR_IN         = 1;
    public const DIR_OUT        = 0;
    public const STATUS_ENABLE  = 1; // 启用
    public const STATUS_DISABLE = 0; // 禁用
    protected $guarded          = ['id'];

    /**
     * @var string
     */
    protected $table = 'finance_platform_channel';

    /**
     * @var array
     */
    protected $fillable = [
        'platform_sign', 'type_sign', 'channel_name', 'channel_sign',
        'banks_code', 'request_mode', 'direction', 'status'
    ];

    /**
     * @var array
     */
    public $create_rules = [
        'channel_name'  => 'required|string|unique:finance_platform_channel,channel_name', // 支付方式名称
        'channel_sign'  => 'required|string|unique:finance_platform_channel,channel_sign', // 支付方式标记
        'platform_sign' => 'required|string', // 支付方式名称
        'type_sign'     => 'required|string', // 支付方式名称
        'banks_code'    => 'string',          // 支付方式请求地址
        'request_mode'  => 'in:0,1',          // 支付的请求方式 0 jump 1 json
        'direction'     => 'in:0,1',          // 金流的方向 1 入款 0 出款
        'status'        => 'in:0,1',          // 状态 1 上架 0 下架
    ];

    /**
     * @var array
     */
    public $edit_rules = [
        'platform_sign' => 'string',          // 支付方式名称
        'type_sign'     => 'string',          // 支付方式名称
        'channel_name'  => 'string',          // 支付方式名称
        'channel_sign'  => 'string',          // 支付方式标记
        'banks_code'    => 'string',          // 支付方式请求地址
        'request_mode'  => 'in:0,1',          // 支付的请求方式 0 jump 1 json
        'direction'     => 'in:0,1',          // 金流的方向 1 入款 0 出款
        'status'        => 'in:0,1',          // 状态 1 上架 0 下架
    ];

    /**
     * 获取配置列表信息
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 20)
    {
        $query = self ::orderBy('id', 'desc');

        if(isset($c['partner_sign']) && $c['partner_sign'] ) {
            $query -> where('partner_sign', $c['partner_sign']);
        }

        //ID是否存在
        if (isset($c['id']) && $c['id']) {
            $query -> where('id', $c['id']);
        }

        // 支付平台的标识
        if (isset($c['platform_sign']) && $c['platform_sign'] && $c['platform_sign'] != "all") {
            $query -> where('platform_sign', $c['platform_sign']);
        }

        // 支付子平台的标识
        if (isset($c['platform_child_sign']) && $c['platform_child_sign'] && $c['platform_child_sign'] != "all") {
            $query -> where('platform_child_sign', $c['platform_child_sign']);
        }

        //status是否存在
        if (isset($c['status']) && $c['status'] !== null) {
            $query -> where('status', $c['status']);
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
     * 判断前端接收过滤参数
     * @param $params
     * @return string
     */
    public function Validator($params)
    {
        if ($this -> id > 0) {
            $validator = Validator ::make($params, $this -> create_rules);
        } else {
            $validator = Validator ::make($params, $this -> edit_rules);
        }
        if ($validator -> fails()) {
            return $validator -> errors() -> first();
        }
        return true;
    }

    /**
     * 判断添加标记和名称不能重复
     * @param $params
     * @return bool
     */
    public function isExist($params)
    {
        $array = [
            ['platform_sign', '=', $params['platform_sign'] ?? $this -> platform_sign],
            ['type_sign', '=', $params['type_sign'] ?? $this -> type_sign],
            ['channel_name', '=', $params['channel_name'] ?? $this -> channel_name],
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

        $isExist = $this -> where($array) -> exists();
        if ($isExist) {
            return $isExist;
        }
    }

    /**
     * 保存
     * @param $params
     * @param $partnerAdminUser
     * @return bool|string
     */
    public function saveItem($params, $partnerAdminUser)
    {
        // 业务校验
        if ($this -> id > 0) {
            //编辑验证
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
            return Help ::returnApiJson('输入参数重复,请重新输入!', 0, []);
        }

        $this -> platform_sign       = $params['platform_sign'] ?? $this -> platform_sign;
        $this -> platform_child_sign = $params['platform_child_sign'] ?? $this -> platform_child_sign;
        $this -> type_sign           = $params['type_sign'] ?? $this     -> type_sign;
        $this -> channel_name        = $params['channel_name'] ?? $this  -> channel_name;
        $this -> channel_sign        = $params['channel_sign'] ?? $this  -> channel_sign;
        $this -> banks_code          = $params['banks_code'] ?? $this    -> banks_code;
        $this -> request_mode        = $params['request_mode'] ?? $this  -> request_mode;
        $this -> direction           = $params['direction'] ?? $this     -> direction;
        $this -> status              = $params['status'] ?? $this        -> status;

        $this -> save();
        return true;
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
            if (!isset($options[$item['platform_sign']])) {
                $options[$item['platform_sign']] = [];
            }

            $options[$item['platform_sign']][$item->channel_sign] = $item['channel_name'];
        }

        return $options;
    }

    /**
     * @param $channel
     * @return array
     */
    static function getOption($channel)
    {
        if (isset($channel)){
            $data    = self::where('channel_sign', $channel)->get();
            $options     = [];
            foreach ($data as $key => $item) {
                $options = $item['channel_name'];
            }
        }
        return $options;
    }
}
