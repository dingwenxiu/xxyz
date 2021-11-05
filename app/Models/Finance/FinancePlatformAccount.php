<?php

namespace App\Models\Finance;

use App\Lib\Clog;
use Exception;
use App\Lib\Help;
use App\Models\Base;
use App\Lib\Pay\Fmis;
use Illuminate\Support\Facades\DB;
use App\Models\Partner\PartnerAdminGroup;
use Illuminate\Support\Facades\Validator;
use App\Models\Finance\FinancePlatform;


class FinancePlatformAccount extends Base
{
    /**
     * 商户信息列表
     * @var string
     */
    protected $table = 'finance_platform_account';

    /**
     * 获取需要的字段
     * @var array
     */
    protected $fillable = ['partner_sign', 'platform_sign', 'merchant_code',
        'merchant_secret', 'public_key', 'private_key', 'app_id', 'status'
    ];

    /**
     * @var array
     */
    public $create_rules = [
        'platform_sign'       => 'required|string',  // 第三方域名
        'merchant_code'       => 'required|numeric|unique:finance_platform_account,merchant_code', // 商户号
        'merchant_secret'     => 'required|string',  // 商户秘钥
        'public_key'          => 'string',  // 第三方公钥
        'private_key'         => 'string',  // 第三方私钥
        'app_id'              => 'numeric', // 第三方终端号
        'status'              => 'required|in:0,1',           // 状态 1 启用 0 停用
    ];

    /**
     * @var array
     */
    public $edit_rules = [
        'platform_sign'       => 'string',  // 第三方域名
        'merchant_code'       => 'numeric', // 商户号
        'merchant_secret'     => 'string',  // 商户秘钥
        'public_key'          => 'string',  // 第三方公钥
        'private_key'         => 'string',  // 第三方私钥
        'app_id'              => 'numeric', // 第三方终端号
        'status'              => 'in:0,1',  // 状态 1 启用 0 停用
    ];

