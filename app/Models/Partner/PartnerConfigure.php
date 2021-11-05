<?php

namespace App\Models\Partner;

use App\Lib\Logic\Cache\ConfigureCache;
use App\Models\Admin\Configure;
use Illuminate\Support\Facades\Validator;

/**
 * 伙伴配置
 * Class Configure
 * @package App\Models\Admin
 */
class PartnerConfigure extends Base
{
    protected $table = 'partner_configures';

    protected static $offset = 1000000;
    protected static $pidOffset = 1000;

    public $rules = [
        'name'                  => 'required|min:2|max:32',
        'sign'                  => 'required|min:2|max:32',
        'value'                 => 'required|min:2|max:64',
        'description'           => 'required|min:2|max:64',
    ];

    /**
     * @param $c
     * @return mixed
     */
    static function getConfigList($c) {
        $data = self::where("partner_sign", $c["partner_sign"])->orderBy('id', 'desc')->get();

        $_data = $parentData = [];
        foreach ($data as $item) {
            if ($item->pid > 0) {
                if (!isset($parentData[$item->pid])) {
                    $parentData[$item->pid] = [];
                }

                $parentData[$item->pid][] = $item->toArray();
            } else {
                if (!isset($_data[$item->id])) {
                    $_item = $item->toArray();
                    $_item['child'] = [];
                    $_data[$item->id] = $_item;
                }
            }
        }

        foreach ($_data as &$parent) {
            $parent['child'] = isset($parentData[$parent['id']]) ? $parentData[$parent['id']] : [];
        }

        return $_data;
    }
 
    /**
     * 初始化 商户配置
     * @param $partner
     * @return bool
     */
    static function initPartnerConfig($partner, $param = []) {
        $allConfigItems = Configure::all();
        $data = [];
        foreach ($allConfigItems as $item) {
            $data[]  = [
                'id'                => $partner->id * self::$offset + $item->id,
               // 'pid'               => $item->pid ? $partner->id * self::$offset + $item->pid : 0,
                'pid'               => $item->pid ? $item->pid * self::$pidOffset+ $partner->id * self::$offset: 0,
                'partner_sign'      => $partner->sign,
                'sign'              => $item->sign,
                'can_edit'          => $item->partner_edit,
                'can_show'          => $item->partner_show,
                'name'              => $item->name,
                'value'             => $param[$item->sign] ?? $item->value,
                'status'            => $item->status,
                'description'       => $item->description,
            ];
        }

        self::insert($data);

        return true;
    }

    /**
     * 保存
     * @param $params
     * @param $partnerSign
     * @param $partnerAdminUser
     * @return bool|string
     * @throws \Exception
     */
    public function saveItem( $params, $partnerSign, $partnerAdminUser = null) {

        $this->can_show    = $params["can_show"];
        $this->can_edit    = $params["can_edit"];
        $this->sign        = $params["sign"];
        $this->name        = $params["name"];
        $this->value       = $params["value"];
        $this->description = $params["description"];

        // 变更人
        if (!$this->id) {
            $this->add_partner_admin_id         = $partnerAdminUser->id;
        } else {
            $this->update_partner_admin_id      = $partnerAdminUser->id;
        }
        $this->save();

        // 刷新缓存
        ConfigureCache::clearPartnerConfigureCache($partnerSign);

        return true;
    }

    // 获取所有配置
    static function getAllConfig() {
        $config = self::select('sign', 'value')->where('status', 1)->get();
        return $config;
    }

    // 设置
    static function configureSet($partnerSign, $key, $value) {
        db()->table("partner_configures")->where('partner_sign', $partnerSign)->where('sign', $key)->update(['value' => $value]);

        return true;
    }
}
