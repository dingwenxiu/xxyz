<?php namespace App\Lib\Game\Method\Pcdd\BZ;

use App\Lib\Game\Method\Pcdd\Base;

// 包子
class BAOZI extends Base
{
    public $totalCount  = 10;

    public static $filter = [
        'b' => "豹子",
    ];

    // 供测试用 生成随机投注
    public function randomCodes()
    {

        $code = "b";
        return $code;
    }

    // 格式解析
    public function codeChange($codes)
    {
        return strtr($codes, self::$filter);
    }

    public function regexp($sCode)
    {
        if ($sCode !== 'b') {
            return false;
        }
        return true;
    }

    public function count($sCodes)
    {
        return 1;
    }

    public function bingoCode(Array $numbers)
    {
        return [];
    }

    // 判定中奖
    public function assertLevel($levelId, $sCodes, Array $openNumbers)
    {
        if ($openNumbers[0] == $openNumbers[1] && $openNumbers[1] == $openNumbers[2]) {
            return true;
        }
        return false;
    }
}
