<?php namespace App\Jobs\Stat;

use App\Lib\Clog;
use App\Lib\Logic\Stat\StatLogic;
use App\Models\Player\Player;
use App\Models\Report\ReportStatUser;
use App\Models\Report\ReportStatUserDay;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019
 * Class StatUser
 * @package App\Jobs\Stat
 */
class StatUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type    = '';
    public $data    = null;
    public $userId  = null;

    public function __construct($type, $userId, $data) {
        $this->type     = $type;
        $this->data     = $data;
        $this->userId   = $userId;
    }

    public function handle() {
        //  再次检测
        $player     = Player::find($this->userId);

        if (!$player) {
            Clog::statUser("stat-user-check-error-" . $player->id  . "-无效的玩家用户", $this->data);
            return true;
        }


        $data = $this->data;

       if (!isset($data['date'])) {
           Clog::statUser("stat-user-check-error-" . $player->id  . "-日期不存在", $this->data);
           return true;
       }

        db()->reconnect();
        db()->beginTransaction();
        try {

            //  投注
            if ('bet' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-bet-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'bets'  => $amount,
                ];

                // 人数
                $day = date("Ymd", strtotime($data['date']));
                $item = ReportStatUserDay::where("partner_sign", $player->partner_sign)->where("user_id", $player->id)->where("day", $day)->first();
                if ($item && $item->have_bet <= 0) {
                    $change["have_bet"] = 1;
                }


                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-bet-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }

            }

            //  投注
            if ('bonus' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-bonus-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'bonus'  => $amount,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-bonus-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  撤单
            if ('cancel' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-cancel-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'cancel'  => $amount,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-cancel-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  返点下级
            if ('commission_child' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-commission_child-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'commission_child'  => $amount,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-commission_child-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  返点自己
            if ('commission_self' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-commission_self-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'commission_self'  => $amount,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-commission_self-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  礼金
            if ('gift' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-gift-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'gift'  => $amount,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-gift-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  日工资
            if ('salary' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-salary-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'salary'  => $amount,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-salary-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  注册
            if ('register' == $this->type) {
                $change = [
                    'first_register'        => 1,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-register-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  系统理赔
            if ('system_transfer' == $this->type) {

                // 是否存在
                if (!isset($data['type'])) {
                    Clog::statUser("stat-user-system_transfer-" . $player->id  . "-转账类型不存在", $data);
                    db()->rollback();
                    return false;
                }

                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-system_transfer-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $type = $data['type'];

                if ("add" == $type) {
                    $change = [
                        'system_transfer_add'       => $data['amount'],
                    ];
                } else {
                    $change = [
                        'system_transfer_reduce'    => $data['amount'],
                    ];
                }

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-system_transfer-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  充值
            if ('recharge' == $this->type) {

                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-register-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'recharge_count'    => 1,
                    'recharge_amount'   => $amount,
                ];

                $userStat = ReportStatUser::where("user_id", $player->id)->first();
                if ($userStat->recharge_amount <= 0) {
                    $change['first_recharge_count'] = 1;
                }

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-register-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            //  提现
            if ('withdraw' == $this->type) {
                // 是否存在
                if (!isset($data['amount'])) {
                    Clog::statUser("stat-user-withdraw-" . $player->id  . "-金额不存在", $data);
                    db()->rollback();
                    return false;
                }

                $amount = $data['amount'];
                $change = [
                    'withdraw_count'    => 1,
                    'withdraw_amount'   => $amount,
                ];

                $res = StatLogic::change($player, $change, $data['date']);

                if ($res !== true) {
                    Clog::statUser("stat-user-withdraw-" . $player->id  . "-变更失败-{$res}", $change);
                    db()->rollback();
                    return false;
                }
            }

            // 玩家转账
            if ('player_transfer' == $this->type) {

            }

            db()->commit();
        } catch(\Exception $e) {
            db()->rollback();
            Clog::statUser("Error-" . $player->id  . "-数据统计失败-" . $e->getMessage() . "|" . $e->getLine() . "|" . $e->getFile());
            throw $e;
        }

        return true;
    }

}
