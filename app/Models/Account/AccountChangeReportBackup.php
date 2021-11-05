<?php
namespace App\Models\Account;

use App\Models\Game\BaseGame;
use Illuminate\Support\Carbon;
use function GuzzleHttp\Psr7\str;

/**
 * 历史
 * Class AccountChangeReportHistory
 * @package App\Models\Account
 */
class AccountChangeReportBackup extends BaseGame
{
    protected $table = 'account_change_report_backup';

    static function getList($c, $pageSize = 20)
    {
        $query = self::orderBy('id', 'desc');

        $timeToday = Carbon::now();
        $timeNow = strtotime($timeToday) - 60 * 60 * 24 * 6;
        $timeFuture = strtotime($timeToday);



        $currentPage    = isset($c['page_index']) ? intval($c['page_index']) : 1;
        $pageSize       = isset($c['page_size']) ? intval($c['page_size']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        // 帐变id
        if (isset($c['hash_id']) && isset($c['hash_id'])) {
            $id = hashId()->decode($c['hash_id']);
            if ($id){
                $query->where('id', $id);
            } else {
                $query->where('id', '');
            }
        }
        // 商户
        if (isset($c['partner_sign']) && $c['partner_sign']) {
            $query->where('partner_sign', trim($c['partner_sign']));
        }

        // 直属下级
        if (isset($c['parentname']) && $c['parentname']) {
            $query->where('parent_id', $c['user_id']);
        }

        // 用户名
        if (isset($c['username']) && $c['username']) {
            $query->where('username', trim($c['username']));
        }

        // 游戏名
        if (isset($c['lottery_name']) && $c['lottery_name']) {
            $query->where('lottery_name', trim($c['lottery_name']));
        }

        // 圆角分
        if (isset($c['mode']) && $c['mode']) {
            $query->where('mode', trim($c['mode']));
        }

        // 游戏
        if (isset($c['lottery_sign']) && $c['lottery_sign'] && $c['lottery_sign'] != "all") {
            $query->where('lottery_sign', trim($c['lottery_sign']));
        }

        // 玩法
        if (isset($c['method_sign']) && $c['method_sign'] && $c['method_sign'] != "all") {
            $query->where('method_sign', trim($c['method_sign']));
        }


        // 上级
        if (isset($c['parent_id']) && $c['parent_id']) {
            $query->where('parent_id', trim($c['parent_id']));
        }

        // 类型
        if (isset($c['type']) && $c['type'] && $c['type'] != 'all') {
            if (is_array($c['type'])) {
                $query->whereIn('type_sign', $c['type']);
            } else {
                $query->where('type_sign', $c['type']);
            }

        }

        // project id
        if (isset($c['project_id']) && $c['project_id']) {
            $query->where('project_id', $c['project_id']);
        }

        // start time
        if (isset($c['start_time']) && $c['start_time']) {
            $query->where('process_time', ">=", strtotime($c['start_time']));
        }

        // end time
        if (isset($c['end_time']) && $c['end_time']) {
            $query->where('process_time', "<=", strtotime($c['end_time']));
        }

        // 管理员id
        if (isset($c['from_admin_id']) && $c['from_admin_id']) {
            $query->where('from_admin_id', $c['from_admin_id']);
        }

        // 前台所有下级
        if (isset($c['rid']) && $c['rid'] && !isset($c['user_id'])) {
            $query->where('rid', 'like', '%'.$c['rid'].'|%');
        }

        // 总代下级
        if (isset($c['top_id']) && $c['top_id'] && !isset($c['user_id'])) {
            $query->where('top_id', $c['top_id']);
        }

        //总代理以及所有下级
        if (isset($c['user_id'], $c['top_id']) && $c['user_id'] && $c['top_id']) {
            $query->where('rid', 'like','%'.$c['user_id'].'%');
        }

        if (isset($c['user_id']) && $c['user_id'] && !isset($c['top_id'])) {
            $query->where('user_id', $c['user_id']);
        }

        // 不计总代
        if (isset($c['top_agent']) && $c['top_agent']) {
            $query->where('top_id', '!=', 0);
        }

        // amount 帐变金额
        if (isset($c['amount_min'], $c['amount_max']) && $c['amount_min'] && $c['amount_max']) {
            $query->whereBetween('amount', [$c['amount_min'], $c['amount_max']]);
        }

        // is_tester
        if (isset($c['is_tester'])) {
            $query->where('is_tester', $c['is_tester']);
        }

        // 开始时间
        // 结束时间
        if (isset($c['start_time']) && $c['start_time'] && isset($c['end_time']) && $c['end_time']) {

            if (strtotime($c['end_time']) - strtotime($c['start_time']) >= 60 * 60 * 24 * 30) {
                self::$errStatic = '最长只能查询一个月';
                return false;
            }

            $query->whereBetween('created_at',[$c['start_time'], $c['end_time']]);
        }else{
            $query->whereBetween('created_at',[date('Y-m-d H:i:s', $timeNow), date('Y-m-d H:i:s', $timeFuture)]);
        }

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();


        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }
}
