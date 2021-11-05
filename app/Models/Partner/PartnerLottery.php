<?php

namespace App\Models\Partner;

use App\Lib\Clog;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Lottery\LotteryLogic;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryMethod;
use App\Models\Game\LotteryIssueRule;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Self_;

class PartnerLottery extends Base
{
    protected $table = 'partner_lottery';

    /**
     * 初始化彩种
     * @param $partnerSign
     */
    static function initPartnerLottery($partnerSign) {
        $lotteryList = Lottery::where("auto_open", 0)->where('status',1)->get();

        $data = [];
        foreach ($lotteryList as $lottery) {
            $data[] = [
                'partner_sign'          => $partnerSign,
                'series_id'             => $lottery->series_id,
                'icon_path'             => $lottery->lottery_icon,
                'is_fast'               => $lottery->is_fast,
                'is_sport'              => $lottery->is_sport,
                'lottery_sign'          => $lottery->en_name,
                'lottery_name'          => $lottery->cn_name,
                'max_trace_number'      => $lottery->max_trace_number,

                'min_times'             => $lottery->min_times,
                'max_times'             => $lottery->max_times,

                'open_casino'           => $lottery->open_casino,
                'min_prize_group'       => $lottery->min_prize_group,
                'max_prize_group'       => $lottery->max_prize_group,
                'diff_prize_group'      => $lottery->diff_prize_group,
                'max_prize_per_code'    => $lottery->max_prize_per_code,
                'max_prize_per_issue'   => $lottery->max_prize_per_issue,

                'valid_modes'           => $lottery->valid_modes,
                'valid_price'           => $lottery->valid_price,
                'status'                => 1,
            ];
        }

        self::insert($data);
    }


    /**
     * 初始化自开彩
     * @param $partner
     */
    static function initPartnerSelfOpenLottery($sign) {
        $selfOpenLottery = config("game.self_open_lottery.lottery");

        foreach ($selfOpenLottery as $lotteryData) {
            $lottery = new Lottery();
            $lotteryData['partner_sign'] = $sign;
            $res = $lottery->addPartnerLottery($lotteryData, $sign);
            if (is_object($res)) {
                $selfOpenLotteryIssueRule = config("game.self_open_lottery.rule");
                $rule = isset($selfOpenLotteryIssueRule[$lotteryData['en_name']]) ? $selfOpenLotteryIssueRule[$lotteryData['en_name']] : [];

                if ($rule) {
                    $rule['lottery_sign'] = $res->en_name;
                    $lotteryIssueRule = new LotteryIssueRule();
                    $lotteryIssueRule->insert($rule);
                }
            } else {
                echo $res;
            }
        }
    }

    /**
     * 添加私彩 单个彩种
     * @param $lottery
     * @param $partnerSign
     * @return mixed
     */
    static function addLotteryToPartner($lottery, $partnerSign) {
        $data[] = [
            'partner_sign'          => $partnerSign,
            'series_id'             => $lottery->series_id,
            'icon_path'             => $lottery->lottery_icon,
            'is_fast'               => $lottery->is_fast,
            'is_sport'              => $lottery->is_sport,
            'lottery_sign'          => $lottery->en_name,
            'lottery_name'          => $lottery->cn_name,
            'max_trace_number'      => $lottery->max_trace_number,

            'min_times'             => $lottery->min_times,
            'max_times'             => $lottery->max_times,

            'open_casino'           => $lottery->open_casino,
            'min_prize_group'       => $lottery->min_prize_group,
            'max_prize_group'       => $lottery->max_prize_group,
            'diff_prize_group'      => $lottery->diff_prize_group,
            'max_prize_per_code'    => $lottery->max_prize_per_code,
            'max_prize_per_issue'   => $lottery->max_prize_per_issue,

            'valid_modes'           => $lottery->valid_modes,
            'valid_price'           => $lottery->valid_price,
            'status'                => 1,
        ];

        self::insert($data);

        // 同步玩法
        $methodList = LotteryMethod::where("lottery_sign", $lottery->en_name)->where("status", 1)->get();

        $data = [];
        foreach ($methodList as $method) {
            $data[] = [
                'partner_sign'          => $partnerSign,
                'config_id'             => $method->id,
                'method_name'           => $method->method_name,
                'method_sign'           => $method->method_sign,

                'lottery_sign'          => $method->lottery_sign,
                'lottery_name'          => $method->lottery_name,

                'max_prize_per_code'    => $method->max_prize_per_code,
                'max_prize_per_issue'   => $method->max_prize_per_issue,
                'challenge_type'        => $method->challenge_type,
                'challenge_min_count'   => $method->challenge_min_count,
                'challenge_config'      => $method->challenge_config,
                'challenge_bonus'       => $method->challenge_bonus,
                'status'                => 1,
            ];
        }

        PartnerMethod::insert($data);
        return true;
    }

