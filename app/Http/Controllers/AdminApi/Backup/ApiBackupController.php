<?php

namespace App\Http\Controllers\AdminApi\Backup;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Account\Account;
use App\Models\Account\AccountChangeReportBackup;
use App\Models\Account\AccountChangeType;
use App\Models\Game\LotteryCommissionBackup;
use App\Models\Game\LotteryIssueBackup;
use App\Models\Game\LotteryProjectBackup;
use App\Models\Game\LotteryTraceBackup;
use App\Models\Game\LotteryTraceListBackup;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerAdminAccessLogBackup;
use App\Models\Partner\PartnerAdminBehaviorBackup;
use App\Models\Partner\PartnerAdminUser;
use App\Models\Partner\PartnerLottery;
use App\Models\Player\PlayerIpBackup;
use App\Models\Player\PlayerLogBackup;
use App\Models\Player\PlayerTransferRecords;
use App\Models\Report\ReportUserSalary;

class ApiBackupController extends ApiBaseController
{
    public $partnerSign;

    // 帐变记录备份
    public function funcChange()
    {

        $c                  = request()->all();
        $this->partnerSign  = $c['partner_sign'] ?? '';
        $data   = AccountChangeReportBackup::getList($c);

        if ($this->partnerSign == '') {
            $data['agent'] = AccountChangeReportBackup::where('top_id', "=", 0)->groupBy('user_id')->get();
        } else {
            $data['agent'] = AccountChangeReportBackup::where('partner_sign', $this->partnerSign)->where('top_id', "=", 0)->groupBy('user_id')->get();
        }
        $_data = [];
        foreach ($data['agent'] as $item) {
            $_data[] = [
                'user_id' => $item->user_id,
                'username' => $item->username,
                'rid' => $item->rid
            ];
        }
        $data['agent'] = $_data;

        $types  = AccountChangeType::getDataListFromCache();
        foreach ($data['data'] as $item) {
            $item->before_balance               = number4($item->before_balance);
            $item->balance                      = number4($item->balance);
            $item->before_frozen_balance        = number4($item->before_frozen_balance);
            $item->frozen_balance               = number4($item->frozen_balance);
            $item->type_flow                    = ($types[$item->type_sign]['type']) == 1 ? '增加' : '减少';
            $item->amount                       = number4($item->amount);
            $item->hash_id                      = hashId()->encode($item->id);
            unset($item->id);
            $item->project_id                   = $item->project_id;
        }

        $data['type_options'] = AccountChangeType::getTypeOptions();
        $data['partner_option']     = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }


