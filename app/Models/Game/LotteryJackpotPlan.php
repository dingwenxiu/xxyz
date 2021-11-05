<?php

namespace App\Models\Game;

class LotteryJackpotPlan extends BaseGame
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'lottery_jackpot_plan';
    public $timestamps = false;

}
