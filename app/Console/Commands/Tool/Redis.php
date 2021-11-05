<?php namespace App\Console\Commands\Tool;

use App\Console\Commands\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

class Redis extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
    protected $signature = 'tool:redis {action} {--redis=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
    protected $description = 'gen 生成配置文件,stop:停止,start:启动, restart:重启';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $path       = __DIR__ . '/../Runtime/';
        $pm2_tpl    = __DIR__ . '/tpl/pm2.redis.tpl.php';
        $config_tpl = __DIR__ . '/tpl/redis.config.tpl.php';

        $gen_pm2_path = $path . 'pm2.redis.json';

        $action = $this->argument('action');

        if($action == 'stop' || $action == 'end') {
            if ($this->confirm('Do you wish to stop all redis-server from this? [yes|no]')) {
                $this->stop($gen_pm2_path);
            }
        } elseif ($action == 'gen') {
            if ($this->confirm('Do you wish to gen redis from this? [yes|no]'))
            {
                $this->gen($pm2_tpl, $config_tpl, $path, $gen_pm2_path);
            }
        } elseif ($action == 'run' || $action == 'start') {
            if ($this->confirm('Do you wish to refresh all redis-server from this? [yes|no]'))
            {
                $this->check();
                $this->gen($pm2_tpl, $config_tpl, $path, $gen_pm2_path);
                $this->start($gen_pm2_path);
            }
        } elseif ($action == 'restart') {
            if ($this->confirm('Do you wish to refresh all redis-server from this? [yes|no]'))
            {
                $this->check();
                $this->stop($gen_pm2_path);
                $this->gen($pm2_tpl,$config_tpl,$path,$gen_pm2_path);
                $this->start($gen_pm2_path);
            }
        }

        $this->info($action . " end!");
	}

	// 检测是否存在
	public function check() {
	    $redisBin   = "redis-server";
        $process    = `ps -w -eo pid,command | grep -e '{$redisBin}' | grep -v grep`;
        $process    = array_filter(explode("\n", $process));

        $ids = [];
        if (count($process) > 0) {
            foreach ($process as $item) {
                $this->info("已存在Redis进程-" . $item);
                $a      = explode(' ', trim($item), 2);
                $ids[]  = intval($a[0]);
            }

            if ($this->confirm('已经存在redis的进程, 是否删除 [yes|no]')) {
                foreach ($ids as $_id) {
                    `kill {$_id} > /dev/null  2>&1 & `;
                    $this->info("删除进程-{$_id}!");
                }
            }
        }

        return true;
    }

	// 开始
	public function start($gen_pm2_path) {
        $this->info("开启Redis ......");

        $process = new Process("pm2 start {$gen_pm2_path}");
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->error($buffer);
            } else {
                $this->info($buffer);
            }
        });
    }

    // 结束redis
    public function stop($gen_pm2_path) {
        $this->info("结束Redis ......");

        $process = new Process("pm2 stop {$gen_pm2_path} & pm2 delete {$gen_pm2_path}");
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->error($buffer);
            } else {
                $this->info($buffer);
            }
        });
    }

    /**
     * 生成
     * @param $pm2Tpl
     * @param $configTpl
     * @param $path
     * @param $gen_pm2_path
     * @return bool
     */
    public function gen($pm2Tpl, $configTpl, $path, $gen_pm2_path) {
        $this->info("生成Redis的配置文件!");

        if(!file_exists($configTpl)) {
            $this->error('Redis 模板文件不存在!');
            return false;
        }

        $configs    = config("database.redis");
        $_confs     = [];

        // 生成redis配置文件
        foreach($configs as $key => $redis) {
            if(!is_array($redis) || !isset($redis['port'])) {
                continue;
            }

            // client配置去掉
            if ($key == 'client') {
                continue;
            }

            $logfile = storage_path() . "/redis/lottery_{$key}.log";

            $slaveConfig = '';
            if(isset($redis['slave_config'])) {
                $tmp = $configs[$redis['slave_config']];
                $slaveConfig = "slaveof ". $tmp['host']." ".$tmp['port'];
            }

            $string = include($configTpl);
            $conf   = $path . strtolower($key) . '.pm2.conf';
            @file_put_contents($conf, $string);

            $_confs[$key]=$conf;
        }

        // 生成pm2控制文件
        $server = $this->option('redis');
        if(!$server) {
            // 在$PATH中存在
            $server = 'redis-server';
        }

        $string = include($pm2Tpl);
        @file_put_contents($gen_pm2_path, $string);
    }


}
