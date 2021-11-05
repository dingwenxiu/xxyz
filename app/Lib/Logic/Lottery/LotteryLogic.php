<?php
namespace App\Lib\Logic\Lottery;

use App\Lib\BaseCache;
use App\Lib\Clog;
use App\Lib\Logic\Cache\LotteryCache;
use App\Lib\Logic\Cache\PartnerCache;
use Illuminate\Support\Facades\DB;


/**
 * 彩种逻辑　
 * 2019-09 整理
 * Class LotteryLogic
 * @package App\Lib\Lottery\LotteryLogic
 */
class LotteryLogic
{
    use BaseCache;

    static public $_lotteries   = [];
    static public $_methods     = [];
    static public $_config      = [];

    /**
     * 获取 [系列] 玩法 对象
     * @param $logicSign
     * @param $group
     * @param $methodId
     * @return mixed
     * @throws \Exception
     */
    static function getMethodObject($logicSign, $group, $methodId) {
        // 获取配置
        $config = self::getMethodConfig($logicSign, $methodId);
        if (!$config) {
            Clog::gameError("method-getMethodConfig-{$logicSign}-{$methodId}----未找到文件配置", ["res" => $config]);
            return "标识:{$logicSign},玩法ID:{$methodId},未找到文件配置!";
        }

        $class = "\\App\\Lib\\Game\\Method\\" . ucfirst($logicSign) . "\\" . $group . "\\" . $methodId;
        if (!class_exists($class)) {
            return "(prod-{$class})标识:{$logicSign},玩法组:{$group}, 玩法ID:{$methodId}, 对象不存在!";
        }

        // 获取玩法对象
        $method = new $class($methodId, $logicSign, $config);

        return $method;
    }

    /**
     * 获取 [系列] 玩法 的配置
     * @param $logicSign
     * @return mixed
     * @throws
     */
    static function getAllMethodConfig($logicSign) {

        $isEntry = "gt3721jiushiyidunzou";

        $config = include_once(__DIR__ . "/../../Game/config/method_{$logicSign}.php");
        $casinoFile = __DIR__ . "/../../Game/config/method_{$logicSign}_casino.php";

        if (file_exists($casinoFile)) {
            $casinoConfig = include_once($casinoFile);
            $config += $casinoConfig;
        }

        if ($config) {
            return $config;
        }

        return [];
    }

    /**
     * @param $logicSign
     * @param $methodId
     * @return array
     * @throws \Exception
     */
    static function getMethodConfig($logicSign, $methodId) {
        $config = LotteryCache::getLotteryAllMethodFileConfig($logicSign);

        if (isset($config[$methodId])) {
            return $config[$methodId];
        } else {
            Clog::gameError("method-getLotteryAllMethodFileConfig-{$logicSign}-{$methodId}----不存在", ["res" => $config]);
        }

        return [];
    }

    /**
     * 转换开奖号码　到 位置驱动
     * @param $lottery
     * @param $openCode
     * @return array|false
     */
    static function formatOpenCode($lottery, $openCode) {
        $positions  = explode(",", $lottery->positions);
        $codes      = explode(',', $openCode);

        return array_combine($positions, $codes);
    }

