<?php namespace App\Console\Commands\Tool;

use App\Console\Commands\Command;

use App\Lib\SystemCheck;
use Symfony\Component\Process\Process;

// 服务检测
class Check extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'tool:check {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "系统健康检查 all:所有 support:[memcached,redis,beanstalkd] web:[php-fpm,nginx] db:数据库链接 disk:系统盘空间";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');
        if(!in_array($type,['all', 'support', 'web', 'db', 'disk'])) {
            $this->info("参数不正确");
        }

        $err_count  = 0;
        $err_msg    = '';
        $detail     = '';
        $diskInfo   = "";

        if($type == 'all' || $type == 'support') {
            self::check_support($err_count,$err_msg,$detail);
        }

        if($type == 'all' || $type == 'web') {
            self::check_web($err_count,$err_msg,$detail);
        }

        if($type == 'all' || $type == 'db') {
            self::check_db($err_count,$err_msg,$detail);
        }

        if($type == 'all' || $type == 'disk') {
            $this->info("检测磁盘中....!");
            $diskInfo = SystemCheck::checkDisk();
            $headers = ['名称', '总容量', '已用容量', '可用容量', '使用百分比'];
            $this->table($headers,  $diskInfo);

        }

        if($err_count == 0) {
            $this->info("全部检查通过!");
            $this->info("反馈信息:\n{$detail}");
        }else{
            $this->info("错误数量{$err_count}");
            $this->error("错误信息:\n$err_msg");
        }

        return true;
    }

    public static  function check_support(&$err_count,&$err_msg,&$detail)
    {
        // redis
        $configs = config("database.redis");
        foreach($configs as $service_name=> $redis){
            if(!is_array($redis) || !isset($redis['port'])) continue;
            $exe = "redis-server";
            if(self::check_process_count($exe,$redis['port'])!=1){
                $err_count++;
                $err_msg .= "错误[{$err_count}]:redis-server[$service_name] 端口:{$redis['port']} 未检测到进程存在! \n";
            }
        }

        //memcached
        if(env('CACHE_DRIVER')=='memcached'){
            $configs = config("cache.stores");
            $exe = "memcached";

            foreach($configs as $service_name=>$store){
                if($store['driver']!='memcached') continue;
                if(empty($store['servers'])) continue;

                foreach($store['servers'] as $server){
                    if(!isset($server['host'])) continue;
                    $port=$server['port'];

                    if(self::check_process_count($exe,$port)!=1){
                        $err_count++;
                        $err_msg .= "错误[{$err_count}]:memcached[$service_name] 端口:{$port} 未检测到进程存在! \n";
                    }
                }
            }
        }

        // beanstalkd
        if(env('QUEUE_DRIVER')=='beanstalkd'){
            $configs = config("queue.connections.beanstalkd");
            $exe = "beanstalkd";
            $port='11300';
            if(isset($configs) && !empty($configs) ){
                if(self::check_process_count($exe,$port)!=1){
                    $err_count++;
                    $err_msg .= "错误[{$err_count}]:beanstalkd 端口:{$port} 未检测到进程存在! \n";
                }
            }
        }

        return true;
    }


    public static  function check_web(&$err_count,&$err_msg,&$detail)
    {
        //nginx
        $exe = "nginx";
        if(self::check_process_count($exe)<=0){
            $err_count++;
            $err_msg .= "错误[{$err_count}]:nginx 未检测到进程存在! \n";
        }

        //php-fpm
        $exe = "php-fpm";
        if(self::check_process_count($exe)<=0){
            $err_count++;
            $err_msg .= "错误[{$err_count}]:php-fpm 未检测到进程存在! \n";
        }

        return true;
    }

    public static  function check_db(&$err_count,&$err_msg,&$detail)
    {
        try{
            if(! db()->getDatabaseName())
            {
                $err_count++;
                $err_msg .= "错误[{$err_count}]:mysql[主] 无法连接! \n";
            }

            if(! db('slave')->getDatabaseName())
            {
                $err_count++;
                $err_msg .= "错误[{$err_count}]:mysql[从] 无法连接! \n";
            }

        }catch(\Exception $e){
            logger()->error($e);
        }

        return true;
    }

    // 检测硬盘
    public static  function checkDisk() {
        $disks      = [];
        $process    = new Process("df -k");
        $process->run();
        $buffer     =   $process->getOutput();
        $_disks     =   array_filter(preg_split("[\r|\n]", trim($buffer)));
        array_shift($_disks);
        foreach($_disks as $disk){
            $temp   =   array_values(array_filter(explode(" ",  $disk)));
            if(count($temp) < 5) {
                continue;
            }

            if(strpos($temp[4],'%') === false) {
                continue;
            }

            $disks[] = [
                'disk'  => $temp[0],
                'size'  => self::byte_format($temp[1]),
                'used'  => self::byte_format($temp[2]),
                'avail' => self::byte_format($temp[3]),
                'use%'  => $temp[4],
            ];
        }

        return $disks;
    }
}
