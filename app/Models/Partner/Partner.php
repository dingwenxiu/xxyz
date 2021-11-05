<?php

namespace App\Models\Partner;

use App\Lib\Casino\CasinoApi;
use App\Lib\CC;
use App\Lib\Clog;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Lib\Logic\Cache\IssueCache;
use App\Lib\Logic\Cache\PartnerCache;
use App\Lib\Logic\Partner\PartnerLogic;
use App\Models\Casino\CasinoCategorie;
use App\Models\Casino\CasinoMethod;
use App\Models\Casino\CasinoPlatform;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryIssueRule;
use App\Models\System\SysTelegramChannel;
use App\Models\Template\Template;
use App\Models\Template\TemplateColor;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Psr\SimpleCache\InvalidArgumentException;

class Partner extends Model
{
    protected $table = 'partners';

    protected $fillable = ['logo_image', 'name', 'sign'];

    /**
     * 添加过滤参数
     * @var array
     */
    public $create_rules = [
        'id' => 'string|exists:partners,id',//商户ID
        'name' => 'required|string',
        'sign' => 'required|string',//商户标示
    ];

    /**
     * 添加过滤参数返回消息
     * @var array
     */
    public $create_messages = [
        'id.exists' => 'ID不存在',
        'name.required' => '名称不合法或则不存在',
        'sign.required' => '商户类标记不合法或则不存在',
    ];

    /**
     * 添加过滤参数
     * @var array
     */
    public $edit_rules = [
        'id' => 'string|exists:partners,id',//商户ID
    ];

    /**
     * 添加过滤参数返回消息
     * @var array
     */
    public $edit_messages = [
        'id.exists' => 'ID不存在',
    ];

    public $rules = [
        'name' => 'required|min:2|max:64',
        'sign' => 'required|min:2|max:32',
        'admin_email' => 'required|min:2|max:64',
        'admin_password' => 'required|min:2|max:64',
        'admin_fund_password' => 'required|min:2|max:64',
    ];

    /**
     * 通过域名获取 商户
     * @param $domain
     * @param $type
     * @return bool
     */
    static function findPartnerByDomain($domain, $type)
    {
        $domain = PartnerDomain::where("domain", $domain)->where("status", 1)->first();
        if ($domain) {
            return partner::where("sign", $domain->partner_sign)->where("status", 1)->first();
        }
        return false;
    }

    /**
     * 通过 sign 获取 商户
     * @param $sign
     * @return bool
     */
    static function findPartnerBySign($sign)
    {
        return partner::where("sign", $sign)->where("status", 1)->first();
    }