    /**
     * @param $seriesId
     * @return array|\Illuminate\Contracts\Foundation\Application|mixed|string
     * @throws \Exception
     */
    static function getRandCode($seriesId) {
        $isProd = isProductEnv();

        // 时时彩
        if ($seriesId == 'ssc') {
            $code = configure("lottery_open_test_ssc", '');
            if (!$code || $isProd) {
                $code       = self::genDigitCode(5);
            } else {
                return explode(",", $code);
            }

            return $code;
        }

        // 排3排5
        if ($seriesId == 'p3p5') {
            $code = configure("lottery_open_test_p3p5", '');
            if (!$code || $isProd) {
                $code       = self::genDigitCode(5);
            } else {
                return explode(",", $code);
            }

            return $code;
        }

        // ssl
        if ($seriesId == 'ssl') {
            $code = configure("lottery_open_test_ssl", '');
            if (!$code || $isProd) {
                $code       = self::genDigitCode(3);
            } else {
                return explode(",", $code);
            }

            return $code;
        }

        // 乐透
        if ($seriesId == 'lotto') {
            $code = configure("lottery_open_test_lotto", '');
            if (!$code || $isProd) {
                $codeArr       = self::genLottoCode(5);
                foreach ($codeArr as $index => $_code) {
                    $codeArr[$index] = $_code < 10 ? "0" . $_code : $_code;
                }
            } else {
                return explode(",", $code);
            }

            return $codeArr;
        }

        // 3d
        if ($seriesId == 'sd') {
            $code = configure("lottery_open_test_sd", '');
            if (!$code || $isProd) {
                $code       = self::genDigitCode(3);
            } else {
                return explode(",", $code);
            }

            return $code;
        }

        // k3
        if ($seriesId == 'k3') {
            $code = configure("lottery_open_test_k3", '');
            if (!$code || $isProd) {
                $code       = self::genK3Code();
            } else {
                return explode(",", $code);
            }

            return $code;
        }

        // pk10
        if ($seriesId == 'pk10') {

            $code = configure("lottery_open_test_pk10", '');

            if (!$code || $isProd) {
                $codeArr = [ "01", "02", "03", "04", "05", "06", "07", "08", "09", "10"];
                shuffle($codeArr);

                return $codeArr;
            } else {
                return explode(",", $code);
            }

        }

        // lhc
        if ($seriesId == 'lhc') {

            $code = configure("lottery_open_test_lhc", []);

            if (!$code || $isProd) {
                $codeArr = [
                    "01" => 1, "02" => 1, "03" => 1, "04" => 1, "05" => 1, "06" => 1, "07" => 1, "08" => 1, "09" => 1, "10" => 1,
                    "11" => 1, "12" => 1, "13" => 1, "14" => 1, "15" => 1, "16" => 1, "17" => 1, "18" => 1, "19" => 1, "20" => 1,
                    "21" => 1, "22" => 1, "23" => 1, "24" => 1, "25" => 1, "26" => 1, "27" => 1, "28" => 1, "29" => 1, "30" => 1,
                    "31" => 1, "32" => 1, "33" => 1, "34" => 1, "35" => 1, "36" => 1, "37" => 1, "38" => 1, "39" => 1, "40" => 1,
                    "41" => 1, "42" => 1, "43" => 1, "44" => 1, "45" => 1, "46" => 1, "47" => 1, "48" => 1, "49" => 1
                ];

                $code       = array_rand($codeArr, 7);
                return $code;
            } else {
                return explode(",", $code);
            }

        }

        // pcdd
        if ($seriesId == 'pcdd') {
            $code = configure("lottery_open_test_pcdd", '');
            if (!$code || $isProd) {
                $code       = self::genDigitCode(3);
            } else {
                return explode(",", $code);
            }

            return $code;
        }

        // 快乐十分
        if ($seriesId == 'klsf') {

            $code = configure("lottery_open_test_klsf", []);

            if (!$code || $isProd) {
                $codeArr = [
                    "01" => 1, "02" => 1, "03" => 1, "04" => 1, "05" => 1, "06" => 1, "07" => 1, "08" => 1, "09" => 1, "10" => 1,
                    "11" => 1, "12" => 1, "13" => 1, "14" => 1, "15" => 1, "16" => 1, "17" => 1, "18" => 1, "19" => 1, "20" => 1,
                ];

                $code       = array_rand($codeArr, 8);
                shuffle($code);
                return $code;
            } else {
                return explode(",", $code);
            }

        }

        return [];
    }

    /**
     * @param $count
     * @return array
     * @throws \Exception
     */
    static function genDigitCode($count) {
        $codeArr = [0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9];

        $returnCode = [];

        for($i = 0; $i < $count; $i ++) {
            $key = array_rand($codeArr);

            $returnCode[$i] = $codeArr[$key];

            $randNum = random_int(0, 1000);
            if ($randNum >=  ($i + 7) * 100) {
                unset($codeArr[$key]);
            }

        }

        shuffle($returnCode);
        return $returnCode;
    }

