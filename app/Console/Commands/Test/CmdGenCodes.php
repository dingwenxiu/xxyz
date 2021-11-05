<?php namespace App\Console\Commands\Test;

use App\Console\Commands\Command;



class CmdGenCodes extends Command {

    protected $signature = 'test:genCodes';

    protected $description = "生成投注号码!";

    public function handle()
    {
        $string = "";
        for($i = 10000;$i < 30000; $i ++) {
            $string .= $i . ",";
        }

        $this->info($string);
    }

}
