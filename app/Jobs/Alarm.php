<?php

namespace App\Jobs;

use App\Lib\T;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Alarm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type = 1;
    public $data = null;

    public function __construct($type, $data) {
        $this->type = $type;
        $this->data = $data;
    }

    public function handle() {
        if ('telegram' == $this->type) {
            $this->sendTelegramMessage($this->data);
        }

        if ('mail' == $this->type) {
            $this->sendMailMessage($this->data);
        }

        if ('sms' == $this->type) {
            $this->sendMailMessage($this->data);
        }

        if ('google_code' == $this->type) {
            $this->sendMailMessage($this->data);
        }

        return true;
    }

    // 发送Telegram消息
    public function sendTelegramMessage($data) {
        T::sendMessage($data['type'], $data['msg']);
    }

    // 发送邮件
    public function sendMailMessage($data) {

    }

    // 发送谷歌验证
    public function sendGoogleCode($data) {

    }

    // 发送短信
    public function sendSms($data) {

    }
}
