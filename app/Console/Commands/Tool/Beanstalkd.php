<?php namespace App\Console\Commands\Tool;

use App\Console\Commands\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

class Beanstalkd extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'tool:beanstalkd {action} {--beanstalkd=}';

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
        $path = __DIR__.'/../Runtime/';
        $pm2_tpl = __DIR__.'/tpl/pm2.beanstalkd.tpl.php';

        $gen_pm2_path = $path.'pm2.beanstalkd.json';

        $action = $this->argument('action');

        $stop = function () use ($gen_pm2_path) {
            $this->info("stoping ......");

            $process = new Process("pm2 stop {$gen_pm2_path} & pm2 delete {$gen_pm2_path}");
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    $this->error($buffer);
                } else {
                    $this->info($buffer);
                }
            });
        };

        $gen=function() use($pm2_tpl,$path,$gen_pm2_path) {
            $this->info("gen file ......");

            //生成pm2控制文件
            $server = $this->option('beanstalkd');
            if(!$server){
                //在$PATH中存在
                $server='beanstalkd';
            }
            $string = include($pm2_tpl);
            @file_put_contents($gen_pm2_path,$string);
        };

        $start = function() use($gen_pm2_path) {
            $this->info("starting ......");

            $process = new Process("pm2 start {$gen_pm2_path}");
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    $this->error($buffer);
                } else {
                    $this->info($buffer);
                }
            });
        };

        if($action=='stop' || $action=='end') {
            if ($this->confirm('Do you wish to stop all beanstalkd from this? [yes|no]')) {
                $stop();
            }
        }elseif($action=='gen'){
            if ($this->confirm('Do you wish to gen beanstalkd from this? [yes|no]'))
            {
                $gen();
            }
        }elseif($action=='run' || $action=='start'){
            if ($this->confirm('Do you wish to refresh all beanstalkd from this? [yes|no]'))
            {
                $gen();
                $start();
            }
        }elseif($action=='restart'){
            if ($this->confirm('Do you wish to refresh all beanstalkd from this? [yes|no]'))
            {
                $stop();
                $gen();
                $start();
            }
        }

        $this->info($action . " end!");
    }

}
