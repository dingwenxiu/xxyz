<?php

namespace App\Models\Game;

use App\Lib\Clog;
use App\Lib\Help;
use App\Lib\Logic\Cache\ConfigureCache;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Lottery\JackpotLogic;
use App\Models\Partner\Partner;
use App\Lib\Logic\Lottery\IssueLogic;
use App\Models\Partner\PartnerLottery;
use App\Lib\Logic\Lottery\LotteryLogic;
use App\Models\Partner\PartnerMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class Lottery extends BaseGame
{
    public $rules = [
        'cn_name' => 'required|min:4|max:32',
        'en_name' => 'required|min:4|max:32',
        'series_id' => 'required|min:2|max:32',
        'max_trace_number' => 'required|min:1|max:32',
        'issue_format' => 'required|min:2|max:32',
    ];

    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'lotteries';

    public $method_config = [];

    // 标识获取彩种
    static function findBySign($sign)
    {
        return self::where("en_name", $sign)->first();
    }

    static function getLotteryAddList()
    {
        $query = self::select(
            DB::raw('en_name'),
            DB::raw('cn_name'),
            DB::raw('lottery_icon')

        );
        return $query->get();
    }

    public function lotteryEdit($data, $id){

        /*
         * cn_name 須填寫
         * en_name 是拼寫
         * */

        $validator = Validator::make($data, $this->rules);
        /*
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        */

        $lottery = Lottery::find($id);
        if(!$lottery){
            return Help::returnApiJson("对不起, 不存在的彩種id！", 0);
        }

        db()->beginTransaction();
        try {


            $lottery->cn_name = $data['cn_name'];                                      // 彩票中文名字
            $lottery->en_name = $data['en_name'];                                      // 彩票英文名字
            $lottery->lottery_icon = $data['lottery_icon'];                            // 图片地址
            $lottery->partner_sign = $data['partner_sign'];                            // 商户标识

            $lottery->series_id = $data['series_id'];                                  // 彩票系列id
            $lottery->logic_sign =  $data['logic_sign'];                               //目前與series_id 一致 以後可能不用了

            $lottery->is_fast = isset($data['is_fast']) ? 1 : 0;                       // 高频彩 1 是 0不是
            $lottery->is_sport = isset($data['is_sport']) ? 1 : 0;                      // 是否是体育彩票
            $lottery->auto_open = isset($data['auto_open']) ? 1 : 0;                   // 自开:

            $lottery->max_trace_number = $data['max_trace_number'];                    // 追号
            $lottery->day_issue = $data['day_issue'];                                  // 每日奖期

            $lottery->issue_format = $data['issue_format'];
            $lottery->issue_part = $data['issue_part'];
            $lottery->issue_type = $data['issue_type'];                            // 奖期类型

            $lottery->valid_code = $data['valid_code'];                            // 合法号码
            $lottery->code_length = $data['code_length'];                          // 合法号码长度
            $lottery->open_casino = isset($data['open_casino']) ? 1 : 0;           // 是否娱乐城

            $lottery->positions = $data['positions'];                              // 位置
            $lottery->open_time = $data['open_time'];                              // 开放时间

            $lottery->min_prize_group = $data['min_prize_group'];                  // 最小奖金组:
            $lottery->max_prize_group = $data['max_prize_group'];                  // 最大奖金组:
            $lottery->diff_prize_group = $data['diff_prize_group'];                // 奖金组差值
            $lottery->min_times = $data['min_times'];                              // 最小倍数
            $lottery->max_times = $data['max_times'];                              // 最大倍数
            $lottery->max_prize_per_code = $data['max_prize_per_code'];            // 单注奖金
            $lottery->max_prize_per_issue = $data['max_prize_per_issue'];          // 单期最大奖金

            $lottery->valid_modes = $data['valid_modes'];                          // 投注模式: 圆角分
            $lottery->valid_price = $data['valid_price'];                          // 一元/二元模式
            $lottery->status = $data['status'] ?? 1;                               // 开启关闭
            $lottery->issue_desc = $data['issue_desc'] ?? '描述';                      // 描述
            $lottery->save();


            // 生成玩法
            LotteryMethod::where("lottery_sign", $lottery->en_name)->where("status", 1)->delete();
            PartnerLottery::where("lottery_sign", $lottery->en_name)->where("status", 1)->delete();
            PartnerMethod::where("lottery_sign", $lottery->en_name)->where("status", 1)->delete();

            $res = LotteryLogic::genMethodByLottery($lottery);
            if ($res !== true) {
                db()->rollback();
                return $res;
            }

            // 同步游戏到商户
            $res = PartnerLottery::addLotteryToPartner($lottery, $data['partner_sign']);

            if ($res !== true) {
                db()->rollback();
                return ['msg' => '对不起, 同步到商户失败', 'res' => 0];
            }

            db()->commit();
            return ['msg' => '修改成功', 'res' => 1];
        } catch (\Exception $e) {
            db()->rollback();
            return ['msg' => $e->getMessage(), 'res' => 0];
        }

    }

    public function lotteryAdd($data)
    {


        /*
         * cn_name 須填寫
         * en_name 是拼寫
         * */

        $validator = Validator::make($data, $this->rules);
        /*
        if ($validator->fails()) {
            return $validator->errors()->first();
        }
        */

        // 如果是系统自建彩票
        if ($data['partner_sign'] == "system") {
            $en_name =  $data['en_name'];
        } else {
            $en_name =  strtolower( $data['partner_sign']) . $data['en_name'];
        }

        db()->beginTransaction();
        try {

            $this->cn_name = $data['cn_name'];                                      // 彩票中文名字
            $this->en_name = $en_name; // 彩票英文名字
            $this->lottery_icon = $data['lottery_icon'];                            // 图片地址
            $this->partner_sign = $data['partner_sign'];                            // 商户标识

            $this->series_id = $data['series_id'];                                  // 彩票系列id
            $this->logic_sign =  $data['logic_sign'];                               //目前與series_id 一致 以後可能不用了

            $this->is_fast = isset($data['is_fast']) ? 1 : 0;                       // 高频彩 1 是 0不是
            $this->is_sport = isset($data['is_sport']) ? 1 : 0;                      // 是否是体育彩票
            $this->auto_open = isset($data['auto_open']) ? 1 : 0;                   // 自开:

            $this->max_trace_number = $data['max_trace_number'];                    // 追号
            $this->day_issue = $data['day_issue'];                                  // 每日奖期

            $this->issue_format = $data['issue_format'];
            $this->issue_part = $data['issue_part'];
            $this->issue_type = $data['issue_type'];                            // 奖期类型

            $this->valid_code = $data['valid_code'];                            // 合法号码
            $this->code_length = $data['code_length'];                          // 合法号码长度
            $this->open_casino = isset($data['open_casino']) ? 1 : 0;           // 是否娱乐城

            $this->positions = $data['positions'];                              // 位置
            $this->open_time = $data['open_time'];                              // 开放时间

            $this->min_prize_group = $data['min_prize_group'];                  // 最小奖金组:
            $this->max_prize_group = $data['max_prize_group'];                  // 最大奖金组:
            $this->diff_prize_group = $data['diff_prize_group'];                // 奖金组差值
            $this->min_times = $data['min_times'];                              // 最小倍数
            $this->max_times = $data['max_times'];                              // 最大倍数
            $this->max_prize_per_code = $data['max_prize_per_code'];            // 单注奖金
            $this->max_prize_per_issue = $data['max_prize_per_issue'];          // 单期最大奖金

            $this->valid_modes = $data['valid_modes'];                          // 投注模式: 圆角分
            $this->valid_price = $data['valid_price'];                          // 一元/二元模式
            $this->status = $data['status'] ?? 1;                               // 开启关闭
            $this->issue_desc = $data['issue_desc'] ?? '描述';                      // 描述

            $this->save();

            // 生成玩法
            $res = LotteryLogic::genMethodByLottery($this);
            if ($res !== true) {
                db()->rollback();
                return $res;
            }

            // 同步游戏到商户
            $res = PartnerLottery::addLotteryToPartner($this, $data['partner_sign']);
            if ($res !== true) {
                db()->rollback();
                return ['msg' => '对不起, 同步到商户失败', 'res' => 0];
            }

            db()->commit();
            return ['msg' => '建立成功', 'res' => 1];
        } catch (\Exception $e) {
            db()->rollback();
            return ['msg' => $e->getMessage() . '--' . $e->getLine(), 'res' => 0];
        }

    }

    // 获取列表
    static function getList($c)
    {
        $query = self::orderBy('id', 'desc');

        if (isset($c['en_name']) && $c['en_name']) {
            $query->where('en_name', $c['en_name']);
        }

        if (isset($c['series_id']) && $c['series_id']) {
            $query->where('series_id', $c['series_id']);
        }

        $currentPage = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset = ($currentPage - 1) * $pageSize;

        $total = $query->count();
        $data = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $data, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    // 保存
    public function saveItem($sign)
    {

        $data = request()->all();


        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $this->series_id = $data['series_id'];
        $this->logic_sign = $data['logic_sign'];
        $this->cn_name = $data['cn_name'];
        $this->en_name = $data['en_name'];

        $this->max_trace_number = intval($data['max_trace_number']);
        $this->issue_format = $data['issue_format'];
        $this->day_issue = $data['day_issue'];
        $this->valid_code = $data['valid_code'];
        $this->code_length = $data['code_length'];
        $this->positions = $data['positions'];
        $this->open_time = $data['open_time'];

        $this->min_prize_group = $data['min_prize_group'];
        $this->max_prize_group = $data['max_prize_group'];
        $this->diff_prize_group = $data['diff_prize_group'];

        $this->max_prize_per_code = $data['max_prize_per_code'];
        $this->max_prize_per_issue = $data['max_prize_per_issue'];

        $this->valid_modes = $data['valid_modes'];

        $this->is_fast = isset($data['is_fast']) ? 1 : 0;
        $this->auto_open = isset($data['auto_open']) ? 1 : 0;

        $this->save();
        return true;
    }

    /**
     * 给商户添加彩种
     * @param $data
     * @param $partnerSign
     * @return bool|string
     */

    public function addPartnerLottery($data, $partnerSign)
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        $partner = Partner::findPartnerBySign($partnerSign);
        if (!$partner) {
            return "对不起, 无效的商户!";
        }

        $sign = strtolower($partnerSign) . $data['en_name'];

        db()->beginTransaction();
        try {
            $this->series_id = $data['series_id'];
            $this->partner_sign = $partnerSign;
            $this->logic_sign = $data['logic_sign'];
            $this->lottery_icon = "system/lottery/" . $data['en_name'] . ".png";
            $this->cn_name = $data['cn_name'];
            $this->en_name = $sign;

            $this->max_trace_number = intval($data['max_trace_number']);
            $this->issue_format = $data['issue_format'];
            $this->issue_part = 1;
            $this->issue_type = $data['issue_type'];
            $this->open_casino = isset($data['open_casino']) && $data['open_casino'] && $data['series_id'] == 'ssc' ? 1 : 0;
            $this->day_issue = $data['day_issue'];
            $this->valid_code = $data['valid_code'];
            $this->code_length = $data['code_length'];
            $this->positions = $data['positions'];
            $this->open_time = $data['open_time'];

            $this->min_times = $data['min_times'];
            $this->max_times = $data['max_times'];

            $this->min_prize_group = $data['min_prize_group'];
            $this->max_prize_group = $data['max_prize_group'];
            $this->diff_prize_group = $data['diff_prize_group'];

            $this->max_prize_per_code = $data['max_prize_per_code'];
            $this->max_prize_per_issue = $data['max_prize_per_issue'];

            $this->valid_modes = $data['valid_modes'];
            $this->valid_price = $data['valid_price'];


            $this->is_fast = $data['is_fast'];
            $this->is_sport =$data['is_sport'];
            $this->auto_open = 1;


            $this->issue_desc = $data['issue_desc'];

            $this->status = 1;

            $this->save();

            // 生成玩法
            $res = LotteryLogic::genMethodByLottery($this);
            if ($res !== true) {
                db()->rollback();
                return $res;
            }

            // 同步游戏到商户
            $res = PartnerLottery::addLotteryToPartner($this, $partnerSign);
            if ($res !== true) {
                db()->rollback();
                return "对不起, 同步到商户失败";
            }

            db()->commit();
        } catch (\Exception $e) {
            db()->rollback();
            return $e->getMessage();
        }
        
        return $this;
    }

    /**
     * 商户 - 设置彩种
     * @param $params
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setLottery($params)
    {
        self::where('en_name', $this->lottery_sign)->update($params);
        ConfigureCache::forget("method_config_" . $this->en_name);
        self::flushAllLotteryToFrontEnd();
        self::flushAllPartnerLotteryCache();
        return true;
    }

    /**
     * 刷新彩种
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function flushAllLotteryToFrontEnd()
    {
        $key = "lottery_for_frontend_";
        if (ConfigureCache::has($key)) {
            ConfigureCache::forget($key);
        }

        return true;
    }

    /**
     * 刷新缓存
     * @param $partnerSign
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    static function flushAllPartnerLotteryCache()
    {
        $key = "lottery_all_";
        if (ConfigureCache::has($key)) {
            return ConfigureCache::forget($key);
        }
        return true;
    }

    /**
     * 格式化 模式
     * @return string
     */
    public function formatModes()
    {
        $modeConfig = config("game.main.modes");
        $modeArr = explode(",", $this->valid_modes);

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
        $modeArr = explode(",", $this->valid_price);

        $str = "";
        foreach ($modeArr as $id) {
            $str .= $priceConfig[$id]['val'] . ",";
        }

        return trim($str, ",");
    }

    // 获取选项
    static function getOptions()
    {
        $items = self::where('status', 1)->orderBy('series_id')->get();
        $data = [];
        foreach ($items as $item) {
            if (!isset($data[$item->series_id])) {
                $data[$item->series_id] = [$item->en_name => $item->cn_name];
            } else {
                $data[$item->series_id] = array_merge($data[$item->series_id], [$item->en_name => $item->cn_name]);
            }

        }

        return $data;
    }

    /**
     * 系列下的tab选项
     * @return array
     */
    static function getSeriesLotteryTabOptions()
    {
        $items = self::where('status', 1)->get();
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
     * ['cqssc' => "ssc"]
     * @return array
     */
    static function getSeriesOptions()
    {
        $items = self::where('status', 1)->get();
        $data = [];
        foreach ($items as $item) {
            $data[$item->en_name] = $item->series_id;
        }

        return $data;
    }

    /**
     * @param bool $hasMethod
     * @return array
     * @throws \Exception
     */
    static function getSelectOptions($hasMethod = true)
    {
        $items = self::getAllLotteryByCache();
        $series = config("game.main.series");
        $partnerOption = Partner::getOptions();
        $data = [
            "default" => [
                "label" => "所有系列",
                'value' => "all",
                "children" => [
                    [
                        "label" => "所有彩种",
                        'value' => "all",
                        "isLeaf" => true,
                    ]
                ],
            ],
        ];

        if ($hasMethod) {
            $data['default']["children"]["isLeaf"] = false;
            $data['default']["children"]["children"] = [
                [
                    "label" => "所有玩法",
                    'value' => "all",
                    "children" => [],
                    "isLeaf" => true
                ]
            ];
        }

        foreach ($items as $item) {
            if (!isset($data[$item->series_id])) {
                $data[$item->series_id] = [
                    "label" => $series[$item->series_id],
                    'value' => $item->series_id,
                    "children" => [
                        [
                            "label" => "所有彩种",
                            'value' => "all",
                            "children" => [
                                [
                                    "label" => "所有玩法",
                                    'value' => "all",
                                    "children" => [],
                                    "isLeaf" => true
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
                    "label" => "所有玩法",
                    'value' => "all",
                    "isLeaf" => true
                ];

                foreach ($item->method_config as $_method) {
                    $methods[] = [
                        "label" => $_method['method_name'],
                        'value' => $_method['method_sign'],
                        "isLeaf" => true
                    ];
                }

                if ($item->method_config_casino) {
                    foreach ($item->method_config_casino as $_method) {
                        $methods[] = [
                            "label" => $_method['method_name'],
                            'value' => $_method['method_sign'],
                            "isLeaf" => true
                        ];
                    }
                }
            }
            // 组装
            $data[$item->series_id]["children"][] = [
                "label" => $item->cn_name,
                'value' => $item->en_name ? $item->en_name : 'system',
                "children" => $methods,
                "isLeaf" => $hasMethod ? false : true
            ];;
        }

        return array_values($data);
    }

    // 获取彩种 通过系列
    static function getLotteryBySeries($seriesId, $selfOpenOnly = false)
    {
        $query = self::where('status', 1)->where('series_id', $seriesId);
        if ($selfOpenOnly) {
            $query->where("auto_open", 1);
        }

        $items = $query->get();
        $data = [];
        foreach ($items as $item) {
            $data[] = $item->en_name;
        }

        return $data;
    }

    public function getFormatMode()
    {
        $modeConfig = config("game.main.modes");
        $currentModes = explode(",", $this->valid_modes);

        $data = [];
        foreach ($currentModes as $index) {
            $_mode = $modeConfig[$index];
            $data[$index] = $_mode;
        }

        return $data;
    }

    /**
     * 预处理 已经失败的代码
     * @param $seriesId
     * @param $code
     */
    static function getPreLoseMethod($seriesId, $code)
    {
        if ($seriesId == "ssc") {

        }
    }

    /** ================================= 游戏相关 ================================== */

    /**
     * 合法的倍数
     * @param $times
     * @return bool
     */
    public function isValidTimes($times)
    {
        if (!$times || $times <= 0) {
            return false;
        }

        if ($times > $this->max_times || $times < $this->min_times) {
            return false;
        }

        return true;
    }

    /**
     * 是否是彩种合法的奖金组
     * @param $prizeGroup
     * @return bool
     */
    public function isValidPrizeGroup($prizeGroup)
    {

        if (!$prizeGroup) {
            return false;
        }

        if ($prizeGroup > $this->max_prize_group || $prizeGroup < $this->min_prize_group) {
            return false;
        }

        return true;
    }

    /**
     * 检测追号数据
     * @param $traceData
     * @return array|string
     */
    public function checkTraceData($traceData)
    {
        if (!$traceData || !is_array($traceData)) {
            return "对不起, 无效的追号奖期数据!";
        }

        $_traceData = array_keys($traceData);
        $issueItems = LotteryIssue::whereIn('issue', $_traceData)->where('lottery_sign', $this->en_name)->orderBy('begin_time', 'ASC')->get();
        $nowTime = time();

        $data = [];
        $itemCount = 0;
        foreach ($issueItems as $item) {
            if ($item->end_time <= $nowTime) {
                return "对不起, 存在无效的奖期!";
            }

            $data[$item->issue] = $traceData[$item->issue];
            $itemCount++;
        }

        // 追好期数
        if (count($traceData) != $itemCount) {
            return "对不起, 追号奖期不正确!";
        }

        return $data;
    }

    /** ================================= 缓存相关 ================================== */

    /**
     * 获取所有游戏通过缓存 包含玩法
     * @return array|mixed
     * @throws \Exception
     */
    static function getAllLotteryByCache()
    {

        if (self::_hasCache('lottery_all')) {
            return self::_getCacheData('lottery_all');
        } else {
            $lotteries = self::getAllLotteries();
            self::_saveCacheData('lottery_all', $lotteries);
            return $lotteries;
        }
    }

    /**
     * 刷新所有游戏通过缓存 包含玩法
     */
    static function flushAllLotteryByCache()
    {

        if (self::_hasCache('lottery_all')) {
            self::_flushCache('lottery_all');
        }
        return true;
    }

    /**
     * 获取单个彩种缓存
     * @param $lotterySign
     * @return mixed
     * @throws \Exception
     */
    static function getLotteryByCache($lotterySign)
    {

        if (self::_hasCache('lottery', $lotterySign)) {
            return self::_getCacheData('lottery', $lotterySign);
        } else {
            $lotteries = self::getAllLotteryByCache();
            $data = isset($lotteries[$lotterySign]) ? $lotteries[$lotterySign] : [];

            self::_saveCacheData('lottery', $data, $lotterySign);
            return $data;
        }
    }

    /**
     * 获取所有彩种 包换玩法数据
     * methods_config = [
     *      'total'  => []
     *      'level'  => []
     *      'object' => new StdClass()
     * ]
     * @return array
     * @throws \Exception
     */
    static function getAllLotteries()
    {

        $lotteries = self::where('status', 1)->get();
        $lotteryData = [];

        foreach ($lotteries as $lottery) {

            $methods = LotteryMethod::where('lottery_sign', $lottery->en_name)->where('status', 1)->get();
            $officialMethods = [];
            $casinoMethods = [];
            foreach ($methods as $method) {
                if ($method->method_type == 'casino') {
                    $_method = $method->toArray();
                    $casinoMethods[$method->method_sign] = $_method;
                } else {
                    $_method = $method->toArray();
                    $officialMethods[$method->method_sign] = $_method;
                }
            }

            $lottery->method_config = $officialMethods;
            $lottery->method_config_casino = $casinoMethods;
            $lottery->valid_modes = self::buildModes($lottery->valid_modes);
            $lotteryData[$lottery->en_name] = $lottery;
        }

        return $lotteryData;
    }

    /**
     * 构建模式
     * @param $validMode
     * @return array
     */
    static function buildModes($validMode)
    {
        if (!$validMode) {
            return [];
        }

        $modes = explode(",", $validMode);
        $modeConfig = config("game.main.modes");

        $data = [];
        foreach ($modes as $mode) {
            if (array_key_exists($mode, $modeConfig)) {
                $data[$mode] = $modeConfig[$mode];
            }
        }

        return $data;
    }

    /**
     * 构建 单价
     * @param $validPrice
     * @return array
     */
    static function buildPrice($validPrice)
    {
        if (!$validPrice) {
            return [];
        }

        $priceArr = explode(",", $validPrice);
        $priceConfig = config("game.main.price");

        $data = [];
        foreach ($priceArr as $price) {
            if (array_key_exists($price, $priceConfig)) {
                $data[$price] = $priceConfig[$price];
            }
        }

        return $data;
    }

    /**
     * 后台进程拉起　轻量数据
     */
    static function getAllLotteryForLoop()
    {

        if (self::_hasCache('lottery_loop_config')) {
            return self::_getCacheData('lottery_loop_config');
        } else {
            $lotteries = self::where('status', 1)->get();
            $data = [];
            foreach ($lotteries as $lottery) {
                $data[$lottery->en_name] = [
                    'name' => $lottery->cn_name,
                    'open_time' => explode(",", $lottery->open_time),
                ];
            }

            self::_saveCacheData('lottery_loop_config', $data);
            return $data;
        }
    }


    /**
     * 获取 玩法配置
     * @param $methodId
     * @return array
     */
    public function getMethodConfig($methodId)
    {
        $methods = $this->method_config;
        if ($methods && isset($methods[$methodId])) {
            return $methods[$methodId];
        }

        $method = LotteryMethod::where('lottery_sign', $this->en_name)->where('method_sign', $methodId)->where('status', 1)->first();

        return $method ? $method->toArray() : [];
    }

    /**
     * @param $methodId
     * @return array|\Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMethodObject($methodId)
    {
        $cacheKey = "method_object_" . $this->en_name . "_" . $methodId;
        if (ConfigureCache::get($cacheKey)) {
            return ConfigureCache::get($cacheKey);
        }

        // 获取配置
        $methodConfig = $this->getMethodConfig($methodId);
        if (!$methodConfig) {
            return [];
        }

        $methodObject = LotteryLogic::getMethodObject($methodConfig['logic_sign'], $methodConfig['method_group'], $methodId);
        if (!is_object($methodObject)) {
            Clog::gameError("获取玩法对象失败-" . $methodConfig['logic_sign'] . "-" . $methodId . '-' . $methodObject);
            return [];
        }

        ConfigureCache::forever($cacheKey, $methodObject);

        return $methodObject;

    }

    /**
     * 只用于前端展示
     * @return array
     * @throws
     */
    static function getAllLotteryToFrontEnd()
    {
        if (self::_hasCache("lottery_for_frontend")) {
            return self::_getCacheData("lottery_for_frontend");
        }

        $lotteries = self::where('status', 1)->get();

        $cacheData = [];
        foreach ($lotteries as $lottery) {
            $lottery->valid_modes = $lottery->getFormatMode();

            // 获取所有玩法
            $methods = LotteryMethod::getMethodConfig($lottery->en_name);
            $officialMethods = [];
            $casinoMethods = [];

            $groupName = config("game.method.group_name");
            $rowName = config("game.method.row_name");

            $rowData = [];
            foreach ($methods as $index => $method) {
                $rowData[$method->method_group][$method->method_row][] = [
                    'method_name' => $method->method_name,
                    'method_sign' => $method->method_sign
                ];
            }

            $groupData = [];
            $hasRow = [];
            foreach ($methods as $index => $method) {
                // 行
                if (!isset($groupData[$method->method_group])) {
                    $groupData[$method->method_group] = [];
                }

                if (!isset($hasRow[$method->method_group]) || !in_array($method->method_row, $hasRow[$method->method_group])) {
                    $groupData[$method->method_group][] = [
                        'name' => $rowName[$lottery->series_id][$method->method_row],
                        'sign' => $method->method_row,
                        'methods' => $rowData[$method->method_group][$method->method_row],
                    ];
                    $hasRow[$method->method_group][] = $method->method_row;
                }
            }

            // 组
            $defaultGroup = "";
            $defaultMethod = "";
            $hasGroup = [];
            foreach ($methods as $index => $method) {
                if ($index == 0) {
                    $defaultGroup = $method->method_group;
                    $defaultMethod = $method->method_sign;
                }

                // 组
                if (!in_array($method->method_group, $hasGroup)) {
                    if ($method->method_type == 'casino') {
                        $casinoMethods[] = [
                            'name' => $groupName[$lottery->series_id][$method->method_group],
                            'sign' => $method->method_group,
                            'rows' => $groupData[$method->method_group]
                        ];
                    } else {
                        $officialMethods[] = [
                            'name' => $groupName[$lottery->series_id][$method->method_group],
                            'sign' => $method->method_group,
                            'rows' => $groupData[$method->method_group]
                        ];
                    }

                    $hasGroup[] = $method->method_group;
                }

            }

            $cacheData[$lottery->en_name] = [
                'lottery' => $lottery,
                'methodConfig' => $officialMethods,
                'methodConfig_casino' => $casinoMethods,
                'defaultGroup' => $defaultGroup,
                'defaultMethod' => $defaultMethod,
            ];
        }

        self::_saveCacheData('lottery_for_frontend', $cacheData);

        return $cacheData;
    }

    public function genIssue($startDay, $endDay, $openTime = null)
    {
        return IssueLogic::genIssue($this, $startDay, $endDay, $openTime);
    }

    // 获取随机号码
    public function getRandCode($partnerLottery, $issue)
    {
        if (LotteryLogic::isJackpotLottery($partnerLottery))  {
            $code = JackpotLogic::getCodeFrom2001($partnerLottery, $issue);
            if (!$code) {
                return LotteryLogic::getRandCode($this->series_id);
            }

            return $code;

        } else {
            return LotteryLogic::getRandCode($this->series_id);
        }
    }

    /**
     * 检查录入的号码
     * @param $codeArr
     * @return bool
     */
    public function checkCodeFormat($codeArr)
    {
        $series = $this->series_id;
        // 数字彩票
        if (in_array($series, ['ssc', 'sd', 'p3p5', 'ssl', "pcdd"])) {
            $_code = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            foreach ($codeArr as $c) {
                if (!in_array($c, $_code)) {
                    return false;
                }
            }

            if (in_array($series, ['ssc', 'p3p5']) && count($codeArr) != 5) {
                return false;
            }

            if (in_array($series, ['sd', 'ssl', 'pcdd']) && count($codeArr) != 3) {
                return false;
            }

            return true;
        }

        // 乐透彩票
        if (in_array($series, ['lotto',])) {
            $_code = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11'];
            foreach ($codeArr as $c) {
                if (!in_array($c, $_code)) {
                    return false;
                }
            }

            if (count($codeArr) != 5) {
                return false;
            }

            return true;
        }

        // 快乐彩
        if (in_array($series, ['klsf',])) {
            $_code = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'];
            foreach ($codeArr as $c) {
                if (!in_array($c, $_code)) {
                    return false;
                }
            }

            if (count($codeArr) != 8) {
                return false;
            }

            return true;
        }

        // pk10
        if ($series == 'pk10') {
            $_code = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
            foreach ($codeArr as $c) {
                if (!in_array($c, $_code)) {
                    return false;
                }
            }

            if (count($codeArr) != 10) {
                return false;
            }

            return true;
        }

        // 快三
        if (in_array($series, ['k3'])) {
            $_code = [1, 2, 3, 4, 5, 6];
            foreach ($codeArr as $c) {
                if (!in_array($c, $_code)) {
                    return false;
                }
            }

            if (count($codeArr) != 3) {
                return false;
            }

            return true;
        }

        // 六合彩
        if (in_array($series, ['lhc'])) {
            $_code = [
                "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20",
                "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40",
                "41", "42", "43", "44", "45", "46", "47", "48", "49",
            ];

            foreach ($codeArr as $c) {
                if (!in_array($c, $_code)) {
                    return false;
                }
            }

            if (count($codeArr) != 7) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function formatOpenCode($openCode)
    {
        $positions = explode(",", $this->positions);
        $codes = explode(',', $openCode);
        return array_combine($positions, $codes);
    }

    /**
     * 号码转换
     * @param $code
     * @return string
     */
    public function codeTransferOnEncode($code)
    {
        switch ($this->series_id) {
            case 'ssc':
            case 'ssl':
            case 'k3':
            case 'p3p5':
            case 'sd':
            case 'pcdd':
                $codeArr = str_split($code, 1);
                return implode(',', $codeArr);
                break;
            case 'lotto':
                $codeArr = explode(" ", $code);
                return implode(',', $codeArr);
                break;

            case 'pk10':
                $codeArr = explode(",", $code);
                $str = "";
                foreach ($codeArr as $_code) {
                    if ($_code == 10) {
                        $str .= "10,";
                    } else {
                        $str .= "0" . $_code . ",";
                    }
                }
                return trim($str, ",");
                break;
            case "klsf":
                return $code;
                break;
        }

        return "";
    }

    static function getDefaultOpenCode($seriesId)
    {
        switch ($seriesId) {

            case 'ssl':
            case 'k3':
            case 'pcdd':
            case 'sd':
                return "-,-,-";
                break;
            case 'ssc':
            case 'p3p5':
            case 'lotto':
                return "-,-,-,-,-";
                break;

            case 'pk10':
                return "-,-,-,-,-,-,-,-,-,-";
                break;
            case 'lhc':
                return "-,-,-,-,-,-,-";
                break;
        }

        return "-,-,-,-,-";
    }

    /**
     * 判断彩种是否休市
     * @param $lotterySign
     * @return int
     */
    static function  isClosedMarket($lotterySign) {
        $currentTime = time();

        $config = config('game.closed_vacation');
        if(isset($config[$lotterySign])) {
            $closeStart = strtotime($config[$lotterySign]['start']);
            $closeEnd   = strtotime($config[$lotterySign]['end']);
            if($currentTime >= $closeStart && $currentTime <= $closeEnd) {
                $isCloseMarket = 1;
            } else {
                $isCloseMarket = 0;
            }
        } else {
            $isCloseMarket = 0;
        }

        return $isCloseMarket;
    }
}
