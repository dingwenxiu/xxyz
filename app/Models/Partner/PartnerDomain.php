<?php

namespace App\Models\Partner;

use App\Lib\Logic\Cache\PartnerDomainCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PartnerDomain extends Model
{
    protected $table = 'partner_domain';

    public $rules = [
        'domain'    => 'required|min:2|max:64',
        'name'      => 'required|min:2|max:32',
        'type'      => 'required|in:1,2,3,4',
        'env_type'  => 'required|in:1,2,3',
        'remark'    => 'required|min:2|max:128',
    ];

    const DOMAIN_TYPE_FRONTEND  = 1;
    const DOMAIN_TYPE_PARTNER   = 2;

    static $envTypeList = [
        1 => "本地环境",
        2 => "测试环境",
        3 => "线上环境",
    ];

    static $typeList = [
        1   => "投注 API",
        2   => "商户 API",
    ];

    /**
     * 获取列表
     * @param $c
     * @return mixed
     */
    static function getList($c)
    {
        $query = self::orderBy('id', 'DESC');

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign'] && $c['partner_sign'] != 'all') {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 类型
        if (isset($c['type']) && $c['type'] && $c['type'] != "all") {
            $query->where('type', $c['type']);
        }

        // env_type
        if (isset($c['env_type']) && $c['env_type'] && $c['env_type'] != "all") {
            $query->where('env_type', $c['env_type']);
        }

        // 域名显示类型
        if (isset($c['type_list']) && $c['type_list'] && $c['type_list'] != "all") {
            $items = $query->where('type', $c['type_list'])->get();
            foreach ($items as $item) {
                $url = str_replace("api.", "www.", trim($item->domain));
                $item->domain = $url;
            }
            return ['data' => $items];
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $items = $query->skip($offset)->take($pageSize)->get();

        foreach ($items as $item) {
            $item->env_type_desc     = self::$envTypeList[$item->env_type];
            $item->type_desc         = self::$typeList[$item->type];
        }

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取商户域名
     * @param $partnerSign
     * @return array
     */
    static function getPartnerDomain($partnerSign) {
        $items  = self::where("partner_sign", $partnerSign)->where('status', 1)->get();
        $data   = [];
        foreach ($items as $item) {
            $item->env_type_desc     = self::$envTypeList[$item->env_type];
            $item->type_desc         = self::$typeList[$item->type];
            $data[] = $item;
        }

        return $data;
    }

    /**
     * 保存域名
     * @param $data
     * @param null $admin
     * @return bool|string
     * @throws \Exception
     */
    public function saveItem($data, $admin = null) {

        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $this->partner_sign         = $data['partner_sign'];
        $this->name                 = $data['name'];
        $this->domain               = $data['domain'];
        $this->type                 = $data['type'];
        $this->env_type             = $data['env_type'];
        $this->remark               = $data['remark'];
        $this->add_partner_admin_id = $admin ? $admin->id : '999999';

        $this->save();

        // 刷新缓存
        PartnerDomainCache::flushPartnerDomain($this->partner_sign);

        return true;
    }

    static function domainTestSet($c, $partner)
    {
        $domains = self::where('partner_sign', $partner->sign)->get();

        db()->beginTransaction();
        try {

            foreach ($domains as $domain) {
                $item = self::find($domain->id);
                switch ($domain->type) {
                    case 1:
                    case 2:
                    $domainName = $c['test_domain_name'];
                        break;
                    default:
                        $domainName = "";
                }

                $item->domain = $domainName;
                $item->save();

                db()->commit();
                // 刷新缓存
                PartnerDomainCache::flushPartnerDomain($partner->sign);
                return ['res' => 1];
            }

        } catch (\Exception $e) {
            db()->rollback();
            return ['res' => 0, 'msg'=> $e->getMessage()];
        }

    }

}
