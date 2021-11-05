<?php namespace App\Console\Commands\Tool;

use App\Console\Commands\Command;
use Symfony\Component\Process\Process;

class Pm2CasinoRecord extends Command {

    protected $signature = 'tool:pm2CasinoRecord {action}';
    protected $description = 'gen 生成配置文件,stop:停止,start:启动, restart:重启';

	public function handle()
	{
        $path       = __DIR__ . '/../Runtime/';
        $pm2_tpl    = __DIR__ . '/tpl/pm2.casino.record.tpl.php';

        $gen_pm2_path = $path . 'pm2.casino.record.json';

        $action = $this->argument('action');

        if($action == 'stop' || $action == 'end') {
            $this->stop($gen_pm2_path);
        } elseif ($action == 'gen') {
            $this->gen($pm2_tpl, $path, $gen_pm2_path);
        } elseif ($action == 'run' || $action == 'start') {
            if ($this->check()) {
                $this->info("运行中");
                return true;
            }
            $this->gen($pm2_tpl, $path, $gen_pm2_path);
            $this->start($gen_pm2_path);
        } elseif ($action == 'restart') {
            if ($this->check()) {
                $this->info("运行中");
                return true;
            }

            $this->stop($gen_pm2_path);
            $this->gen($pm2_tpl, $path, $gen_pm2_path);
            $this->start($gen_pm2_path);
        }

        $this->info($action . " end!");
	}

	// 开始
	public function start($gen_pm2_path) {
        $this->info("开启 casino.record 脚本 ......");

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
        $this->info("结束 casino.record 脚本 ......");

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
        $this->info("生成 casino.record 的配置文件!");
        $string = include($pm2Tpl);
        @file_put_contents($gen_pm2_path, $string);

        return true;
    }


    // 检测是否存在
    public function check() {
        $redisBin   = "casino:CasinoRecord";
        $process    = `ps -w -eo pid,command | grep -e '{$redisBin}' | grep -v grep`;
        $process    = array_filter(explode("\n", $process));

        if (count($process) > 0) {
            var_dump($process);
            return true;
        }

        return false;
    }

}
