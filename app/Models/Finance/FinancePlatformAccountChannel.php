<?php

namespace App\Models\Finance;

use App\Lib\Help;
use App\Models\Base;
use App\Models\Admin\SysBank;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class FinancePlatformAccountChannel extends Base
{
    public const STATUS_ENABLE  = 1; //启用
    public const STATUS_DISABLE = 0; //禁用

    public const DIRECTION_IN   = 1; //入款
    public const DIRECTION_OUT  = 0; //出款

    public const STATUS_ALL     = 0; //全部
    public const STATUS_PC      = 1; //电脑端
    public const STATUS_MOBILE  = 2; //手机端
    /**
     * @var string
     */
    protected $table = 'finance_platform_account_channel';

    /**
     * @var array
     */
    protected $fillable = [
        'partner_sign', 'account_id', 'platform_sign', 'channel_sign', 'type_sign',
        'platform_channel_id', 'front_name', 'front_remark', 'back_name', 'back_remark', 'fee_type', 'fee_from',
        'fee_amount', 'fee_return', 'max', 'min', 'device', 'sort', 'level', 'status','do_fixed_price'
    ];

    /**
     * @var array
     */
    public $rules = [
        'platform_sign'       => 'required|string', // 支付厂商
        'type_sign'           => 'required|string', // 支付类型
        'channel_sign'        => 'required|string', // 支付厂商表开放渠道channel_sign
        'platform_channel_id' => 'numeric',         // 充值渠道ID
        'front_name'          => 'required|string', // 前台名称
        'front_remark'        => 'required|string', // 前台备注
        'back_name'           => 'required|string', // 后台名称
        'back_remark'         => 'required|string', // 后台备注
        'fee_type'            => 'required|numeric',// 手续费类型
        'fee_from'            => 'required|numeric',// 手续费来源
        'fee_amount'          => 'required|numeric',// 手续费
        'fee_return'          => 'required|numeric',// 返利
        'max'                 => 'required|numeric',// 最大金额
        'min'                 => 'required|numeric',// 最小金额
        'device'              => 'in:0,1,2',        // 设备 0 全部 1 电脑端 2 手机端
        'sort'                => 'numeric',         // 排序
        'status'              => 'in:0,1',          // 状态 1 启用 0 停用
        'do_fixed_price'      => 'array',           // 单价
    ];

    /**
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 20)
    {
        $query = self ::orderBy('id', 'desc');

        // 1.查询本商户下面数据
        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != "all") {
            $query -> where('partner_sign', $c['partner_sign']);
        }

        // 2.查询本商户下面数据
        if (isset($c['platform_sign']) && $c['platform_sign'] && $c['platform_sign'] != "all") {
            $query -> where('platform_sign', $c['platform_sign']);
        }

        // type_sign
        if (isset($c['type_sign']) && $c['type_sign'] && $c['type_sign'] != "all") {
            $query -> where('type_sign', $c['type_sign']);
        }

        // 3.ID查询
        if (isset($c['id']) && !empty($c['id'])) {
            $query -> where('id', $c['id']);
        }

        // 4.设备device 0:出款  1充值
        if (isset($c['device'])) {
            $query -> where('device', $c['device']);
        }

        // 5.状态
        if (isset($c['status'])) {
            $query -> where('status', $c['status']);
        }

        // 6.手续费类型
        if (isset($c['fee_type'])) {
            $query -> where('fee_type', $c['fee_type']);
        }

        // 7.手续费来源
        if (isset($c['fee_from'])) {
            $query -> where('fee_from', $c['fee_from']);
        }

        // 8.支付平台
        if (isset($c['platform_name']) && !empty($c['platform_name'])) {
            $query -> where('platform_name', $c['platform_name']);
        }


        $total       = $query -> count();

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize    = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;

        $offset      = ($currentPage - 1) * $pageSize;
        $data        = $query -> skip($offset) -> take($pageSize) -> get();
        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    public function saveItem($params, $partnerAdminUser)
    {

        $validator = Validator ::make($params, $this ->rules);
        if ($validator -> fails()) {
            return $validator -> errors() -> first();
        }

        // 2.验证金额是否符合通道所规定的最大最小值
        if (isset($params['max']) && isset($params['min'])){
            if ($params['max'] < $params['min']) {
                return Help ::returnApiJson('金额不符合通道所规定的最大最小值', 0, []);
            }
        }

        // 3.判断是否重复添加
        $isExist = $this -> isExist($params, $partnerAdminUser);
        if ($isExist == true) {
            return Help ::returnApiJson('输入参数重复,请重新输入!', 0, []);
        }

        $this -> partner_sign        = $partnerAdminUser -> partner_sign;
        $this -> account_id          = $params['account_id'] ?? $this -> account_id;
        $this -> platform_sign       = $params['platform_sign'] ?? $this -> platform_sign;
        $this -> platform_child_sign = $params['platform_child_sign'] ?? $this -> platform_child_sign;
        $this -> channel_sign        = $params['channel_sign'] ?? $this -> channel_sign;
        $this -> type_sign           = $params['type_sign'] ?? $this -> type_sign;
        $this -> type_sign           = str_ireplace('_wap','',$params['type_sign']);
        $this -> platform_channel_id = $params['platform_channel_id'] ?? $this -> platform_channel_id;
        $this -> front_name          = $params['front_name'] ?? $this -> front_name;
        $this -> front_remark        = $params['front_remark'] ?? $this -> front_remark;
        $this -> back_name           = $params['back_name'] ?? $this -> back_name;
        $this -> back_remark         = $params['back_remark'] ?? $this -> back_remark;

        $this -> fee_type            = $params['fee_type'] ?? $this -> fee_type;
        $this -> fee_from            = $params['fee_from'] ?? $this -> fee_from;
        $this -> fee_amount          = $params['fee_amount'] ?? $this -> fee_amount;
        $this -> fee_return          = $params['fee_return'] ?? $this -> fee_return;

        $this -> max                 = $params['max'] ?? $this -> max;
        $this -> min                 = $params['min'] ?? $this -> min;
        $this -> device              = $params['device'] ?? $this -> device;
        $this -> sort                = $params['sort'] ?? $this -> sort;
        $this -> status              = $params['status'] ?? $this -> status;
        $this -> level               = trim($params['level'],",") ?? $this -> level;

        if (isset($params['do_fixed_price']) && !empty($params['do_fixed_price']) && is_array($params['do_fixed_price'])){
            $this -> do_fixed_price      = implode(',',$params['do_fixed_price']) ?? $this -> do_fixed_price;
        }else{
            $this -> do_fixed_price      = $params['do_fixed_price'] ?? $this -> do_fixed_price;
        }

        $this -> save();
        return true;
    }

    /**
     * 判断添加标记和名称不能重复
     * @param $params
     * @param $partnerAdminUser
     * @return bool
     */
    public function isExist($params, $partnerAdminUser)
    {
        $array = [
            ['partner_sign', '=', $params['partner_sign'] ?? $partnerAdminUser -> partner_sign],
            ['account_id', '=', $params['account_id'] ?? $this -> account_id],
            ['platform_sign', '=', $params['platform_sign'] ?? $this -> platform_sign],
            ['platform_channel_id', '=', $params['platform_channel_id'] ?? $this -> platform_channel_id],
            ['channel_sign', '=', $params['channel_sign'] ?? $this -> channel_sign]
        ];
        // 判断自己是否可以修改
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
     * 获取充值列表
     * @param $c
     * @param string $from
     * @return array
     */
    static function getRechargeChannel($c, $from = "front")
    {
        $query = self ::select(
            DB ::raw('finance_platform_account_channel.id as channel_id'),    // 渠道的ID
            DB ::raw('finance_platform_account_channel.platform_channel_id'), // 渠道的ID
            DB ::raw('finance_platform_account_channel.partner_sign'),        // 商户号
            DB ::raw('finance_platform_account_channel.platform_sign'),       // 平台
            DB ::raw('finance_platform_account_channel.channel_sign'),        // 渠道的标识
            DB ::raw('finance_platform_account_channel.max'),                 // 最大值
            DB ::raw('finance_platform_account_channel.min'),                 // 最小值
            DB ::raw('finance_platform_account_channel.type_sign'),           // 渠道类型
            DB ::raw('finance_platform_account_channel.front_name'),          // 前台名称
            DB ::raw('finance_platform_account_channel.front_remark'),        // 前台备注
            DB ::raw('finance_platform_account_channel.do_fixed_price'),      // 单价
            DB ::raw('finance_platform_account_channel.device'),              // 前台备注
            DB ::raw('finance_platform_account_channel.level'),               // 等级
            DB ::raw('finance_platform_account_channel.status'),              // 状态

            DB ::raw('finance_channel_type.type_name'),                       // 支付种类的名称
            DB ::raw('finance_channel_type.icon'),                            // 支付种类的图标
            DB ::raw('finance_channel_type.is_bank'),                         // 支付种类的图标

            DB ::raw('finance_platform_channel.channel_name'),                // 支付方式名称
            DB ::raw('finance_platform_channel.direction'),                   // 是否跳转
            DB ::raw('finance_platform_channel.banks_code'),                  // 银行码
            DB ::raw('finance_platform_channel.request_mode')                 // 请求方式
        )
            -> leftJoin('finance_channel_type', 'finance_channel_type.type_sign', '=', 'finance_platform_account_channel.type_sign')
            -> leftJoin('finance_platform_channel', 'finance_platform_channel.channel_sign', '=', 'finance_platform_account_channel.channel_sign');

        // 1.只能查看登录商户的信息
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query -> where('finance_platform_account_channel.partner_sign', $c['partner_sign']);
        }

        // 2.设备来源
        if (isset($c['device']) && $c['device']) {
            $query -> whereIn('finance_platform_account_channel.device', $c['device']);
        }

        // 2.2.等级
        if (isset($c['level'])) {
            $query -> whereRaw('FIND_IN_SET(?, finance_platform_account_channel.level)', [$c['level']]);
        }

        // 3.状态 1 启用 0 停用
        if (isset($c['channel_status'])) {
            $query -> where('finance_platform_account_channel.status', $c['channel_status']);
        }

        // 3.金流的方向 1 入款 0 出款
        if (isset($c['direction'])) {
            $query -> where('finance_platform_channel.direction', $c['direction']);
        }

        // 4.状态
        if (isset($c['status'])) {
            $query -> where('finance_platform_account_channel.status', $c['status']);
        }

        // 5.API 返回
        if ("front" === $from) {
            $bankArr = [];
            // 5.1图标
            $sysBanks = SysBank ::get() -> toArray();
            foreach ($sysBanks as $bank) {
                $bankArr[$bank['code']] = [
                    'icon'  => $bank['icon'],
                    'title' => $bank['title'],
                    'code'  => $bank['code'],
                ];
            }

            $data = $query -> get();
            $_data = [];
            // 5.2拼接数据
            foreach ($data as $channel) {
                if (!empty($channel -> banks_code && $channel -> is_bank == 1)) {
                    $tmpData1 = explode('|', $channel -> banks_code);

                    $tmpData2 = [];
                    foreach ($tmpData1 as $_codeStr) {
                        $bankCodeArr = explode('=', $_codeStr);
                        $tmpData2[]  = $bankArr[$bankCodeArr[0]];
                    }
                    $channel -> banks_code = $tmpData2;
                    unset($tmpData2);
                } else {
                    $channel -> banks_code = null;
                }

                if (!isset($_data[$channel -> type_name])) {
                    $_data[$channel -> type_name] = [
                        'name' => $channel -> type_name,
                        'list' => [$channel],
                    ];
                } else {
                    $_data[$channel -> type_name]['list'][] = [
                        'platform_channel_id' => $channel -> platform_channel_id??'',
                        'platform_sign'       => $channel -> platform_sign??'',
                        'channel_sign'        => $channel -> channel_sign??'',
                        'front_name'          => $channel -> front_name??'',
                        'front_remark'        => $channel -> front_remark??'',
                        'type_name'           => $channel -> type_name??'',
                        'type_sign'           => $channel -> type_sign??'',
                        'do_fixed_price'      => $channel -> do_fixed_price??'',
                        'channel_id'          => $channel -> channel_id,
                        'icon'                => $channel -> icon,
                        'level'               => $channel -> level,
                        'max'                 => $channel -> max,
                        'min'                 => $channel -> min,
                        'banks_code'          => $channel -> is_bank && !$channel -> direction ? $channel -> banks_code : [],
                    ];
                }
            }

            return $_data;
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize    = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset      = ($currentPage - 1) * $pageSize;
        // 5.统计总页数
        $total       = $query -> count();
        // 6.分页查询数据
        $data        = $query -> skip($offset) -> take($pageSize) -> get();
        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * @param $partnerSign
     * @return array
     */
    static function getOptions($partnerSign)
    {
        if (isset($partnerSign)){
            $data    = self::where('partner_sign', $partnerSign)->get();
        }
        $data        = self::get();
        $options     = [];
        foreach ($data as $key => $item) {
            $options[$item->platform_channel_id] = $item['front_name'];
        }
        return $options;
    }


    /**
     * @param $channel
     * @param $partnerSign
     * @return array
     */
    static function getOption($channel, $partnerSign)
    {
        if (isset($channel, $partnerSign)){
            $data    = self::where('channel_sign', $channel)->where('partner_sign', $partnerSign)->get();
            $options     = [];
            foreach ($data as $key => $item) {
                $options[$item->platform_channel_id] = $item['front_name'];
            }
        }
        return $options;
    }

    /**
     * 手续费
     * @param $partnerSign
     * @param $channel_sign
     * @return mixed
     */
    static function getFeeAmount($partnerSign,$channel_sign)
    {
        if (isset($channel_sign, $partnerSign)){
            $data = self::where('platform_channel_id', $channel_sign)
                ->where('partner_sign', $partnerSign)
                ->first();
            if ($data){
                return $data->fee_amount;
            }
        }
    }
}
