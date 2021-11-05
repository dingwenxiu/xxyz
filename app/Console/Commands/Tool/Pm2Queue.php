<?php namespace App\Console\Commands\Tool;

use App\Console\Commands\Command;
use Symfony\Component\Process\Process;

class Pm2Queue extends Command {

    protected $signature = 'tool:pm2Queue {action} {--redis=}';
    protected $description = 'gen 生成配置文件,stop:停止,start:启动, restart:重启';

	public function handle()
	{
        $path       = __DIR__ . '/../Runtime/';
        $pm2_tpl    = __DIR__ . '/tpl/pm2.queue.tpl.php';

        $gen_pm2_path = $path . 'pm2.queue.json';

        $action = $this->argument('action');

        if($action == 'stop' || $action == 'end') {
            $this->stop($gen_pm2_path);
        } elseif ($action == 'gen') {
            $this->gen($pm2_tpl, $path, $gen_pm2_path);
        } elseif ($action == 'run' || $action == 'start') {
            $this->gen($pm2_tpl, $path, $gen_pm2_path);
            $this->start($gen_pm2_path);
        } elseif ($action == 'restart') {
            $this->stop($gen_pm2_path);
            $this->gen($pm2_tpl, $path, $gen_pm2_path);
            $this->start($gen_pm2_path);
        }

        $this->info($action . " end!");
	}

	// 开始
	public function start($gen_pm2_path) {
        $this->info("开启 queue 脚本 ......");

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
        $this->info("结束 queue 脚本 ......");

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
     * @param $path
     * @param $gen_pm2_path
     * @return bool
     */
    public function gen($pm2Tpl, $path, $gen_pm2_path) {
        $this->info("生成 queue的配置文件!");

        $queueConfig    = config("command.queue");
        $string = include($pm2Tpl);
        @file_put_contents($gen_pm2_path, $string);
    }


}
