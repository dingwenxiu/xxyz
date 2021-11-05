<?php

$phpbin     = env("PHP_BIN_PATH", '/usr/bin/php-7.1');
$basepath   = __DIR__.'/../'; //根目录

$cron_path  = __DIR__.'/../storage/logs/queue/cron_';

$statSlotCount          = env("STAT_SLOT", 5);
$projectSlotCount       = env("PROJECT_SLOT", 5);
$traceSlotCount         = env("TRACE_SLOT", 5);
$commissionSlotCount    = env("COMMISSION_SLOT", 5);

$cronConfig =  array(
    'phpbin'        => $phpbin,
    'basepath'      => $basepath,
    'cron_path'     => $cron_path,

// 用 crontab 调度的进程
    'crontab' => array(
        /** ---- 一般cron ----- */
        'cron_gen_issue' => array(
            'name'      => '生成奖期',
            'cron'      => '1 6 * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:genIssue all",
            'logfile'   => $cron_path.'cron_gen_issue.log',
        ),

        'cron_withdraw_query' => array(
            'name'      => '提现轮询',
            'cron'      => '*/2 * * * *',
            'command'   => "{$phpbin} {$basepath}artisan withdraw:query",
            'logfile'   => $cron_path.'cron_withdraw_query.log',
        ),

        // 发放日工资
        'cron_send_salary'=>array(
            'name'      => '发放日工资',
            'cron'      => '1 1 * * *',
            'command'   => "{$phpbin} {$basepath}artisan player:send_salary last_day",
            'logfile'   => $cron_path.'send_salary.log',
        ),

        // 按日　玩家统计
        'cron_stat_5' => array(
            'name'      => '统计',
            'cron'      => '*/5 * * * *',
            'command'   => "{$phpbin} {$basepath}artisan stat:player last",
            'logfile'   => $cron_path.'cron_stat_player.log',
        ),

        // 按日　彩种统计
        'cron_stat_lottery'=>array(
            'name'      => '按日统计彩种',
            'cron'      => '30 0 * * *',
            'command'   => "{$phpbin} {$basepath}artisan stat:lottery last",
            'logfile'   => $cron_path.'cron_stat_lottery.log',
        ),

        // 按日　商户统计
        'cron_stat_partner'=>array(
            'name'      => '按日统计商户',
            'cron'      => '30 0 * * *',
            'command'   => "{$phpbin} {$basepath}artisan stat:partner last",
            'logfile'   => $cron_path.'cron_stat_partner.log',
        ),

        'cron_stat_gen_player'=>array(
            'name'      => '用户数据初始化',
            'cron'      => '10 3 * * *',
            'command'   => "{$phpbin} {$basepath}artisan stat:initPlayerDay next",
            'logfile'   => $cron_path.'gen_player_stat.log',
        ),

        /** ===================  备份脚本 ====================== */
        'cron_backup_change_report'=>array(
            'name'      => '帐变备份',
            'cron'      => '10 4 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:changeReport",
            'logfile'   => $cron_path.'change_report.log',
        ),

        'cron_backup_lottery_commission'=>array(
            'name'      => '返点备份',
            'cron'      => '30 4 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:lotteryCommissions",
            'logfile'   => $cron_path.'backup_commission.log',
        ),

        'cron_backup_project'=>array(
            'name'      => '注单备份',
            'cron'      => '50 4 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:project",
            'logfile'   => $cron_path.'backup_project.log',
        ),

        'cron_backup_issue'=>array(
            'name'      => '奖期备份',
            'cron'      => '1 5 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:lotteryIssues",
            'logfile'   => $cron_path.'backup_issue.log',
        ),

        'cron_backup_trace'=>array(
            'name'      => '追号备份',
            'cron'      => '5 5 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:lotteryTraces",
            'logfile'   => $cron_path.'backup_trace.log',
        ),


        'cron_backup_trace_detail'=>array(
            'name'      => '追号详情备份',
            'cron'      => '10 5 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:lotteryTracesDetail",
            'logfile'   => $cron_path.'backup_trace_detail.log',
        ),

        'cron_backup_partner_access'=>array(
            'name'      => '商户访问日志备份',
            'cron'      => '15 5 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:partnerAccessLog",
            'logfile'   => $cron_path.'backup_partner_access.log',
        ),

        'cron_backup_partner_behavior'=>array(
            'name'      => '商户访问行为备份',
            'cron'      => '20 5 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:partnerBehaviorLog",
            'logfile'   => $cron_path.'backup_partner_behavior.log',
        ),

        'cron_backup_user_player_ip'=>array(
            'name'      => '用户IP',
            'cron'      => '25 5 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:userPlayerIpLog",
            'logfile'   => $cron_path.'backup_user_player_ip.log',
        ),

        'cron_backup_user_player'=>array(
            'name'      => '用户日志',
            'cron'      => '40 5 * * *',
            'command'   => "{$phpbin} {$basepath}artisan backup:userPlayerLog",
            'logfile'   => $cron_path.'backup_user_player.log',
        ),

        'cron_casino_record'=>array(
            'name'      => '娱乐城订单拉取',
            'cron'      => '0 0 1 * *',
            'command'   => "{$phpbin} {$basepath}artisan casino:CasinoRecord",
            'logfile'   => $cron_path.'casino_record.log',
        ),

        'cron_activity_prize'=>array(
            'name'      => '活动奖金',
            'cron'      => '1 1 * * *',
            'command'   => "{$phpbin} {$basepath}artisan activity:ActivityPrize",
            'logfile'   => $cron_path.'activity_prize.log',
        ),

        'cron_player_balance'=>array(
            'name'      => '用户余额',
            'cron'      => '0 0 * * *',
            'command'   => "{$phpbin} {$basepath}artisan stat:playerBalance",
            'logfile'   => $cron_path.'player_balance.log',
        ),

        /** ===================  自动开奖 ====================== */

        // 自动录号 时时彩
        'cron_self_encode_ssc' => array(
            'name'      => '时时彩自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode ssc",
            'logfile'   => $cron_path.'self_encode_ssc.log',
        ),

        // 自动录号 乐透
        'cron_self_encode_lotto' => array(
            'name'      => '乐透自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode lotto",
            'logfile'   => $cron_path.'self_encode_lotto.log',
        ),

        // 自动录号 3D
        'cron_self_encode_sd' => array(
            'name'      => '3D自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode sd",
            'logfile'   => $cron_path.'self_encode_sd.log',
        ),

        // 自动录号 飞车
        'cron_self_encode_pk10' => array(
            'name'      => '飞车自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode pk10",
            'logfile'   => $cron_path.'self_encode_pk10.log',
        ),

        // 自动录号 快三
        'cron_self_encode_k3' => array(
            'name'      => '快三自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode k3",
            'logfile'   => $cron_path.'self_encode_k3.log',
        ),

        // 自动录号 六合彩
        'cron_self_encode_lhc' => array(
            'name'      => '六合彩自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode lhc",
            'logfile'   => $cron_path.'self_encode_lhc.log',
        ),

        // 自动录号 p3p5
        'cron_self_encode_p3p5' => array(
            'name'      => 'p3p5自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode p3p5",
            'logfile'   => $cron_path.'self_encode_p3p5.log',
        ),

        'cron_self_encode_pcdd' => array(
            'name'      => 'pcdd自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode pcdd",
            'logfile'   => $cron_path.'self_encode_pcdd.log',
        ),

        // 时时乐
        'cron_self_encode_ssl' => array(
            'name'      => '时时乐自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode ssl",
            'logfile'   => $cron_path.'self_encode_ssl.log',
        ),

        // 快乐十分
        'cron_self_encode_klsf' => array(
            'name'      => '快乐十分自动录号',
            'cron'      => '* * * * *',
            'command'   => "{$phpbin} {$basepath}artisan lottery:selfEncode klsf",
            'logfile'   => $cron_path.'self_encode_klsf.log',
        ),
    ),
);

    // queue
    $cronConfig['queue']['issue'] = [
        'name'      => '队列_issue',
        'index'     => 'issue',
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=issue", "--sleep=1", "--timeout=300"],
        'logfile'   => $cron_path . "queue_issue.log",
    ];

    $cronConfig['queue']['log'] = [
        'name'      => '队列_log',
        'index'     => 'log',
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=log", "--sleep=1", "--timeout=300"],
        'logfile'   => $cron_path . "queue_log.log",
    ];

    $cronConfig['queue']['common'] = [
        'name'      => '队列_common',
        'index'     => 'common',
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=common", "--sleep=1", "--timeout=300"],
        'logfile'   => $cron_path . "queue_common.log",
    ];

    $cronConfig['queue']['notify'] = [
        'name'      => '队列_notify',
        'index'     => 'notify',
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=notify", "--sleep=1", "--timeout=300"],
        'logfile'   => $cron_path . "queue_notify.log",
    ];

    $cronConfig['queue']['login_code'] = [
        'name'      => '队列_login_code',
        'index'     => 'login_code',
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=login_code", "--sleep=1", "--timeout=90"],
        'logfile'   => $cron_path . "queue_login_code.log",
    ];

    $cronConfig['queue']['jackpot'] = [
        'name'      => '队列_jackpot',
        'index'     => 'jackpot',
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=jackpot", "--sleep=1", "--timeout=300"],
        'logfile'   => $cron_path . "queue_jackpot.log",
    ];

    $cronConfig['queue']['self_open'] = [
        'name'      => '队列_self_open',
        'index'     => 'self_open',
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=self_open", "--sleep=1", "--timeout=300"],
        'logfile'   => $cron_path . "queue_self_open.log",
    ];

