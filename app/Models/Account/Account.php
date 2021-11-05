<?php namespace App\Models\Account;

use App\Models\Base;
use App\Models\Player\Player;
use Illuminate\Support\Facades\DB;

/**
 * Tom 2019
 * Class Account
 * @package App\Models\Account
 */
class Account extends Base
{
    protected $table = 'user_accounts';

    const FROZEN_STATUS_OUT         = 1;
    const FROZEN_STATUS_BACK        = 2;
    const FROZEN_STATUS_TO_PLAYER   = 3;
    const FROZEN_STATUS_TO_SYSTEM   = 4;
    const FROZEN_STATUS_BONUS       = 5;


    const MODE_CHANGE_AFTER = 2;
    const MODE_CHANGE_NOW   = 1;

    public $mode    = 1;
    public $changes = [];

    /**
     * 获取用户
     * @return mixed
     */
    public function user() {
        return Player::find($this->user_id);
    }

    /**
     * 获取账户
     * @param $userId
     * @return mixed
     */
    static function findAccountByUserId($userId) {
        return self::where("user_id", $userId)->first();
    }

    /**
     * 获取格式好的账户
     * @param $userId
     * @return bool
     */
    static function findFormatAccountByUserId($userId) {
        $account = self::select('balance','frozen')->where("user_id", $userId)->first();
        if (!$account) {
            return false;
        }

        $account->balance   = number4($account->balance); // 可用余额
        $account->frozen    = number4($account->frozen);  // 冻结余额

        return $account;
    }

    static function initUserAccount($player) {
        // 生成账户
        $account = new Account();
        $account->partner_sign          = $player->partner_sign;
        $account->user_id               = $player->id;
        $account->top_id                = $player->top_id;
        $account->rid                   = $player->rid;
        $account->parent_id             = $player->parent_id;
        $account->status                = 1;

        // 测试账号默认 50000
        if ($player->is_tester) {
            $balance   = configure('user_tester_default_balance', 50000);
            $account->balance = moneyUnitTransferIn($balance);
        }

        $account->save();
        return true;
    }

    // 获取列表
    static function getList($c, $pageSize = 10) {
        $query = Account::select(
            DB::raw('user_accounts.*'),
            DB::raw('users.username'),
            DB::raw('users.prize_group')
        )->leftJoin('users', 'user_accounts.user_id', '=', 'users.id')->orderBy('id', 'desc');

        // 用户名
        if (isset($c['username'])) {
            $query->where('username', $c['username']);
        }

        // 上级
        if (isset($c['parent_name'])) {
            $query->where('parent_name', $c['parent_name']);
        }

        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $pageSize       = isset($c['pageSize']) ? intval($c['pageSize']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];
    }
}
