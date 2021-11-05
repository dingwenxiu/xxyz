<?php namespace App\Console\Commands\Casino;

use App\Console\Commands\Command;
use App\Lib\BaseCache;
use App\Lib\Xcrypt;
use App\Models\Casino\CasinoApiLog;
use App\Models\Casino\CasinoPlayerBet;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerConfigure;

/**
 * tom 2019
 * Class CmdWithdrawQuery
 * @package App\Console\Commands\Finance
 */
class CmdCasinoRecord extends Command {

    protected $signature    = 'casino:CasinoRecord';
    protected $description  = "获取投注记录!";

    use BaseCache;

    public function handle()
    {
        ini_set('date.timezone','Asia/Shanghai');

        $casinoStatus = 1;

        try {

            $startTime = '';
            while ($casinoStatus) {
                // 1. 获取时间
                $key = 'casino_record';

                $partner = Partner::get();
                foreach ($partner as $item) {

                    $startTime = self::_getCacheData($key, $item->sign);

                    if (empty($startTime)) {
                        $timer     = strtotime('now');
                        $startTime = date('Y-m-d H:i:s', $timer);
                        self::_saveCacheData($key, $startTime, $item->sign);
                    }

                    if ($startTime > date('Y-m-d H:i:s')) {
                        $this->info("时间未到, 年度最优代理 禁止发言 {$item->sign} --  {$startTime} ");
                        sleep(5);
                        continue;
                    }


                    $timer     = strtotime($startTime);
                    $timer     = $timer + 10 * 60;
                    $endTime   = date('Y-m-d H:i:s', $timer);
                    $this->info("年度最优代理{$item->sign} -- 开始获取记录 {$startTime} -- {$endTime}: start");

                    db()->beginTransaction();

                    $casinoKey = ['casino_secret_key', 'casino_merchant', 'casino_gateway', 'casino_encryption_time'];
                    $partnerConfigure = PartnerConfigure::where('partner_sign', $item->sign)->whereIn('sign', $casinoKey)->get(['sign', 'value']);

                    if ($partnerConfigure->isEmpty()) {
                        continue;
                    }

                    $sign = $merchant = $gateway = $enTime = 1;
                    foreach ($partnerConfigure as $itemConfig) {
                        if ($itemConfig->sign === 'casino_secret_key') {
                            $sign = $itemConfig->value;
                        }
                        if ($itemConfig->sign === 'casino_merchant') {
                            $merchant = $itemConfig->value;
                        }
                        if ($itemConfig->sign === 'casino_gateway') {
                            $gateway = $itemConfig->value;
                        }
                        if ($itemConfig->sign === 'casino_encryption_time') {
                            $encryptionTime = $itemConfig->value;
                        }
                    }

                    $page = 1;
                    $CasinoRecordEveryOne = 1;  // 每页状态 当获取页数大于 所有页数 为 0

                    // 每页最多获取 100条数据 分多页获取
                    while ($CasinoRecordEveryOne) {
                        $this->info("获取 {$merchant} 代理的-第{$page}页,投注记录-start:");
                        $paramArr    = [
                            'username'     => $merchant,  // 验证接口-不是参数
                            'mainGamePlat' => 'pt',      // 验证接口-不是参数
                            'startTime'    => $startTime,
                            'endTime'      => $endTime,
                            'pagesize'     => 100,
                            'page'         => $page,
                        ];
                        $paramStr    = urldecode(http_build_query($paramArr));
                        $paramEncode = Xcrypt::authCode(
                            $paramStr, 'ENCODE', $sign, $encryptionTime
                        );

                        $apiUrl = $gateway . '/getRecord?' . $paramStr . '&param=' . urlencode($paramEncode);
                        $data   = casino_request('GET', $apiUrl, [], '', 0, 0, 0);
                        $dataD = json_decode($data, 1);

                        $apiLog = [
                            'api'            => 'getRecord',
                            'params'         => $paramStr,
                            'call_url'       => $apiUrl,
                            'return_content' => $data ?? '',
                            'ip'             => real_ip(),
                            'partner_sign'   => 'all',
                        ];

                        $logStatus = (new CasinoApiLog())->saveItem($apiLog);
                        $betStatus = (new CasinoPlayerBet)->saveItem($dataD['data']['data']);

                        if ($dataD['data']['total_page'] <= $page) {
                            $CasinoRecordEveryOne = 0;
                        }

                        $this->info("获取 {$merchant} 代理的-第{$page}页,投注记录-end!");
                        $this->info("获取投注记录-总页数：" . $dataD['data']['total_page']);
                        $page++;
                    }

                    db()->commit();

                    self::_saveCacheData($key, $endTime, $item->sign);
                    $this->info('3秒钟后, 有请下一位代理,开始发言');
                    sleep(1);
                }
                $this->info('5秒钟后, 进行下一轮发言~~');
                sleep(3);
            }
        }catch (\Exception $e) {
            db()->rollback();
            $msg = '获取投注记录ERR:' .$e->getFile() . $e->getLine() . '-' . $e->getMessage() ;
            var_dump($msg);
//            return T::exceptionNotice($msg);
        }
    }

}
