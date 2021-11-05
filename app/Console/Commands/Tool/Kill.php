<?php namespace App\Console\Commands\Tool;

use App\Console\Commands\Command;

// 杀掉所有的　queue
class Kill extends Command {

    protected $signature = 'tool:kill {sign}';

    protected $description = "系统健康检查 all:所有 support:[memcached,redis,beanstalkd] web:[php-fpm,nginx] db:数据库链接 disk:系统盘空间";

    public function handle()
    {
        $type = $this->argument('sign');

        $buffer = `ps -ef | grep -e '{$type}' | grep -e 'queue' | grep -v grep | awk '{print $2}' | xargs sudo kill -9`;

        $procs  = array_filter(explode("\n", $buffer));
        $this->info(json_encode($buffer));
        $this->info(json_encode($procs));
        return true;
    }

}