    /**
     * 获取商户信息列表信息
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 20)
    {
        $query = self ::select('finance_platform_account.*','finance_platform.is_pull')
            ->leftjoin('finance_platform','finance_platform.platform_sign','=','finance_platform_account.platform_sign')
            ->groupby('finance_platform_account.id')
            ->orderBy('finance_platform_account.id', 'desc');

        // 查询本商户下面数据
        if (isset($c['partner_sign']) && $c['partner_sign'] !== null) {
            $query -> where('finance_platform_account.partner_sign', $c['partner_sign']);
        }


        // ID查询
        if (isset($c['id']) && $c['id']) {
            $query -> where('finance_platform_account.id', $c['id']);
        }

        //状态查询
        if (isset($c['status']) && $c['status'] !== null) {
            $query -> where('finance_platform_account.status', $c['status']);
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
     * 判断不能重复
     * @param $params
     * @param $platform_sign
     * @return bool
     */
    public function isExist($params, $platform_sign)
    {
        $array = [
            ['partner_sign', '=', $params['partner_sign'] ?? $platform_sign],
            ['platform_sign', '=', $params['platform_sign'] ?? $this -> platform_sign],
            ['merchant_code', '=', $params['merchant_code'] ?? $this -> merchant_code],
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
        } else {
            return false;
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
        $isExist = $this -> isExist($params, $partnerAdminUser -> partner_sign);
        if ($isExist == true) {
            return Help ::returnApiJson('对不起,请勿重复输入!', 0, []);
        }

        $financePlatform             = FinancePlatform::where('platform_sign',$this -> platform_sign)->first();

        $this -> partner_sign        = $partnerAdminUser -> partner_sign;
        $this -> platform_sign       = $params['platform_sign']?? $this -> platform_sign;
        $this -> merchant_code       = $params['merchant_code'] ?? $this -> merchant_code;
        $this -> merchant_secret     = $params['merchant_secret'] ?? $this -> merchant_secret;
        $this -> public_key          = $params['public_key'] ?? $this -> public_key;
        $this -> private_key         = $params['private_key'] ?? $this -> private_key;
        $this -> app_id              = $params['app_id'] ?? $this -> app_id;
        $this -> status              = $params['status'] ?? $this -> status;

        $this -> save();
        return true;
    }

    /**
     * 更新支付渠道
     * @param $params
     * @param $partnerAdminUser
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \ErrorException
     */
    public function updateChannel($params, $partnerAdminUser)
    {
        // 获取用户等级
        $user_level         = PartnerAdminGroup ::select('level') -> find($partnerAdminUser -> group_id);
        if (!$user_level) {
            return Help ::returnApiJson("获取支付渠道成功", 0, []);
        }

        $this -> username      = $partnerAdminUser -> username;
        $this -> user_level    = $user_level -> level;

        $merchant_id        = $params['merchant_code'];
        $partnerAdminUser -> merchant_code = $merchant_id;

        if(!isset($params->platform_sign)){
            return Help ::returnApiJson("支付渠道不存在", 0, []);
        }

        // 实例化
        $class = "\\App\\Lib\\Pay\\" . ucfirst(strtolower($params->platform_sign));

        $financePlatform = new $class();
        $payment_channel    = $financePlatform -> payment_channel($merchant_id, $params);
        
        // 获取支付渠道
        $data               = $financePlatform -> getRechargeChannel($this);

        // 充值写入数据开启事务处理
        DB ::beginTransaction();
        try {
            $this -> del_rechargr($params); //充值信息删除
            $resouce = [];
            foreach ($data as $data_list) {
                $resouce[] = $this -> insert($data_list->list, $partnerAdminUser); // 添加
            }
            if ($resouce[0] !== true) {
                DB ::rollBack();
            }

            $this -> del_withdraw($params); //提现信息删除
            $resouce1 = [];
            foreach ($payment_channel as $data_list) {
                $resouce1[] = $this -> insert1($data_list, $partnerAdminUser); // 添加
            }
            if ($resouce1[0] !== true) {
                DB ::rollBack();
            }

            DB ::commit();
            return $resouce[0];
        } catch (Exception $e) {
            DB ::rollBack();
        }
    }

    /**
     * 充值删除
     * @param $params
     * @return mixed
     */
    public function del_rechargr($params)
    {
        return FinancePlatformAccountChannel ::where('partner_sign', $params -> partner_sign)
            -> where('type_sign', '!=', 'withdraw')
            -> delete();
    }

    /**
     * 提现删除
     * @param $params
     * @return mixed
     */
    public function del_withdraw($params)
    {
        return FinancePlatformAccountChannel ::where('partner_sign', $params -> partner_sign)
            -> where('type_sign', '=', 'withdraw')
            -> delete();
    }

    /**
     * 充值插入数据
     * @param $data
     * @param $partnerAdminUser
     * @return bool|\Illuminate\Http\JsonResponse|string
     */
    private function insert($data, $partnerAdminUser)
    {
        if (!isset($data) || empty($data) || !is_array($data)){
            return true;
        }
        $_update = [];
        foreach ($data as $params) {
            $type_name = FinanceChannelType ::where('type_sign', $params -> channel_sign)
                -> select('type_name')
                -> first();
            $account = FinancePlatformAccount ::where('partner_sign', $partnerAdminUser -> partner_sign)
                -> where('merchant_code', $partnerAdminUser -> merchant_code)
                -> select('id','platform_sign')
                -> first();
            $update[] = [
                'account_id'          => $account -> id ?? '',                                               // 帐户
                'partner_sign'        => $partnerAdminUser -> partner_sign,                                     // 合作者标记
                'platform_sign'       => $account -> platform_sign ?? '',                                        // 平台
                'platform_child_sign' => $params -> platform_sign ?? '',                                        // 平台下级
                'channel_sign'        => $params -> platform_sign.'_'.$params -> channel_sign ?? '',            // 渠道的标识
                'type_sign'           => $params -> channel_sign ?? '',                                         // 渠道类型
                'platform_channel_id' => $params -> id ?? '',                                                   // 渠道的id
                'front_name'          => $type_name -> type_name ?? '',                                         // 前台名称
                'front_remark'        => $type_name -> type_name ?? '',                                         // 前台备注
                'back_name'           => $type_name -> type_name ?? '',                                         // 后台名称
                'back_remark'         => $type_name -> type_name ?? '',                                         // 后台备注
                'fee_type'            => 1,                                                                     // 手续费类型
                'fee_from'            => 1,                                                                     // 手续费来源
                'fee_amount'          => 0,                                                                     // 手续费
                'fee_return'          => 0,                                                                     // 返利
                'max'                 => $params ->max??'',                                                     // 最大金额
                'min'                 => $params ->min??1,                                                      // 最小金额
                'level'               => !empty($params->user_level)?$params -> user_level:'1,2,3,4,5,6,7,8,9', // 等级
                'device'              => $params -> is_web ?? 0,                                                // 设备 0 全部 1 电脑端 2 手机端
                'sort'                => 0,                                                                     // 排序
                'status'              => 1,                                                                     // 状态 1 启用 0 停用
                'do_fixed_price'      => !empty($params->do_fixed_price)?$params->do_fixed_price:'',            // 单价，空就是可以输入金额/固定金额
            ];
        }
        $_update[] = $update;
        foreach ($_update[0] as $items){
            $accountChannel = new FinancePlatformAccountChannel();
            $accountChannel -> saveItem($items, $partnerAdminUser);
        }
        return true;
    }

    /**
     * 提现插入数据
     * @param $params
     * @param $partnerAdminUser
     * @return bool|\Illuminate\Http\JsonResponse|string
     */
    private function insert1($params, $partnerAdminUser)
    {
        $account = FinancePlatformAccount ::where('partner_sign', $partnerAdminUser -> partner_sign)
                -> select('id','platform_sign')
                -> first();
        $update = [
                'account_id'          => $account -> id ?? '',                                        // 帐户
                'partner_sign'        => $partnerAdminUser -> partner_sign,                           // 合作者标记
                'platform_sign'       => $account->platform_sign,                                     // 合作者标记
                'platform_child_sign' => $params->sign,                                               // 合作者标记
                'channel_sign'        => isset($params -> sign) ?$params -> sign .'_withdraw':'',     // 渠道的标识
                'type_sign'           => 'withdraw',                                                  // 渠道类型
                'platform_channel_id' => $params -> id ?? '',                                         // 渠道的id
                'front_name'          => isset($params -> sign) ?$params -> sign :'',                 // 前台名称
                'front_remark'        => isset($params -> sign) ?$params -> sign :'',                 // 前台备注
                'back_name'           => isset($params -> sign) ?$params -> sign :'',                 // 后台名称
                'back_remark'         => isset($params -> sign) ?$params -> sign :'',                 // 后台备注
                'fee_type'            => 1,                                                           // 手续费类型
                'fee_from'            => 1,                                                           // 手续费来源
                'fee_amount'          => 0,                                                           // 手续费
                'fee_return'          => 0,                                                           // 返利
                'max'                 => $params ->max ?? '',                                         // 最大金额
                'min'                 => $params ->min ?? 1,                                          // 最小金额
                'device'              => 0,                                                           // 设备 0 全部 1 电脑端 2 手机端
                'sort'                => 0,                                                           // 排序
                'level'               => '1,2,3,4,5,6,7,8,9',                                         // 状态 1 启用 0 停用
                'status'              => 1,                                                           // 状态 1 启用 0 停用
                'do_fixed_price'      => '',                                                          // 单价，空就是可以输入金额/固定金额

        ];
        $accountChannel = new FinancePlatformAccountChannel();
        return $accountChannel -> saveItem($update, $partnerAdminUser);
    }

    /**
     * 更新支付渠道
     * @param $params
     * @param $partnerAdminUser
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \ErrorException
     */
    public function updatePaymentChannel($params, $partnerAdminUser)
    {
        // 获取用户等级
        $user_level         = PartnerAdminGroup ::select('level') -> find($partnerAdminUser -> group_id);
        if (!$user_level) {
            return Help ::returnApiJson("获取支付渠道成功", 0, []);
        }

        $this -> username      = $partnerAdminUser -> username;
        $this -> user_level    = $user_level -> level;

        if(!isset($params->platform_sign)){
            return Help ::returnApiJson("支付渠道不存在", 0, []);
        }

        $merchant_id        = $params['merchant_code'];

        // 实例化
        $class = "\\App\\Lib\\Pay\\" . ucfirst(strtolower($params->platform_sign));

        $financePlatform = new $class();
        $payment_channel    = $financePlatform -> payment_channel($merchant_id, $params);
        
        Clog ::rechargeLog("更新支付渠道", [$payment_channel]);
        if (empty($payment_channel)){
            return '对不起,没有可以更新的代付渠道';
        }
        $partnerAdminUser -> merchant_code = $merchant_id;
        // 充值写入数据开启事务处理
        DB ::beginTransaction();
        try {
            $this -> del_withdraw($params); //提现信息删除
            $resouce = [];
            foreach ($payment_channel as $data_list) {
                $resouce[] = $this -> insert1($data_list, $partnerAdminUser); // 添加
            }
            if ($resouce[0] !== true) {
                DB ::rollBack();
            }
            DB ::commit();
            return $resouce[0];
        } catch (Exception $e) {
            DB ::rollBack();
        }
    }

    /**
     * 更新充值渠道
     * @param $params
     * @param $partnerAdminUser
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \ErrorException
     */
    public function updateRechargeChannel($params, $partnerAdminUser)
    {
        // 获取用户等级
        $user_level         = PartnerAdminGroup ::select('level') -> find($partnerAdminUser -> group_id);
        if (!$user_level) {
            return Help ::returnApiJson("获取支付渠道成功", 0, []);
        }

        $this -> username      = $partnerAdminUser -> username;
        $this -> user_level    = $user_level -> level;

        $merchant_id        = $params['merchant_code'];
        $partnerAdminUser -> merchant_code = $merchant_id;

        // 实例化
        $class = "\\App\\Lib\\Pay\\" . ucfirst(strtolower($params->platform_sign));

        $financePlatform = new $class();
        
        $data               = $financePlatform -> getRechargeChannel($this);
        Clog ::rechargeLog("获取支付渠道", [$data]);
        if (empty($data)){
            return '对不起,没有可以更新的充值渠道';
        }
        // 充值写入数据开启事务处理
        DB ::beginTransaction();
        try {
            $this -> del_rechargr($params); //充值信息删除
            $resouce = [];
            foreach ($data as $data_list) {
                $resouce[] = $this -> insert($data_list->list, $partnerAdminUser); // 添加
            }

            if ($resouce[0] !== true) {
                DB ::rollBack();
            }
            DB ::commit();
            return $resouce[0];
        } catch (Exception $e) {
            DB ::rollBack();
        }
    }

}