    /**
     * 获取列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
        $query = self::select(
            DB::raw('partner_lottery.*'),
            DB::raw('lotteries.is_fast'),
            DB::raw('lotteries.is_sport'),
            DB::raw('lotteries.day_issue'),
            DB::raw('lotteries.issue_format'),
            DB::raw('lotteries.issue_type'),
            DB::raw('lotteries.valid_code'),
            DB::raw('lotteries.code_length'),
            DB::raw('lotteries.positions'),
            DB::raw('lotteries.open_time'),
            DB::raw('lotteries.auto_open')
        )->leftJoin('lotteries', 'lotteries.en_name', '=', 'partner_lottery.lottery_sign')->orderBy('partner_lottery.id', 'desc');

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_lottery.partner_sign', $c['partner_sign']);
        }

        // 类型
        if (isset($c['series_id']) && $c['series_id'] && $c['series_id'] != "all") {
            $query->where('partner_lottery.series_id', $c['series_id']);
        }

        // 状态
        if (isset($c['status']) && $c['status'] && $c['status'] != "all") {
            $query->where('partner_lottery.status', $c['status']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取
     * @param $partnerSign
     * @param $lotterySign
     * @return mixed
     */
    static function findBySign($partnerSign, $lotterySign) {
        $query = self::select(
            DB::raw('partner_lottery.*'),
            DB::raw('lotteries.is_fast'),
            DB::raw('lotteries.is_sport'),
            DB::raw('lotteries.day_issue'),
            DB::raw('lotteries.issue_format'),
            DB::raw('lotteries.issue_type'),
            DB::raw('lotteries.valid_code'),
            DB::raw('lotteries.code_length'),
            DB::raw('lotteries.positions'),
            DB::raw('lotteries.open_time'),
            DB::raw('lotteries.auto_open')
        )->leftJoin('lotteries', 'lotteries.en_name', '=', 'partner_lottery.lottery_sign')->orderBy('partner_lottery.id', 'desc');

        // 商户或则总后台 是否传值商户名
        if (isset($partnerSign) && !empty($partnerSign)){
            $query->where('partner_lottery.partner_sign', $partnerSign);
        }
        $query->where('partner_lottery.lottery_sign', $lotterySign);
        return $query->first();
    }

    /**
     * 获取partnerSign
     * @param $partnerSign
     * @return array
     */
    static function getOption($partnerSign) {
        $items  = self::where('status', 1)->where("partner_sign", $partnerSign)->get();
        $data   = [];
        foreach ($items as $item) {
            $data[$item->lottery_sign] = $item->lottery_name;
        }

        return $data;
    }

    /**
     * 获取 系列下的所有猜中 选项
     * @param $partnerSign
     * @return array
     */
    static function getSeriesLotteryOptions($partnerSign) {
        $items = self::where('status', 1)->where("partner_sign", $partnerSign)->orderBy('series_id')->get();
        $data = [];
        foreach ($items as $item) {
            if(!isset($data[$item->series_id])){
                $data[$item->series_id] = [$item->lottery_sign => $item->lottery_name];
            } else {
                $data[$item->series_id] = array_merge($data[$item->series_id], [$item->lottery_sign=>$item->lottery_name]);
            }

        }

        return $data;
    }


