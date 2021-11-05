<?php

namespace App\Http\Controllers\AdminApi\Lottery;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Game\LotteryIssueRule;
use App\Models\Game\Lottery;

/**
 * version 1.0
 * 彩票
 * Class ApiLotteryIssueRuleController
 * @package App\Http\Controllers\AdminApi\Lottery
 */
class ApiLotteryIssueRuleController extends ApiBaseController
{
    // 奖期规则 - 列表
    public function issueRuleList()
    {
        $c      = request()->all();
        $data   = LotteryIssueRule::getList($c);

        $_data = [];
        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                => $item->id,
                "lottery_sign"      => $item->lottery_sign,
                "lottery_name"      => $item->lottery_name,
                "begin_time"        => $item->begin_time,
                "end_time"          => $item->end_time,
                "issue_seconds"     => $item->issue_seconds,
                "first_time"        => $item->first_time,
                "adjust_time"       => $item->adjust_time,
                "encode_time"       => $item->encode_time,
                "issue_count"       => $item->issue_count,
                "status"            => $item->status,
            ];
        }

        $data['data'] = $_data;

        $data['lottery_options'] = Lottery::getSelectOptions(false);
        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 奖期规则编辑 - 详情
    public function issueRuleAdd($id = 0)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 不存在的用户！", 0);
        }
        $params = request()->all();

        if ($id) {
            $model   = LotteryIssueRule::find($id);
            $params['lottery_sign'] = $model->lottery_sign;
            if (!$model) {
                return Help::returnApiJson("对不起, 目标对象不存在！", 0);
            }
        } else {
            $model = new LotteryIssueRule();
        }

        $res    = $model->saveItem($params, $adminUser->id);
        if(true !== $res) {
            return Help::returnApiJson($res, 0);
        }

        return Help::returnApiJson('保存数据成功!', 1);
    }

    // 奖期规则 - 详情
    public function issueRuleDetail($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['issue_rule'] = [];

        // 获取用户
        if ($id) {
            $issueRule = LotteryIssueRule::find($id);
            if ($issueRule) {
                $data['issue_rule']    = $issueRule;
            }
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }
}
