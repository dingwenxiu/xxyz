<?php namespace App\Console\Commands\Backup;

use App\Console\Commands\Command;
use App\Models\Game\Record;


class CmdBackupGameRecord extends Command {

    protected $signature = 'backup:backupGameRecord';

    protected $description = "备份抢包历史记录!";

    public function handle ()
    {
        $keepDataDay    = configure("system_record_data_keep_day", 3);
        $endDay         = date("Ymd", time() - $keepDataDay * 86400);

        //　总数据
        $totalCount = Record::where('day', '<=', $endDay)->count();

        if ($totalCount <= 0) {
            $this->info("对不起, 没有可移动的数据!");
            return true;
        }

        $this->info("备份抢包数据-start: 总条数{$totalCount}!");

        $pageSize   = 3000;
        $totalPage  = ceil($totalCount / $pageSize);

        $i = 0;
        do  {
            $offset = $i * $pageSize;

            $query  = Record::where('day','<=', $endDay)->skip($offset)->take($pageSize)->orderBy('id','asc');

            $bindings       = $query->getBindings();
            $insertQuery    = 'INSERT INTO `game_project_records_history` ' . $query->toSql();
            try {
                // 备份
                $ret = db()->insert($insertQuery, $bindings);
                if(!$ret) {
                    $this->info("备份抢包数据-process:写入失败");
                    return true;
                }

                // 删除
                $query->delete();

            } catch (\Exception $e) {
                $this->info($e->getMessage());
            }

            $finishedCount = $pageSize * ($i + 1);
            $this->info("备份抢包数据-process: 完成{$finishedCount}条!");

            $i ++;
        } while ($i <= ($totalPage + 1));

        $this->info("备份抢包数据-end!");
        return true;
    }

}
