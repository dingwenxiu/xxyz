<?php namespace App\Console\Commands\Queue;
use Symfony\Component\Console\Input\InputArgument;

//修复retry的bug
class RetryCommand extends \Illuminate\Queue\Console\RetryCommand {

    protected $signature = 'queue:retry {id}';

    protected $description = 'Retry a failed queue job  id or all';

    public function handle()
    {
        $_id = $this->argument('id');

        if($_id=='all'){
            $ids = db()->table(config('queue.failed.table'))->pluck('id');
        }else{
            $ids=(array) $_id;
        }

        foreach($ids as $id){
            $this->process($id);
        }
    }

    public function process($id)
    {
        $failed = $this->laravel['queue.failer']->find($id);

        if (! is_null($failed)) {
            $failed = (object) $failed;

            $failed->payload = $this->resetAttempts($failed->payload);

            $this->laravel['queue']->connection($failed->connection)
                ->pushRaw($failed->payload, $failed->queue);

            $this->laravel['queue.failer']->forget($failed->id);

            $this->info("The failed job [{$id}] has been pushed back onto the queue!");
        } else {
            $this->error("No failed job matches the given ID [{$id}].");
        }
    }

}