<?php namespace App\Console\Commands\Queue;

use App\Console\Commands\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;

class Loop extends Command {

    protected $signature = 'queue:loop {num} {--queue=} {--tries=} {--timeout=} {--sleep=}';

    protected $description = 'queue进程守护';

    public function handle()
    {
        $queue = $this->option('queue');
        $tries = intval($this->option('tries'));
        $timeout = $this->option('timeout');
        $sleep = $this->option('sleep');

        $num = $this->argument('num');

        $phpbin = config("command.phpbin");
        $root = app_path()."/..";

        if($num <= 0) $num = 0; //为0kill当前相关进程

        //$cmd="artisan queue:work --queue={$queue} --tries={$tries} --sleep=1";
        $exe_cmd = $cmd ="artisan queue:work --queue={$queue}";

        if($tries>0 && $tries< 100) {
            $exe_cmd.=" --tries={$tries}";
        }

        if(!is_null($sleep)) {
            $exe_cmd.=" --sleep={$sleep}";
        }

        if(!is_null($timeout)) {
            $exe_cmd.=" --timeout={$timeout}";
        }

        //找到正在运行的进程
//        $process = new Process("ps -w -eo pid,command | grep {$phpbin} | grep -e '{$cmd}' | grep -v grep");
//        $process->run();
//        $buffer=$process->getOutput();
        $buffer = `ps -w -eo pid,command | grep -e '{$phpbin}' | grep -e '{$cmd}' | grep -v grep`;

        $procs  = array_filter(explode("\n",$buffer));

        $n = $num-count($procs);
        if($n > 0){
            //进程不够 新增进程
            //$exec="{$phpbin} ".$root."/{$cmd} > /dev/null 2>&1 & ";
            for($i = 0; $i < $n; $i ++){
                //with(new Process($exec))->run();
                $temp=`{$phpbin} {$root}/{$exe_cmd} > /dev/null 2>&1 & `;
            }
            $this->info("add queue work {$n} proc");
        } elseif($n< 0){
            //进程过多 杀掉一些进程
            $curProcPids = [];
            foreach ($procs as $p) {
                $a = explode(' ', trim($p), 2);
                $curProcPids[] = intval($a[0]);
            }
            $n=abs($n);
            $needKill = $n;
            foreach ($curProcPids as $pid) {
                //$exec="kill $pid > /dev/null  2>&1 & ";
                //with(new Process($exec))->run();
                $temp = `kill {$pid} > /dev/null  2>&1 & `;
                if (--$needKill <= 0) {
                    break;
                }
            }
            $this->info("reduce queue work {$n} proc");
        }else{
            $this->info("nothing to do");
        }

    }

}
