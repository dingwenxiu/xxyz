<?php namespace App\Console\Commands\Queue;

//修复retry的bug
class RetryFixCommand extends RetryCommand {

    protected $signature = 'queue:retry_custom';

    protected $description = '根据Class执行失败的任务';

    public function handle()
    {
        $queue = 'medium';
        $limit = 100;

        $datas = db()->table(config('queue.failed.table'))->where('queue','=',$queue)->take($limit)->get();

        foreach($datas as $data){
            $json=json_decode($data['payload'],true);
            //$d=unserialize($json['data']['command']);
            print_r($json);
            exit;
            //echo $d->type."\n";
//            if($d->type=='medium'){
//                $this->process($data['id']);
//            }
        }
    }

    public function process($id)
    {
        $failed =(object) $this->laravel['queue.failer']->find($id);
        if ( ! is_null($failed))
        {
            $failed->payload = $this->resetAttempts($failed->payload);

            $this->laravel['queue']->connection($failed->connection)->pushRaw($failed->payload, $failed->queue);

            $this->laravel['queue.failer']->forget($failed->id);

            $this->info('The failed job has been pushed back onto the queue!');
        }
        else
        {
            $this->error('No failed job matches the given ID.');
        }
    }
}