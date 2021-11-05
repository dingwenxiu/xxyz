<?php namespace App\Lib\Telegram;
use App\Lib\Clog;
use App\Models\Partner\Partner;
use App\Models\System\SysTelegramChannel;

trait TelegramTrait
{
    /**
     * 发送异常
     * @param $text
     * @param string $partnerSign
     * @return bool
     */
    static function sendException($text, $partnerSign = "system") {

        $message = '<b>时  间 : '.date('Y-m-d H:i:s',time()).'</b>'.chr(10);

        $env        = isProductEnv() ?  "生产" : "测试";
        $envName    = $env . "(" . envName() . ")";
        $hostName   = gethostname();

        $dbHost   = env('DB_HOST', "default");

        if($partnerSign == "system")
        {
            $message .= '<b>平  台 : 游侠总控 </b>'.chr(10);
            $message .= '<b>环  境 : ' . $envName . ' </b>'.chr(10);
            $message .= '<b>主  机 : ' . $hostName . ' </b>'.chr(10);
        }
        else
        {
            $partner = Partner::where('sign', $partnerSign)->first();
            if(!$partner)
            {
                return "不存在的商户-{$partnerSign}";
            }
            $message .= '<b>平  台 : '.$partner->name.'</b>'.chr(10);
        }

        $message.= '<b>异  常 : '.$text.'</b>';

        $sysTelegramChannel = SysTelegramChannel::where('channel_sign','send_exception')->first();

        if (!$sysTelegramChannel) {
            return "不存在的商户渠道-send_exception-{$partnerSign}";
        }

        $channelId = $sysTelegramChannel->channel_id;

        if (!$channelId) {
            return "不存在的商户渠道ID-send_exception-{$partnerSign}";
        }

        $res =  self::sendMessage(configure('web_send_boot_token'), $sysTelegramChannel->channel_id, $message);

        if (!$res) {
            return "send_exception-{$partnerSign}-{$sysTelegramChannel->channel_id}-发送失败";
        }

        return true;
    }

    /**
     * 发送 队列 异常
     * @param $text
     * @return bool
     */
    static function sendJobException($text) {

        $envName = isProductEnv() ?  "生产" : "测试";

        $message  = '<b>时  间 : '.date('Y-m-d H:i:s',time()).'</b>'.chr(10);
        $message .= '<b>平  台 : 游侠总控 </b>'.chr(10);
        $message .= '<b>环  境 : ' . $envName . ' </b>'.chr(10);
        $message .= '<b>异  常 : '.$text.'</b>';

        $sysTelegramChannel = SysTelegramChannel::where('channel_sign','send_job_exception')->first();

        if (!$sysTelegramChannel) {
            return false;
        }

        $channelId = $sysTelegramChannel->channel_id;

        if (!$channelId) {
            return false;
        }

        return self::sendMessage(configure('web_send_boot_token'), $sysTelegramChannel->channel_id, $message);
    }

    /**
     * 未开奖期
     * @param $text
     * @return bool
     */
    static function sendNotOpenIssue($text) {

        $message  = '<b>时  间 : '.date('Y-m-d H:i:s',time()).'</b>'.chr(10);
        $message .= '<b>平  台 : 游侠总控 </b>'.chr(10);
        $message .= '<b>异  常 : '. $text . '</b>';

        $sysTelegramChannel = SysTelegramChannel::where('channel_sign','send_not_open_issue')->first();

        if (!$sysTelegramChannel) {
            return false;
        }

        $channelId = $sysTelegramChannel->channel_id;

        if (!$channelId) {
            return false;
        }

        return self::sendMessage(configure('web_send_boot_token'), $sysTelegramChannel->channel_id, $message);
    }

