<?php

// 默认彩种
return [
    'lottery'   => [
        [
            'cn_name'                                       => "极速1分彩",
            'en_name'                                       => "jsffc",

            'lottery_icon'                                  => "system/lottery/jsffc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_type'                                    => "day",
            'issue_format'                                  => "ymd|N4",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 400000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>00</span> 点至 <span>00</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],

        [
            'cn_name'                                       => "极速11选5",
            'en_name'                                       => "js115",

            'lottery_icon'                                  => "system/lottery/js115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 50,
            'day_issue'                                     => 1440,
            'issue_type'                                    => "day",
            'issue_format'                                  => "Ymd|N4",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "01,02,03,04,05,06,07,08,09,10,11",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 20,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],
        [
            'cn_name'                                       => "极速快3",
            'en_name'                                       => "js1fk3",

            'lottery_icon'                                  => "system/lottery/js1fk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3",
            "valid_code"                                    => "1,2,3,4,5,6",
            "code_length"                                   => 3,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'issue_format'                                  => "Ymd|N4",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],
        [
            'cn_name'                                       => "极速3D",
            'en_name'                                       => "js3d",

            'lottery_icon'                                  => "system/lottery/js3d.png",

            'series_id'                                     => "sd",
            'logic_sign'                                    => "digit3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 50,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 3,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 30,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],
        [
            'cn_name'                                       => "极速时时乐",
            'en_name'                                       => "jsssl",

            'lottery_icon'                                  => "system/lottery/jsssl.png",

            'series_id'                                     => "ssl",
            'logic_sign'                                    => "digit3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 50,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 3,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 30,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],

        [
            'cn_name'                                       => "极速排列35",
            'en_name'                                       => "jsp3p5",

            'lottery_icon'                                  => "system/lottery/jsp3p5.png",

            'series_id'                                     => "p3p5",
            'logic_sign'                                    => "p3p5",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 50,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 3,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 30,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],

        [
            'cn_name'                                       => "极速飞艇",
            'en_name'                                       => "jsftpk10",

            'lottery_icon'                                  => "system/lottery/jsftpk10.png",

            'series_id'                                     => "pk10",
            'logic_sign'                                    => "pk10",
            'is_fast'                                       => 1,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3,4,5,6,7,8,9,10",
            "valid_code"                                    => "1,2,3,4,5,6,7,8,9,10",
            "code_length"                                   => 10,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'open_casino'                                   => 1,
            'status'                                        => 1,
        ],

        [
            'cn_name'                                       => "极速PK10",
            'en_name'                                       => "jspk10",

            'lottery_icon'                                  => "system/lottery/jspk10.png",

            'series_id'                                     => "pk10",
            'logic_sign'                                    => "pk10",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3,4,5,6,7,8,9,10",
            "valid_code"                                    => "1,2,3,4,5,6,7,8,9,10",
            "code_length"                                   => 10,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'open_casino'                                   => 1,
            'status'                                        => 1,
        ],
        [
            'cn_name'                                       => "极速六合彩",
            'en_name'                                       => "jslhc",

            'lottery_icon'                                  => "system/lottery/jslhc.png",

            'series_id'                                     => "lhc",
            'logic_sign'                                    => "lhc",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 1,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3,4,5,6,7",
            "valid_code"                                    => "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49",
            "code_length"                                   => 7,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 350000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>0</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],
        [
            'cn_name'                                       => "极速PC蛋蛋",
            'en_name'                                       => "jsxy28",

            'lottery_icon'                                  => "system/lottery/jsxy28.png",

            'series_id'                                     => "pcdd",
            'logic_sign'                                    => "pcdd",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_type'                                    => "day",
            'issue_format'                                  => "ymd|N4",
            "positions"                                     => "1,2,3",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 3,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>23</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],

        [
            'cn_name'                                       => "极速快乐十分",
            'en_name'                                       => "jsklsf",

            'lottery_icon'                                  => "system/lottery/jsklsf.png",

            'series_id'                                     => "klsf",
            'logic_sign'                                    => "klsf",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 1,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_type'                                    => "day",
            'issue_format'                                  => "ymd|N4",
            "positions"                                     => "1,2,3,4,5,6,7,8",
            "valid_code"                                    => "01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20",
            "code_length"                                   => 8,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>0</span> 点至 <span>23</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ],

    ],
    'rule'      => [
        'jsffc' => [
            'lottery_sign'              => "jsffc",
            'lottery_name'              => "极速1分彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'jslhc' => [
            'lottery_sign'              => "jslhc",
            'lottery_name'              => "极速六合彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'js115' => [
            'lottery_sign'              => "js115",
            'lottery_name'              => "极速十一选5",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => 0,
            'encode_time'               => 0,
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'js1fk3' => [
            'lottery_sign'              => "js1fk3",
            'lottery_name'              => "极速快3",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'js3d' => [
            'lottery_sign'              => "js3d",
            'lottery_name'              => "极速3D",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'jsp3p5' => [
            'lottery_sign'              => "jsp3p5",
            'lottery_name'              => "极速排列35",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        // 急速飞艇
        'jsftpk10' => [
            'lottery_sign'              => "jsftpk10",
            'lottery_name'              => "极速飞艇",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],
        'jspk10' => [
            'lottery_sign'              => "jspk10",
            'lottery_name'              => "极速PK10",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'jsssl' => [
            'lottery_sign'              => "jsssl",
            'lottery_name'              => "极速时时乐",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'jsxy28' => [
            'lottery_sign'              => "jsxy28",
            'lottery_name'              => "极速PC蛋蛋",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],

        'jsklsf' => [
            'lottery_sign'              => "jsklsf",
            'lottery_name'              => "极速快乐十分",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ],
    ],
];

