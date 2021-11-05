<?php

namespace App\Lib\Casino;

use App\Lib\Help;
use App\Lib\Xcrypt;
use App\Models\Account\Account;
use App\Models\Casino\CasinoApiLog;
use App\Lib\Logic\AccountChange;
use App\Lib\Logic\AccountLocker;
use App\Models\Casino\CasinoMethod;
use App\Models\Casino\CasinoPlatform;

class CasinoApi
{
    public $player = null;
    public $partner = null;
    public $model = null;

    public function __construct($player, $partner)
    {
        $this->player  = $player;
        $this->partner = $partner;
        $this->model   = new CasinoApiLog();
    }


    // @@@@@@@@@@@@@@@@@@  前台接口  @@@@@@@@@@@@@@@@@

    /**
     * 进游戏
     *
     * @param $c
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinGame($c)
    {
        try {
            $config = $this->getConfig();

            $paramArr = [
                'username'          => $config['casino_merchant'],
                'account_user_name' => $this->player->username,
                'main_game_plat'    => $c['main_game＿plat'],
                'gamecode'          => $c['gamecode'],
                'demo'              => $c['demo'] ?? 1,
                'is_mobile'         => $c['is_mobile'] ?? 1,
                'ip'                => real_ip(),
            ];

            $paramArr = $this->convertUnderline($paramArr);
            $paramStr    = http_build_query($paramArr);
            $paramEncode = Xcrypt::authCode(
                $paramStr, 'ENCODE', $config['casino_secret_key'],
                $config['casino_encryption_time']
            );

            $apiUrl = $config['casino_gateway'] . '/joinGame?' . $paramStr
                . '&param=' . urlencode($paramEncode);

            $apiLog = [
                'call_url'       => $apiUrl,
                'params'         => json_encode($paramArr),
                'return_content' => '成功获取游戏地址',
                'api'            => 'joinGame',
                'username'       => $this->player->username,
                'user_id'        => $this->player->id,
                'partner_sign'   => $this->player->partner_sign,
                'ip'             => real_ip(),
                'platform_sign'  => $c['main_game＿plat'],
            ];

            $this->saveLog($apiLog);

            return Help::returnApiJson('获取游戏链接成功', 1, $apiUrl);

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
            $apiLog['return_content'] = json_encode(
                [$e->getMessage(), $e->getLine(), $e->getFile()]
            );  // 日志

            $this->saveLog($apiLog);

            return Help::returnApiJson(
                '对不起, 访问超时Err', 0, ['reason_code' => 999]
            );
        }
    }

    /**
     * 获取余额
     *
     * @param $c
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance($c)
    {
        $config   = $this->getConfig();
        $username = $this->player->username;
        $userId   = $this->player->id;

        try {
            $paramArr = [
                'username'        => $config['casino_merchant'],
                'mainGamePlat'    => $c['mainGamePlat'],
                'accountUserName' => $username,
            ];

            $paramStr    = http_build_query($paramArr);
            $paramEncode = Xcrypt::authCode(
                $paramStr, 'ENCODE', $config['casino_secret_key'],
                $config['casino_encryption_time']
            );

            $apiUrl = $config['casino_gateway'] . '/getBalance?' . $paramStr
                . '&param=' . urlencode($paramEncode);

            $data  = casino_request('GET', $apiUrl, [], '', 0, 0, 0);
            $dataD = json_decode($data, 1);


            $apiLog = [
                'api'            => 'getBalance',
                'call_url'       => $apiUrl,
                'params'         => json_encode($paramArr),
                'username'       => $username,
                'user_id'        => $userId,
                'ip'             => real_ip(),
                'partner_sign'   => $this->player->partner_sign,
                'platform_sign'  => $c['mainGamePlat'] ?? '',
                'return_content' => $data,
            ];

            $this->saveLog($apiLog);

            if ($dataD['success']) {
                return Help::returnApiJson('获取数据成功', 1, $dataD);
            }

            return Help::returnApiJson(
                '对不起, 获取数据失败', 0, ['reason_code' => 999]
            );
        } catch (\Exception $e) {
            $apiLog['return_content'] = json_encode(
                [$e->getMessage(), $e->getLine(), $e->getFile()]
            );  // 日志
            $this->saveLog($apiLog);

            return Help::returnApiJson(
                '对不起, 访问超时Err', 0, ['reason_code' => 999]
            );
        }
    }

    /**
     * 转入娱乐城
     *
     * @param $c
     *
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Exception
     */
    public function transferIn($c)
    {
        $config       = $this->getConfig();
        $username     = $this->player->username;
        $userId       = $this->player->id;
        $agentName    = $config['casino_merchant'];
        $amount       = $c['amount'];
        $mainGamePlat = $c['mainGamePlat'];

        $apiLog = [
            'api'           => 'transferIn',
            'username'      => $username,
            'user_id'       => $userId,
            'ip'            => real_ip(),
            'partner_sign'  => $this->player->partner_sign,
            'platform_sign' => $c['mainGamePlat'],
        ];

        // 1. 获取账户锁
        $accountLocker = new AccountLocker(
            $userId, "transfer-from-casino-in" . $userId
        );
        if ( ! $accountLocker->getLock()) {
            $accountLocker->release();

            return "对不起, 获取账户锁失败, 请稍后再试1!";
        }


        // 帐变
        db()->beginTransaction();
        try {
            // 1帐变处理
            $accountChange = new AccountChange();
            // 真实扣款

            $account = Account::findAccountByUserId($userId);
            if ( ! $account) {
                $accountLocker->release();
                db()->rollback();

                return "对不起, 账户信息不存在, 请稍后再试2!";
            }

            $params = [
                'user_id'              => $userId,
                'amount'               => $amount * config('game.main.money_unit'),
                'casino_platform_sign' => $mainGamePlat,
            ];

            $res = $accountChange->change(
                $account, 'casino_transfer_in', $params
            );
            if ($res !== true) {
                $accountLocker->release();
                db()->rollback();

                return $res;
            }


            // 2 api请求
            $paramArr = [
                'username'        => $agentName,
                'mainGamePlat'    => $mainGamePlat,
                'accountUserName' => $username,
                'price'           => $amount,
            ];

            $apiLog['params'] = json_encode($paramArr);       // 日志

            $paramStr    = http_build_query($paramArr);
            $paramEncode = Xcrypt::authCode(
                $paramStr, 'ENCODE', $config['casino_secret_key'],
                $config['casino_encryption_time']
            );

            $apiUrl             = $config['casino_gateway'] . '/transferIn?'
                . $paramStr . '&param=' . urlencode($paramEncode);
            $apiLog['call_url'] = $apiUrl;                   // 日志

            $data   = casino_request('GET', $apiUrl, [], '', 0, 0, 0);
            $dataDe = json_decode($data, 1);

            $apiLog['return_content'] = $data;  // 日志

            if ( ! empty($dataDe) && isset($dataDe['success'])
                && $dataDe['success']
            ) {
                $accountLocker->release();
                $this->saveLog($apiLog);
                db()->commit();

                return Help::returnApiJson('金额转入成功', 1, $dataDe);
            }

            $accountLocker->release();
            db()->rollback();
            $this->saveLog($apiLog);

            return Help::returnApiJson(
                '对不起, 访问超时waring', 0, ['reason_code' => 999]
            );
        } catch (\Exception $e) {
            $accountLocker->release();
            db()->rollback();
            $apiLog['return_content'] = json_encode(
                [$e->getMessage(), $e->getLine(), $e->getFile()]
            );  // 日志
            $this->saveLog($apiLog);

            return Help::returnApiJson(
                '对不起, 访问超时Err', 0, ['reason_code' => 999]
            );
        }
    }

