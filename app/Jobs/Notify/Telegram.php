<?php

namespace App\Jobs\Notify;

use App\Lib\Clog;
use App\Lib\Telegram\TelegramTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Telegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use TelegramTrait;

    public $type        = 1;
    public $date        = '';
    public $data        = null;
    public $partnerSign = null;

    public $tries = 1;

    public function __construct($type, $data) {
        $this->type = $type;
        $this->data = $data;
        if (!isset($this->data['partner_sign']) || !$this->data['partner_sign']) {
            $this->partnerSign = "system";
        } else {
            $this->partnerSign = $this->data['partner_sign'];
        }

        if (isset($this->data['date'])) {
            $this->date = $this->data['date'];
        } else {
            $this->date = date("Y-m-d H:i:s");
        }

    }

    public function handle() {
        try {
            // 单挑
            if ('send_challenge' == $this->type) {
                $res = $this->_sendChallengeMsg($this->data, $this->partnerSign);
                if ($res !== true) {
                    Clog::telegramLog("send_challenge-发送失败-" . $res, ['res' => $this->data]);
                }
            }

            // 异常
            if ('send_exception' == $this->type) {
                $res = $this->_sendException($this->data, $this->partnerSign);
                if ($res !== true) {
                    Clog::telegramLog("send_exception-发送失败-" . $res, ['res' => $this->data]);
                }
            }

            // job 异常
            if ('send_job_exception' == $this->type) {
                $this->_sendJobException($this->data);
            }

            // issue 异常
            if ('send_issue_exception' == $this->type) {
                $this->_sendIssueException($this->data);
            }

            // 财务
            if ('send_finance' == $this->type) {
                $this->_sendFinance($this->data, $this->partnerSign);
            }

            // 验证码
            if ('send_code' == $this->type) {
                $this->_sendCode($this->data, $this->partnerSign);
            }

            // 统计
            if ('send_stat' == $this->type) {
                $this->_sendStat($this->data, $this->partnerSign);
            }
        } catch (\Exception $e) {
            Clog::telegramLog($e->getMessage() . "-" .  $e->getLine());
        }


        return true;
    }

    // 单挑警告
    public function _sendChallengeMsg($data, $partnerSign) {
        return  self::sendChallenge($data['msg'], $partnerSign);
    }

    // 异常
    public function _sendException($data, $partnerSign) {
        return self::sendException($data['msg'], $partnerSign);
    }

    // job 异常
    public function _sendJobException($data) {
        self::sendJobException($data['msg']);
    }

    // issue 异常
    public function _sendIssueException($data) {
        self::sendNotOpenIssue($data['msg']);
    }

    // 财务
    public function _sendFinance($data, $partnerSign) {
        self::sendFinance($data['msg'], $partnerSign);
    }

    // 验证码
    public function _sendCode($data, $partnerSign) {
        self::sendCode($data['msg'], $partnerSign);
    }

    // 统计
    public function _sendStat($data, $partnerSign) {
        self::sendStat($data['msg'], $partnerSign);
    }
}
