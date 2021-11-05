<?php

namespace App\Http\Controllers\AdminApi\Lottery;

use App\Lib\Help;
use App\Models\Game\Lottery;
use App\Exports\ProjectExport;
use App\Lib\Logic\Lottery\BetLogic;
use App\Lib\Logic\Lottery\ProjectLogic;
use App\Lib\Logic\Lottery\TraceLogic;
use App\Lib\Logic\LotteryTrend;
use Illuminate\Http\JsonResponse;
use App\Models\Game\LotteryProject;
use App\Models\Game\LotteryCommission;
use App\Models\Partner\PartnerLottery;
use App\Models\Account\AccountChangeType;
use App\Models\Account\AccountChangeReport;
use App\Http\Controllers\AdminApi\ApiBaseController;

/**
 * version 1.0
 * 订单
 * Class ApiLotteryProjectController
 * @package App\Http\Controllers\AdminApi\Lottery
 */
class ApiLotteryProjectController extends ApiBaseController
{
    // 奖期规则列表
    public function projectHistoryList()
    {
        $c                      = request()->all();
        // 如果是下载
        if (isset($c['is_export']) && $c['is_export'] == 1) {
            $date = date("Y-m-d");
            return (new ProjectExport($c)) -> download("project-{$date}.csv", \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv']);
        }

        $data   = LotteryProject::getList($c);

        // 模式
        $modeArr = config("game.main.modes");
        foreach ($data["data"] as $item) {
			if ($item->time_send != null) {
				$timeSend = date("Y-m-d H:i:s", $item->time_send);
			} else {
				$timeSend = '';
			}
			if ($item->time_commission) {
				$timeCommission = date("Y-m-d H:i:s", $item->time_commission);
			} else {
				$timeCommission = '';
			}
            $item->hash_id      = hashId()->encode($item->id);
            $item->mode         = $modeArr[$item->mode]['title'];
            $item->total_cost   = number4($item->total_cost);
            $item->bonus        = number4($item->bonus);
            $item->time_bought  = date("Y-m-d H:i:s", $item->time_bought);

            $item->time_open        = date("Y-m-d H:i:s", $item->time_open);
            $item->time_send        = $timeSend;
            $item->time_commission  = $timeCommission;
            $item->status           = $item->getStatus();
            $item->can_cancel       = $item->getStatus();
            unset($item->id);
        }
        $partnerSign = '';
        $data['lottery_options']    = PartnerLottery::getSelectOptions($partnerSign);

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 订单返点
     * @param $projectId
     * @return JsonResponse
     */
    public function projectCommission($projectId)
    {
        // 订单
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

        $commission = LotteryCommission::getProjectCommission($project->id);

        return Help::returnApiJson('获取数据成功!', 1, $commission);
    }

    /**
     * 帐变接口
     * @param $projectId
     * @return JsonResponse
     * @throws \Exception
     */
    public function projectAccountChange($projectId)
    {
        // 订单
        if (!$projectId) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        $ret     = hashId()->decode($projectId);
        if (!$ret || !isset($ret[0])) {
            return Help::returnApiJson('对不起, 无效的订单Id!', 0);
        }

        $project = LotteryProject::find($ret[0]);
        if (!$project || !$project->id) {
            return Help::returnApiJson('对不起, 无效的订单!', 0);
        }

        $c = [
            'project_id'    => $project->id,
        ];

        $data    = AccountChangeReport::getList($c);
        $types   = AccountChangeType::getDataListFromCache();

        foreach ($data['data'] as $item) {
            $item->before_balance               = number4($item->before_balance);
            $item->balance                      = number4($item->balance);
            $item->before_frozen_balance        = number4($item->before_frozen_balance);
            $item->frozen_balance               = number4($item->frozen_balance);
            $item->type_sign                    = ($types[$item->type_sign]['type']) == 1 ? '增加' : '减少';
            $item->amount                       = number4($item->amount);
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 投注订单列表
     * @return JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function projectList()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $c                      = request()->all();
        // 如果是下载
        if (isset($c['is_export']) && $c['is_export'] == 1) {
            $date = date("Y-m-d");
            return (new ProjectExport($c)) -> download("project-{$date}.csv", \Maatwebsite\Excel\Excel::CSV, ['Content-Type' => 'text/csv']);
        }

        $data   = LotteryProject::getList($c);
        // 模式
        $modeArr = config("game.main.modes");
        foreach ($data["data"] as $item) {
        	if ($item->time_send != null) {
				$timeSend = date("Y-m-d H:i:s", $item->time_send);
			} else {
				$timeSend = '';
			}
        	if ($item->time_commission) {
				$timeCommission = date("Y-m-d H:i:s", $item->time_commission);
			} else {
				$timeCommission = '';
			}
            /*
            $item->hash_id      = hashId()->encode($item->id);
            */
            $item->id           = $item->id;
            $item->hash_id      = hashId()->encode($item->id);

            $item->mode         = $modeArr[$item->mode]['title'];
            $item->total_cost   = number4($item->total_cost);
            $item->bonus        = number4($item->bonus);
            $item->time_bought  = date("Y-m-d H:i:s", $item->time_bought);

            $item->time_open        = date("Y-m-d H:i:s", $item->time_open);
            $item->time_send        = $timeSend;
            $item->time_commission  = $timeCommission;
            $item->status           = $item->getStatus();
            $item->can_cancel       = $item->getStatus();
//            unset($item->id);
        }
        $partnerSign = '';
        $data['lottery_options']    = Lottery::getSelectOptions();

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 撤单
     * @param $projectId
     * @return JsonResponse
     * @throws \Exception
     */
    public function cancelProject($projectId) {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        // 订单
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

        $res = ProjectLogic::cancel($project);

        if ($res !== true) {
            return Help::returnApiJson($res, 0);
        } else {
            return Help::returnApiJson("恭喜, 撤单成功!", 1);
        }
    }
}
