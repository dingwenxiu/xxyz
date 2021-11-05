<?php namespace App\Jobs;

use App\Models\Game\LotteryIssue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class IssueOpen  implements ShouldQueue
{

    public $issueId;
    public $params  = [];

    public function __construct($issueId, $params = [])
    {
        $this->issueId  = $issueId;
        $this->params   = $params;
    }

    public function handle()
    {
        db()->reconnect();

        $issueId = $this->issueId;
        try {
            $issue  =   LotteryIssue::find($issueId);
            if(!$issue) {
                throw new \Exception("job-开奖:issue:" . $issueId." not exists!");
            }

            if($issue->decidetime > 0) {
               return true;
            }

            Artisan::call("issue:open", ['lotterySign' => $issue->lottery_sign]);

        } catch (\Exception $e) {
            logger()->error($e);
            throw $e;
        }

        return true;
    }
}