    /**
     * 发送单挑信息
     * @param $text
     * @param string $partnerSign
     * @return bool
     */
    static function sendChallenge($text, $partnerSign = "system") {

        $message = '<b>时  间 : '.date('Y-m-d H:i:s', time()) . '</b>' . chr(10);

        $env        = isProductEnv() ?  "生产" : "测试";

        if($partnerSign == "system") {
            $message .= '<b>平  台 : 游侠总控 (' . $env . ')</b>' . chr(10);
        }  else {
            $partner = Partner::where('sign', $partnerSign)->first();
            if(!$partner) {
                return "send-challenge-无效的商户-{$partnerSign}";
            }

            $message .= '<b>平  台 : '.$partner->name . '(' . $env . ')</b>' . chr(10);
        }

        $sysTelegramChannel = SysTelegramChannel::where('partner_sign', $partnerSign)->where('channel_sign', 'send_challenge')->first();
        if (!$sysTelegramChannel) {
            return "send-challenge-无效的渠道-{$partnerSign}";
        }

        $channelId = $sysTelegramChannel->channel_id;
        if (!$channelId) {
            return "send-challenge-无效的ID-{$partnerSign}";
        }

        $message.= '<b>信  息 : '.$text.'</b>';


        $res = self::sendMessage(configure('web_send_boot_token'), $channelId, $message);
        if ($res !== true) {
            return "send-challenge-发送失败-{$partnerSign}-{$channelId}";
        }

        return true;
    }

    /**
     * 发送二维码
     * @param $text
     * @param string $partnerSign
     * @return bool
     */
    static function sendCode($text, $partnerSign = "system") {

        $message = '<b>时    间 : '.date('Y-m-d H:i:s',time()).'</b> '.chr(10);

        $env        = isProductEnv() ?  "生产" : "测试";

        if($partnerSign == "system") {
            $message .= '<b>平    台 : 游侠总控(' . $env . ')</b>'.chr(10);
        } else {
            $partner = Partner::where('sign',$partnerSign)->first();
            if(!$partner) {
                return false;
            }

            $message                .= '<b>平    台：'.$partner->name . "($env)" . '</b>'.chr(10);
        }

        $sysTelegramChannel     = SysTelegramChannel::where('partner_sign', $partnerSign)->where('channel_sign','send_code')->first();
        if (!$sysTelegramChannel) {
            return false;
        }

        $channel_id             = $sysTelegramChannel->channel_id;

        if (!$channel_id) {
            return false;
        }

        $message .= $text;

        return self::sendMessage(configure('web_send_boot_token'), $channel_id, $message);
    }

    /**
     * @param $document
     * @param string $partnerSign
     * @return bool
     * @throws \Exception
     */
    static function sendStat($document, $partnerSign = "system") {

        if($partnerSign != "system") {
            $partner = Partner::where('sign', $partnerSign)->first();
            if(!$partner) {
                Clog::telegramLog("send-stat-error-{$partnerSign}-无效的商户");
                return false;
            }
        }

        $sysTelegramChannel     = SysTelegramChannel::where('partner_sign', $partnerSign)->where('channel_sign', 'send_stat')->first();

        if (!$sysTelegramChannel) {
            Clog::telegramLog("send-stat-error-{$partnerSign}-无效的渠道");
            return false;
        }

        $channelId = $sysTelegramChannel->channel_id;

        if (!$channelId) {
            Clog::telegramLog("send-stat-error-{$partnerSign}-无效的渠道ID");
            return false;
        }

        return self::sendDocument(configure('web_send_boot_token'), $channelId, $document);
    }

    /**
     * @param $text
     * @param string $partnerSign
     * @return bool
     */
    static function sendFinance($text, $partnerSign = "system") {

        $message = '<b>时    间 : '.date('Y-m-d H:i:s',time()).'</b> '.chr(10);

        $env     = isProductEnv() ?  "生产" : "测试";
        if($partnerSign == "system") {
            $message .= '<b>平    台 : 游侠总控(' . $env . ')</b>'.chr(10);
        } else {
            $partner = Partner::where('sign',$partnerSign)->first();
            if(!$partner) {
                return false;
            }

            $message                .= '<b> 平台：'.$partner->name . '(' . $env . ')</b>'.chr(10);
        }

        $sysTelegramChannel     = SysTelegramChannel::where('partner_sign', $partnerSign)->where('channel_sign','send_finance')->first();

        if (!$sysTelegramChannel) {
            return false;
        }

        $channel_id             = $sysTelegramChannel->channel_id;

        if (!$channel_id) {
            return false;
        }

        $message .= $text;

        return self::sendMessage(configure('web_send_boot_token'), $channel_id, $message);
    }

