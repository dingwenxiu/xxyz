<?php
namespace App\Models\Report;

use App\Jobs\Stat\PartnerLotteryStatNotify;
use App\Lib\Clog;
use App\Models\Base;
use App\Models\Game\Lottery;
use App\Models\Partner\Partner;
use Carbon\Carbon;

class ReportStatLotteryDay extends Base {
    protected $table = 'report_stat_lottery_day';

    /**
     * 获取列表
     * @param $c
     * @return array
     */
    static function getList($c) {
        $yestoday = Carbon::yesterday()->format("Ymd");
        $today    = Carbon::today()->format('Ymd');
        $query = self::orderBy('bets', 'desc');

        // 商户
        if(isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', $c['partner_sign']);
        }

        // 彩种
        if(isset($c['lottery_sign']) && $c['lottery_sign']) {
            $query->where('lottery_sign', $c['lottery_sign']);
        }

        // 日期 开始
        if(isset($c['start_day']) && $c['start_day']) {
            $query->where('day', ">=", $c['start_day']);
        } else {
            $query->where('day', ">=", $yestoday);
        }

        // 日期 结束
        if(isset($c['end_day']) && $c['end_day']) {
            $query->where('day', "<=", $c['end_day']);
        } else {
            $query->where('day', "<=", $today);
        }

        // 时间范围
        if (isset($c['start_day'], $c['end_day']) && $c['end_day'] && $c['end_day']) {
            $query->whereBetween('day', [$c['start_day'], $c['end_day']]);
        } else {
            $query->whereBetween('day', [$yestoday, $today]);
        }

        

        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : 15;
        $offset         = ($currentPage - 1) * $pageSize;

        $total       = $query->count();
        $statDatas   = $query->get();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total,'statDatas'=>$statDatas, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }

    /**
     * @param $startTime
     * @param int $day
     * @return mixed
     */
    static function initStat($startTime, $day = 3) {

        $endTime    = time() + 86400 * $day;
        $daySet     = getDaySet($startTime, $endTime);

        $res        = Partner::all();

        foreach ($res as $partner) {
            $allLottery = Lottery::where("status", 1)->get();
            foreach ($allLottery as $lottery) {
                $data   = [];
                foreach ($daySet as $day) {
                    $check = self::where("partner_sign", $partner->sign)->where("lottery_sign", $lottery->en_name)->where("day", $day)->first();
                    if ($check) {
                        continue;
                    }
                    $data[] = [
                        'partner_sign'      => $partner->sign,
                        'lottery_sign'      => $lottery->en_name,
                        'lottery_name'      => $lottery->cn_name,
                        'day'               => $day,
                    ];
                }

                self::insert($data);
            }
        }

        return true;
    }

    // 发送报表
    static function sendReport($day, $partnerSign) {
        jtq(new PartnerLotteryStatNotify($day, $partnerSign), "notify");
        return true;
    }

    /**
     * 加商户 = 生成
     * @param $partner
     * @param int $day
     * @return mixed
     */
    static function initPartnerStat($partner, $day = 3) {

        $endTime    = time() + 86400 * $day;
        $daySet     = getDaySet(time(), $endTime);

        $allLottery = Lottery::all();
        foreach ($allLottery as $lottery) {
            $data   = [];
            foreach ($daySet as $day) {
                $check = self::where("partner_sign", $partner->sign)->where("lottery_sign", $lottery->en_name)->where("day", $day)->first();

                if ($check) {
                    continue;
                }

                $data[] = [
                    'partner_sign'      => $partner->sign,
                    'lottery_sign'      => $lottery->en_name,
                    'lottery_name'      => $lottery->cn_name,
                    'day'               => $day,
                ];
            }

            self::insert($data);
        }


        return true;
    }

    /**
     * 加彩种 = 生成
     * @param $lottery
     * @param int $day
     * @return mixed
     */
    static function initLotteryStat($lottery, $day = 3) {

        $endTime    = time() + 86400 * $day;
        $daySet     = getDaySet(time(), $endTime);

        $partners = Partner::all();
        foreach ($partners as $partner) {
            $data   = [];
            foreach ($daySet as $day) {
                $check = self::where("partner_sign", $partner->sign)->where("lottery_sign", $lottery->en_name)->where("day", $day)->first();

                if ($check) {
                    continue;
                }

                $data[] = [
                    'partner_sign'      => $partner->sign,
                    'lottery_sign'      => $lottery->en_name,
                    'lottery_name'      => $lottery->cn_name,
                    'day'               => $day,
                ];
            }

            self::insert($data);
        }


        return true;
    }

    /** @var array ================================= 数据变更 ================================== */

    public static $filters = [
        'bets',
        'commission',
        'bonus',
        'cancel'
    ];

    // 统计改变
    public function change($lotteryId,  $changes, $date)
    {
        $changes = array_intersect_key($changes, array_flip(self::$filters));
        if(empty($changes)) {
            Clog::statLottery("stat-lottery-empty-{$date}", $lotteryId, $changes);
            return true;
        }

        $selfUpdate = '';
        $selfAdd    = '';

        foreach($changes as $field => $v) {
            $selfUpdate .= $selfAdd . "`{$field}` = `{$field}` + {$v}";
            $selfAdd = ',';
        }

        $date_day = date("Ymd", strtotime($date));

        // 更新自身量
        if($selfUpdate) {
            $ret = db()->update("update `report_stat_lottery_day` set {$selfUpdate} where  `lottery_sign` = '{$lotteryId}'  and `day`='{$date_day}'");
            if(!$ret) {
                Clog::statLottery("stat-lottery-error-self-update-{$date}", $lotteryId, ['sql' => $selfUpdate, 'res' => $ret]);
                return true;
            }
        }

        return true;
    }
}
