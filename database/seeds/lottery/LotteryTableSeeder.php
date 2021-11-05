<?php

use Illuminate\Database\Seeder;

class LotteryTableSeeder extends Seeder
{
    // 彩种
    public function run()
    {
        DB::table('lotteries')->insert([
            'cn_name'                                       => "重庆时时彩",
            'en_name'                                       => "cqssc",

            'lottery_icon'                                  => "system/lottery/cqssc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 59,
            'issue_format'                                  => "ymd|N3",
            'issue_part'                                    => 2,
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "positions"                                     => "1,2,3,4,5",
            "code_length"                                   => 5,
            'day_issue'                                     => 59,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,

            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 400000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'open_time'                                     => "1,2,3,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'issue_desc'                                    => "当天 <span>00</span> 点至 <span>23</span> 点   当日共有 <span>59</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "新疆时时彩",
            'en_name'                                       => "xjssc",

            'lottery_icon'                                  => "system/lottery/xjssc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 48,
            'issue_format'                                  => "ymd|N2",
            'issue_part'                                    => 1,
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
            'open_time'                                     => "0,1,2,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>10</span> 点至 <span>次日2</span> 点   当日共有 <span>48</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "黑龙江时时彩",
            'en_name'                                       => "hljssc",

            'lottery_icon'                                  => "system/lottery/hljssc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 42,
            'issue_type'                                    => "increase",
            "positions"                                     => "1,2,3,4,5",
            'issue_format'                                  => "C7",
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
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>22</span> 点   当日共有 <span>42</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "腾讯分分彩",
            'en_name'                                       => "txffc",

            'lottery_icon'                                  => "system/lottery/txffc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>00</span> 点至 <span>00</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "腾讯5分彩",
            'en_name'                                       => "tx5fc",

            'lottery_icon'                                  => "system/lottery/tx5fc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 288,
            'issue_format'                                  => "Ymd|N3",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>00</span> 点至 <span>00</span> 点   当日共有 <span>288</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "腾讯时时彩",
            'en_name'                                       => "tx10fc",

            'lottery_icon'                                  => "system/lottery/tx10fc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 144,
            'issue_format'                                  => "Ymd|N3",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>00</span> 点至 <span>00</span> 点   当日共有 <span>144</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "奇趣腾讯分分彩",
            'en_name'                                       => "qqtxffc",

            'lottery_icon'                                  => "system/lottery/qqtxffc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>00</span> 点至 <span>00</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "多彩腾讯分分彩",
            'en_name'                                       => "dctxffc",

            'lottery_icon'                                  => "system/lottery/dctxffc.png",

            'series_id'                                     => "ssc",
            'logic_sign'                                    => "ssc",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 1440,
            'issue_format'                                  => "Ymd|N4",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>00</span> 点至 <span>00</span> 点   当日共有 <span>1440</span> 期",
            'status'                                        => 1,
        ]);

        /** ======= 11选5 ======= */
        DB::table('lotteries')->insert([
            'cn_name'                                       => "山东11选5",
            'en_name'                                       => "sd115",

            'lottery_icon'                                  => "system/lottery/sd115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 43,
            'issue_format'                                  => "ymd|N2",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "01,02,03,04,05,06,07,08,09,10,11",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1700,
            'max_prize_group'                               => 1990,
            'diff_prize_group'                              => 20,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 200000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>23</span> 点   当日共有 <span>43</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "广东11选5",
            'en_name'                                       => "gd115",

            'lottery_icon'                                  => "system/lottery/gd115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 42,
            'issue_format'                                  => "ymd|N2",
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
            'open_time'                                     => "9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>23</span> 点   当日共有 <span>42</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "山西11选5",
            'en_name'                                       => "sx115",

            'lottery_icon'                                  => "system/lottery/sx115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 47,
            'issue_format'                                  => "ymd|N3",
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
            'open_time'                                     => "0,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>0</span> 点   当日共有 <span>47</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "上海11选5",
            'en_name'                                       => "sh115",

            'lottery_icon'                                  => "system/lottery/sh115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 45,
            'issue_format'                                  => "ymd|N3",
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
            'open_time'                                     => "0,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>0</span> 点   当日共有 <span>45</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "江西11选5",
            'en_name'                                       => "jx115",

            'lottery_icon'                                  => "system/lottery/jx115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 50,
            'day_issue'                                     => 42,
            'issue_format'                                  => "Ymd-|N2",
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
            'open_time'                                     => "0,1,2,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>23</span> 点   当日共有 <span>42</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "安徽11选5",
            'en_name'                                       => "ah115",

            'lottery_icon'                                  => "system/lottery/ah115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 40,
            'day_issue'                                     => 40,
            'issue_format'                                  => "ymd|N3",
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
            'open_time'                                     => "0,1,2,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>23</span> 点   当日共有 <span>40</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "湖北11选5",
            'en_name'                                       => "hb115",

            'lottery_icon'                                  => "system/lottery/hb115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 40,
            'day_issue'                                     => 40,
            'issue_format'                                  => "Ymd|N2",
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
            'open_time'                                     => "0,1,2,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>21</span> 点   当日共有 <span>40</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "江苏11选5",
            'en_name'                                       => "js115",

            'lottery_icon'                                  => "system/lottery/js115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 41,
            'day_issue'                                     => 41,
            'issue_format'                                  => "Ymd|N2",
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
            'open_time'                                     => "0,1,2,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>22</span> 点   当日共有 <span>41</span> 期",
            'status'                                        => 0,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "陕西11选5",
            'en_name'                                       => "shx115",

            'lottery_icon'                                  => "system/lottery/shx115.png",

            'series_id'                                     => "lotto",
            'logic_sign'                                    => "lotto",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 41,
            'day_issue'                                     => 44,
            'issue_format'                                  => "Ymd|N2",
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
            'open_time'                                     => "0,1,2,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>22</span> 点   当日共有 <span>44</span> 期",
            'status'                                        => 1,
        ]);

        /**  ========= K3 ========  */
        DB::table('lotteries')->insert([
            'cn_name'                                       => "江苏快3",
            'en_name'                                       => "jsk3",

            'lottery_icon'                                  => "system/lottery/jsk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 41,
            'day_issue'                                     => 41,
            'issue_format'                                  => "Ymd|N3",
            "positions"                                     => "1,2,3",
            "valid_code"                                    => "1,2,3,4,5,6",
            "code_length"                                   => 3,
            'min_prize_group'                               => 1700,
            'max_prize_group'                               => 1990,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 350000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>22</span> 点   当日共有 <span>41</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "安徽快3",
            'en_name'                                       => "ahk3",

            'lottery_icon'                                  => "system/lottery/ahk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 40,
            'issue_format'                                  => "Ymd|N3",
            "positions"                                     => "1,2,3",
            "valid_code"                                    => "1,2,3,4,5,6",
            "code_length"                                   => 3,
            'min_prize_group'                               => 1700,
            'max_prize_group'                               => 1990,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 350000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_modes'                                   => "1,2,3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>22</span> 点   当日共有 <span>40</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "甘肃快3",
            'en_name'                                       => "gsk3",

            'lottery_icon'                                  => "system/lottery/gsk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 36,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>21</span> 点   当日共有 <span>36</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "河南快3",
            'en_name'                                       => "hnk3",

            'lottery_icon'                                  => "system/lottery/hnk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 42,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>22</span> 点   当日共有 <span>42</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "湖北快3",
            'en_name'                                       => "hbk3",

            'lottery_icon'                                  => "system/lottery/hbk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 39,
            'day_issue'                                     => 39,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>22</span> 点   当日共有 <span>39</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "内蒙古快3",
            'en_name'                                       => "nmgk3",

            'lottery_icon'                                  => "system/lottery/nmgk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 36,
            'day_issue'                                     => 36,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>21</span> 点   当日共有 <span>36</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "江西快3",
            'en_name'                                       => "jxk3",

            'lottery_icon'                                  => "system/lottery/jxk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 42,
            'day_issue'                                     => 42,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>08</span> 点至 <span>22</span> 点   当日共有 <span>42</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "河北快3",
            'en_name'                                       => "hebk3",

            'lottery_icon'                                  => "system/lottery/hebk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 41,
            'day_issue'                                     => 41,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>22</span> 点   当日共有 <span>39</span> 期",
            'status'                                        => 0,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "广西快3",
            'en_name'                                       => "gxk3",

            'lottery_icon'                                  => "system/lottery/gxk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 40,
            'day_issue'                                     => 40,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>22</span> 点   当日共有 <span>39</span> 期",
            'status'                                        => 0,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "吉林快3",
            'en_name'                                       => "jlk3",

            'lottery_icon'                                  => "system/lottery/jlk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 41,
            'day_issue'                                     => 41,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>22</span> 点   当日共有 <span>41</span> 期",
            'status'                                        => 0,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "上海快3",
            'en_name'                                       => "shk3",

            'lottery_icon'                                  => "system/lottery/shk3.png",

            'series_id'                                     => "k3",
            'logic_sign'                                    => "k3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 41,
            'day_issue'                                     => 41,
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
            'issue_format'                                  => "ymd|N3",
            'valid_price'                                   => "1,2",
            'open_time'                                     => "8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'open_casino'                                   => 1,
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>22</span> 点   当日共有 <span>41</span> 期",
            'status'                                        => 0,
        ]);

        /**  ========= 福彩3D ========  */
        DB::table('lotteries')->insert([
            'cn_name'                                       => "福彩3D",
            'en_name'                                       => "fc3d",

            'lottery_icon'                                  => "system/lottery/fc3d.png",

            'series_id'                                     => "sd",
            'logic_sign'                                    => "digit3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 20,
            'day_issue'                                     => 1,
            'issue_format'                                  => "Y|T3",
            'issue_type'                                    => "increase",
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
            'open_time'                                     => "20,21",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>20</span> 点至 次日<span>20</span> 点   当日共有 <span>1</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "上海时时乐",
            'en_name'                                       => "shssl",

            'lottery_icon'                                  => "system/lottery/shssl.png",

            'series_id'                                     => "ssl",
            'logic_sign'                                    => "digit3",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 20,
            'day_issue'                                     => 23,
            'issue_format'                                  => "Ymd-|N2",
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
            'open_time'                                     => "6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>10</span> 点至 次日<span>21</span> 点   当日共有 <span>23</span> 期",
            'status'                                        => 1,
        ]);

        // p3p5
        DB::table('lotteries')->insert([
            'cn_name'                                       => "排列35",
            'en_name'                                       => "p3p5",

            'lottery_icon'                                  => "system/lottery/p3p5.png",

            'series_id'                                     => "p3p5",
            'logic_sign'                                    => "p3p5",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 20,
            'day_issue'                                     => 1,
            'issue_format'                                  => "Y|T3",
            'issue_type'                                    => "increase",
            "positions"                                     => "1,2,3,4,5",
            "valid_code"                                    => "0,1,2,3,4,5,6,7,8,9",
            "code_length"                                   => 5,
            'min_prize_group'                               => 1700,
            'max_prize_group'                               => 1990,
            'diff_prize_group'                              => 30,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 350000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "20,21",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>20</span> 点至 次日<span>20</span> 点   当日共有 <span>1</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "北京PK10",
            'en_name'                                       => "bjpk10",

            'lottery_icon'                                  => "system/lottery/bjpk10.png",

            'series_id'                                     => "pk10",
            'logic_sign'                                    => "pk10",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 44,
            'issue_format'                                  => "C6",
            'issue_type'                                    => "increase",
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
            'open_time'                                     => "0,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>09</span> 点至 <span>23</span> 点   当日共有 <span>44</span> 期",
            'open_casino'                                   => 1,
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "幸运飞艇",
            'en_name'                                       => "xyftpk10",

            'lottery_icon'                                  => "system/lottery/xyftpk10.png",

            'series_id'                                     => "pk10",
            'logic_sign'                                    => "pk10",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 180,
            'issue_format'                                  => "ymd|N3",
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
            'issue_desc'                                    => "当天 <span>13</span> 点至 次日<span>04</span> 点   当日共有 <span>180</span> 期",
            'open_casino'                                   => 1,
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "北京PC蛋蛋",
            'en_name'                                       => "bjxy28",

            'lottery_icon'                                  => "system/lottery/bjxy28.png",

            'series_id'                                     => "pcdd",
            'logic_sign'                                    => "pcdd",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 179,
            'issue_format'                                  => "C6",
            'issue_type'                                    => "increase",
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
            'open_time'                                     => "0,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>9</span> 点至 <span>23</span> 点   当日共有 <span>179</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "台湾PC蛋蛋",
            'en_name'                                       => "tw28",

            'lottery_icon'                                  => "system/lottery/tw28.png",

            'series_id'                                     => "pcdd",
            'logic_sign'                                    => "pcdd",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 60,
            'day_issue'                                     => 203,
            'issue_format'                                  => "C9",
            'issue_type'                                    => "increase",
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
            'open_time'                                     => "0,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>9</span> 点至 <span>23</span> 点   当日共有 <span>203</span> 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "香港六合彩",
            'en_name'                                       => "hklhc",

            'lottery_icon'                                  => "system/lottery/hklhc.png",

            'series_id'                                     => "lhc",
            'logic_sign'                                    => "lhc",
            'is_fast'                                       => 0,
            'is_sport'                                      => 0,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 1,
            'day_issue'                                     => 1,
            'issue_format'                                  => "y|T3",
            'issue_type'                                    => "random",
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
            'open_time'                                     => "21,22,23",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 如果术赛马日 开 1 期",
            'status'                                        => 1,
        ]);

        DB::table('lotteries')->insert([
            'cn_name'                                       => "广东快乐十分",
            'en_name'                                       => "gdklsf",

            'lottery_icon'                                  => "system/lottery/gdklsf.png",

            'series_id'                                     => "klsf",
            'logic_sign'                                    => "klsf",
            'is_fast'                                       => 1,
            'is_sport'                                      => 1,
            'auto_open'                                     => 0,
            'max_trace_number'                              => 42,
            'day_issue'                                     => 42,
            'issue_format'                                  => "Ymd|N3",
            'issue_type'                                    => "day",
            "positions"                                     => "1,2,3,4,5,6,7,8",
            "valid_code"                                    => "01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20",
            "code_length"                                   => 8,
            'min_prize_group'                               => 1800,
            'max_prize_group'                               => 1980,
            'diff_prize_group'                              => 0,
            'max_prize_per_code'                            => 350000,
            'max_prize_per_issue'                           => 350000,

            'min_times'                                     => 1,
            'max_times'                                     => 99999,

            'valid_price'                                   => "1,2",
            'open_time'                                     => "9,10,11,12,13,14,15,16,17,18,19,20,21",
            'valid_modes'                                   => "1,2,3",
            'issue_desc'                                    => "当天 <span>9</span> 点至 <span>21</span> 点   当日共有 <span>42</span> 期",
            'status'                                        => 1,
        ]);
    }
}