    /**
     * 转出娱乐城
     *
     * @param $c
     *
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Exception
     */
    public function transferTo($c)
    {
        $config       = $this->getConfig();
        $username     = $this->player->username;
        $userId       = $this->player->id;
        $agentName    = $config['casino_merchant'];
        $amount       = $c['amount'];
        $mainGamePlat = $c['mainGamePlat'];

        $apiLog = [
            'api'           => 'transferTo',
            'username'      => $username,
            'user_id'       => $userId,
            'ip'            => real_ip(),
            'partner_sign'  => $this->player->partner_sign,
            'platform_sign' => $c['mainGamePlat'],
        ];

        // 1. 获取账户锁
        $accountLocker = new AccountLocker(
            $userId, "transfer-from-casino-to" . $userId
        );
        if ( ! $accountLocker->getLock()) {
            $accountLocker->release();

            return "对不起, 获取账户锁失败, 请稍后再试1!";
        }


        // 帐变
        db()->beginTransaction();
        try {
            // 1帐变处理
            $accountChange = new AccountChange();
            // 真实扣款

            $account = Account::findAccountByUserId($userId);
            if ( ! $account) {
                $accountLocker->release();
                db()->rollback();

                return "对不起, 账户信息不存在, 请稍后再试2!";
            }

            $params = [
                'user_id'              => $userId,
                'amount'               => $amount * config('game.main.money_unit'),
                'casino_platform_sign' => $mainGamePlat,
            ];

            $res = $accountChange->change(
                $account, 'casino_transfer_out', $params
            );
            if ($res !== true) {
                $accountLocker->release();
                db()->rollback();

                return $res;
            }


            // 2 api请求
            $paramArr = [
                'username'        => $agentName,
                'mainGamePlat'    => $mainGamePlat,
                'accountUserName' => $username,
                'price'           => $amount,
            ];

            $apiLog['params'] = json_encode($paramArr);       // 日志

            $paramStr    = http_build_query($paramArr);
            $paramEncode = Xcrypt::authCode(
                $paramStr, 'ENCODE', $config['casino_secret_key'],
                $config['casino_encryption_time']
            );

            $apiUrl             = $config['casino_gateway'] . '/transferTo?' . $paramStr . '&param=' . urlencode($paramEncode);
            $apiLog['call_url'] = $apiUrl;                   // 日志

            $data   = casino_request('GET', $apiUrl, [], '', 0, 0, 0);
            $dataDe = json_decode($data, 1);

            $apiLog['return_content'] = $data;  // 日志

            if ( ! empty($dataDe) && isset($dataDe['success'])
                && $dataDe['success']
            ) {
                $accountLocker->release();
                $this->saveLog($apiLog);
                db()->commit();

                return Help::returnApiJson('金额转出成功', 1, $dataDe);
            }

            $accountLocker->release();
            db()->rollback();
            $this->saveLog($apiLog);

            return Help::returnApiJson(
                $dataDe['message'], 0, ['reason_code' => 999]
            );
        } catch (\Exception $e) {
            $accountLocker->release();
            db()->rollback();
            $apiLog['return_content'] = json_encode(
                [$e->getMessage(), $e->getLine(), $e->getFile()]
            );  // 日志
            $this->saveLog($apiLog);

            return Help::returnApiJson(
                '对不起, 访问超时Err', 0, ['reason_code' => 999]
            );
        }
    }


