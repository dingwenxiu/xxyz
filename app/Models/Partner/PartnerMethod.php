<?php

namespace App\Models\Partner;

use App\Lib\Logic\Cache\LotteryCache;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *  两个缓存
 *  单个玩法缓存 "lmc_" partner_sign lottery_sign method_sign
 *  单个彩种下的玩法的缓存 "method_config_"
 * Class PartnerMethod
 * @package App\Models\Partner
 */
class PartnerMethod extends Model
{
    protected $table = 'partner_methods';

    /**
     * 初始化玩法
     * @param $partnerSign
     */
    static function initPartnerMethod($partnerSign) {
        $ids        = Lottery::where('auto_open',0)->pluck('en_name')->toArray();
        $methodList = LotteryMethod::where("status", 1)->whereIn('lottery_sign',$ids)->get();

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

        self::insert($data);
    }

    /**
     * 获取列表
     * @param $c
     * @return mixed
     */
    static function getList($c) {
        $query = DB::table('partner_methods')->select('lottery_methods.id','lottery_methods.series_id','lottery_methods.logic_sign','lottery_methods.lottery_sign','lottery_methods.method_sign','lottery_methods.method_type','lottery_methods.method_name','lottery_methods.method_group','lottery_methods.method_row','lottery_methods.group_sort','lottery_methods.row_sort','lottery_methods.method_sort','lottery_methods.max_prize_per_code','lottery_methods.max_prize_per_issue','lottery_methods.win_mode','lottery_methods.challenge_type','lottery_methods.challenge_min_count','lottery_methods.challenge_config','lottery_methods.challenge_bonus','lottery_methods.show','lottery_methods.status','lottery_methods.created_at','lottery_methods.updated_at','partner_methods.status as partner_methods_status','partner_methods.lottery_name', 'partner_methods.sort as tab_sort','partner_methods.id as partner_methods_id')
            ->leftJoin('lottery_methods', "partner_methods.config_id", '=', "lottery_methods.id");

        // 平台
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_methods.partner_sign', $c['partner_sign']);
        }

        // 系列
        if (isset($c['series_id']) && $c['series_id'] && $c['series_id'] != "all") {
            $query->where('lottery_methods.series_id', $c['series_id']);
        }