    // 帐变详情
    public function accountChangeProjectDetail()
    {
        $c                  = request()->all();
        $this->partnerSign  = $c['partner_sign'] ?? '';
        $id = request('hash_id',0);
        if (!$id) {
            return Help::returnApiJson('对不起, 账号信息错误!', 0);
        }

        $accountChangeId = hashId()->decode($id);
        $realId = $accountChangeId[0];

        if ($this->partnerSign == '') {
            $dataNew = AccountChangeReportBackup::where('id', $realId)->first();
        } else {
            $dataNew = AccountChangeReportBackup::where('id', $realId)->where('partner_sign', $this->partnerSign)->first();
        }

        $data = [];
        if (!$dataNew){
            return Help::returnApiJson('对不起, 账号信息错误!', 0);
        }
        switch ($dataNew->type_sign) {
            // 撤单返款
            case 'cancel_order':
                // 撤单返款
            case 'cancel_trace_order':
                //撤单手续费
            case 'cancel_fee':
                // 游戏奖金 投注
            case 'game_bonus':
                // 下级返点
            case 'commission_from_child':
                //个人返点
            case 'commission_from_self':
                //投注返点
            case 'commission_from_bet':
                // 投注扣款
            case 'bet_cost':
                // 和局返款
            case 'he_return':
                if ($this->partnerSign == '') {
                    $dataNews = LotteryProjectBackup::where('id', $dataNew->project_id)->first();
                } else {
                    $dataNews = LotteryProjectBackup::where('id', $dataNew->project_id)->where('partner_sign', $this->partnerSign)->first();
                }

                $modeArr   = config("game.main.modes");
                // 注单号
                $data['hash_id'] = hashId()->encode($dataNews->id);
                //用户名
                $data['username'] = $dataNews->username;
                // 模式
                $data['mode'] = $modeArr[$dataNews->mode]['title'];
                // 彩票名
                $data['lottery_name'] = $dataNews->lottery_name;
                // 玩法
                $data['method_name'] = $dataNews->method_name;
                // 是不是单挑
                $data['is_challenge'] = $dataNews->is_challenge;
                // 期号
                $data['issue'] = $dataNews->issue;
                // 倍数
                $data['times'] = $dataNews->times;
                // 投注金额
                $data['total_cost'] = number4($dataNews->total_cost);
                // 奖金
                $data['bonus'] = number4($dataNews->bonus);
                // 投注时间
                $data['time_bought'] = date("Y-m-d H:i:s", $dataNews->time_bought);
                // 单价
                $data['price'] = $dataNews->price;
                // 下注号码
                $data['bet_number'] = $dataNews->bet_number;
                // 下注号码预览
                $data['bet_number_view'] = $dataNews->bet_number_view;
                // 开奖号
                $data['open_number'] = $dataNews->open_number;
                // 是否赢
                $data['is_win'] = $dataNews->is_win;
                $data['time_open'] = date("Y-m-d H:i:s", $dataNews->time_open);
                $data['time_send'] = date("Y-m-d H:i:s", $dataNews->time_send);
                $data['time_commission'] = date("Y-m-d H:i:s", $dataNews->time_commission);
                $data['status'] = $dataNews->getStatus();
                $data['can_cancel'] = $dataNews->getStatus();
                unset($dataNews->id);
                break;
            // 用户提现
            case 'withdraw_finish':
                //充值
            case 'recharge':
                //日工资
            case 'day_salary':
                //解冻
            case 'withdraw_un_frozen':
                //冻结
            case 'withdraw_frozen':
                //系统扣减
            case 'system_transfer_reduce':
                //系统理赔
            case 'system_transfer_add':
                // 单挑
            case 'bonus_challenge_reduce':
                // 娱乐城转入转出
            case 'casino_transfer_out':
            case 'casino_transfer_in':
                //活动礼金
            case 'active_amount':
                // 撤销派奖
            case 'cancel_bonus':
                $data = null;
                break;
            // 真实扣款
            case 'real_cost':
                // 活动礼金
            case 'gift':
                if ($this->partnerSign == '') {
                    $data = Account::where('id', $dataNew->project_id)->first();
                } else {
                    $data = Account::where('id', $dataNew->project_id)->where('partner_sign', $this->partnerSign)->first();
                }
                break;
            // 下级转账
            case 'transfer_to_child':
                // 上级转账
            case 'transfer_from_parent':
                if ($this->partnerSign == '') {
                    $data = PlayerTransferRecords::where('id', $dataNew->project_id)->first();
                } else {
                    $data = PlayerTransferRecords::where('id', $dataNew->project_id)->where('partner_sign', $this->partnerSign)->first();
                }
                break;
            // 分红给下级
            case 'dividend_to_child':
                // 奖金限额扣除
            case 'bonus_limit_reduce':
                // 上级分红
            case 'dividend_from_parent':
                if ($this->partnerSign == '') {
                    $data = ReportUserSalary::where('project_id', $dataNew->project_id)->first();
                } else {
                    $data = ReportUserSalary::where('project_id', $dataNew->project_id)->where('partner_sign', $this->partnerSign)->first();
                }
                break;
            // 追号扣款
            case 'trace_cost':
                if ($this->partnerSign == '') {
                    $dataOld = LotteryTraceBackup::where('id', $dataNew->project_id)->first();
                } else {
                    $dataOld = LotteryTraceBackup::where('id', $dataNew->project_id)->where('partner_sign', $this->partnerSign)->first();
                }
                $modeArr   = config("game.main.modes");
                $data = [];
                // 注单号
                $data['id'] = hashId()->encode($dataOld->id);
                // 用户名
                $data['username'] = $dataOld->username;
                // 模式
                $data['mode'] = $modeArr[$dataOld->mode]['title'];
                // 彩票名
                $data['lottery_name'] = $dataOld->lottery_name;
                // 玩法
                $data['method_name'] = $dataOld->method_name;
                // 开始期号
                $data['start_issue'] = $dataOld->start_issue;
                // 追的总期数
                $data['total_issues'] = $dataOld->total_issues;
                // 完成期数
                $data['finished_issues'] = $dataOld->finished_issues;
                // 完成金额
                $data['finished_amount'] = $dataOld->finished_amount;
                // 追停
                $data['win_stop'] = $dataOld->win_stop;
                // 状态
                $data['status'] = $dataOld->status;
                // 取消期数
                $data['canceled_issues'] = $dataOld->canceled_issues;
                // 奖金
                $data['finished_bonus'] = number4($dataOld->finished_bonus);
                $data['canceled_amount'] = number4($dataOld->canceled_amount);
                // 投注金额
                $data['total_price'] = number4($dataOld->trace_total_cost);
                // 投注时间
                $data['created_at'] = date("Y-m-d H:i:s", $dataOld->time_bought);
                // 单价
                $data['price'] = $dataOld->price;
                // 下注号码
                $data['bet_number'] = $dataOld->bet_number;
                // 下注号码预览
                $data['bet_number_view'] = $dataOld->bet_number_view;
                // 奖金组
                $data['bet_prize_group'] = $dataOld->bet_prize_group;
                break;
        }

        return Help::returnApiJson("恭喜, 获取数据成功!", 1, $data);
    }

