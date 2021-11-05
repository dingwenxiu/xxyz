<?php namespace App\Http\Controllers\Api;

use App\Lib\Help;
use App\Lib\Logic\Cache\ApiCache;
use App\Lib\Logic\Cache\IssueCache;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Lottery\BetLogic;
use App\Lib\Logic\Lottery\ProjectLogic;
use App\Lib\Logic\Lottery\TraceLogic;
use App\Lib\Logic\LotteryTrend;
use App\Models\Casino\CasinoPlayerBet;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryIssue;
use App\Models\Game\LotteryProject;
use App\Models\Game\LotteryTrace;
use App\Models\Game\LotteryTraceList;
use App\Models\Partner\PartnerLottery;
use App\Models\Player\Player;
use Illuminate\Http\JsonResponse;

class ApiGameController extends ApiBaseController
{

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function openList() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $data = IssueCache::getOpenList($user->partner_sign);

        return Help::returnApiJson('获取游戏列表成功!', 1, $data);
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function lotteryList() {
        $lotteries      = LotteryCache::getPartnerAllLottery($this->partner->sign);
        $seriesConfig   = config("game.main.series");

        $data = [];
        foreach ($lotteries as $lottery) {
            $imgs = substr($lottery['icon_path'], 0,7) == 'lottery' ? strtolower($this->partner->sign).'/'.$lottery['icon_path'] : $lottery['icon_path'];
            $aDimgs = substr($lottery['ad_img'], 0,7) == 'lottery' ? strtolower($this->partner->sign).'/'.$lottery['ad_img'] : $lottery['ad_img'];
            if (!isset($data[$lottery->series_id])) {
                $data[$lottery->series_id] = [
                    'name'  => $seriesConfig[$lottery->series_id],
                    'sign'  => $lottery->series_id,
                    'list'  => []
                ];
            }

            $data[$lottery->series_id]['list'][] = [
                'id'                => $lottery->lottery_sign,
				'is_hot'			=> $lottery->is_hot,
                'icon_path'         => $imgs,
                'ad_img'            => $aDimgs,
                'number_id'         => $lottery->id,
                'series_id'         => $lottery->series_id,
                'open_casino'       => $lottery->open_casino,
                'name'              => $lottery->lottery_name,
                'valid_modes'       => $lottery->valid_modes,
                'min_prize_group'   => $lottery->min_prize_group,
                'max_prize_group'   => $lottery->max_prize_group,
                'max_trace_number'  => $lottery->max_trace_number,
                'day_issue'         => $lottery->day_issue,
                'desc'              => $lottery->issue_desc,
                'status'            => $lottery->status,
                'closed_vacation'   => Lottery::isClosedMarket($lottery->lottery_sign)
            ];
           

        }


        $params = [
            'data'      => $data,
            'version'   => configure("data_version", '1.0.0'),
        ];
        return Help::returnApiJson('获取游戏列表成功!', 1, $params);
    }

    // 获取彩种信息
    public function lotteryInfo() {

        $data = PartnerLottery::getAllLotteryToFrontEnd($this->partner->sign);

        return Help::returnApiJson('获取玩法列表成功!', 1, $data );
    }

    /**
     * 奖期信息
     * @return JsonResponse
     * @throws \Exception
     */
    public function issueInfo() {
        $lotterySign    = request('lottery_sign', '');
        $lottery        = Lottery::findBySign($lotterySign);

        if (!$lottery) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        $currentIssue   = IssueCache::getCurrentIssue($lotterySign);


        // 上一期
        $preIssue = IssueCache::getLastIssue($lotterySign);
        $lastIssue  = [
            'issue_no'      => $preIssue ? $preIssue->issue : "",
            'begin_time'    => $preIssue ? $preIssue->begin_time : "",
            'end_time'      => $preIssue ? $preIssue->end_time : "",
            'open_time'     => $preIssue ? $preIssue->allow_encode_time : "",
            'code'          => $preIssue && $preIssue->official_code ? $preIssue->official_code : Lottery::getDefaultOpenCode($lottery->series_id)
        ];

        // 获取下面N期
        $canUserInfo        = IssueCache::getNextMultipleIssue($lotterySign, $lottery->max_trace_number);
        $canBetIssueData    = [];

        foreach ($canUserInfo as $index => $issue) {
            $canBetIssueData[] = [
                'issue_no'      => $issue->issue,
                'begin_time'    => $issue->begin_time,
                'end_time'      => $issue->end_time,
                'open_time'     => $issue->allow_encode_time
            ];
        }

        $currentIssueData = [];
        if ($currentIssue) {
            $currentIssueData = [
                'issue_no'      => $currentIssue->issue,
                'begin_time'    => $currentIssue->begin_time,
                'end_time'      => $currentIssue->end_time,
                'open_time'     => $currentIssue->allow_encode_time,
            ];
        }

        return Help::returnApiJson('获取奖期成功!', 1,
            [
                'lottery'           => $lotterySign,
                'currentIssue'      => $currentIssueData,
                'lastIssue'         => $lastIssue,
                'issueInfo'         => $canBetIssueData,
                'serverTime'        => time()
            ] 
        );
    }

