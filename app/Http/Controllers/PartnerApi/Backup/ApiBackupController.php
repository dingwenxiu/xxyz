<?php

namespace App\Http\Controllers\PartnerApi\Backup;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Account\AccountChangeReportBackup;
use App\Models\Account\AccountChangeType;
use App\Models\Game\BaseGame;
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

class ApiBackupController extends ApiBaseController
{
    // 帐变记录备份
    public function funcChange() {
        $c                  = request()->all();
        $c['partner_sign']  = $this->partnerSign;

        $data   = AccountChangeReportBackup::getList($c);

        $data['agent'] = AccountChangeReportBackup::where('partner_sign', $this->partnerSign)->where('top_id', "=", 0)->groupBy('user_id')->get();
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

            $item->project_id                   = $item->project_id;
        }

        $data['type_options'] = AccountChangeType::getTypeOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 商户访问记录备份
    public function partnerVisit() {
        $c          = request()->all();
        $c['partner_sign']  = $this->partnerSign;
        $data       = PartnerAdminAccessLogBackup::getList($c);

        if (!$data) {
            return Help::returnApiJson(BaseGame::$errStatic, 0);
        }

        $data['partner_admin_user'] = PartnerAdminUser::getAdminUserOptions($c['partner_sign']);
        $data['partner_options']    = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
   }

    // 商户行为记录备份
    public function partnerBehavior() {
        $c          = request()->all();
        $data       = PartnerAdminBehaviorBackup::getList($c);

        if (!$data) {
            return Help::returnApiJson(BaseGame::$errStatic, 0);
        }

        foreach ($data['data'] as $item) {
            $item->add_time = date("Y-m-d H:i:S", $item->add_time);
        }

        $data['partner_admin_user']     = PartnerAdminUser::getAdminUserOptions();
        $data['partner_options']        = Partner::getOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩家访问记录备份
    public function playerVisit() {
        $c                  = request()->all();
        $c["partner_sign"]  = $this->partnerSign;
        $data               = PlayerLogBackup::getList($c);
        if (!$data) {
            return Help::returnApiJson(BaseGame::$errStatic, 0);
        }
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩家IP记录备份
    public function playerIp() {
        $c          = request()->all();
        $c["partner_sign"]  = $this->partnerSign;
        $data       = PlayerIpBackup::getList($c);

        if (!$data) {
            return Help::returnApiJson(BaseGame::$errStatic, 0);
        }

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩家返点记录
    public function playerCommission() {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;

        $data   = LotteryCommissionBackup::getList($c);

        if (!$data) {
            return Help::returnApiJson(BaseGame::$errStatic, 0);
        }

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
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

        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 玩家投注记录
    public function playerProject() {
        $c                      = request()->all();
        $c['partner_sign']      = $this->partnerSign;
        $data   = LotteryProjectBackup::getList($c);

        if (!$data) {
            return Help::returnApiJson(BaseGame::$errStatic, 0);
        }


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

        $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

   // 玩家追号记录
   public function playerTrace() {
       $c      = request()->all();
       $c['partner_sign'] = $this->partnerSign;
       $data   = LotteryTraceBackup::getList($c);

       if (!$data) {
           return Help::returnApiJson(BaseGame::$errStatic, 0);
       }

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

       $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign);
       return Help::returnApiJson('获取数据成功!', 1, $data);
   }

   // 玩家追号记录详情
   public function playerTraceDes() {
       $c = request()->all();
       $c['partner_sign'] = $this->partnerSign;
       $traceDetailData = LotteryTraceListBackup::getList($c);

       return Help::returnApiJson("恭喜, 获取详情成功!", 1, $traceDetailData);
   }

    // 奖期列表记录
   public function issuesList() {
       $c      = request()->all();
       $c['partner_sign'] = $this->partnerSign;
       $data   = LotteryIssueBackup::getList($c);

       if (!$data) {
           return Help::returnApiJson(BaseGame::$errStatic, 0);
       }

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

       $data['lottery_options']    = PartnerLottery::getSelectOptions($this->partnerSign, false);
       $data['series_options']     = PartnerLottery::getSeriesLotteryOptions($this->partnerSign);
       return Help::returnApiJson('获取数据成功!', 1, $data);
   }

}