// trace
for($i = 0; $i < $traceSlotCount; $i++) {
    $cronConfig['trace']['queue_trace_' . $i] = [
        'name'      => '追号' . $i,
        'index'     => $i,
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=trace_{$i}", "--sleep=2", "--timeout=300"],
        'logfile'   => $cron_path . "queue_trace_{$i}.log",
    ];
}

// open
for($i = 0; $i < $commissionSlotCount; $i++) {
    $cronConfig['open']['cron_open_' . $i] = [
        'name'      => '开奖',
        'index'     => $i,
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=open_{$i}", "--sleep=2", "--timeout=300"],
        'logfile'   => $cron_path . 'self_open.log',
    ];
}

// send
for($i = 0; $i < $commissionSlotCount; $i++) {
    $cronConfig['send']['cron_send_' . $i] = [
        'name'      => '派奖',
        'index'     => $i,
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=send_{$i}", "--sleep=2", "--timeout=300"],
        'logfile'   => $cron_path . 'self_send.log',
    ];
}

// commission
for($i = 0; $i < $commissionSlotCount; $i++) {
    $cronConfig['commission']['cron_commission_' . $i] = [
        'name'      => '返点',
        'index'     => $i,
        'cron'      => '* * * * *',
        'command'   => "{$phpbin}",
        'args'      => ["{$basepath}artisan", "queue:work", "--queue=commission_{$i}", "--sleep=2", "--timeout=300"],
        'logfile'   => $cron_path . 'self_commission.log',
    ];
}

return $cronConfig;