    // 追号可用奖期
    public function traceIssueList() {
        $lotterySign    = request('lottery_sign', '');
        $lottery        = Lottery::findBySign($lotterySign);

        if (!$lottery) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        // 获取下面N期
        $canUserInfo    = IssueCache::getNextMultipleIssue($lotterySign, $lottery->max_trace_number);
        $data           = [];

        foreach ($canUserInfo as $index => $issue) {
            $data[] = [
                'issue_no'      => $issue->issue,
                'begin_time'    => $issue->begin_time,
                'end_time'      => $issue->end_time,
                'open_time'     => $issue->allow_encode_time
            ];
        }

        return Help::returnApiJson('获取奖期成功!', 1, $data);
    }

    // 上期开奖
    public function lastIssue($lotterySign) {
        $lottery        = Lottery::findBySign($lotterySign);

        if (!$lottery) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        $lotteryIssue = LotteryIssue::getLastIssue($lotterySign);
        $data = [
            'lottery_sign'  => $lotteryIssue->lottery_sign ?? null,
            'lottery_name'  => $lotteryIssue->lottery_name ?? null,
            'issue'         => $lotteryIssue->issue ?? null,
            'official_code' => $lotteryIssue->official_code ?? null,
            'encode_time'   => $lotteryIssue->time_encode ?? null,
            'serverTime'    => time(),
        ];

        return Help::returnApiJson('恭喜, 获取上期开奖成功!', 1, $data);
    }

    // 历史奖期
    public function issueHistory() {

        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $lotterySign    = request('lottery_sign', '');
        $lottery        = PartnerLottery::findBySign($user->partner_sign, $lotterySign);

        if (!$lottery) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        $pageSize = request("count", 10);
        $pageSize = $pageSize > 50 ? 50 : $pageSize;
        $pageSize = $pageSize < 1 ? 1 : $pageSize;

        $c = request()->all();
        $c['page_size']         = $pageSize;
        $c['from_frontend']     = 1;

        $issueList = LotteryIssue::getHistoryIssue($lotterySign, $c);
        $data = [];
        foreach ($issueList as $issue) {
            $data[] = [
                'issue_no'  => $issue->issue,
                'code'      => $issue->official_code ? $issue->official_code : Lottery::getDefaultOpenCode($lottery->series_id),
            ];
        }

        return Help::returnApiJson('获取将期历史数据成功!', 1, $data);
    }

