<?php

namespace App\Models\Finance;

use App\Lib\Help;
use App\Models\Base;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class FinancePlatform extends Base
{

    protected $guarded = ['id'];

    /**
     * 支付方式厂商列表
     * @var string
     */
    protected $table = 'finance_platform';

    /**
     * @var array
     */
    protected $fillable = ['platform_name', 'platform_url','platform_sign', 'whitelist_ips'];

    /**添加验证方法
     * @var array
     */
    public $create_rules = [
        'platform_name' => 'required|string',  // 厂商名称
        'platform_url'  => 'required|string',  // 厂商URL
        'platform_sign' => 'required|string',  // 厂商標識
        'whitelist_ips' => 'required|string',  // IP白名单
        'is_pull'       => 'in:0,1',           // 是否拉取
    ];

    /**
     * 修改验证方法
     * @var array
     */
    public $edit_rules = [
        'platform_name' => 'string', // 厂商名称
        'platform_url'  => 'string', // 厂商URL
        'platform_sign' => 'string', // 厂商標識
        'whitelist_ips' => 'string', // IP白名单
        'is_pull'       => 'in:0,1', // 是否拉取
    ];

    /**
     * 获取支付方式厂商列表信息
     * @param $c
     * @param int $pageSize
     * @return array
     */
    static function getList($c, $pageSize = 20)
    {
        $query = self ::orderBy('id', 'desc');
        //ID查询
        if (isset($c['id']) && $c['id']) {
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
     * 判断添加标记和名称不能重复
     * @param $params
     * @return bool
     */
    public function isExist($params)
    {
        $array = [
            ['platform_name', '=', $params['platform_name'] ?? $this -> platform_name],
            ['platform_sign', '=', $params['platform_sign'] ?? $this -> platform_sign],
        ];
        //判断自己是否可以修改
        if ($this -> id > 0) {
            $count = $this -> where($array) -> count();
            if ($count > 1) {
                return true;
            } else {
                return false;
            }
        }

        $isExist = $this -> where($array)->exists();
        if ($isExist) {
            return $isExist;
        }
    }

    /**
     * 保存
     * @param $params
     * @param $partnerAdminUser
     * @return bool|\Illuminate\Http\JsonResponse|string
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

        //处理拼接前台获取的IP白名单数据
        if (!empty($params['whitelist_ips']) && isset($params['whitelist_ips'])) {
            $params['whitelist_ips'] = $params['whitelist_ips'] . '|';
            $params['whitelist_ips'] = trim($params['whitelist_ips'], '|');
        }

        //判断是否重复添加
        $isExist = $this -> isExist($params);
        if ($isExist == true) {
            return Help ::returnApiJson('对不起,输入重复,请重新输入!', 0, []);
        }

        $this -> platform_name = $params['platform_name'] ?? $this -> platform_name;
        $this -> platform_sign = $params['platform_sign'] ?? $this -> platform_sign;
        $this -> platform_url  = $params['platform_url'] ?? $this -> platform_url;
        $this -> whitelist_ips = $params['whitelist_ips'] ?? $this -> whitelist_ips;
        $this -> is_pull       = $params['is_pull'] ?? $this -> is_pull;

        $this -> save();
        return true;
    }

    /**
     * @param $c
     * @return array
     */
    static public function getListChild($c)

    {
        $query = self::select(
            DB::raw('finance_platform.platform_name'),
            DB::raw('finance_platform.platform_url'),
            DB::raw('finance_platform.platform_sign'),
            DB::raw('finance_platform_channel.type_sign'),
            DB::raw('finance_platform_channel.channel_name'),
            DB::raw('finance_platform_channel.channel_sign'),
            DB::raw('finance_channel_type.type_name'),
            DB::raw('finance_platform_account.id as account_id'),
            DB::raw('finance_platform_account.partner_sign as partner_sign')
        )
        ->leftJoin('finance_platform_channel', 'finance_platform_channel.platform_sign', '=', 'finance_platform.platform_sign')
        ->leftJoin('finance_channel_type', 'finance_channel_type.type_sign', '=', 'finance_platform_channel.type_sign')
        ->leftJoin('finance_platform_account', 'finance_platform_account.platform_sign', '=', 'finance_platform.platform_sign')
        ->orderBy('finance_platform.id', 'asc');

        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('finance_platform_account.partner_sign', $c['partner_sign']);
        }

        // ID
        if (isset($c['sign']) && $c['sign']) {
            $query->where('finance_platform.platform_sign', $c['sign']);
        }
        $data  = $query -> distinct()->get();
        $_data = [];
        foreach ($data as $k=>$tiem) {
            $_data['account_id'][0] = [
                'account_id'        => $tiem -> account_id,
            ];
            $_data['platform'][0]   = [
                'platform_name'     => $tiem -> platform_name,
                'platform_sign'     => $tiem -> platform_sign,
            ];
            $_data['type'][]        = [
                'type_sign'         => $tiem -> type_sign,
                'type_name'         => $tiem -> type_name,
                'back_name'         => $tiem -> channel_name,
                'back_remark'       => $tiem -> channel_name,
                'channel_sign'      => $tiem -> channel_sign,
            ];
            $_data['channel'][]     = [
                'channel_name'      => $tiem -> channel_name,
                'channel_sign'      => $tiem -> channel_sign,
            ];
        }
        return $_data;
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
            $options[$item['platform_sign']] = $item['platform_name'];
        }

        return $options;
    }

    /**
     * @return array
     * @throws \Exception
     */
    static function getPlatformList()
    {
        $data    = self::all();
        $options = [];
        foreach ($data as $key  => $item) {
            $options[] = [
                'platform_sign' => $item['platform_sign'],
                'platform_name' => $item['platform_name'],
            ];
        }

        return $options;
    }

    /**
     * @param $c
     * @return array
     */
    static function getPlatformAccountList($c)
    {
        $data = self::select(
            DB::raw('finance_platform.platform_name'),
            DB::raw('finance_platform.platform_sign'),
            DB::raw('finance_platform_account.id as account_id'),
            DB::raw('finance_platform_account.partner_sign as partner_sign')
        )
            ->leftJoin('finance_platform_account', 'finance_platform_account.platform_sign', '=', 'finance_platform.platform_sign')
            ->where('finance_platform_account.partner_sign',$c['partner_sign'])
            ->orderBy('finance_platform.id', 'desc')
            ->get();

        $options = [];
        foreach ($data as $key  => $item) {
            $options[] = [
                'account_id'    => $item['account_id'],
                'platform_sign' => $item['platform_sign'],
                'platform_name' => $item['platform_name'],
            ];
        }
        return $options;
    }
}
