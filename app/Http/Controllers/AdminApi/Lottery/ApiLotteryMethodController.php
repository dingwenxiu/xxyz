<?php

namespace App\Http\Controllers\AdminApi\Lottery;

use App\Lib\Help;
use App\Lib\Logic\Cache\ActivityCache;
use App\Models\Game\Lottery;
use App\Models\Game\LotteryMethod;
use App\Models\Partner\PartnerMethod;
use App\Http\Controllers\AdminApi\ApiBaseController;

/**
 * version 1.0
 * 玩法
 * Class ApiLotteryMethodController
 * @package App\Http\Controllers\AdminApi\Lottery
 */
class ApiLotteryMethodController extends ApiBaseController
{
    // 玩法列表
    public function methodList()
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $c      = request()->all();
        $data   = LotteryMethod::getList($c);

        $groupNameArr   = config("game.method.group_name");
        $rowNameArr     = config("game.method.row_name");

        $challengeTypeArr = config("game.challenge.type");

        foreach ($data["data"] as $item) {
            $_data[] = [
                "id"                    => $item->id,
                "series_id"             => $item->series_id,
                "lottery_name"          => $item->lottery_name,
                "method_name"           => $item->method_name,
                "method_sign"           => $item->method_sign,

                "method_group"          => $item->method_group,
                "method_group_name"     => $groupNameArr[$item->series_id][$item->method_group],
                "group_sort"            => $item->group_sort,

                "method_row"            => $item->method_row,
                "method_row_name"       => $rowNameArr[$item->series_id][$item->method_row],
                "tab_sort"              => $item->tab_sort,

                "method_sort"           => $item->method_sort,

                "challenge_type"        => $item->challenge_type,
                "challenge_type_desc"   => $item->challenge_type ? $challengeTypeArr[$item->challenge_type] : "无",
                "challenge_min_count"   => $item->challenge_min_count,
                "challenge_config"      => is_bool($item->challenge_config)?unserialize($item->challenge_config):'',
                "challenge_bonus"       => $item->challenge_bonus,

                "add_time"              => $item->created_at ? date("m-d H:i", strtotime($item->created_at)) :'',
                "update_time"           => $item->updated_at ? date("m-d H:i", strtotime($item->updated_at)) : '',

                "status"                => $item->status,
            ];
        }

        $data['data'] = $_data;
        $data['lottery_options'] = Lottery::getSelectOptions(false);
        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }

    // 玩法详情
    public function methodDetail($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户不存在！", 0);
        }

        $data['method'] = [];

        // 获取用户
        if ($id) {
            $method = LotteryMethod::find($id);
            if ($method) {
                $data['method'] = $method;
            }
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    // 玩法状态
    public function methodStatus($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取彩种
        $model = LotteryMethod::find($id);
        if (!$model) {
            return Help::returnApiJson("对不起, 无效的玩法id！", 0);
        }

        $model->status = $model->status ? 0 : 1;
        $model->save();

        if(!$model->status)
        {
            $partners = PartnerMethod::where('lottery_sign',$model->lottery_sign)->where('method_sign',$model->method_sign)->get(); 
            foreach ($partners as $partner) {
                $partner->modifyStatus();
            }
        }

        return Help::returnApiJson("恭喜, 修改状态成功！", 1, ['status' => $model->status]);
    }

    /**
     * 设置玩法
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function methodSet($id)
    {
        $adminUser = auth()->guard('admin_api')->user();
        if (!$adminUser) {
            return Help::returnApiJson("对不起, 用户未登录！", 0);
        }

        // 获取玩法
        $model     = LotteryMethod::find($id);

        if (!$model) {
            return Help::returnApiJson("对不起, 无效的玩法id！", 0);
        }

        $action    = request("action", "process");

        /*
         * challenge_bonus     20000
         * challenge_config    false
         * challenge_min_count 0
         * challenge_type      1
         * */


        // 获取数据
        if ($action == 'option') {
            $model->challenge_config       = $model->challenge_config?unserialize($model->challenge_config):'';

            $data['challenge_type_option'] = config("game.challenge.type");
            $data['method']                = $model;

            return Help::returnApiJson("对不起, 获取数据成功！", 1, $data);
        }

        $params = request() -> all();

        // 保存数据
        $res = $model->saveItem($params);
        if(true !== $res){
            return Help::returnApiJson($res, 0);
        }

        ActivityCache::clearAll('');

        return Help::returnApiJson("恭喜, 设置玩法数据成功！", 1, []);
    }
}