    /**
     * 获取玩法列表
     * @return mixed
     * @throws \Exception
     */
    public function methodList() {
        $lotteryId      = request('lottery_sign');
        $lotteries      = Lottery::getAllLotteryByCache(true);

        if (!array_key_exists($lotteryId, $lotteries)) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        $lottery = $lotteries[$lotteryId];

        $data = [];
        foreach ($lottery->methods as $method) {
            $data[] = [
                'group' => $method['method_group'],
                'id'    => $method['method_sign'],
                'name'  => $method['method_name'],
            ];
        }
        return Help::returnApiJson('获取玩法列表成功!', 1, $data);
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function bet() {
        $player   = auth()->guard('api')->user();
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 间隔不能少于1S
        $key = "bet_wait_" . $player->id;
        if (!$player->is_tester && !ApiCache::saveMemKey($key, time(), now()->addSeconds(1) )) {
            return Help::returnApiJson('对不起, 投注过于频繁!', 0);
        }

        $lotterySign    = request('lottery_sign');
        if (!$lotterySign) {
            return Help::returnApiJson('对不起, 无效的彩种标识!', 0);
        }

        $lottery        = LotteryCache::getPartnerLottery($lotterySign, $player->partner_sign);
        if (!$lottery) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        // 是否休市
        if (Lottery::isClosedMarket($lottery->lottery_sign)) {
            return Help::returnApiJson('对不起, 休市中!', 0);
        }

        if(!$lottery->status)
        {
            return Help::returnApiJson('对不起, 此彩种已关闭!', 0);
        }

        $params     = request()->all();
        $from       = isset($params['from']) ? isset($params['from']) : 1;

        $ret = BetLogic::bet($player, $lottery, $params, $from);

        if ($ret !== true) {
            $text = "\r\n" . "错误：{$ret}"  . "\r\n";
            $text .= "玩家：$player->username"  . "\r\n";
            $text .= "彩种：$lottery->lottery_name"  . "\r\n";
            $text .= "时间：" . date("Y-m-d H:i:s") . "\r\n";
            $text .= "参数：" . json_encode($params)  . "\r\n";

            $ip = real_ip();
            if ($ip) {
                $text .= "IP：" . $ip . "(" . getIpAddress($ip) . ') </b>'.chr(10);
            }

            telegramSend("send_exception", $text);
            return Help::returnApiJson($ret, 0);
        } else {
            return Help::returnApiJson("恭喜, 投注成功!", 1);
        }
    }

    /**
     * 撤单
     * @return JsonResponse
     * @throws \Exception
     */
    public function cancelProject() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0);
        }

        // 订单
        $projectId      = request('project_id');
        if (!$projectId) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        $ret        = hashId()->decode($projectId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        $project    = LotteryProject::find($ret[0]);
        if (!$project || !$project->id) {
            return Help::returnApiJson('对不起, 无效的订单!', 0);
        }

        // 是否有权限
        if ($project->user_id != $user->id) {
            return Help::returnApiJson('对不起, 您没有权限撤单!', 0);
        }

        $res = ProjectLogic::cancel($project);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        } else {
            return Help::returnApiJson("恭喜, 撤单成功!", 1);
        }
    }

    /**
     * 撤追号详情
     * @return JsonResponse
     * @throws \Exception
     */
    public function cancelTraceDetail() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 订单ID 数组
        $traceId      = request('trace_detail_id', "");
        if (!$traceId) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        $ret        = hashId()->decode($traceId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        $traceIds = [$ret[0]];

        // 追号订单详情
        $traceDetailData    = LotteryTraceList::whereIn('id', $traceIds)->get();
        if (!$traceDetailData || count($traceIds) != count($traceDetailData)) {
            return Help::returnApiJson("对不起, 包含无效的追号订单ID!", 0);
        }

        // 检测权限
        $traceMainId = $traceDetailData[0]->trace_id;
        foreach ($traceDetailData as $detail) {
            // 是否属于自己
            if ($detail->user_id != $user->id) {
                return Help::returnApiJson("对不起, 您没有权限!", 0);
            }

            // 不是同一个
            if ($detail->trace_id != $traceMainId) {
                return Help::returnApiJson("对不起, 不符合的撤单ID!", 0);
            }

            // 订单状态
            if ($detail->status != LotteryTraceList::STATUS_TRACE_INIT) {
                return Help::returnApiJson("对不起, 追号订单状态不正确!", 0);
            }
        }

        // 追号main
        $traceMain = LotteryTrace::find($traceDetailData[0]->trace_id);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        // 追好状态
        if ($traceMain->status != LotteryTrace::STATUS_INIT) {
            return Help::returnApiJson("对不起, 追号状态不正确(0x002)!", 0);
        }

        $res = TraceLogic::traceDetailCancel($traceDetailData, $traceMain);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        } else {
            return Help::returnApiJson("恭喜, 撤销追号成功!", 1);
        }
    }

    /**
     * 撤总追号
     * @return JsonResponse
     * @throws \Exception
     */
    public function cancelTrace() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 订单ID 数组
        $traceId      = request('trace_id');
        if (!$traceId ) {
            return Help::returnApiJson('对不起, 无效的追号订单Id!', 0);
        }

        $ret        = hashId()->decode($traceId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        // 追号main
        $traceMain = LotteryTrace::find($ret[0]);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        // 是否属于自己
        if ($traceMain->user_id != $user->id) {
            return Help::returnApiJson("对不起, 您没有权限!", 0);
        }

        // 追好状态
        if ($traceMain->status != LotteryTrace::STATUS_INIT) {
            return Help::returnApiJson("对不起, 追号状态不正确!", 0);
        }

        $res = TraceLogic::traceMainCancel($traceMain);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        } else {
            return Help::returnApiJson("恭喜, 撤销追号成功!", 1);
        }
    }

    /**
     * 追号详情
     * 获取追号详情
     * @param $traceId
     * @return JsonResponse
     */
    public function traceDetail($traceId) {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        if (!$traceId ) {
            return Help::returnApiJson('对不起, 无效的追号订单Id!', 0);
        }

        $ret        = hashId()->decode($traceId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        // 追号main
        $traceMain = LotteryTrace::find($ret[0]);
        if (!$traceMain) {
            return Help::returnApiJson("对不起, 不存在的追号!", 0);
        }

        // 是否属于自己
        if ($traceMain->user_id != $user->id) {
            return Help::returnApiJson("对不起, 您没有权限!", 0);
        }

        $traceDetailData = LotteryTraceList::where('trace_id', $traceMain->id)->get();

        $data = [];
        foreach ($traceDetailData as $item) {
            $tmp = [];
            $tmp['id']                 = hashId()->encode($item->id);
            $tmp['lottery_name']       = $item->lottery_name;
            $tmp['issue']              = $item->issue;
            $tmp['bet_number_view']    = $item->bet_number_view;
            $tmp['times']              = $item->times;
            $tmp['total_price']        = number4($item->total_cost);
            $tmp['is_challenge']       = $item->is_challenge;
            $tmp['status']             = $item->status;
            $tmp['bonus']              = number4($item->bonus);

            $data[] = $tmp;
        }

        return Help::returnApiJson("恭喜, 获取详情成功!", 1, $data);

    }

    /**
     * 奖期历史
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function issueList() {
        $params         = request()->all();
        $lotterySign    = $params["lottery_sign"];
        if (!$lotterySign) {
            return Help::returnApiJson('对不起, 无效的彩种ID!', 0);
        }

        $lotteries      = Lottery::getAllLotteries(true);

        if (!array_key_exists($lotterySign, $lotteries)) {
            return Help::returnApiJson('对不起, 无效的彩种!', 0);
        }

        $issueList = LotteryIssue::getCanBetIssue($lotterySign);

        $issueData = [];
        foreach ($issueList as $issue) {
            $issueData[] = [
                'lottery_sign'            => $issue->lottery_sign,
                'issue'                 => $issue->issue,
                'start_time'            => date("Y-m-d H:i:s", $issue->begin_time),
                'end_time'              => date("Y-m-d H:i:s", $issue->end_time),
                'official_open_time'    => date("Y-m-d H:i:s", $issue->official_open_time),
            ];
        }

        return Help::returnApiJson('获取游戏奖期成功!', 1, $issueData);
    }
    /**
     * 获取订单记录
     * @return JsonResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function projectHistory() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c              = request()->all();
        $lotterySign    = request('lottery_sign','');
        $userId         = $user->id;
        $user->id       = request('user_id') ?? $user->id;

        // 直属下级玩家列表
        $child = Player::where('parent_id', $userId)->get();
        $childs = [];
        foreach ($child as $index => $item) {
            $childExt = [
                'id'       => $item->id,
                'username' => $item->username,
            ];
            $childs[] = $childExt;
        }

        $userName = request('username');
        if (isset($userName) && $userName != $user->username) {
            $child = Player::where('username',  $userName)->first();
            if (!$child) {
                return Help::returnApiJson('对不起, 无效下级信息!', 0);
            }
            // 是否是下级
            if(!$user->hasChild($child->id)) {
                return Help::returnApiJson('对不起, 无效目标用户!', 0);
            }
            $c['username'] = $userName;
            $c['user_id']  = '';
            $data = LotteryProject::getProjectListForFrontend($child->id, $lotterySign, 0, 10, $c);
        } else if ($userName == $user->username) {
			$c['username'] = $userName;
			$c['user_id']  = '';
            $data = LotteryProject::getProjectListForFrontend($user->id, $lotterySign, 0, 10, $c);
        } else {
			$data = LotteryProject::getProjectListForFrontend($user->id, $lotterySign, 0, 10, $c);
		}


        $modeArr   = config("game.main.modes");
        $dataSelf = [];
        foreach ($data["data"] as $item) {
        	$lottery = PartnerLottery::where('partner_sign', $user->partner_sign)->where('lottery_sign', $item->lottery_sign)->first();
			$imgs = substr($lottery['icon_path'], 0,7) == 'lottery' ? strtolower($user->partner_sign).'/'.$lottery['icon_path'] : $lottery['icon_path'];

            $dataSelf[] = [
                'username'        => $item->username,
                'lottery_name'    => $item->lottery_name,
				'lottery_sign'	  => $item->lottery_sign,
				'icon_path'		  => $imgs,
                'hash_id'         => hashId()->encode($item->id),
                'issue'           => $item->issue,
                'method_name'     => $item->method_name,
                'bet_number'      => $item->bet_number,
                'open_number'     => $item->open_number,
				'count'		      => $item->count,
                'bet_number_view' => $item->bet_number_view,
                'total_cost'      => number4($item->total_cost),
                'bonus'           => number4($item->bonus),
                'price'           => $item->price,
                'times'           => $item->times,
                'bet_prize_group' => $item->bet_prize_group,
                'mode'            => $modeArr[$item->mode]['title'],
                'is_win'          => $item->is_win,
                'is_challenge'    => $item->is_challenge,
                'time_bought'     => date("m-d H:i:s", $item->time_bought),
                'time_open'       => date("Y-m-d H:i:s", $item->time_open),
                'time_send'       => date("Y-m-d H:i:s", $item->time_send),
                'time_commission' => date("Y-m-d H:i:s", $item->time_commission),
                'status'          => $item->getStatus(),
                'can_cancel'      => $item->getStatus(),
                'closed_vacation' => Lottery::isClosedMarket($item->lottery_sign)
            ];
        }
        $data["data"] = $dataSelf;

        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data, ['self' =>$userId, 'child' =>$childs]);
    }

    /**
     * 获取娱乐城记录
     * @return JsonResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function casinoProjectHistory()
    {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c              = request()->all();
        $userId         = $user->id;
        $user->id       = request('user_id') ?? $user->id;
        // 直属下级玩家列表
        $child = Player::where('parent_id', $userId)->get();
        $childs = [];
        foreach ($child as $index => $item) {
            $childExt = [
                'id'       => $item->id,
                'username' => $item->username,
            ];
            $childs[] = $childExt;
        }

        $userName = request('username');
        if ($userName) {
            $child = Player::where('username',  $userName)->first();
            if (!$child) {
                return Help::returnApiJson('对不起, 无效下级信息!', 0);
            }
            // 是否是下级
            if(!$user->hasChild($child->id)) {
                return Help::returnApiJson('对不起, 无效目标用户!', 0);
            }
            $c['username'] = $userName;
            $c['user_id']  = '';
            $data = CasinoPlayerBet::getcasinoPlayerBet($user->id, $c);;
        } else {
            $data = CasinoPlayerBet::getcasinoPlayerBet($user->id, $c);
        }

        $modeArr   = config("game.main.modes");
        $dataSelf = [];
        foreach ($data["data"] as $item) {
            $dataSelf[] = [
                'c_name'      => $item->c_name,
                'username'       => $item->account_username,
                'platform_order_id'       => $item->platform_order_id,
                'bet_amount'      => $item->bet_amount,
                'company_payout_amount'   => $item->company_payout_amount,
                'company_win_amount'   => $item->company_win_amount,
                'bet_time'   => $item->bet_time,
            ];
        }
        $data["data"] = $dataSelf;

        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data, ['self' =>$userId, 'child' =>$childs]);
    }

    /**
     * 追号记录 - 个人中心
     * @return JsonResponse
     */
    public function traceHistory() {
        $user   = auth()->guard('api')->user();
        if (!$user) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c  = request()->all();
        $c['partner_sign'] = $user->partner_sign;
        $c['user_id'] = request('user_id') ?? $user->id;

        // 直属下级玩家列表
        $child = Player::where('parent_id', $user->id)->get();
        $childs = [];
        foreach ($child as $index => $item) {
            $childExt = [
                'id'       => $item->id,
                'username' => $item->username,
            ];
            $childs[] = $childExt;
        }


        $userName = request('username');
        if ($userName && $userName != $user->username) {
            $child = Player::where('username',  $userName)->first();
            if (!$child) {
                return Help::returnApiJson('对不起, 无效下级信息!', 0);
            }
            // 是否是下级
            if(!$user->hasChild($child->id)) {
                return Help::returnApiJson('对不起, 无效目标用户!', 0);
            }
            $c['username'] = $userName;
            $c['user_id']  = '';
            $data = LotteryTrace::getList($c);
        } else if($userName == $user->username){
			$c['username'] = $userName;
			$c['user_id']  = '';
            $data = LotteryTrace::getList($c);
        } else {
			$data = LotteryTrace::getList($c);
		}

        $modeArr   = config("game.main.modes");
        foreach ($data['data'] as $index => $item) {
			$lottery = PartnerLottery::where('partner_sign', $user->partner_sign)->where('lottery_sign', $item->lottery_sign)->first();
			$imgs = substr($lottery['icon_path'], 0,7) == 'lottery' ? strtolower($user->partner_sign).'/'.$lottery['icon_path'] : $lottery['icon_path'];

            $tmp = [
                'id'                => hashId()->encode($item->id),
                'lottery_name'      => $item->lottery_name,
				'lottery_sign'		=> $item->lottery_sign,
				'icon_path'			=> $imgs,
                'method_name'       => $item->method_name,
                'start_issue'       => $item->start_issue,
                'total_issues'      => $item->total_issues,
                'finished_issues'   => $item->finished_issues,
                'canceled_issues'   => $item->canceled_issues,
                'total_price'       => number4($item->trace_total_cost),
                'created_at'        => date("Y-m-d H:i:s", $item->time_bought),
                'win_stop'          => $item->win_stop,
                'finished_bonus'    => number4($item->total_bonus),
                'status'            => $item->status,

                'finished_amount'   => number4($item->finished_amount),
                'canceled_amount'   => number4($item->canceled_amount),
                'mode'              => $modeArr[$item->mode]['title'],
                'username'          => $item->username,
                'bet_prize_group'   => $item->bet_prize_group,
                'closed_vacation'   => Lottery::isClosedMarket($item->lottery_sign)
            ];

            $data['data'][$index] = $tmp;
        }

        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data, ['self'=> $user->id, 'child' => $childs]);
    }

    /**
     * 走势图
     * @return JsonResponse
     */
    public function trend() {
        $data = request()->all();

        $sLotterySign   = $data['lottery_sign'];            // 彩种id
        $iNumType       = $data['num_type'];                // 号码位数
        $iCount         = $data['count'];                   // 要获取的记录数
        $iBeginTime     = $data['begin_time'] ?? null;      // 开始时间, utc秒值
        $iEndTime       = $data['end_time'] ?? null;        // 结束时间, utc秒值

        if ($iCount > 300) { // 规定可取的范围
            return Help::returnApiJson('对不起, 超过最大奖期!', 0);
        }


        $oTrend = new LotteryTrend();

        $iType = 1;
        switch ($iType) {
            case 2:
                $data = $oTrend->getProbabilityOfOccurrenceByParams(
                    $sLotterySign,
                    $iNumType,
                    $iBeginTime,
                    $iEndTime,
                    $iCount
                );
                break;
            case 1:
            default:
                $data = $oTrend->getTrendDataByParams(
                    $sLotterySign,
                    $iNumType,
                    $iBeginTime,
                    $iEndTime,
                    $iCount
                );
                break;
        }

        return !$data ? Help::returnApiJson('对不起, 无数据!', 0) : Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }
}