    static function sendMessage($token, $chat_id, $message ,$model='HTML')
    {

        $url    = 'https://api.telegram.org/bot'.$token.'/sendMessage';
        $params = ['chat_id'=>$chat_id,'text'=>$message,'parse_mode'=>$model];
        $output = self::request('POST',$url,$params);

        $output =json_decode($output,true);

        if(isset($output) && $output['ok'])
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static function _update($token,$_data)
    {
        $url = 'https://api.telegram.org/bot'.$token.'/getUpdates';
        $params = [];
        $output = self::request('POST',$url,$params);
        $output =json_decode($output,true);

        $arr=[];
        if(isset($output['result']))
        {
            $data=[];
            foreach ($output['result'] as $key => $value) {
                if(isset($value['message']['chat']) && $value['message']['chat']['type']=='group')
                {
                    if($_data['send_code_name']==$value['message']['chat']['title'])
                    {
                        $data['send_code_sign']=$value['message']['chat']['id'];
                    }

                    if($_data['report_push_name']==$value['message']['chat']['title'])
                    {
                        $data['report_push_sign']=$value['message']['chat']['id'];
                    }

                    if($_data['recharge_cash_push_name']==$value['message']['chat']['title'])
                    {
                        $data['recharge_cash_push_sign']=$value['message']['chat']['id'];
                    }

                    if($_data['background_audit_name']==$value['message']['chat']['title'])
                    {
                        $data['background_audit_sign']=$value['message']['chat']['id'];
                    }
                }
            }
            return $data;
        }
        else
        {
            return false;
        }
    }

    /**
     * 根据名称查找 id
     * @param $token
     * @param $name
     * @return bool
     */
    static function findChannelId($token, $name)
    {
        $url    = 'https://api.telegram.org/bot' . $token.  '/getUpdates';
        $params = [];
        $output = self::request('POST', $url, $params);
        $output = json_decode($output,true);

        if(isset($output['result']))
        {
            $channelId = "";
            foreach ($output['result'] as $key => $value) {
                if(isset($value['message']['chat']) && $value['message']['chat']['type'] == 'group')
                {
                    if($name == $value['message']['chat']['title'])
                    {
                        $channelId = $value['message']['chat']['id'];
                    }
                }
            }

            return $channelId;
        }

        return false;

    }

    static function sendDocument($token, $chat_id,$document)
    {
        $url = 'https://api.telegram.org/bot'.$token.'/sendDocument';
        $params = ['chat_id' => $chat_id, 'document' => $document];
        $output = self::request('POST', $url, $params);
        $output = json_decode($output,true);

        Clog::telegramLog("send-stat-send-document", ['res' => $output]);

        if(isset($output) && $output['ok'])
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static function request($method = 'GET', $url, $params = [], $header = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if (!is_null($header)) {

            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        }

        if(isset($params['document']))
        {
            //$params['document'] = $params['document'];
            $params['document'] = curl_file_create($params['document']);
        }

        $output = '';
        switch ($method)
        {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

                $output = curl_exec($ch);
                $request_header = curl_getinfo($ch,CURLINFO_HEADER_OUT);
                if (curl_errno($ch)) {

                    curl_close($ch);
                    return false;
                }
                return $output;
                break;
            case 'GET':
                $output = curl_exec($ch);
                if (curl_errno($ch)) {
                    curl_close($ch);
                    return false;
                }
                return $output;
                break;
        }
    }

}
