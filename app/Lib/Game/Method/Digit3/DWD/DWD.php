<?php namespace App\Lib\Game\Method\Digit3\DWD;

use App\Lib\Game\Method\Digit3\BaseDWD;
use App\Lib\Game\Method\Ssc\Base;

class DWD extends Base
{
    public $allCount = 10;

    public static $filterArr = array(
        0 => 1,
        1 => 1,
        2 => 1,
        3 => 1,
        4 => 1,
        5 => 1,
        6 => 1,
        7 => 1,
        8 => 1,
        9 => 1
    );

    use BaseDWD;
}
