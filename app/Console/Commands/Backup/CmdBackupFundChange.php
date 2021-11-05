<?php namespace App\Console\Commands\Backup;

use App\Console\Commands\Command;
use App\Models\Account\AccountChangeReport;

class CmdBackupFundChange extends Command {

    protected $signature = 'backup:fundChangeReport';

    protected $description = "备份帐变数据!";

    public function handle ()
    {
        $keepDataDay    = configure("system_fund_change_data_keep_day", 3);
        $endDay         = date("Ymd", time() - $keepDataDay * 86400);

        //　总数据
        $totalCount = AccountChangeReport::where('day', '<=', $endDay)->count();

        if ($totalCount <= 0) {
            $this->info("对不起, 没有可移动的数据!");
            return true;
        }

        $this->info("备份帐变数据-start: 总条数{$totalCount}!");

        $pageSize   = 3000;
        $totalPage  = ceil($totalCount / $pageSize);

        $i = 0;
        do  {
            $offset = $i * $pageSize;

            $query  = AccountChangeReport::where('day','<=', $endDay)->skip($offset)->take($pageSize)->orderBy('id','asc');

            $bindings       = $query->getBindings();
            $insertQuery    = 'INSERT INTO `account_change_report_history` ' . $query->toSql();
            try {
                // 备份
                $ret = db()->insert($insertQuery, $bindings);
                if(!$ret) {
                    return true;
                }

                // 删除
                $query->delete();

            } catch (\Exception $e) {
                $this->info($e->getMessage());
            }


            $i ++;
        } while ($i <= ($totalPage + 1));

        $this->info("备份帐变数据-end!");
        return true;
    }

}