    /**
     * 获取 系列下的所有猜中 选项
     * @param $partnerSign
     * @return array
     */
    static function getSeriesSelfOpenLotteryOptions($partnerSign) {
        $items = self::select(
            DB::raw('partner_lottery.*'),
            DB::raw('partner_lottery.lottery_name as cn_name'),
            DB::raw('lotteries.en_name'),
            DB::raw('lotteries.is_fast'),
            DB::raw('lotteries.is_sport'),
            DB::raw('lotteries.auto_open'),
            DB::raw('lotteries.day_issue'),
            DB::raw('lotteries.issue_format'),
            DB::raw('lotteries.issue_part'),
            DB::raw('lotteries.issue_type'),
            DB::raw('lotteries.valid_code'),
            DB::raw('lotteries.code_length'),
            DB::raw('lotteries.positions'),
            DB::raw('lotteries.open_time'),
            DB::raw('lotteries.issue_desc')
        )->leftJoin('lotteries', 'lotteries.en_name', '=', 'partner_lottery.lottery_sign')
            ->where([
                'partner_lottery.partner_sign' => $partnerSign,
                'partner_lottery.status' => 1,
                'lotteries.auto_open'  => 1
            ])->get();
        $data = [];
        foreach ($items as $item) {
            if (!isset($data[$item->series_id])) {
                $data[$item->series_id] = [];
            }

            $data[$item->series_id][] = [
                'label' => $item->cn_name,
                'value' => $item->en_name
            ];

        }

        return $data;
    }

    /**
     * @param $params
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setLottery($params)
    {
        // if(isset($params['lottery_name']))
        //  {
        //     self::where('lottery_sign', $this->lottery_sign)->where('partner_sign', $this->partner_sign)->update(['lottery_name' => $params['lottery_name']]);
        // }
        self::where('lottery_sign', $this->lottery_sign)->where('partner_sign', $this->partner_sign)->update($params);

        ConfigureCache::forget("method_config_" . $this->partner_sign . "_" . $this->lottery_sign);

        self::flushAllLotteryToFrontEnd($this->partner_sign);
        LotteryCache::flushPartnerAllLottery($this->partner_sign);
        return true;
    }

    // 修改状态
    public function changeStatus() {
        $this->is_hot = $this->is_hot > 0 ? 0 : 1;
        $this->save();
        return true;
    }

    /**
     * 格式化 模式
     * @return string
     */
    public function formatModes() {
        $modeConfig     = config("game.main.modes");
        $modeArr        = explode(",", $this->valid_modes);

        $str = "";
        foreach ($modeArr as $id) {
            $str .= $modeConfig[$id]['title'] . ",";
        }

        return trim($str, ",");
    }

    /**
     * 格式化 单价
     * @return string
     */
    public function formatPrice()
    {

        $priceConfig = config("game.main.price");
        $currentPrice = explode(",", $this->valid_price);

        $str = "";
        foreach ($currentPrice as $price) {
            $str .= $price . ",";
        }

        return trim($str, ",");
    }

    // 格式化单价
    public function getFormatPrice()
    {
        $priceConfig = config("game.main.price");
        $currentModes = explode(",", $this->valid_price);

        $data = [];
        foreach ($currentModes as $index) {
            $_mode = $priceConfig[$index];
            $data[$index] = $_mode;

        }

        return $data;
    }

    /** =================================== 选项 ==================================== */