        // 类型
        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('partner_methods.lottery_sign', $c['lottery_sign']);
        }

        // 类型
        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != "all") {
            $query->where('partner_methods.method_sign', $c['method_sign']);
        }

        // 多个玩法类型
        if (isset($c['logic_sign']) && $c['logic_sign'] && $c['logic_sign'] != "all") {
            $query->where('lottery_methods.logic_sign', $c['logic_sign']);
        }

        // 分组
        if (isset($c['method_group']) && $c['method_group']) {
            $query->where('lottery_methods.method_group', $c['method_group']);
        }
        // 状态
        if (isset($c['status']) && $c['status'] != "all") {
            $query->where('partner_methods.status', $c['status']);
        }

        //id
        if (isset($c['config_id']) && $c['config_id']) {
            $query->where('partner_methods.id', $c['config_id']);
        }

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;
        $total  = $query->count();
        $items  = $query->skip($offset)->take($pageSize)->orderBy('id')->get();
        return ['data' => $items, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * 获取 商户 指定彩种 所有玩法配置
     * @param $partnerSign
     * @param $lotterySign
     * @return mixed
     */
    static function  getLotteryMethodConfigFromDb($partnerSign, $lotterySign) {
        $query = self::select(
            'lottery_methods.method_name',
            'lottery_methods.method_sign',
            'lottery_methods.method_type',
            'lottery_methods.method_group',
            'lottery_methods.method_row',

            'lottery_methods.group_sort',
            'lottery_methods.row_sort',
            'lottery_methods.method_sort',

            'lottery_methods.win_mode',
            'lottery_methods.show',

            'lottery_methods.logic_sign',
            'lottery_methods.series_id',
            'lottery_methods.lottery_sign',
            'lottery_methods.lottery_name',

            'partner_methods.sort',

            'partner_methods.max_prize_per_code',
            'partner_methods.max_prize_per_issue',

            'partner_methods.challenge_type',
            'partner_methods.challenge_min_count',
            'partner_methods.challenge_config',
            'partner_methods.challenge_bonus',
            'partner_methods.status'
        )
            ->leftJoin('lottery_methods', "partner_methods.config_id", '=', "lottery_methods.id")
            ->where("partner_methods.partner_sign", $partnerSign)
            ->where("partner_methods.lottery_sign", $lotterySign)
            ->where("partner_methods.status", 1);

        $res =  $query->get();

        $data = [];
        foreach ($res as $item) {
            $data[] = $item->objectToArray();
        }
        return $data;
    }

    /**
     * 获取 商户 指定彩种 指定玩法 配置
     * @param $partnerSign
     * @param $lotterySign
     * @param $methodSign
     * @return mixed
     */
    static function  getLotteryOneMethodConfigFromDb($partnerSign, $lotterySign, $methodSign) {

        if ($partnerSign == 'system') {
            $query = LotteryMethod::select(
                'lottery_methods.method_name',
                'lottery_methods.method_sign',
                'lottery_methods.method_type',
                'lottery_methods.method_group',
                'lottery_methods.method_row',

                'lottery_methods.group_sort',
                'lottery_methods.row_sort',
                'lottery_methods.method_sort',

                'lottery_methods.win_mode',
                'lottery_methods.show',

                'lottery_methods.logic_sign',
                'lottery_methods.series_id',
                'lottery_methods.lottery_sign',
                'lottery_methods.lottery_name',

                'lottery_methods.method_sort as sort',

                'lottery_methods.max_prize_per_code',
                'lottery_methods.max_prize_per_issue',

                'lottery_methods.challenge_type',
                'lottery_methods.challenge_min_count',
                'lottery_methods.challenge_config',
                'lottery_methods.challenge_bonus',
                'lottery_methods.status'
            )
                ->where("lottery_methods.lottery_sign", $lotterySign)
                ->where("lottery_methods.method_sign", $methodSign)
                ->where("lottery_methods.status", 1);

            $item =  $query->first();
        } else {
            $query = self::select(
                'lottery_methods.method_name',
                'lottery_methods.method_sign',
                'lottery_methods.method_type',
                'lottery_methods.method_group',
                'lottery_methods.method_row',

                'lottery_methods.group_sort',
                'lottery_methods.row_sort',
                'lottery_methods.method_sort',

                'lottery_methods.win_mode',
                'lottery_methods.show',

                'lottery_methods.logic_sign',
                'lottery_methods.series_id',
                'lottery_methods.lottery_sign',
                'lottery_methods.lottery_name',

                'partner_methods.sort',

                'partner_methods.max_prize_per_code',
                'partner_methods.max_prize_per_issue',

                'partner_methods.challenge_type',
                'partner_methods.challenge_min_count',
                'partner_methods.challenge_config',
                'partner_methods.challenge_bonus',
                'partner_methods.status'
            )
                ->leftJoin('lottery_methods', "partner_methods.config_id", '=', "lottery_methods.id")
                ->where("partner_methods.partner_sign", $partnerSign)
                ->where("partner_methods.lottery_sign", $lotterySign)
                ->where("partner_methods.method_sign", $methodSign)
                ->where("partner_methods.status", 1);

            $item =  $query->first();
        }

        // 如果被禁止　返回空n
        if (!$item) {
            return [];
        }

        $item->challenge_config = $item->challenge_config ? unserialize($item->challenge_config) : [];

        return $item->toArray();
    }

    /**
     * 获取单个玩法配置
     * @param $partnerSign
     * @param $methodSign
     * @return mixed
     */
    static function findBySign($partnerSign, $methodSign) {
        $query = self::select(
            DB::raw('lottery_methods.*'),
            DB::raw('partner_methods.sort as real_sort')
        )->leftJoin('lottery_methods', 'lottery_methods.method_sign', '=', 'partner_methods.method_sign')->orderBy('partner_methods.sort', 'desc');

        $query->where('partner_methods.partner_sign', $partnerSign);
        $query->where('partner_methods.method_sign', $methodSign);
        return $query->first();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function modifyStatus() {
        $this->status = $this->status ? 0 : 1;
        $this->save();

        // 刷新单个玩法配置
        LotteryCache::flushPartnerLotteryAllMethodConfig($this->lottery_sign, $this->partner_sign);
        return true;
    }

    /**
     * 本期开奖时间
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentIssue()
    {
        return $this->hasOne(LotteryIssue::class, 'lottery_sign', 'lottery_sign')
            ->where('end_time', '>', time())
            ->orderBy('begin_time', 'ASC');
    }

    public function objectToArray() {
        $data = [
            'method_name'       => $this->method_name,
            'method_sign'       => $this->method_sign,
            'method_type'       => $this->method_type,
            'method_group'      => $this->method_group,
            'method_row'        => $this->method_row,

            'group_sort'        => $this->group_sort,
            'row_sort'          => $this->row_sort,
            'method_sort'       => $this->method_sort,

            'win_mode'          => $this->win_mode,
            'show'              => $this->show,

            'logic_sign'        => $this->logic_sign,
            'series_id'         => $this->series_id,
            'lottery_sign'      => $this->lottery_sign,
            'lottery_name'      => $this->lottery_name,

            'sort'              => $this->sort,

            'max_prize_per_code'    => $this->max_prize_per_code,
            'max_prize_per_issue'   => $this->max_prize_per_issue,

            'challenge_type'        => $this->challenge_type,
            'challenge_min_count'   => $this->challenge_min_count,
            'challenge_config'      => $this->challenge_config ? unserialize($this->challenge_config) : [],
            'challenge_bonus'       => $this->challenge_bonus,
            'status'                => $this->status
        ];

        return $data;
    }
}
