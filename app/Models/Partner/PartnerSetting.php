<?php

namespace App\Models\Partner;

use App\Models\Casino\CasinoPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PartnerSetting extends Model
{
    protected $table = 'partner_setting';

    protected $fillable = ['partner_sign', 'qr_code'];

    public $rules = [
        'partner_sign'  => 'required|min:2|max:32',
    ];

    /**
     * 添加过滤参数
     * @var array
     */
    public $create_rules = [
        'partner_sign' => 'required|string|exists:partner_sting,partner_sign',
    ];

    /**
     * 添加过滤参数返回消息
     * @var array
     */
    public $create_messages = [
        'partner_sign.required' => '名称不合法或则不存在',
    ];

    /**
     * 添加过滤参数
     * @var array
     */
    public $edit_rules = [
        'id' => 'string|exists:partner_setting,id',//商户ID
    ];

    /**
     * 添加过滤参数返回消息
     * @var array
     */
    public $edit_messages = [
        'id.exists' => 'ID不存在',
    ];

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
     * 判断添加标记和名称不能重复
     * @param $c
     * @param $finance_channel_type
     * @return bool
     */
    public function isExist($c, $partner)
    {
        $array = [
            ['partner_sign', '=', $c['partner_sign'] ?? ''],
        ];
        //判断自己是否可以修改
        if (!empty($c['id']) && isset($c['id'])) {
            $partner = $this->find($c['id']);
            if ($partner->partner_sign == $c['partner_sign'] ?? '') {
                return true;
            }
        }
        $isExist = $this->where($array)->first();
        if ($isExist) {
            return false;
        }
        return true;
    }



    /**
     * 获取列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
        $query = self::orderBy('id', 'DESC');

        // 商户标识
        if (isset($c['sign']) && $c['sign'] && $c['sign'] != "all") {
            $query->where('sign', $c['sign']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();
        $ids    = [];

        foreach ($items as $item) {
            $ids[] = $item->sign;
        }

        // 获取所有匹配的 casino platform
        $allPlatform    = PartnerCasinoPlatform::getAllPlatformBySign($ids);
        $allMenus       = PartnerMenu::getAllMenuBySign($ids);

        foreach ($items as $item) {
            $item->casino_platform  = isset($allPlatform[$item->sign]) ? $allPlatform[$item->sign] : [];
            $item->menus            = isset($allMenus[$item->sign]) ? $allMenus[$item->sign] : [];
        }

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    static function initItem($partner) {
        $setting = new self();
        $setting->partner_sign = $partner->sign;

        $popularLottery = self::getDefaultOpenListLottery();

        $setting->lottery_open_list = serialize($popularLottery);
        $setting->save();

        return true;
    }

    // 设置娱乐城平台
    public function setCasinoPlatform($codeArr, $adminUser = null) {
        // 检测code是否合法
        $total = CasinoPlatform::whereIn('main_game_plat_code', $codeArr)->where('status', 1)->count();
        if (count($codeArr) != $total) {
            return "对不起, 包含无效的平台Code!s";
        }

        // 上出所有的老的
        PartnerCasinoPlatform::where("partner_sign", $this->sign)->delete();

        // 插入
        $data = [];
        foreach ($codeArr as $code) {
            $data[] = [
                'partner_sign'  => $this->sign,
                'platform_code' => $code,
                'status'        => 1,
                'add_admin_id'  => $adminUser ? $adminUser->id : 999999
            ];
        }

        PartnerCasinoPlatform::insert($data);
        return true;
    }



    /** ===================================== 首页 ======================================= */

    static function getDefaultOpenListLottery() {
        return ['cqssc', "fc3d", 'jsffc', 'sd115', 'txffc', 'jsk3'];
    }
}