    /**
     * @param $partnerSign
     * @param bool $hasMethod
     * @return array
     * @throws \Exception
     */
    static function getSelectOptions($partnerSign, $hasMethod = true) {
        $items      = LotteryCache::getPartnerAllLottery($partnerSign);
        $series     = config("game.main.series");
        $data = [
            "default" => [
                "label"     => "所有系列",
                'value'     => "all",
                "isLeaf"    => true,
                "children"  => [
                    [
                        "label"     => "所有彩种",
                        'value'     => "all",
                        "children"  => [],
                        "isLeaf"    => true,
                    ]
                ],
            ],
        ];

        if ($hasMethod) {
            $data['default']["children"]["isLeaf"] = true;
            $data['default']["children"][0]["children"] =  [
                [
                    "label"     => "所有玩法",
                    'value'     => "all",
                    "children"  => [],
                    "isLeaf"    => true
                ]
            ];
        }

        foreach ($items as $item) {
            if (!isset($data[$item->series_id])) {
                $data[$item->series_id] = [
                    "label"     => $series[$item->series_id],
                    'value'     => $item->series_id,
                    "children"  => [
                        [
                            "label"     => "所有彩种",
                            'value'     => "all",
                            "children"  => [
                                [
                                    "label"     => "所有玩法",
                                    'value'     => "all",
                                    "children"  => [],
                                    "isLeaf"    => true
                                ]
                            ],
                        ]
                    ],
                ];
            }

            // 组合玩法
            $methods = [];
            if ($hasMethod) {
                $methods[] = [
                    "label"     => "所有玩法",
                    'value'     => "all",
                    "isLeaf"    => true
                ];

                foreach ($item->method_config as $_method) {
                    $methods[] = [
                        "label"     => $_method['method_name'],
                        'value'     => $_method['method_sign'],
                        "isLeaf"    => true
                    ];
                }

                if ($item->method_config_casino) {
                    foreach ($item->method_config_casino as $_method) {
                        $methods[] = [
                            "label"     => $_method['method_name'],
                            'value'     => $_method['method_sign'],
                            "isLeaf"    => true
                        ];
                    }
                }
            }

            // 组装
            $data[$item->series_id]["children"][] = [
                "label"     => $item->cn_name,
                'value'     => $item->en_name,
                "children"  => $methods,
                "isLeaf"    => $hasMethod ? false : true
            ];;
        }

        return array_values($data);
    }


    /**
     * @param $partnerSign
     * @param bool $hasMethod
     * @return array
     * @throws \Exception
     */
    static function getSelectSelfOpenOptions($partnerSign) {
        $items      = self::getAllSelfOpenLottery($partnerSign);
        $series     = config("game.main.series");
        $data = [
            "default" => [
                "label"     => "所有系列",
                'value'     => "all",
                "isLeaf"    => true,
                "children"  => [
                    [
                        "label"     => "所有彩种",
                        'value'     => "all",
                        "children"  => [],
                        "isLeaf"    => true,
                    ]
                ],
            ],
        ];


        foreach ($items as $item) {
            if (!isset($data[$item->series_id])) {
                $data[$item->series_id] = [
                    "label"     => $series[$item->series_id],
                    'value'     => $item->series_id,
                    "children"  => [
                        [
                            "label"     => "所有彩种",
                            'value'     => "all",
                        ],
                    ]
                ];
            }


            // 组装
            $data[$item->series_id]["children"][] = [
                "label"     => $item->cn_name,
                'value'     => $item->en_name,
            ];;
        }

        return array_values($data);
    }

    /** =================================== lottery ==================================== */

