<?php namespace App\Console\Commands\User;

use App\Console\Commands\Command;

class Test extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'Test:statCode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "初始化用户的结算和销量报表";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [];
        for($i = 0;  $i <= 9; $i++) {
            for($j = 0;  $j <= 9; $j++) {
                for($k = 0;  $k <= 9; $k++) {
                    $data[] = $i + $j +$k;
                }
            }
        }

        $dd = $ds = $xd = $xs = $td = $tx = $red = $blue = $green = 0;
        $redArr    = [3,6,9,12,15,18,21,24];
        $blueArr   = [2,5,8,11,17,20,23,26];
        $greenArr  = [1,4,7,10,16,19,22,25];
        foreach ($data as $z) {
            if ($z > 13) {
                if ($z % 2 > 0 ) {
                    $dd ++;
                } else {
                    $ds ++;
                }
            } else {
                if ($z % 2 > 0 ) {
                    $xd ++;
                } else {
                    $xs ++;
                }
            }
            if ($z >= 22) {
                $td ++;
            }
            if ($z <= 5) {
                $tx ++;
            }

            if (in_array($z, $redArr)) {
                $red ++;
            }

            if (in_array($z, $blueArr)) {
                $blue ++;
            }

            if (in_array($z, $greenArr)) {
                $green ++;
            }
        }

        $this->info($dd);
        $this->info($ds);
        $this->info($xd);
        $this->info($xs);

        $this->info($red);
        $this->info($blue);
        $this->info($green);
    }

}