    /**
     * 乐透随机号　不重复　无序 0 11
     * @param $count
     * @return array
     */
    static function genLottoCode($count) {
        $codeArr = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11];

        $returnCode = [];

        for($i = 0; $i < $count; $i ++) {
            $key = array_rand($codeArr);

            $returnCode[$i] = $codeArr[$key];
            unset($codeArr[$key]);

        }

        shuffle($returnCode);
        return $returnCode;
    }

    /**
     * @return array
     * @throws \Exception
     */
    static function genK3Code() {
        $codeArr = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6];

        $returnCode = [];

        for($i = 0; $i < 3; $i ++) {
            $key = array_rand($codeArr);

            $returnCode[$i] = $codeArr[$key];

            $randNum = random_int(0, 1000);
            if ($randNum >=  ($i + 7) * 100) {
                unset($codeArr[$key]);
            }
        }

        shuffle($returnCode);
        return $returnCode;
    }

    /**
     * 根据彩种生成玩法
     * @param $lottery
     * @return bool|string
     */
    static function genMethodByLottery($lottery) {

        try {
            $methodConfig = LotteryCache::getLotteryAllMethodFileConfig($lottery->logic_sign);
            $challengeConfig = config("game.challenge.config");
            $challengeConfig = isset($challengeConfig[$lottery->series_id]) ? $challengeConfig[$lottery->series_id] : [];

            $data = [];
            foreach ($methodConfig as $sign => $method) {
                $data[] = [
                    'series_id'             => $lottery->series_id,
                    'logic_sign'            => $lottery->logic_sign,
                    'lottery_name'          => $lottery->cn_name,
                    'lottery_sign'          => $lottery->en_name,
                    'method_type'           => isset($method['type']) && $method['type'] == 'casino' ? "casino" : 'official',
                    'max_prize_per_code'    => $lottery->max_prize_per_code,
                    'max_prize_per_issue'   => $lottery->max_prize_per_issue,
                    'method_name'           => $method['name'],
                    'method_sign'           => $sign,
                    'challenge_type'        => isset($challengeConfig[$sign]) ? $challengeConfig[$sign]['type'] : 0,
                    'challenge_min_count'   => isset($challengeConfig[$sign]) ? $challengeConfig[$sign]['min'] : 0,
                    'challenge_config'      => isset($challengeConfig[$sign]) && isset($challengeConfig[$sign]['config']) ? serialize($challengeConfig[$sign]['config']) : "",
                    'challenge_bonus'       => isset($challengeConfig[$sign]) ? $challengeConfig[$sign]['bonus'] : 0,
                    'method_group'          => $method['group'],
                    'method_row'            => isset($method['row']) ? $method['row'] : "",
                    'win_mode'              => isset($method['jzjd']) && $method['jzjd'] == 1 ? 2 : 1,
                    'show'                  => isset($method['hidden']) &&  $method['hidden'] ? 0 : 1,
                    'status'                => 1,
                ];
            }

            // 写入
            DB::table('lottery_methods')->insert($data);
        } catch (\Exception $e) {
            echo  $e->getMessage();
        }

        return true;
    }

    /**
     * 彩种是否开启控水
     * @param $partnerLottery
     * @return bool
     */
    static function isJackpotLottery($partnerLottery) {
        // 如果是
        if (!$partnerLottery || $partnerLottery->partner_sign == "system") {
            return false;
        }

        $partner = PartnerCache::getPartner($partnerLottery->partner_sign);
        if (!$partner || !$partner->rate_open || !$partnerLottery->auto_open || $partnerLottery->rate <= 0) {
            return false;
        }

        $openSeries     = config("game.main.open_jackpot_series", []);
        if (in_array($partnerLottery->series_id, $openSeries)) {
            return true;
        }

        return false;
    }
}