    // @@@@@@@@@@@@@@@@@@@  后台接口  @@@@@@@@@@@@@@@@@@@

    /**
     * 获取游戏列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function callGameList()
    {
        $gameListM = new CasinoMethod();
        $plats     = CasinoPlatform::where('partner_sign', $this->partner->sign)->get();

        foreach ($plats as $plat) {
            $data       = $this->callCasinoList($plat->main_game_plat_code);
            $casinoList = json_decode($data, 1);
            if (empty($casinoList)) {
                return Help::returnApiJson('成功三方游戏列表', 1, $data);
            }

            unset($casinoList['data1']);
            if (!$gameListM->saveItemAll($casinoList, $this->partner->sign)) {
                return Help::returnApiJson('三方游戏列表失败', 0, []);
            }
        }
        return Help::returnApiJson('成功三方游戏列表', 1, []);
    }

    /**
     * 获取平台列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function seriesLists()
    {
        $config    = $this->getConfig();
        $agentName = $config['casino_merchant'];

        $platformM = new CasinoPlatform();
        $paramArr  = [
            'username'       => $agentName,
            'main_game_plat' => 'pt',
        ];
        $paramArr = $this->convertUnderline($paramArr);

        $paramStr    = http_build_query($paramArr);
        $paramEncode = Xcrypt::authCode($paramStr, 'ENCODE', $config['casino_secret_key'], $config['casino_encryption_time']);

        $apiUrl = $config['casino_gateway'] . '/getGamePlat?' . $paramStr . '&param=' . urlencode($paramEncode);
        $data  = casino_request('GET', $apiUrl, [], '', 0, 0, 0);
        $dataD = json_decode($data, 1);

        $apiLog = [
            'api'            => 'getGamePlat',
            'params'         => json_encode($paramArr),
            'call_url'       => $apiUrl,
            'ip'             => real_ip(),
            'return_content' => $data,
            'partner_sign'   => $this->partner->sign,
        ];

        if (empty($dataD)) {
            $this->saveLog($apiLog);
            return Help::returnApiJson('失败获取平台1', 1, [$data]);
        }
        unset($dataD['data1']);
        if ($a = $platformM->saveItemAll($dataD, $this->partner->sign)) {
            $this->saveLog($apiLog);
            return Help::returnApiJson('成功获取平台2', 1, [$a]);
        }
        $this->saveLog($apiLog);
        return Help::returnApiJson('失败获取平台', 0, []);
    }


    /**
     * 获取游戏列表
     * @param  string  $plat  平台.
     *
     * @return string
     */
    public function callCasinoList(string $plat)
    {
        $config    = $this->getConfig();
        $agentName = $config['casino_merchant'];

        $paramArr = [
            'username'     => $agentName,
            'mainGamePlat' => $plat,
        ];
        $paramStr            = http_build_query($paramArr);
        $paramEncode = Xcrypt::authCode($paramStr, 'ENCODE', $config['casino_secret_key'], $config['casino_encryption_time']);

        $apiUrl = $config['casino_gateway'] . '/getGameList?' . $paramStr . '&param=' . urlencode($paramEncode);
        $data   = casino_request('GET', $apiUrl, [], '', 0, 0, 0);

        $apiLog = [
            'api'            => 'getGameList',
            'params'         => json_encode($paramArr),
            'call_url'       => $apiUrl,
            'return_content' => $data,
            'ip'             => real_ip(),
            'partner_sign'   => $this->partner->sign,
        ];



        $this->saveLog($apiLog);
        return $data;
    }