    /**
     * 只用于前端展示
     * @param $partnerSign
     * @return array
     * @throws
     */
    static function getAllLotteryToFrontEnd($partnerSign) {
        $key = "lottery_for_frontend_" . $partnerSign;

        if (ConfigureCache::has($key)) {
            return ConfigureCache::get($key);
        }

        $lotteries = self::where("partner_sign", $partnerSign)->where('status', 1)->get();

        $cacheData = [];
        foreach ($lotteries as $lottery) {
            $imgs = substr($lottery['icon_path'], 0,7) == 'lottery' ? strtolower($partnerSign).'/'.$lottery['icon_path'] : $lottery['icon_path'];

            $lottery->icon_path     = $imgs;
            $lottery->en_name = $lottery->lottery_sign;
            $lottery->cn_name = $lottery->lottery_name;

            $lottery->valid_modes = $lottery->getFormatMode();
            //格式化 一元模式 两元模式
            $lottery->valid_price = $lottery->getFormatPrice();

            // 获取所有玩法
            $methods    = LotteryCache::getPartnerLotteryAllMethodConfig($lottery->lottery_sign, $lottery->partner_sign);
            $officialMethods    = [];
            $casinoMethods      = [];

            $groupName  = config("game.method.group_name");
            $rowName    = config("game.method.row_name");

            $rowData = [];
            foreach($methods as $index => $method) {
                if (!$method['show']) {
                    continue;
                }

                $rowData[$method['method_group']][$method['method_row']][] = [
                    'method_name'           =>    $method['method_name'],
                    'method_sign'           =>    $method['method_sign'],
                    'challenge_type'        =>    $method['challenge_type'],
                    'challenge_min_count'   =>    $method['challenge_min_count'],
                    'challenge_bonus'       =>    $method['challenge_bonus'],
                    'challenge_config'      =>    $method['challenge_config'],
                    'status'                =>    $method['status'],
                ];
            }

            $groupData  = [];
            $hasRow     = [];
            foreach($methods as $index => $method) {
                if (!$method['show']) {
                    continue;
                }

                // 行
                if (!isset($groupData[$method['method_group']])) {
                    $groupData[$method['method_group']] = [];
                }

                if (!isset($hasRow[$method['method_group']]) || !in_array($method['method_row'], $hasRow[$method['method_group']])) {
                    $groupData[$method['method_group']][] = [
                        'name'      => $rowName[$lottery->series_id][$method['method_row']],
                        'sign'      => $method['method_row'],
                        'methods'   => $rowData[$method['method_group']][$method['method_row']],
                    ];
                    $hasRow[$method['method_group']][] = $method['method_row'];
                }
            }

            // 组
            $defaultGroup   = "";
            $defaultMethod  = "";
            $hasGroup       = [];
            foreach($methods as $index => $method) {
                if (!$method['show']) {
                    continue;
                }

                if ($index == 0) {
                    $defaultGroup   = $method['method_group'];
                    $defaultMethod  = $method['method_sign'];
                }

                // 组
                if (!in_array($method['method_group'], $hasGroup)) {
                    if ($method['method_type'] == 'casino') {
                        $casinoMethods[] = [
                            'name' => $groupName[$lottery->series_id][$method['method_group']],
                            'sign' => $method['method_group'],
                            'rows' => $groupData[$method['method_group']]
                        ];
                    } else {
                        $officialMethods[] = [
                            'name' => $groupName[$lottery->series_id][$method['method_group']],
                            'sign' => $method['method_group'],
                            'rows' => $groupData[$method['method_group']]
                        ];
                    }

                    $hasGroup[] = $method['method_group'];
                }

            }

            $cacheData[$lottery->en_name] = [
                'lottery'               => $lottery,
                'methodConfig'          => $officialMethods,
                'methodConfig_casino'   => $casinoMethods,
                'defaultGroup'          => $defaultGroup,
                'defaultMethod'         => $defaultMethod,
            ];
        }

        ConfigureCache::forget('lottery_for_frontend', $cacheData);

        return $cacheData;
    }

