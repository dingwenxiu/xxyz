<?php

namespace App\Jobs\Lottery;

use App\Lib\Clog;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Lottery\ProjectLogic;
use App\Models\Game\LotteryIssueBet;
use App\Models\Game\LotteryProject;
use Curl\Curl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Tom 2019.10
 * kevin 控水设置
 * Class Jackpot1001Process
 * @package App\Jobs\Lottery
 */
class Jackpot1001Process implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $projectId   = null;
    public $type        = "add";
    public $timeout     = 300;

    public function __construct($projectId, $type) {
        $this->projectId    = $projectId;
        $this->type         = $type == "add" ? $type : "del";
    }

    // 开始
    public function handle() {

        // 不能超过 3
        if ($this->attempts() > 2) {
            return true;
        }

        $project = LotteryProject::find($this->projectId);
        if (!$project) {
            telegramSend("send_exception", "jackpot-fail-无效的订单-{$this->projectId}");
            return true;
        }

        $methods = config("game.jackpot.methods");
        if (!isset($methods[$project->method_sign])) {
            return true;
        }

        try {
            $wayId  = $methods[$project->method_sign];


            $lottery    = LotteryCache::getPartnerLottery($project->lottery_sign, $project->partner_sign);
            $oMethod    = LotteryCache::getMethodObject($lottery->lottery_sign, $project->method_sign, $project->partner_sign);
            $prizes     = ProjectLogic::countPrize($lottery, $oMethod, $project);

            $_prizes = [];
            foreach ($prizes as $level => $bonus) {
                if ($project->is_challenge) {
                    $_prizes[$level] = $project->challenge_prize > $bonus ? $bonus : $project->challenge_prize;
                } else {
                    $_prizes[$level] = $bonus;
                }
            }

            $url = configure("lottery_jackpot_gateway", 'http://35.221.221.10:10010/jackpot');

            $curlHandle = new Curl();
            $curlHandle->setHeader("ptype",             "BW");
            if (!isProductEnv()) {
                $mark = "test_";
            } else {
                $mark = "";
            }

            $curlHandle->setHeader("platform",          $mark . $project->partner_sign);
            $curlHandle->setHeader("jackpotmode",       $this->type);
            $curlHandle->setHeader("Content-Type",      "application/json");

            $curlHandle->setConnectTimeout(10);
            $curlHandle->setTimeout(10);

            $modeArr = config("game.main.modes");


            Clog::jackpot("jackpot-start-" . $project->id, $_prizes);

            $betNum     = ProjectLogic::codeTransferToJackpot($project->series_id, $project->method_sign, ProjectLogic::rc4(base64_decode($project->bet_number)));
            $postData   = [
                "id"                => $project->id,
                "multiple"          => $project->times,
                "issue"             => $project->issue,
                "coefficient"       => $modeArr[$project->mode]['val'],
                "bet_number"        => $betNum,
                "amount"            => $project->total_cost,
                "position"          => "",

                "lottery_id"        => $project->lottery_sign,
                "way_id"            => $wayId,
                "prize_set"         => json_encode(["88" => ProjectLogic::prizeTransfer($project->series_id, $project->method_sign, $_prizes, $betNum)]),
                "username"          => $project->username,
                "is_challenge"      => $project->is_challenge,
                "challenge_prize"   => $project->challenge_prize,
            ];

            Clog::jackpot("jackpot-post-data-", $postData);
            $postData   = $curlHandle->buildPostData($postData);
            $requestRes = $curlHandle->post($url, $postData);

            $httpCode = $curlHandle->getHttpStatusCode();

            $curlHandle->close();

            if ($httpCode != 200) {
                self::release(1);
                $error = $curlHandle->getErrorMessage();

                telegramSend("send_exception", "jackpot-1001-{$httpCode}-{$error}");
                return true;
            }

            // 计算投注
            if ($this->type == "add") {
                $res = LotteryIssueBet::addProjectUpdate($project);
            } else {
                $res = LotteryIssueBet::cancelProjectUpdate($project);
            }

            if ($res !== true) {
                Clog::jackpot("jackpot-update-bet-fail-" . $project->id . "-{$httpCode}", ["res" => $res]);
                telegramSend("send_exception", "jackpot-更新失败");
            }

            Clog::jackpot("jackpot-end-" . $project->id . "-{$httpCode}", ["res" => $requestRes]);
        } catch (\Exception $e) {
            Clog::jackpot("jackpot-exception-" . $project->id . "-" . $e->getMessage());

            telegramSend("send_exception", "jackpot-exception-{$e->getMessage()}");
        }

        return true;
    }

}
