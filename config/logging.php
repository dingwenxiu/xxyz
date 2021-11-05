<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'permission' => 0777,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
            'permission' => 0777,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver'  => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
        'pay-recharge' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pay/pay-recharge.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'pay-withdraw' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pay/pay-withdraw.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'calculate-prize' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pay/calculate_prize.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'pay-info' => [ //发起支付前参数信息
            'driver' => 'daily',
            'path' => storage_path('logs/pay/pay-info/pay-info.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'sign-clear' => [ //签名明文
            'driver' => 'daily',
            'path' => storage_path('logs/pay/sign-clear/sign-clear.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'sign' => [ //签名
            'driver' => 'daily',
            'path' => storage_path('logs/pay/sign/sign.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'post-data' => [ //请求数据
            'driver' => 'daily',
            'path' => storage_path('logs/pay/post-data/post-data.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'curl-res' => [ //同步返回数据
            'driver' => 'daily',
            'path' => storage_path('logs/pay/curl-res/curl-res.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'callback-data' => [ //异步返回数据
            'driver' => 'daily',
            'path' => storage_path('logs/pay/callback-data/callback-data.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'callback-exception' => [ //异步回调异常日志
            'driver' => 'daily',
            'path' => storage_path('logs/pay/callback-exception/callback-exception.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'callback-log' => [ //回调日志
            'driver' => 'daily',
            'path' => storage_path('logs/pay/callback-log/callback-log.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'withdrawstatus-log' => [ //改变提现订单状态日志
            'driver' => 'daily',
            'path' => storage_path('logs/pay/withdrawstatus-log/withdrawstatus-log.log'),
            'level' => 'debug',
            'days' => 14,
        ],
    ],

];