    /**
     * 刷新彩种
     * @param $partnerSign
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function  flushAllLotteryToFrontEnd($partnerSign)
    {
        $key = "lottery_for_frontend_" . $partnerSign;
        if (ConfigureCache::has($key)) {
            ConfigureCache::forget($key);
        }

        return true;
    }

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getAllLotteriesFromDb($partnerSign) {
        $query = self::select(
            DB::raw('partner_lottery.*'),

            // DB::raw('lotteries.cn_name'),
            DB::raw('partner_lottery.lottery_name as cn_name'),
            DB::raw('lotteries.en_name'),
            DB::raw('lotteries.is_fast'),
            DB::raw('lotteries.is_sport'),
            DB::raw('lotteries.auto_open'),
            DB::raw('lotteries.day_issue'),
            DB::raw('lotteries.issue_format'),
            DB::raw('lotteries.issue_part'),
            DB::raw('lotteries.issue_type'),
            DB::raw('lotteries.valid_code'),
            DB::raw('lotteries.code_length'),
            DB::raw('lotteries.positions'),
            DB::raw('lotteries.open_time'),
            DB::raw('lotteries.issue_desc')

        )->leftJoin('lotteries', 'lotteries.en_name', '=', 'partner_lottery.lottery_sign')
            ->where([
                'partner_lottery.partner_sign' => $partnerSign,
                'partner_lottery.status' => 1,
            ])
            ->orderBy('partner_lottery.sort', 'ASC');

        $lotteries      = $query->get();

        $lotteryData    = [];

        foreach ($lotteries as $lottery) {
            $methods    = LotteryCache::getPartnerLotteryAllMethodConfig($lottery->lottery_sign, $partnerSign);
            $officialMethods = [];
            $casinoMethods   = [];
            foreach ($methods as $method) {
                if ($method['method_type'] == 'casino') {
                    $_method    = $method;
                    $casinoMethods[$method['method_sign']] = $_method;
                } else {
                    $_method    = $method;
                    $officialMethods[$method['method_sign']] = $_method;
                }
            }

            $lottery->method_config         = $officialMethods;
            $lottery->method_config_casino  = $casinoMethods;
            $lottery->valid_modes   = Lottery::buildModes($lottery->valid_modes);
            $lottery->valid_price   = Lottery::buildPrice($lottery->valid_price);
            $lotteryData[$lottery->lottery_sign] = $lottery;
        }

        return $lotteryData;
    }

    /**
     * @param $partnerSign
     * @return array
     * @throws \Exception
     */
    static function getAllSelfOpenLottery($partnerSign) {
        $query = self::select(
            DB::raw('partner_lottery.*'),
            DB::raw('partner_lottery.lottery_name as cn_name'),
            DB::raw('lotteries.en_name'),
            DB::raw('lotteries.is_fast'),
            DB::raw('lotteries.is_sport'),
            DB::raw('lotteries.auto_open'),
            DB::raw('lotteries.day_issue'),
            DB::raw('lotteries.issue_format'),
            DB::raw('lotteries.issue_part'),
            DB::raw('lotteries.issue_type'),
            DB::raw('lotteries.valid_code'),
            DB::raw('lotteries.code_length'),
            DB::raw('lotteries.positions'),
            DB::raw('lotteries.open_time'),
            DB::raw('lotteries.issue_desc')

        )->leftJoin('lotteries', 'lotteries.en_name', '=', 'partner_lottery.lottery_sign')
            ->where([
                'partner_lottery.partner_sign' => $partnerSign,
                'partner_lottery.status' => 1,
                'auto_open'  => 1
            ])
            ->orderBy('partner_lottery.sort', 'ASC');

        $lotteries      = $query->get();

        return $lotteries;
    }


    /**
     * @param $partnerSign
     * @param $lotterySign
     * @return mixed
     * @throws \Exception
     */
    static function getLotteryFromDb($partnerSign, $lotterySign) {
        $lottery = self::select(
            DB::raw('partner_lottery.*'),
            DB::raw('partner_lottery.lottery_name as cn_name'),
            DB::raw('lotteries.en_name'),
            DB::raw('lotteries.is_fast'),
            DB::raw('lotteries.is_sport'),
            DB::raw('lotteries.auto_open'),
            DB::raw('lotteries.day_issue'),
            DB::raw('lotteries.issue_format'),
            DB::raw('lotteries.issue_part'),
            DB::raw('lotteries.issue_type'),
            DB::raw('lotteries.valid_code'),
            DB::raw('lotteries.code_length'),
            DB::raw('lotteries.positions'),
            DB::raw('lotteries.open_time'),
            DB::raw('lotteries.issue_desc')

        )->leftJoin('lotteries', 'lotteries.en_name', '=', 'partner_lottery.lottery_sign')
            ->where('partner_lottery.partner_sign', $partnerSign)
            ->where('partner_lottery.lottery_sign', $lotterySign)
            ->where('partner_lottery.status', 1)
            ->first();
        if(!$lottery) {
            return null;
        }
        $methods    = LotteryCache::getPartnerLotteryAllMethodConfig($lotterySign, $partnerSign);
        $officialMethods = [];
        $casinoMethods   = [];
        foreach ($methods as $method) {
            if ($method['method_type'] == 'casino') {
                $_method    = $method;
                $casinoMethods[$method['method_sign']] = $_method;
            } else {
                $_method    = $method;
                $officialMethods[$method['method_sign']] = $_method;
            }
        }

        $lottery->method_config         = $officialMethods;
        $lottery->method_config_casino  = $casinoMethods;
        $lottery->valid_modes   = Lottery::buildModes($lottery->valid_modes);
        $lottery->valid_price   = Lottery::buildPrice($lottery->valid_price);

        return $lottery;
    }

