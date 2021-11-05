<?php namespace App\Console\Commands;

use \App\Models\Crontab;

abstract class Command extends \Illuminate\Console\Command
{
    public function lock($sFileName = '', $sParameter = '')
    {
        return Crontab::lock($sFileName,$sParameter);
    }

    public function locking($sFileName = '', $sParameter = '')
    {
        return Crontab::locking($sFileName,$sParameter);
    }

    public function unlock($sFileName = '', $sParameter = '')
    {
        return Crontab::unlock($sFileName,$sParameter);
    }

    public function __construct()
    {
        //防止执行超时造成异常
        set_time_limit(0);
        parent::__construct();
    }
//    public function __destruct()
//    {
//        game_on_process_end();
//    }
}