    public function getConfig()
    {

        $config = [
            'casino_secret_key'      => $this->getCasinoSecretKey() ?? '',
            'casino_gateway'         => $this->getCasinoGateWay() ?? '',
            'casino_merchant'        => $this->getCasinoMerchant() ?? '',
            'casino_encryption_time' => $this->getCasinoEncryptionTime() ?? 30,
        ];

        return $config;
    }

    public function saveLog(array $apiLog)
    {
        $this->model->saveItem($apiLog);
    }

    /**
     * 数据转换
     *
     * @param        $str
     * @param  bool  $ucfirst
     *
     * @return string
     */
    function convertUnderline($paramArr, $ucfirst = true)
    {
        foreach ($paramArr as $key => $str) {
            unset($paramArr[$key]);
            $key   = explode('_', $key);
            $ucKey = '';
            foreach ($key as $val) {
                $ucKey = $ucKey . ucfirst($val);
            }
            if ($ucfirst) {
                $paramArr[lcfirst($ucKey)] = $str;
            }
        }
        return $paramArr;
    }


    /** ====================== 数据设定 ====================== */

    // 获取地址
    public function getCasinoGateWay()
    {
        return partnerConfigure($this->partner->sign, "casino_gateway");
    }

    // 获取娱乐成用户名
    public function getCasinoMerchant()
    {
        return partnerConfigure($this->partner->sign, "casino_merchant");
    }

    // 获取安全key
    public function getCasinoSecretKey()
    {
        return partnerConfigure($this->partner->sign, "casino_secret_key");
    }

    // 获取安全key
    public function getCasinoEncryptionTime()
    {
        return partnerConfigure($this->partner->sign, "casino_encryption_time");
    }
}