    /**
     * 检测追号数据
     * @param $traceData
     * @return array|string
     */
    public function checkTraceData($traceData) {
        if (!$traceData || !is_array($traceData)) {
            return "对不起, 无效的追号奖期数据!";
        }

        $_traceData  = array_keys($traceData);
        $issueItems = LotteryIssue::whereIn('issue', $_traceData)->where('lottery_sign', $this->lottery_sign)->orderBy('begin_time', 'ASC')->get();
        $nowTime    = time();

        $data       = [];
        $itemCount  = 0;
        foreach ($issueItems as $item) {
            if ($item->end_time <= $nowTime) {
                return "对不起, 存在无效的奖期!";
            }

            $data[$item->issue] = $traceData[$item->issue];
            $itemCount ++;
        }

        // 追好期数
        if (count($traceData) != $itemCount) {
            return "对不起, 追号奖期不正确!";
        }

        return $data;
    }

    public function getFormatMode() {
        $modeConfig     = config("game.main.modes");
        $currentModes   = explode(",", $this->valid_modes);

        $data = [];
        foreach ($currentModes as $index) {
            $_mode = $modeConfig[$index];
            $data[$index] = $_mode;
        }

        return $data;
    }

    /**
     * 检测挑战
     * @param $method
     * @param $oMethod
     * @param $project
     * @return float|int|mixed|string|null
     */
    static function checkChallenge($method, $oMethod, $project) {

        $challengeType = $method['challenge_type'];

        // 没有配置 走默认
        if (!$challengeType || !$method['challenge_bonus']) {
            $defaultPercentage  = config("game.challenge.default_percentage", 0.02);

            if ($project['count'] < $oMethod->total * $defaultPercentage) {
                $defaultBonus       = config("game.challenge.default_bonus", 20000);
                return moneyUnitTransferIn($defaultBonus);
            }
        }

        switch($challengeType) {
            case 1:
            case 2:
                if ($project['count'] <= $method['challenge_min_count']) {
                    return moneyUnitTransferIn($method['challenge_bonus']);
                }

                break;
            case 3:
            case 7:
                $config = $method['challenge_config'];
                $code   = strval($config['code']);

                if ($code && preg_match("/[{$code}]/", $project['codes'])) {
                    return moneyUnitTransferIn($method['challenge_bonus']);
                }
                break;
            case 4:
                $config = $method['challenge_config'];
                $zu3MinCount = $config['zu3'];
                $zu6MinCount = $config['zu6'];

                $projectArr = explode(',', $project['codes']);
                $zu3CodeCount = 0;
                $zu6CodeCount = 0;

                foreach ($projectArr as $_code) {
                    $_codeArr = str_split($_code, 1);
                    if (count($_codeArr) != count(array_unique($_codeArr))) {
                        $zu3CodeCount += 1;
                    } else {
                        $zu6CodeCount += 1;
                    }
                }

                if (($zu3CodeCount > 0 && $zu3CodeCount <= $zu3MinCount) || ($zu6CodeCount > 0 && $zu6CodeCount <= $zu6MinCount)) {
                    return moneyUnitTransferIn($method['challenge_bonus']);
                }
                break;
            case 5:
                return moneyUnitTransferIn($method['challenge_bonus']);
                break;
            // N个号码里面 出M个
            case 6:
                $config     = $method['challenge_config'];
                $codeArr    = $config['code'];
                $min        = $config['min'];

                $exitCode = 0;

                $betCodeArr = $oMethod->transferCodeToArray($project['codes']);
                foreach ($betCodeArr as $_code) {
                    if (isset($codeArr[$_code])) {
                        $exitCode += 1;
                    }
                }

                if ($exitCode >= $min) {
                    return moneyUnitTransferIn($method['challenge_bonus']);
                }
                break;
            default:
                return 0;

        }

        return 0;
    }

}