    /**
     * 获取商户列表
     * @param $c
     * @return mixed
     */
    static function getList($c)
    {
        $query = self::orderBy('id', 'DESC');

        // 商户标识
        if (isset($c['sign']) && $c['sign'] && $c['sign'] != "all") {
            $query->where('sign', $c['sign']);
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $items = $query->skip($offset)->take($pageSize)->get();
        $ids = [];

        foreach ($items as $item) {
            $ids[] = $item->sign;
        }

        // 获取所有匹配的 casino platform
        $allPlatform = PartnerCasinoPlatform::getAllPlatformBySign($ids);

        $allMenus = PartnerMenu::getAllMenuBySign($ids);
        $webs = PartnerDomain::where('partner_sign', $c['sign'])->where('type',1)->get(['domain','type','env_type'])->toArray();
        $config = config('web.domain.test_domain');
        $webAddress = 'api'.'.'.$config[0];
        $webTest = PartnerDomain::where('partner_sign', $c['sign'])->where('domain',$webAddress)->first();
        if ($webTest) {
            $test = false;
        } else {
            $test = true;
        }

        $_data = [];
        foreach ($webs as $value) {
            $va1 = self::cut_str($value['domain'],'.', -1);
            $va2 = self::cut_str($value['domain'],'.', -2);
        	$_data[] =[
				'domain'   => $va2.'.'.$va1,
			];
		}
        $webs = $_data;

        foreach ($items as $item) {
            $domains = PartnerDomain::getPartnerDomain($item->sign);

            foreach ($domains as $domain) {
                switch ($domain->type) {
                    case 1:
                        $domain->domain_desc = '投注';
                        break;
                    case 2:
                        $domain->domain_desc = '商户';
                        break;
                    default:
                        $domain->domain_desc = '未知';
                        break;
                }
                if ($domain->env_type == 2) {
                    $domain->domain_desc .= '(测试)';
                }

                if ($domain->env_type == 3) {
                    $domain->domain_desc .= '(线上)';
                }
            }
            $item->domain = $domains;

            $item->casino_platform = isset($allPlatform[$item->sign]) ? $allPlatform[$item->sign] : [];
            $item->menus = isset($allMenus[$item->sign]) ? $allMenus[$item->sign] : [];
        }

        return ['data' => $items, 'total' => $total, 'web_domain' =>$webs, 'test_web'=>$test,  'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
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
            ['sign', '=', $c['sign'] ?? ''],
        ];
        //判断自己是否可以修改
        if (!empty($c['id']) && isset($c['id'])) {
            $partner = $this->find($c['id']);
            if ($partner->name == $c['name'] ?? '' && $partner->sign == $c['sign'] ?? '') {
                return true;
            }
        }
        $isExist = $this->where($array)->first();
        if ($isExist) {
            return false;
        }
        return true;
    }


    // 保存
    public function saveItem($data, $admin = null)
    {
        db()->beginTransaction();
        try {
            $data['sign'] = strtoupper($data['sign']);
            $validator = Validator::make($data, $this->rules);
            if ($validator->fails()) {
                return $validator->errors()->first();
            }

            // Sign 不能重复
            if (!$this->id || ($this->id && $data['sign'] != $this->sign)) {
                $count = self::where('sign', '=', $data['sign'])->count();
                if ($count > 0) {
                    return "对不起, 标识(sign)已经存在!!";
                }
            }

            // 分配所有菜单
            PartnerMenu::initPartnerMenu($data['sign']);

            // admin_email 不能重复
            if (!$this->id) {
                $count = PartnerAdminUser::where('email', '=', $data['admin_email'])->where('partner_sign', $data['sign'])->count();
                if ($count > 0) {
                    return "对不起, 超级管理员邮箱(admin_email)已经存在!!";
                }
            }

            // 添加超级管理组
            $group = PartnerAdminGroup::initSuperGroup($data['sign'], $admin);
            if (!$group) {
                db()->rollback();
                return "对不起, 添加管理组失败!";
            }

            $this->name = $data['name'];
            $this->sign = $data['sign'];
            $this->template_sign = 'youxia';
            $this->theme = isset($data['theme']) ? $data['theme'] : 'default';
            $this->remark = $data['remark'];
            $this->logo_image_pc_1 = $data['logo_image_pc_1'] ?? '';
            $this->logo_image_pc_2 = $data['logo_image_pc_2'] ?? '';
            $this->logo_image_h5_1 = $data['logo_image_h5_1'] ?? '';
            $this->logo_image_h5_2 = $data['logo_image_h5_2'] ?? '';
            $this->add_admin_id = $admin ? $admin->id : '999999';
            $this->save();

            $this->api_id   = 10000 +  $this->id;
            $this->api_key  = PartnerLogic::genPartnerKey($data['sign']);
            $this->status   = 1;
            $this->save();

            // 添加超级管理员
            $res = $group->addSuperAdminUser($data['admin_email'], $data['admin_password'], $data['admin_fund_password'], $admin);
            if (!is_object($res)) {
                db()->rollback();
                return $res;
            }

            // 添加域名
            if (isset($data['host']) && !empty($data['host'])) {
                $res_api     = (new PartnerDomain())->saveItem($this->domainApi($data, $admin));
                $res_partner = (new PartnerDomain())->saveItem($this->domainPartner($data, $admin));
                if (true !== $res_api || true !== $res_partner) {
                    db()->rollback();
                    return $res;
                }
            }

            // 添加活动奖品
            PartnerActivityPrize::initPartnerActivityPrize($data['sign']);

            // 添加官彩
            PartnerLottery::initPartnerLottery($data['sign']);

            // 添加私彩及玩法
            PartnerLottery::initPartnerSelfOpenLottery($data['sign']);

            // 添加官彩票玩法
            PartnerMethod::initPartnerMethod($data['sign']);

            //生成奖期
            $partnerLottery = PartnerLottery::where('partner_sign',$data['sign'])->pluck('lottery_sign')->toArray();
            $lotteryIssue   = LotteryIssue::pluck('lottery_sign')->toArray();
            $diff           = array_diff($partnerLottery,$lotteryIssue);

            foreach ($diff as $v) {
                Artisan::call('lottery:genIssue', ['lottery_sign' => $v]);
            }

            // 添加首页配置
            PartnerModule::initPartnerModule($data['sign']);

            // 添加telegram默认渠道
            SysTelegramChannel::initDefaultTelegramChannel($data['sign']);

            // 更新游戏平台
            $player = null;
            $partner = Partner::where('sign', $data['sign'])->first();

            $addTemplateSign = $data['add_template_sign'] ?? 'youxia';

            // 初始化 配置
            if (empty($admin)) {
                PartnerConfigure::initPartnerConfig($this);
            } else {
                PartnerConfigure::initPartnerConfig($this, $data);

                $Template = Template::where('sign', $addTemplateSign)->first(['id', 'sign', 'partner_sign', 'module_sign']);
                if (!is_null($Template))
                {
                    Template::where('sign', $addTemplateSign)->update(['partner_sign' => $data['sign'], 'partner_name' => $data['name']]);
                    $module_signArr = explode(',', $Template->module_sign);
                    PartnerModule::where(['template_sign' => $addTemplateSign, 'partner_sign' => $data['sign']])->WhereIn('sign', $module_signArr)->update(['status' => 1]);
                }

                // 商户添加数据
                Partner::where('sign', $data['sign'])->update(['template_sign' => $addTemplateSign]);

            }

            // 更新游戏列表
            $CasinoApi = new CasinoApi($player, $partner);
            $CasinoApi->seriesLists();

            $CasinoApi->callGameList();


            $navigationsList = [];

            $casinoTypeArr = [
                'e-game' => ['bbin', 'mg', 'pt', 'bg'],
                'card' => ['ky', 'lg', 'gg'],
                'fish-game' => ['bg', 'bbin'],
                'live' => ['bg', 'bbin'],
            ];

            foreach ($casinoTypeArr as $casinoKey => $casinoItem) {
                $casinoID = '';
                switch ($casinoKey) {
                    case 'e-game':
                        $partnerCPF = PartnerCasinoPlatform::where('partner_sign', $this->sign)->whereIn('main_game_plat_code', $casinoItem)->get('id');

                        foreach ($partnerCPF as $partnerCPFKey => $partnerCPFItem) {
                            $casinoID .= $partnerCPFItem['id'] . ',';
                        }

                        $navigationsList[] = [
                            'partner_sign' => $this->sign,
                            'name' => "电子",
                            'url' => "/games-page/e-game",
                            'style' => 2,
                            'casino_plat_id' => $casinoID,
                            'status' => 0,
                            'home' => 1
                        ];
                        break;
                    case 'card':
                        $partnerCPF = PartnerCasinoPlatform::where('partner_sign', $this->sign)->whereIn('main_game_plat_code', $casinoItem)->get(['id']);

                        foreach ($partnerCPF as $partnerCPFKey => $partnerCPFItem) {
                            $casinoID .= $partnerCPFItem['id'] . ',';
                        }
                        $navigationsList[] = [
                            'partner_sign' => $this->sign,
                            'name' => "棋牌",
                            'url' => "/games-page/card",
                            'style' => 2,
                            'casino_plat_id' => $casinoID,
                            'status' => 0,
                            'home' => 1
                        ];
                        break;
                    case 'fish-game':
                        $partnerCPF = PartnerCasinoPlatform::where('partner_sign', $this->sign)->whereIn('main_game_plat_code', $casinoItem)->get(['id']);

                        foreach ($partnerCPF as $partnerCPFKey => $partnerCPFItem) {
                            $casinoID .= $partnerCPFItem['id'] . ',';
                        }
                        $navigationsList[] = [
                            'partner_sign' => $this->sign,
                            'name' => "捕鱼",
                            'url' => "/fish-game",
                            'style' => 2,
                            'casino_plat_id' => $casinoID,
                            'status' => 0,
                            'home' => 1
                        ];
                        break;
                    case 'live':
                        $partnerCPF = PartnerCasinoPlatform::where('partner_sign', $this->sign)->whereIn('main_game_plat_code', $casinoItem)->get(['id']);

                        foreach ($partnerCPF as $partnerCPFKey => $partnerCPFItem) {
                            $casinoID .= $partnerCPFItem['id'] . ',';
                        }
                        $navigationsList[] = [
                            'partner_sign' => $this->sign,
                            'name' => "真人",
                            'url' => "/live",
                            'style' => 1,
                            'casino_plat_id' => $casinoID,
                            'status' => 0,
                            'home' => 1
                        ];
                        break;

                }

            }

            //添加导航
            $navigationsList[] = [
                'partner_sign' => $this->sign,
                'name' => "优惠活动",
                'url' => "/active",
                'style' => 1,
                'casino_plat_id' => NULL,
                'status' => 0,
                'home' => 1
            ];
            $navigationsList[] = [
                'partner_sign' => $this->sign,
                'name' => "走势图",
                'url' => "/user-trends",
                'style' => 1,
                'casino_plat_id' => NULL,
                'status' => 0,
                'home' => 1
            ];

            foreach ($navigationsList as $item) {

                $partnerNavigation = new PartnerNavigation();
                $res = $partnerNavigation->saveItem($item, $this->sign, 0);

                info($res);
            }

            //首页设置
            $homeList = [];
            $partnerModules = [
                'live' => ['rodzl', 'dtl', 'dual_rol', 'swl'],
                'e-game' => ['25', '26', '27', '28'],
                'card' => ['100003', '100010', '100001', '100002'],
            ];



            foreach ($partnerModules as $partnerModuleKey => $partnerModuleItem) {
                $partnerModule = PartnerModule::where([
                    'partner_sign'  => $this->sign,
                    'template_sign' => $addTemplateSign,
                ])->where('param', 'like', '%' . $partnerModuleKey .'%')->first();
                $PartnerCasinoM = PartnerCasinoMethod::where('partner_sign', $this->sign)->whereIn('pc_game_code', $partnerModuleItem)->get('id');
                foreach ($PartnerCasinoM as $PartnerCasinoMKey => $PartnerCasinoMItem) {
                    $homeList[] =[
                        'partner_sign' => $this->sign,
                        'template_sign' => $addTemplateSign,
                        'module_id'    => $partnerModule->id ?? 0,
                        'other_id'     => $PartnerCasinoMItem->id,
                        'order'        => $PartnerCasinoMKey + 1,
                        'status'       => 1,
                    ];
                }
            }

            $partnerModules = [
                'hot' => ['cqssc', 'qqtxffc', 'dctxffc', 'txffc', 'hklhc', 'sd115', 'gd115', $this->sign . 'jsftpk10', 'xyftpk10', $this->sign . 'js1fk3', 'bjxy28'],
                'is_hot' => ['cqssc', 'dctxffc', 'hljssc', 'txffc'],
                'popular' => [$this->sign . 'jsffc', 'cqssc', 'sd115', 'xyftpk10'],
                'recommend_open_lottery' => ['dctxffc', 'cqssc', 'hljssc', 'txffc'],
            ];

            foreach ($partnerModules as $partnerModuleKey => $partnerModuleItem) {
                switch ($partnerModuleKey) {
                    case 'hot':
                    case 'is_hot':
                    case 'recommend_open_lottery':
                        $partnerModule = PartnerModule::where([
                            'partner_sign'  => $this->sign,
                            'template_sign' => $addTemplateSign,
                        ])->where('m_name', 'like', '%' . $partnerModuleKey .'%')->first();
                        foreach ($partnerModuleItem as $partnerModuleItemKey => $partnerModuleItem1) {
                            $homeList[] =[
                                'partner_sign' => $this->sign,
                                'module_id'    => $partnerModule->id ?? 0,
                                'template_sign' => $addTemplateSign,
                                'other_id'     => $partnerModuleItem1,
                                'order'        => $partnerModuleItemKey + 1,
                                'status'       => 1,
                            ];
                        }
                        break;
                    case 'popular':
                        $partnerModule = PartnerModule::where([
                            'partner_sign'  => $this->sign,
                            'template_sign' => $addTemplateSign,
                        ])->where('m_name', 'like', '%' . $partnerModuleKey .'%')->first();
                        $PartnerMethod = PartnerMethod::where('partner_sign', $this->sign)->whereIn('lottery_sign', $partnerModuleItem)->where('method_sign', 'YFFS')->get('id');

                        foreach ($PartnerMethod as $PartnerMKey => $PartnerMItem) {
                            $homeList[] =[
                                'partner_sign' => $this->sign,
                                'template_sign' => $addTemplateSign,
                                'module_id'    => $partnerModule->id ?? 0,
                                'other_id'     => $PartnerMItem->id,
                                'order'        => $PartnerMKey + 1,
                                'status'       => 1,
                            ];
                        }

                        break;
                }
            }

            // 模板颜色
            $TemplateColor = TemplateColor::all();
            $partnerModule = PartnerModule::where([
                'partner_sign'  => $this->sign,
                'template_sign' => $addTemplateSign,
            ])->where('sign', 'templateColor')->first();

            foreach ($TemplateColor as $TemplateColorKey => $item) {
                $homeList[] = [
                    'partner_sign' => $this->sign,
                    'module_id'    => $partnerModule->id ?? 0,
                    'other_id'     => $item->id,
                    'template_sign' => $addTemplateSign,
                    'order'        => $TemplateColorKey + 1,
                    'value'        => $item->value,
                    'status'       => 1,
                ];
            }


            foreach ($homeList as $item) {
                $partnerHome = new PartnerHome();
                $res = $partnerHome->saveItem($item, $this->sign ,0);
                info($res);
            }


            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            Clog::partner("partner-add-partner-:" . $e->getMessage() . "|" . $e->getLine() . "|" . $e->getFile());
            return $e->getMessage();
        }

        return $this;
    }

    /**
     * @param $data
     * @param $adminUser
     * @return array
     */
    public function domainApi($data, $adminUser)
    {
        $name = 'api.';
        $domain1 = str_replace("http://www.", "", $data['host']) ?? '';
        $domain2 = str_replace("https://www.", "", $domain1) ?? '';
        $domain = str_replace("www.", "", $domain2) ?? '';
        return $params = [
            'partner_sign' => $data['sign'],
            'name' => $data['name'] . 'Api',
            'domain' => $name . $domain,
            'type' => 1,
            'env_type' => 2,
            'remark' => $data['remark'],
            'add_admin_id' => $adminUser ? $adminUser->id : 999999
        ];
    }

    /**
     * @param $data
     * @param $adminUser
     * @return array
     */
    public function domainAdmin($data, $adminUser)
    {
        $name = 'admin-api.';
        $domain1 = str_replace("http://www.", "", $data['host']) ?? '';
        $domain2 = str_replace("https://www.", "", $domain1) ?? '';
        $domain = str_replace("www.", "", $domain2) ?? '';
        return $params = [
            'partner_sign' => $data['sign'],
            'name' => $data['name'] . 'Admin',
            'domain' => $name . $domain,
            'type' => 2,
            'env_type' => 2,
            'remark' => $data['remark'],
            'add_admin_id' => $adminUser ? $adminUser->id : 999999
        ];
    }

    /**
     * @param $data
     * @param $adminUser
     * @return array
     */
    public function domainPartner($data, $adminUser)
    {
        $name = 'partner-api.';
        $domain1 = str_replace("http://www.", "", $data['host']) ?? '';
        $domain2 = str_replace("https://www.", "", $domain1) ?? '';
        $domain = str_replace("www.", "", $domain2) ?? '';
        return $params = [
            'partner_sign' => $data['sign'],
            'name' => $data['name'] . 'Partner',
            'domain' => $name . $domain,
            'type' => 2,
            'env_type' => 2,
            'remark' => $data['remark'],
            'add_admin_id' => $adminUser ? $adminUser->id : 999999
        ];
    }

    /**
     * @param $data
     * @param $adminUser
     * @return array
     */
    public function domainMobile($data, $adminUser)
    {
        $name = 'mobile-api.';
        $domain1 = str_replace("http://www.", "", $data['host']) ?? '';
        $domain2 = str_replace("https://www.", "", $domain1) ?? '';
        $domain = str_replace("www.", "", $domain2) ?? '';
        return $params = [
            'partner_sign' => $data['sign'],
            'name' => $data['name'] . 'Mobile',
            'domain' => $name . $domain,
            'type' => 3,
            'env_type' => 2,
            'remark' => $data['remark'],
            'add_admin_id' => $adminUser ? $adminUser->id : 999999
        ];
    }

    // 设置娱乐城平台
    public function setCasinoPlatform($codeArr, $adminUser = null)
    {
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
                'partner_sign' => $this->sign,
                'platform_code' => $code,
                'status' => 1,
                'add_admin_id' => $adminUser ? $adminUser->id : 999999
            ];
        }

