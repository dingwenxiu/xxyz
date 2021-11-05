<?php

use Illuminate\Database\Seeder;

class IssueRuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 重庆
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "cqssc",
            'lottery_name'              => "重庆时时彩",
            'begin_time'                => "00:10:00",
            'end_time'                  => "03:10:00",

            'issue_seconds'             => "1200",
            'first_time'                => "00:30:00",

            'adjust_time'               => "30",
            'encode_time'               => "5",
            'issue_count'               => "9",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "cqssc",
            'lottery_name'              => "重庆时时彩",
            'begin_time'                => "07:10:00",
            'end_time'                  => "23:50:00",

            'issue_seconds'             => "1200",
            'first_time'                => "07:30:00",

            'adjust_time'               => "45",
            'encode_time'               => "5",
            'issue_count'               => "50",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 黑龙江
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "hljssc",
            'lottery_name'              => "黑龙江时时彩",
            'begin_time'                => "08:40:00",
            'end_time'                  => "22:40:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:00:00",

            'adjust_time'               => "120",
            'encode_time'               => "10",
            'issue_count'               => "42",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 新疆
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "xjssc",
            'lottery_name'              => "新疆时时彩",
            'begin_time'                => "10:00:00",
            'end_time'                  => "02:00:00",

            'issue_seconds'             => "1200",
            'first_time'                => "10:20:00",

            'adjust_time'               => "120",
            'encode_time'               => "10",
            'issue_count'               => "48",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 腾讯 1 分彩
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "txffc",
            'lottery_name'              => "腾讯分分彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 腾讯 5 分彩
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "tx5fc",
            'lottery_name'              => "腾讯五分彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "300",
            'first_time'                => "00:05:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "288",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 腾讯 10 分彩
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "tx10fc",
            'lottery_name'              => "腾讯分分彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "600",
            'first_time'                => "00:10:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "144",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 多彩腾讯
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "dctxffc",
            'lottery_name'              => "多彩腾讯分分彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 齐趣腾讯
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "qqtxffc",
            'lottery_name'              => "奇趣腾讯分分彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "60",
            'first_time'                => "00:01:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "1440",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 微博5分彩
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "wb5fc",
            'lottery_name'              => "微博5分彩",
            'begin_time'                => "00:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "300",
            'first_time'                => "00:05:00",

            'adjust_time'               => "0",
            'encode_time'               => "0",
            'issue_count'               => "288",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 山东11选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "sd115",
            'lottery_name'              => "山东11选5",
            'begin_time'                => "08:41:00",
            'end_time'                  => "23:01:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:01:00",

            'adjust_time'               => "90",
            'encode_time'               => "60",
            'issue_count'               => "43",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 广东11选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "gd115",
            'lottery_name'              => "广东11选5",
            'begin_time'                => "09:10:00",
            'end_time'                  => "23:10:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:30:00",

            'adjust_time'               => "60",
            'encode_time'               => "60",
            'issue_count'               => "42",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 山西11选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "sx115",
            'lottery_name'              => "山西11选5",
            'begin_time'                => "08:26:00",
            'end_time'                  => "00:06:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:46:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "47",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 上海11选５
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "sh115",
            'lottery_name'              => "上海11选5",
            'begin_time'                => "09:00:00",
            'end_time'                  => "00:00:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:20:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "45",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 江西十一选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "jx115",
            'lottery_name'              => "江西十一选5",
            'begin_time'                => "09:10:00",
            'end_time'                  => "23:10:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:30:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "42",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 安徽十一选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "ah115",
            'lottery_name'              => "安徽十一选5",
            'begin_time'                => "08:40:00",
            'end_time'                  => "22:00:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:00:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "40",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 湖北十一选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "hb115",
            'lottery_name'              => "湖北十一选5",
            'begin_time'                => "08:35:00",
            'end_time'                  => "21:55:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:55:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "40",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 江苏十一选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "js115",
            'lottery_name'              => "江苏十一选5",
            'begin_time'                => "08:25:00",
            'end_time'                  => "22:05:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:45:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "41",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 陕西11选5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "shx115",
            'lottery_name'              => "陕西11选5",
            'begin_time'                => "08:30:00",
            'end_time'                  => "23:10:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:50:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "44",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 江苏快三
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "jsk3",
            'lottery_name'              => "江苏快3",
            'begin_time'                => "08:30:00",
            'end_time'                  => "22:10:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:50:00",

            'adjust_time'               => "180",
            'encode_time'               => "5",
            'issue_count'               => "41",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 安徽快三
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "ahk3",
            'lottery_name'              => "安徽快三",
            'begin_time'                => "08:40:00",
            'end_time'                  => "22:00:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:00:00",

            'adjust_time'               => "180",
            'encode_time'               => "5",
            'issue_count'               => "40",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 甘肃快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "gsk3",
            'lottery_name'              => "甘肃快3",
            'begin_time'                => "09:59:00",
            'end_time'                  => "21:59:00",

            'issue_seconds'             => "1200",
            'first_time'                => "10:19:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "36",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 河南快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "hnk3",
            'lottery_name'              => "河南快3",
            'begin_time'                => "08:34:00",
            'end_time'                  => "22:34:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:54:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "42",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 湖北快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "hbk3",
            'lottery_name'              => "湖北快3",
            'begin_time'                => "09:00:00",
            'end_time'                  => "22:00:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:20:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "39",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 内蒙古快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "nmgk3",
            'lottery_name'              => "内蒙古快3",
            'begin_time'                => "09:40:00",
            'end_time'                  => "21:40:00",

            'issue_seconds'             => "1200",
            'first_time'                => "10:00:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "36",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 江西
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "jxk3",
            'lottery_name'              => "江西快3",
            'begin_time'                => "08:15:00",
            'end_time'                  => "22:15:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:35:00",

            'adjust_time'               => "180",
            'encode_time'               => "45",
            'issue_count'               => "42",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 河北快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "hebk3",
            'lottery_name'              => "河北快3",
            'begin_time'                => "08:29:00",
            'end_time'                  => "22:09:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:49:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "41",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 广西快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "gxk3",
            'lottery_name'              => "广西快3",
            'begin_time'                => "09:10:00",
            'end_time'                  => "22:30:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:30:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "40",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 吉林快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "jlk3",
            'lottery_name'              => "吉林快3",
            'begin_time'                => "08:15:00",
            'end_time'                  => "21:55:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:35:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "41",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 上海快3
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "shk3",
            'lottery_name'              => "上海快3",
            'begin_time'                => "08:38:00",
            'end_time'                  => "22:18:00",

            'issue_seconds'             => "1200",
            'first_time'                => "08:58:00",

            'adjust_time'               => "120",
            'encode_time'               => "60",
            'issue_count'               => "41",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 福彩3D
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "fc3d",
            'lottery_name'              => "福彩3D",
            'begin_time'                => "20:20:00",
            'end_time'                  => "20:20:00",

            'issue_seconds'             => "87000",
            'first_time'                => "20:30:00",

            'adjust_time'               => "600",
            'encode_time'               => "600",
            'issue_count'               => "1",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // P3P5
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "p3p5",
            'lottery_name'              => "排列35",
            'begin_time'                => "20:20:00",
            'end_time'                  => "20:20:00",

            'issue_seconds'             => "87000",
            'first_time'                => "20:30:00",

            'adjust_time'               => "1800",
            'encode_time'               => "1800",
            'issue_count'               => "1",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // PK10
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "bjpk10",
            'lottery_name'              => "北京赛车PK10",
            'begin_time'                => "09:10:00",
            'end_time'                  => "23:50:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:30:00",

            'adjust_time'               => "60",
            'encode_time'               => "0",
            'issue_count'               => "44",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 幸运飞艇
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "xyftpk10",
            'lottery_name'              => "幸运飞艇",
            'begin_time'                => "13:04:00",
            'end_time'                  => "04:04:00",

            'issue_seconds'             => "300",
            'first_time'                => "13:09:00",

            'adjust_time'               => "60",
            'encode_time'               => "0",
            'issue_count'               => "180",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 上海时时乐
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "shssl",
            'lottery_name'              => "上海时时乐",
            'begin_time'                => "06:00:00",
            'end_time'                  => "21:30:00",

            'issue_seconds'             => "1800",
            'first_time'                => "10:30:00",

            'adjust_time'               => "180",
            'encode_time'               => "60",
            'issue_count'               => "23",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 北京幸运28
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "bjxy28",
            'lottery_name'              => "北京PC蛋蛋",
            'begin_time'                => "09:00:00",
            'end_time'                  => "23:55:00",

            'issue_seconds'             => "300",
            'first_time'                => "09:05:00",

            'adjust_time'               => "50",
            'encode_time'               => "0",
            'issue_count'               => "179",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 台湾幸运28
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "tw28",
            'lottery_name'              => "台湾PC蛋蛋",
            'begin_time'                => "07:00:00",
            'end_time'                  => "23:55:00",

            'issue_seconds'             => "300",
            'first_time'                => "07:05:00",

            'adjust_time'               => "50",
            'encode_time'               => "0",
            'issue_count'               => "203",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);

        // 广东快乐10分
        DB::table('lottery_issue_rules')->insert([
            'lottery_sign'              => "gdklsf",
            'lottery_name'              => "广东快乐十分",
            'begin_time'                => "09:00:00",
            'end_time'                  => "23:00:00",

            'issue_seconds'             => "1200",
            'first_time'                => "09:20:00",

            'adjust_time'               => "60",
            'encode_time'               => "20",
            'issue_count'               => "42",

            'status'                    => 1,
            'created_at'                => date("Y-m-d H:i:s"),
        ]);
    }
}