    // 商户访问记录备份
    public function partnerVisit()
    {
        $c          = request()->all();
        $data       = PartnerAdminAccessLogBackup::getList($c);

        $data['partner']            = Partner::get();

        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions();

        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 商户行为记录备份
    public function partnerBehavior() {
        $c                  = request()->all();
        $data       = PartnerAdminBehaviorBackup::getList($c);

        foreach ($data['data'] as $item) {
            $item->add_time = $item->add_time ? date("Y-m-d H:i:S", $item->add_time) : "---";
            $item->review_time = $item->review_time ? date("Y-m-d H:i:S", $item->review_time) : "---";
        }

        $data['partner']            = Partner::get();
        $data['partner_admin_user']     = PartnerAdminUser::getAdminUserOptions();
        $data['partner_options']        = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩家访问记录备份
    public function playerVisit()
    {
        $c                  = request()->all();

        $data                       = PlayerLogBackup::getList($c);
        foreach ($data['data'] as $item) {
            $item->partner_name     = isset($item->partner_sign)?Partner::getNameOptions($item->partner_sign):'';
        }

        $data['partner']            = Partner::get();
        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions();
        $data['partner_options']    = Partner::getOptions();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩家IP记录备份
    public function playerIp() {
        $c           = request()->all();
        $data                    = PlayerIpBackup::getList($c);

        foreach ($data['data'] as $_item){
            $_item->partner_name = Partner::getNameOptions($_item->partner_sign);
        }

        $data['partner_options'] = Partner::getOptions();
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩家返点记录
    public function playerCommission() {
        $c            = request()->all();
        $this->partnerSign = $c['partner_sign'] ?? 'system';
        $data   = LotteryCommissionBackup::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "partner_sign"              => $item->partner_sign,
                "project_id"                => hashId()->encode($item->project_id),
                "lottery_sign"              => $item->lottery_sign,
                "lottery_name"              => $item->lottery_name,
                "method_name"               => $item->method_name,

                "username"                  => $item->username,
                "from_username"             => $item->from_username,

                "from_type"                 => $item->from_type,
                "self_prize_group"          => $item->self_prize_group,
                "child_prize_group"         => $item->child_prize_group,
                "bet_prize_group"           => $item->bet_prize_group,

                "issue"                     => $item->issue,
                "amount"                    => number4($item->amount),

                "process_time"              => $item->process_time ? date("Y-m-d H:i:s", $item->process_time) : "--",
                "account_change_id"         => hashId()->encode($item->account_change_id),
                "status"                    => $item->status,
            ];

            unset($item->id);
        }

        $data['data'] = $_data;

        $data['partner_option']     = Partner::getOptions();
        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 玩家投注记录
    public function playerProject() {
        $c           = request()->all();
        $this->partnerSign = $c['partner_sign'] ?? 'system';
        $data   = LotteryProjectBackup::getList($c);

        // 模式
        $modeArr = config("game.main.modes");
        foreach ($data["data"] as $item) {

            $item->hash_id      = hashId()->encode($item->id);
            $item->mode         = $modeArr[$item->mode]['title'];
            $item->total_cost   = number4($item->total_cost);
            $item->bonus        = number4($item->bonus);
            $item->time_bought  = date("Y-m-d H:i:s", $item->time_bought);

            $item->time_open        = date("Y-m-d H:i:s", $item->time_open);
            $item->time_send        = date("Y-m-d H:i:s", $item->time_send);
            $item->time_commission  = date("Y-m-d H:i:s", $item->time_commission);
            $item->status           = $item->getStatus();
            $item->can_cancel       = $item->getStatus();
            unset($item->id);
        }

        $data['partner_option']     = Partner::getOptions();
        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 玩家追号记录
    public function playerTrace()
    {
        $c           = request()->all();
        $this->partnerSign = $c['partner_sign'] ?? 'system';

        $data   = LotteryTraceBackup::getList($c);
        $_data = [];
        $modeArr = config("game.main.modes");
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"           => hashId()->encode($item->id),
                "user_id"      => $item->user_id,
                "lottery_sign" => $item->lottery_sign,
                "lottery_name" => $item->lottery_name,
                "username"     => $item->username,
                "method_name"  => $item->method_name,
                "price"        => $item->price,
                "bet_number"   => $item->bet_number,
                "count"        => $item->count,
                "ip"           => $item->ip,
                "is_tester"    => $item->is_tester,
                "mode"         => $modeArr[$item->mode]['title'],
                "win_stop"     => $item->win_stop,

                "trace_total_cost" => number4($item->trace_total_cost),
                "bet_prize_group"  => $item->bet_prize_group,
                "user_prize_group" => $item->user_prize_group,
                "total_issues"     => $item->total_issues,

                "finished_issues" => $item->finished_issues,
                "canceled_issues" => $item->canceled_issues,
                "finished_amount" => number4($item->finished_amount),
                "canceled_amount" => number4($item->canceled_amount),

                "total_bonus" => number4($item->total_bonus),
                "start_issue" => $item->start_issue,
                "end_issue"   => $item->end_issue,
                "now_issue"   => $item->now_issue,
                "time_bought" => date("Y-m-d H:i:s", $item->time_bought),
                "status"      => $item->status,
            ];
        }

        $data['data'] = $_data;

        $data['partner_option']     = Partner::getOptions();
        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 玩家追号记录详情
    public function playerTraceDes()
    {
        $c           = request()->all();
        $partnerNum = Partner::getList($c);
        $_data = [];
        foreach ($partnerNum['data'] as $item) {
            $_data = [
                "sign" => $item->sign,
                "id"   => $item->id,
            ];
        }
        $partnerNum = $_data;
        $c['partner_sign'] = $partnerNum['sign'];

        $traceDetailData = LotteryTraceListBackup::getList($c);

        return Help::returnApiJson("恭喜, 获取详情成功!", 1, $traceDetailData);
    }

    // 奖期列表记录
    public function issuesList() {
        $c           = request()->all();
        $this->partnerSign = $c['partner_sign'] ?? 'system';
        $data   = LotteryIssueBackup::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                        => $item->id,
                "series_id"                 => $item->series_id,
                "issue"                     => $item->issue,
                "lottery_name"              => $item->lottery_name,
                "lottery_sign"              => $item->lottery_sign,
                "begin_time"                => date("Y-m-d H:i:s", $item->begin_time),
                "end_time"                  => date("Y-m-d H:i:s", $item->end_time),
                "allow_encode_time"         => date("Y-m-d H:i:s", $item->allow_encode_time),
                "official_code"             => $item->official_code,

                "time_open"                 => $item->time_open ? date("Y-m-d H:i:s", $item->time_open) : '',
                "time_send"                 => $item->time_send ? date("Y-m-d H:i:s", $item->time_send) : '',
                "time_trace"                => $item->time_trace ? date("Y-m-d H:i:s", $item->time_trace) : '',
                "time_commission"           => $item->time_commission ? date("Y-m-d H:i:s", $item->time_commission) : '',

                "time_end_open"             => $item->time_end_open ? date("Y-m-d H:i:s", $item->time_end_open) : '',
                "time_end_send"             => $item->time_end_send ? date("Y-m-d H:i:s", $item->time_end_send) : '',
                "time_end_trace"            => $item->time_end_trace ? date("Y-m-d H:i:s", $item->time_end_trace) : '',
                "time_end_commission"       => $item->time_end_commission ? date("Y-m-d H:i:s", $item->time_end_commission) : '',

                "status_process"            => $item->status_process,
                "status_commission"         => $item->status_commission,
                "status_trace"              => $item->status_trace,
                "can_encode"                => $item->status_process == 0 && $item->allow_encode_time > time() ? 1 : 0,
                "encode_username"           => $item->encode_username,
            ];
        }

        $data['data'] = $_data;

        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);
        $data['series_options']     = config("game.main.series");
        $data['partner_option']     = Partner::getOptions();
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

}
