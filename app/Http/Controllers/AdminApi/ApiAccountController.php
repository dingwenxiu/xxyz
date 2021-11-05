<?php namespace App\Http\Controllers\AdminApi;

use App\Lib\Help;
use App\Models\Account\Account;
use App\Models\Account\AccountChangeReport;
use App\Models\Account\AccountChangeReportHistory;
use App\Models\Account\AccountChangeType;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryProject;
use App\Models\Game\LotteryTrace;
use App\Models\Partner\Partner;
use App\Models\Player\PlayerTransferRecords;
use App\Models\Report\ReportUserSalary;

/**
 * 玩家账户 接口
 * 2019 09
 * Class ApiAccountController
 * @package App\Http\Controllers\AdminApi
 */
class ApiAccountController extends ApiBaseController
{
    // 获取玩家账户
    public function accountList()
    {
        $c      = request()->all();
        $data   = Account::getList($c);

        $data["partner_option"] = Partner::getOptions();

        foreach ($data['data'] as $item) {
            $item->partner_name             = $data["partner_option"][$item->partner_sign];

            $item->before_balance           = number4($item->before_balance);
            $item->balance                  = number4($item->balance);
            $item->before_frozen_balance    = number4($item->before_frozen_balance);
            $item->frozen_balance           = number4($item->frozen_balance);
        }


        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /** ============================= 帐变 - 类型 ============================= */

    /**
     * 帐变类型列表
     * @return mixed
     */
    public function accountChangeTypeList()
    {
        $c      = request()->all();
        $data   = AccountChangeType::getList($c);

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 帐变类型详情
     * @param $id
     * @return mixed
     */
    public function accountChangeTypeDetail($id = 0)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data = [];

        // 获取model
        $model = AccountChangeType::find($id);
        if ($model) {
            $model->type = $model->type == 1 ? '增加':'減少';
            $model->amount = $model->amount == 1 ? '必填':'不必填';
            $model->user_id = $model->user_id == 1 ? '必填':'不必填';
            $model->project_id = $model->project_id == 1 ? '必填':'不必填';
            $model->admin_id = $model->admin_id == 1 ? '必填':'不必填';

            $data['model']    = $model;
        }

        $data['type_options']   = config("user.main.account_change_types");

        return Help::returnApiJson("恭喜, 获取详情数据成功！", 1, $data);
    }


    /**
     * 属性帐变类型缓存
     * @return mixed
     * @throws \Exception
     */
    public function accountChangeTypeFlush()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        AccountChangeType::_flushCache('account_change_type');

        return Help::returnApiJson("恭喜, 刷新帐变类型成功！", 1);
    }

    /** ============================= 帐变 - 记录 ============================= */

    /**
     * 获取帐变记录列表
     * @return mixed
     * @throws \Exception
     */
    public function accountChangeReportList()
    {
        $c      = request()->all();
        $data   = AccountChangeReport::getList($c);

        $data['partner_option']    = Partner::getOptions();
        $types  = AccountChangeType::getDataListFromCache();
        $pageAmount = 0;
        foreach ($data['data'] as $item) {
            $item->before_balance               = number4($item->before_balance);
            $item->balance                      = number4($item->balance);
            $item->before_frozen_balance        = number4($item->before_frozen_balance);
            $item->frozen_balance               = number4($item->frozen_balance);
            $item->amount                       = number4($item->amount);

            if(($types[$item->type_sign]['type']) == 1) {
                $item->type_flow = '增加';
                $pageAmount = bcadd($pageAmount, $item->amount,4);
            } else {
                $item->type_flow = '减少';
                $pageAmount = bcsub($pageAmount, $item->amount,4);
            }

            $item->hash_id                      = hashId()->encode($item->id);
            unset($item->id);
        }

        $data['type_options']       = AccountChangeType::getTypeOptions();
        $data['lottery_options']    = Lottery::getSelectOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    /**
     * 帐变记录详情
     * @param  $id
     * @return mixed
     * @throws \Exception
     */
     // 帐变详情
    public function accountChangeReportDetail($id = 0) {

        if (!$id) {
            return Help::returnApiJson('对不起, 账号信息错误!', 0);
        }

        $partnerSign = request('partner_sign');
        $accountChangeId = hashId()->decode($id);
        $realId = $accountChangeId[0];
        $dataNew = AccountChangeReport::where('id', $realId)->where('partner_sign', $partnerSign)->first();
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
                $dataNews = LotteryProject::where('id', $dataNew->project_id)->where('partner_sign', $partnerSign)->first();
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
                $data = Account::where('id', $dataNew->project_id)->where('partner_sign', $partnerSign)->first();
                break;
            // 下级转账
            case 'transfer_to_child':
                // 上级转账
            case 'transfer_from_parent':
                $data = PlayerTransferRecords::where('id', $dataNew->project_id)->where('partner_sign', $partnerSign)->first();
                break;
            // 分红给下级
            case 'dividend_to_child':
                // 奖金限额扣除
            case 'bonus_limit_reduce':
                // 上级分红
            case 'dividend_from_parent':
                $data = ReportUserSalary::where('project_id', $dataNew->project_id)->where('partner_sign', $partnerSign)->first();
                break;
            // 追号扣款
            case 'trace_cost':
                $dataOld = LotteryTrace::where('id', $dataNew->project_id)->where('partner_sign', $partnerSign)->first();
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

    /**
     * 帐变历史
     * @return mixed
     * @throws \Exception
     */
    public function accountChangeReportHistoryList()
    {
        $c      = request()->all();
        $data   = AccountChangeReportHistory::getList($c);

        foreach ($data['data'] as $item) {
            $item->before_balance               = number4($item->before_balance);
            $item->balance                      = number4($item->balance);
            $item->before_frozen_balance        = number4($item->before_frozen_balance);
            $item->frozen_balance               = number4($item->frozen_balance);

            $item->amount    = number4($item->amount);
        }

        $data['type_options']       = AccountChangeType::getTypeOptions();
        $data['partner_options']    = Partner::getOptions();
        $data['lottery_options']    = Lottery::getSelectOptions();

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }
}