        PartnerCasinoPlatform::insert($data);
        PartnerCache:flushCache($this->sign);
        return true;
    }

    // 设置 菜单
    public function setAdminMenus($idArr, $adminUser = null, $sign = null)
    {
        if ($sign === null)
            return '代理不能为空';
        // 检测用户ID是否存在
        $menus = PartnerMenuConfig::whereIn('id', $idArr)->where('status', 1)->get();
        if (count($idArr) != count($menus)) {
            return "对不起, 包含无效的用户";
        }

        // 把上级加上去
        foreach ($menus as $menu) {
            if ($menu->pid > 0 && !in_array($menu->pid, $idArr)) {
                $idArr[] = $menu->pid;
            }
        }

        // 总代
        PartnerMenu::where("partner_sign", $sign)->delete();

        // 插入
        $data = [];
        foreach ($idArr as $menuId) {
            $data[] = [
                'partner_sign' => $sign,
                'menu_id' => $menuId,
                'status' => 1,
                'add_admin_id' => $adminUser ? $adminUser->id : 999999
            ];
        }

        PartnerMenu::insert($data);
        PartnerCache:flushCache($this->sign);
        return true;
    }

    /**
     * 设置彩种
     * 1. 　则禁用
     * 2. 不存在　跳过
     * @param $lotterySignArr
     * @return string
     * @throws \Exception
     */
    public function setLottery($lotterySignArr)
    {
        // 存在的 lottery
        $exitLotterySignArr = PartnerLottery::where("partner_sign", $this->sign)->pluck("lottery_sign")->all();

        $setExtend = array_diff($lotterySignArr, $exitLotterySignArr);
        $partnerHaveExtend = array_diff($exitLotterySignArr, $lotterySignArr);

        // 禁用设置不存在的
        PartnerLottery::where("partner_sign", $this->sign)->where("disable_bet");
    }


    /**
     * 获取选项
     * @return array
     */
    static function getOptions($partnerSign = '')
    {
        $items = self::where('status', 1);
        if (!empty($partnerSign)) {
            $items = $items->where('sign', $partnerSign);
        }

        $items = $items->get();

        $data = [];
        foreach ($items as $item) {
            $data[$item->sign] = $item->name;
        }

        return $data;
    }

    static function getNameOptions($partnerSign)
    {
        $items = self::where('status', 1);
        if (!empty($partnerSign)) {
            $items = $items->where('sign', $partnerSign);
        }

        $items = $items->get();

        $data = [];
        foreach ($items as $item) {
            $data = $item->name;
        }

        return $data;
    }

    // 获取后台显示的默认伙伴标识
    static function getDefaultPartnerSign()
    {
        return "CX";
    }

    // 获取域名对应的配置
    public function getSiteConfig()
    {
        $partnerS = PartnerSetting::where('partner_sign', $this->sign)->first();
        $partnerSM = Partner::where('sign', $this->sign)->first();
        $templateSign = $partnerSM->template_sign ?? '';

        $data = [
            'logo_image_pc_1' => $partnerSM !== null ? $partnerSM->logo_image_pc_1 : '',
            'logo_image_pc_2' => $partnerSM !== null ? $partnerSM->logo_image_pc_2 : '',
            'banner' => [],
            'qr_code_1' => $partnerS !== null ? $partnerS->qr_code_1 : '',
            'qr_code_2' => $partnerS !== null ? $partnerS->qr_code_2 : '',
            'qr_code_3' => $partnerS !== null ? $partnerS->qr_code_3 : '',
            "cs_url" => $partnerS !== null ? $partnerS->cs_url : '',
            'logo_image_h5_1' => $partnerSM !== null ? $partnerSM->logo_image_h5_1 : '',
            'logo_image_h5_2' => $partnerSM !== null ? $partnerSM->logo_image_h5_2 : '',
            'logo_icon' => $partnerSM !== null ? $partnerSM->logo_icon : '',
            'web_name' => $partnerSM !== null ? $partnerSM->name : '',
        ];

        // 获取首页推荐的游戏城类型
        $partnerModule = PartnerModule::where([
            'partner_sign'  => $this->sign,
            'm_name'        => 'hotGame',
            'status'        => 1,
            'template_sign' => $templateSign,
        ])->orderBy('id', 'desc')->limit(4)->get();

        if (is_null($partnerModule)) {
            return [];
        }

        $casinoCode = [];
        foreach ($partnerModule as $item) {
            $casinoCode[] = json_decode($item->param, 1)['category'] ?? '';
        }
        $CasinoCategorie = CasinoCategorie::whereIn('code', $casinoCode)->get();

        foreach ($CasinoCategorie as $k => $plat) {
            $data['popular_' . $plat->code] = self::getPopularPlat($this->sign, $plat->code, $templateSign);
        }


        $bannerData = config('partner.main.advertising.type');
        $bannerType = [];
        foreach ($bannerData as $key => $bannerItem) {
            foreach ($bannerItem['module'] as $bannerKey => $bannerItem1) {
                if ($bannerItem1['key'] == 'banner') {
                    $bannerType[] = $bannerItem['key'];
                }
            }
        }


        $data["ranking"]           = self::getPartnerRanking($this->sign);  // 中奖排行榜
        $data["popular_lottery"]   = self::getPopularLottery($this->sign, $templateSign);  // 推荐彩票
        $data["casino_navigation"] = self::getNavigation($this->sign);
        $data["template_colors"]   = self::getTemplateColors($this->sign, $templateSign);
		$data["notice_list"]       = PartnerNotice::getDataFromCache($this->sign,$c = null);

		foreach ($bannerType as $key => $item) {
            $data[$item . '_banner']            = self::getBanner($this->sign, $item, $templateSign); // 获取首页banner
        }

        $data['casino_plat']       = CasinoCategorie::get(['code', 'name']);

        return $data;
    }


    /**
     * 获取首页banner
     * @param $partnerSign
     * @param $templateSign
     *
     * @return Repository|string
     * @throws \Exception
     */
    static function getBanner($partnerSign, $type, $templateSign)
    {
        $key = $type . "-banner-" . $templateSign . $partnerSign;
        if (!empty(ConfigureCache::get($key))) {
            return ConfigureCache::get($key);
        }

        $data = PartnerAdvertising::where([
            'partner_sign' => $partnerSign,
            'type'         => $type,
            'module_sign'   => 'banner',
        ])->get();

        ConfigureCache::put($key, $data, now()->addMinutes(10));
        return $data;

    }

    static function getTemplateColors($partnerSign, $templateSign)
    {
        $key = "template-colors-" . $partnerSign;
        if (!empty(ConfigureCache::get($key))) {
            return ConfigureCache::get($key);
        }

        $partnerModule = PartnerModule::where([
            'partner_sign'  => $partnerSign,
            'm_name'        => 'templateColor',
            'status'        => 1,
            'template_sign' => $templateSign,
        ])->first();

        if (is_null($partnerModule)) {
            return [];
        }

        $data = PartnerHome::leftjoin('template_colors', 'partner_homes.other_id', '=', 'template_colors.id')
            ->where('partner_homes.partner_sign', $partnerSign)
            ->where('partner_homes.module_id', $partnerModule->id)->orderBy('partner_homes.order', 'desc')
            ->get(['partner_homes.*', 'template_colors.name as lottery_name', 'template_colors.sign']);


        ConfigureCache::put($key, $data, now()->addMinutes(10));
		$color = [];
		$value = [];
        foreach ($data as $item) {
				$color[] = $item->value;
				$value[] = $item->sign;
		}

        $theme = array_combine($value,$color);
        return $theme;
    }

    /** ===================================== 导航 ======================================= */
    static function getNavigation($partnerSign)
    {
        $key = "home-navigation-" . $partnerSign;
        if (!empty(ConfigureCache::get($key))) {
            return ConfigureCache::get($key);
        }
        $partnerNavigation = PartnerNavigation::where([
            'home' => 1,
            'partner_sign' => $partnerSign
        ])->orderBy('order', 'asc')->get(['home', 'id', 'name', 'style', 'url', 'casino_plat_id']);
        foreach ($partnerNavigation as $item) {
            $item['casino_plat_code'] = PartnerCasinoPlatform::whereIn('id', explode(',', $item->casino_plat_id))->get()->toArray();
        }
        ConfigureCache::put($key, $partnerNavigation, now()->addMinutes(10));
        return $partnerNavigation;
    }

    /** ===================================== 首页 ======================================= */

    /**
     * 获取排行
     * @param $partnerSign
     * @return array|Repository
     * @throws InvalidArgumentException
     */
    static function getPartnerRanking($partnerSign)
    {
        $key = "site-ranking-" . $partnerSign;
        try {
            if (!empty(ConfigureCache::get($key))) {
                return ConfigureCache::get($key);
            }
            $set = ["F", 'A', "C", 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
            $lotteryAll = PartnerLottery::getOption($partnerSign);
            $userIconAll = CC::getUserIcon($partnerSign);

            $data = [];
            for ($i = 0; $i <= 9; $i++) {
                $tmpName = $set[array_rand($set)] . $set[array_rand($set)] . "***" . $set[array_rand($set)] . $set[array_rand($set)];
                $tmpAmount = random_int(10000000, 999999999);
                $randAmount = random_int(1, 9) % 2 == 1 ? intval(number4($tmpAmount)) : number4($tmpAmount);

                $data[$tmpAmount] = [
                    "username" => $tmpName,
                    "lottery_sign" => $lotteryAll[array_rand($lotteryAll)],
                    "user_icon" => '/system/avatar/' . $userIconAll[array_rand($userIconAll)],
                    "bonus" => $randAmount,
                ];
            }

            ksort($data);
            $data = array_values($data);

            ConfigureCache::put($key, $data, now()->addMinutes(10));

        } catch (\Exception $e) {
            info("get-partner-notice-错误-" . $e->getMessage());
            return [];
        }

        return $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    function getOpenList()
    {
        $partnerM = Partner::where('sign', $this->sign)->first();
        $templateSign = $partnerM->template_sign ?? '';

        $partnerModule      = PartnerModule::where([
            'partner_sign'  => $this->sign,
            'm_name'        => 'recommend_open_lottery',
            'status'    => 1,
            'template_sign' => $templateSign,
        ])->first();

        if (is_null($partnerModule)) {
            return [];
        }

        $lotterySignArr     = PartnerHome::where([
            "module_id" => $partnerModule->id,
            'status'    => 1,
            'template_sign' => $templateSign,
        ])->pluck("other_id")->toArray();
        $openList           = LotteryIssue::getOpenList($this->sign, $lotterySignArr);
        return $openList;
    }

    /**
     * 获取流行玩法
     * @param $partnerSign
     * @return array|Repository
     * @throws InvalidArgumentException
     */
    function getPopularMethods()
    {
        $partnerM = Partner::where('sign', $this->sign)->first();
        $templateSign = $partnerM->template_sign ?? '';

        $partnerModule = PartnerModule::where([
            'partner_sign'  => $this->sign,
            'm_name'        => 'popular',
            'status'        => 1,
            'template_sign' => $templateSign,
        ])->first();

        if (is_null($partnerModule)) {
            return [];
        }

        $methodId = PartnerHome::where([
            'partner_sign'  => $this->sign,
            'module_id'     => $partnerModule->id,
            'status'        => 1,
            'template_sign' => $templateSign,
        ])->limit($partnerModule->num_max)->orderBy('order', 'asc')->get(['id', 'other_id', 'order']);

        $popularMethodListEloq = [];
        foreach ($methodId as $key => $item) {

            $popularMethodListEloq[$key] = PartnerMethod::where("id", $item->other_id)
                ->where('status', 1)
                ->with(['currentIssue:lottery_sign,issue,end_time'])
                ->first();
            $popularMethodListEloq[$key]->home_id = $item->id;
        }
        $datas = [];

        foreach ($popularMethodListEloq as $methodItem) {
            if (!empty($methodItem) && PartnerLottery::where('partner_sign', $this->sign)->where('lottery_sign', $methodItem->lottery_sign)->where('status', 1)->first() != null) {
                $dataLottery = PartnerAdvertising::where([
                    'partner_sign' => $this->sign,
//                    'game_id'      => $methodItem->home_id,
                    'sign'         => $methodItem->id,
                    'module_sign'   => 'popular',
                    'type'         => 'home',
                ])->first();

                $data = [
                    'lottery_sign' => $methodItem->lottery_sign,
                    'lottery_name' => $methodItem->lottery_name,
                    'method_name' => $methodItem->method_name,
                    'ad_img' => $dataLottery->img ?? '',
                    //                    'method_group' => $methodItem->method_group,
                    'method_sign' => $methodItem->method_sign,
                    'issue' => $methodItem->currentIssue->issue ?? null,
                    'end_time' => $methodItem->currentIssue->end_time ?? null,
                ];
                $datas[] = $data;
            }
        }
        return $datas;
    }


    /**
     * 获取热门彩票
     * @return array|Repository
     * @throws \Exception
     */
    function getPopularLotterys()
    {
        $partnerM = Partner::where('sign', $this->sign)->first();
        $templateSign = $partnerM->template_sign ?? '';

        $partnerModule = PartnerModule::where([
            'partner_sign'  => $this->sign,
            'm_name'        => 'is_hot',
            'status'        => 1,
            'template_sign' => $templateSign
        ])->first();

        if (is_null($partnerModule)) {
            return [];
        }
        $lotteryItems  = PartnerHome::leftjoin('partner_lottery', 'partner_homes.other_id', '=', 'partner_lottery.lottery_sign')
            ->where([
                'partner_lottery.partner_sign' => $this->sign,
                'partner_homes.partner_sign'   => $this->sign,
                'partner_homes.module_id'      => $partnerModule->id,
                'partner_homes.status'         => 1,
                'partner_homes.template_sign'  => $templateSign,
            ])->limit($partnerModule->num_max + 1)->orderBy('partner_homes.order', 'asc')->get([
                'partner_homes.*', 'partner_lottery.lottery_name','partner_lottery.ad_img',
                'partner_lottery.is_sport', 'partner_lottery.status as status',
                'partner_lottery.lottery_sign', 'partner_lottery.icon_path'
            ]);

        $data = [];
        foreach ($lotteryItems as $item) {
            $lottery = Lottery::findBySign($item->lottery_sign);
            $currentIssue = IssueCache::getCurrentIssue($item->lottery_sign);
            $_lastIssue = IssueCache::getLastIssue($item->lottery_sign);
            $lastIssue = [
                'issue_no' => $_lastIssue ? $_lastIssue->issue : "",
                'begin_time' => $_lastIssue ? $_lastIssue->begin_time : "",
                'end_time' => $_lastIssue ? $_lastIssue->end_time : "",
                'open_time' => $_lastIssue ? $_lastIssue->allow_encode_time : "",
            ];
            // 获取下面N期
            $canUserInfo = IssueCache::getNextMultipleIssue($item->lottery_sign, $lottery->max_trace_number);
            $canBetIssueData = [];

            foreach ($canUserInfo as $index => $issue) {
                $canBetIssueData[] = [
                    'issue_no' => $issue->issue,
                    'begin_time' => $issue->begin_time,
                    'end_time' => $issue->end_time,
                    'open_time' => $issue->allow_encode_time
                ];
            }

            $currentIssueData = [];
            if ($currentIssue) {
                $currentIssueData = [
                    'issue_no' => $currentIssue->issue,
                    'begin_time' => $currentIssue->begin_time,
                    'end_time' => $currentIssue->end_time,
                    'open_time' => $currentIssue->allow_encode_time,
                ];
            }

            if ($item->status) {

                if (substr($item->icon_path, 0, 7) == 'lottery') {
                    $imgs = lotteryIcon($item->icon_path);
                } else {
                    $imgs = $item->icon_path;
                }

                $dataLottery = PartnerAdvertising::where([
                    'partner_sign' => $this->sign,
                    'sign'         => $item->other_id,
//                    'game_id'      => $item->id,
                    'module_sign'   => 'is_hot',
                    'type'         => 'home',
                ])->first();

                $data[] = [
                    "cn_name"           => $item->lottery_name,
                    "day_issue"         => LotteryIssueRule::where('lottery_sign', $item->lottery_sign)->sum('issue_count'),
                    "is_fast"           => $item->is_fast,
                    "ad_img"            => $dataLottery->img ?? '',
                    'currentIssue'      => $currentIssueData,
                    'lastIssue'         => $lastIssue,
                    "en_name"           => $item->lottery_sign,
                    "icon_path"         => $imgs,
                    "closed_vacation"   => Lottery::isClosedMarket($item->lottery_sign),
                ];
            }
        }

        return $data;
    }

    /**
     * 获取所有公告 Mobile
     * @param $c
     * @return array
     */
    function getNoticeConfig($c)
    {
        $c["partner_sign"] = $this->sign;
        $c["status"] = 1;
        $c['device_type'] = [1, 2];

        $data = PartnerNotice::getList($c);
        $_data = [];
        foreach ($data['data'] as $item) {
            $_data[] = [
                "id" => $item->id,
                "title" => $item->title,
                "type_desc" => $item->type_desc,
                'content' => $item->content,
                'device_type_desc' => $item->device_type_desc,
                "notice_image" => $item->notice_image,
                "type" => $item->type,
                "start_time" => $item->start_time,
                "end_time" => $item->end_time,
                "name" => $item->name,
            ];
        }
        $data["data"] = $_data;
        return $data;
    }

    /**
     * 获取所有公告 PC
     * @param $c
     * @return array
     */
    function getNoticeConfigs($c)
    {
        $c["partner_sign"] = $this->sign;
        $c["status"] = 1;
        $c['timeNow'] = time();
        $c['device_type'] = [1, 3];
        $data = PartnerNotice::getList($c);
        $_data = [];
        foreach ($data['data'] as $item) {
            $_data[] = [
                "id" => $item->id,
                "title" => $item->title,
                "type_desc" => $item->type_desc,
                'content' => $item->content,
                'device_type_desc' => $item->device_type_desc,
                "notice_image" => $item->notice_image,
                "type" => $item->type,
                "start_time" => $item->start_time,
                "end_time" => $item->end_time,
                "name" => $item->name,
            ];
        }
        $data["data"] = $_data;
        return $data;
    }

    /**
     * 清理缓存
     * @param $partnerSign
     * @throws \Exception
     */
    static function flushCachePartnerRanking($partnerSign)
    {
        $key = "site-ranking-" . $partnerSign;
        ConfigureCache::forget($key);
    }

    /**
     * 获取热门电游
     * @param $partnerSign
     * @param $platCode
     * @return array
     * @throws \Exception
     */
    static function getPopularPlat($partnerSign, $platGameCode, $templateSign)
    {
        $platCode = '';
        switch ($platGameCode) {
            case 'live':
                $platCode = 'hotGameLive';
                break;
            case 'e-game':
                $platCode = 'hotGameEGame';
                break;
            case 'card':
                $platCode = 'hotGameCard';
                break;
            case 'sport':
                $platCode = 'hotGameSport';
                break;
        }

        $key = 'site-popular-' . $platGameCode . '-' . $partnerSign;
        if (!empty(ConfigureCache::get($key))) {
            return ConfigureCache::get($key);
        }

        $partnerModule = PartnerModule::where([
            'partner_sign'  => $partnerSign,
            'm_name'        => 'hotGame',
            'status'        => 1,
            'template_sign' => $templateSign,
            'sign'          => $platCode,
        ])->first();

        if (is_null($partnerModule)) {
            return [];
        }

        $otherId = PartnerHome::where([
            'partner_sign'  => $partnerSign,
            'status'        => 1,
            'template_sign' => $templateSign,
            'module_id'     => $partnerModule->id
        ])->orderBy('order', 'asc')->get(['id', 'other_id']);
        $lotteryItems = [];
        foreach ($otherId as $key => $item) {
            $casinoM = CasinoMethod::where('status', 1)->where('id', $item->other_id)->first();
            if ($casinoM) {
                $lotteryItems[$key] = $casinoM;
                $lotteryItems[$key]->home_id = $item->id;
            }
        }
        $data = [];

        foreach ($lotteryItems as $item) {
            $dataLottery = PartnerAdvertising::where([
                'partner_sign' => $partnerSign,
                'game_id'      => $item->home_id,
                'sign'         => $item->id,
                'module_sign'   => $platCode,
                'type'         => 'home',
            ])->first();
            $data[] = [
                "cn_name" => $item->cn_name,
                "pc_game_code" => $item->pc_game_code,
                "pc_game_deputy_code" => $item->pc_game_deputy_code,
                "mobile_game_code" => $item->mobile_game_code,
                "mobile_game_deputy_code" => $item->mobile_game_deputy_code,
                "main_game_plat_code"     => $item->main_game_plat_code,
                "en_name"                 => $item->en_name,
                "icon_path"               => $item->img,
                "ad_img"                  => $dataLottery->img ?? '',
            ];
        }
        ConfigureCache::put($key, $data, now()->addMinutes(10));

        return $data;
    }

    // 获取推荐彩种 虚拟
    static function getPopularLottery($partnerSign, $templateSign)
    {
        $key = "site-popular-lottery-" . $partnerSign;
        if (!empty(ConfigureCache::get($key))) {
            return ConfigureCache::get($key);
        }

        $partnerModule = PartnerModule::where([
            'partner_sign'  => $partnerSign,
            'm_name'        => 'hot',
            'status'        => 1,
            'template_sign' => $templateSign,
        ])->first();

        if (is_null($partnerModule)) {
            return [];
        }

        $lotteryItems = PartnerHome::leftjoin('partner_lottery', 'partner_homes.other_id', '=', 'partner_lottery.lottery_sign')->where([
            'partner_lottery.partner_sign' => $partnerSign,
            'partner_homes.partner_sign'   => $partnerSign,
            'partner_homes.module_id'      => $partnerModule->id,
            'partner_homes.status'         => 1,
            'partner_homes.template_sign'  => $templateSign,
        ])->limit($partnerModule->num_max + 1)->orderBy('partner_homes.order', 'asc')->get(['partner_homes.*', 'partner_lottery.lottery_name', 'partner_lottery.is_fast', 'partner_lottery.status as status', 'partner_lottery.lottery_sign', 'partner_lottery.icon_path']);

        $data = [];
        foreach ($lotteryItems as $item) {
            if ($item->status) {

                if (substr($item->icon_path, 0, 7) == 'lottery') {
                    $imgs = lotteryIcon($item->icon_path);
                } else {
                    $imgs = $item->icon_path;
                }

                $data[] = [
                    "cn_name"           => $item->lottery_name,
                    "day_issue"         => LotteryIssueRule::where('lottery_sign', $item->lottery_sign)->sum('issue_count'),
                    "is_fast"           => $item->is_fast,
                    "en_name"           => $item->lottery_sign,
                    "icon_path"         => $imgs,
                    "closed_vacation"   => Lottery::isClosedMarket($item->lottery_sign),
                ];
            }
        }

        ConfigureCache::put($key, $data, now()->addMinutes(10));

        return $data;
    }

    static function flushCachePopularLottery($partnerSign)
    {
        $key = "site-popular-lottery-" . $partnerSign;
        ConfigureCache::forget($key);
    }
    static function findBySign($sign){
        return self::where('sign',$sign)->first();
    }

    /**
     * 按符号截取字符串的指定部分
     * @param string $str 需要截取的字符串
     * @param string $sign 需要截取的符号
     * @param int $number 如是正数以0为起点从左向右截  负数则从右向左截
     * @return string 返回截取的内容
     */
    static function cut_str($str, $sign, $number)
    {
        $array = explode($sign, $str);
        $length = count($array);
        if ($number < 0) {
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if ($abs_number > $length) {
                return 'error';
            } else {
                return $new_array[$abs_number - 1];
            }
        } else {
            if ($number >= $length) {
                return 'error';
            } else {
                return $array[$number];
            }
        }
    }
}
