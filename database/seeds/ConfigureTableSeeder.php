<?php

use Illuminate\Database\Seeder;

class ConfigureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sys_configures')->delete();
        DB::table('sys_configures')->insert([
            'id'                    => 1000,
            'name'                  => "网站",
            'sign'                  => "web",
            'value'                 => "",
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'pid'                   => 0,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2000,
            'name'                  => "财务",
            'sign'                  => "finance",
            'value'                 => "",
            'pid'                   => 0,
            'partner_edit'          => 1,
            'partner_show'          => 1,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3000,
            'name'                  => "游戏",
            'sign'                  => "lottery",
            'value'                 => "",
            'pid'                   => 0,
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5000,
            'name'                  => "用户",
            'sign'                  => "player",
            'value'                 => "",
            'pid'                   => 0,
            'partner_edit'          => 1,
            'partner_show'          => 1,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6000,
            'name'                  => "系统",
            'sign'                  => "system",
            'value'                 => "",
            'pid'                   => 0,
            'partner_edit'          => 1,
            'partner_show'          => 0,
            'status'                => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 7000,
            'name'                  => "包网",
            'sign'                  => "partner",
            'value'                 => "",
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'pid'                   => 0,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 8000,
            'name'                  => "娱乐城",
            'sign'                  => "casino",
            'value'                 => "",
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'pid'                   => 0,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 8001,
            'name'                  => "key",
            'sign'                  => "casino_secret_key",
            'value'                 => "333402fc4f7e319d917046e0bcfe8d72",
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'pid'                   => 8,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 8002,
            'name'                  => "代理名称",
            'sign'                  => "casino_merchant",
            'value'                 => "chenxing",
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'pid'                   => 8,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 8003,
            'name'                  => "地址",
            'sign'                  => "casino_gateway",
            'value'                 => "http://api.youxiatv.com/",
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'pid'                   => 8,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 8004,
            'name'                  => "过期时间",
            'sign'                  => "casino_encryption_time",
            'value'                 => 30,
            'partner_edit'          => 0,
            'partner_show'          => 0,
            'pid'                   => 8,
            'status'                => 1,
        ]);

        /** ========================== 系统 ============================ */
        DB::table('sys_configures')->insert([
            'id'                    => 1001,
            'name'                  => "网站名称",
            'sign'                  => "web_site_name",
            'value'                 => "游侠包网",
            'pid'                   => 1,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 1002,
            'name'                  => "网站标题",
            'sign'                  => "web_site_title",
            'value'                 => "游侠包网",
            'pid'                   => 1,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);
        DB::table('sys_configures')->insert([
            'id'                    => 1003,
            'name'                  => "云存储KEY",
            'sign'                  => "web_oss_key",
            'value'                 => "LTAI4Fn8bncU2WC5E37f4g1G",
            'pid'                   => 1,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);
        DB::table('sys_configures')->insert([
            'id'                    => 1004,
            'name'                  => "云存储Sercet",
            'sign'                  => "web_oss_sercet",
            'value'                 => "JGJUnF1k1roj9lSSLp0zn6EX554HuJ",
            'pid'                   => 1,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);
        DB::table('sys_configures')->insert([
            'id'                    => 1005,
            'name'                  => "云存储上传URL",
            'sign'                  => "web_oss_endpoint",
            'value'                 => "http://oss-cn-hongkong.aliyuncs.com",
            'pid'                   => 1,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 1006,
            'name'                  => "发报机器人TOKEN",
            'sign'                  => "web_send_boot_token",
            'value'                 => "915632195:AAGO7m1Z4qcMZux7lKDNopNOsvUJDgDbD6A",
            'pid'                   => 1,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 1007,
            'name'                  => "发报机器人",
            'sign'                  => "web_send_boot",
            'value'                 => "@youxia_bot",
            'pid'                   => 1,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        /** ========================== 财务 =========================== */

        DB::table('sys_configures')->insert([
            'id'                    => 2001,
            'name'                  => "充值是否维护",
            'sign'                  => "finance_recharge_maintain",
            'value'                 => 0,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2002,
            'name'                  => "提现是否维护",
            'sign'                  => "finance_withdraw_maintain",
            'value'                 => 0,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2003,
            'name'                  => "最大绑卡数",
            'sign'                  => "finance_card_max_bind",
            'value'                 => 4,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2004,
            'name'                  => "绑卡是否可以重名",
            'sign'                  => "finance_card_can_same_owner",
            'value'                 => 1,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2005,
            'name'                  => "充值订单号修正数字",
            'sign'                  => "finance_recharge_order_plus",
            'value'                 => 20013000,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2006,
            'name'                  => "最小充值",
            'sign'                  => "finance_min_recharge",
            'value'                 => 100,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2007,
            'name'                  => "最大充值",
            'sign'                  => "finance_max_recharge",
            'value'                 => 5000,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2008,
            'name'                  => "最小提现",
            'sign'                  => "finance_min_withdraw",
            'value'                 => 100,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2009,
            'name'                  => "最大提现",
            'sign'                  => "finance_max_withdraw",
            'value'                 => 20000,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2010,
            'name'                  => "每日最大提现次数",
            'sign'                  => "finance_day_withdraw_count",
            'value'                 => 5,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,

        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2011,
            'name'                  => "提现订单号修正数字",
            'sign'                  => "finance_withdraw_order_plus",
            'value'                 => 20013000,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2012,
            'name'                  => "提现时间段",
            'sign'                  => "finance_withdraw_time_range",
            'value'                 => "00:00:00-08:00:00|09:00:00-24:00:00",
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2013,
            'name'                  => "银行卡绑定时间限制(小时)",
            'partner_edit'          => 1,
            'sign'                  => "finance_card_withdraw_limit_hour",
            'value'                 => 5,
            'pid'                   => 2,
            'status'                => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2014,
            'name'                  => "提现是否需要审核",
            'sign'                  => "finance_withdraw_need_check",
            'value'                 => 1,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2015,
            'name'                  => "提现需要流水倍数",
            'sign'                  => "finance_withdraw_bet_times",
            'value'                 => "5",
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2016,
            'name'                  => "提现订单前缀",
            'sign'                  => "finance_withdraw_order_prefix",
            'value'                 => "BW",
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2017,
            'name'                  => "提现是否可以存在两笔未处理的",
            'sign'                  => "finance_withdraw_order_can_multi",
            'value'                 => 0,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2018,
            'name'                  => "管理员转帐是否开启审核",
            'sign'                  => "finance_admin_transfer_need_review",
            'value'                 => 1,
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 2019,
            'name'                  => "充值订单前缀",
            'sign'                  => "finance_recharge_order_prefix",
            'value'                 => "BW",
            'pid'                   => 2,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        /** ========================== 系统 ============================ */
        DB::table('sys_configures')->insert([
            'id'                    => 3001,
            'name'                  => "测试开奖号-ssc",
            'sign'                  => "lottery_open_test_ssc",
            'value'                 => "1,2,3,4,5",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3002,
            'name'                  => "测试开奖号-lotto",
            'sign'                  => "lottery_open_test_lotto",
            'value'                 => "01,02,03,04,05",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3003,
            'name'                  => "测试开奖号-ssl",
            'sign'                  => "lottery_open_test_ssl",
            'value'                 => "1,2,3",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3004,
            'name'                  => "测试开奖号-k3",
            'sign'                  => "lottery_open_test_k3",
            'value'                 => "1,2,3",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3005,
            'name'                  => "测试开奖号-3d",
            'sign'                  => "lottery_open_test_3d",
            'value'                 => "1,2,3",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3006,
            'name'                  => "测试开奖号-pk10",
            'sign'                  => "lottery_open_test_pk10",
            'value'                 => "01,02,03,04,05,06,07,08,09,10",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3007,
            'name'                  => "测试开奖号-p3p5",
            'sign'                  => "lottery_open_test_p3p5",
            'value'                 => "1,2,3,4,5",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3008,
            'name'                  => "测试开奖号-pcdd",
            'sign'                  => "lottery_open_test_pcdd",
            'value'                 => "2,5,6",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3009,
            'name'                  => "测试开奖号-六合彩",
            'sign'                  => "lottery_open_test_lhc",
            'value'                 => "01,06,11,38,42,46,21",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3010,
            'name'                  => "测试开奖号-快乐十分",
            'sign'                  => "lottery_open_test_klsf",
            'value'                 => "01,06,11,13,07,09,02,17",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3011,
            'name'                  => "禁止录号彩种",
            'sign'                  => "lottery_disable_encode_lottery",
            'value'                 => "xxffc",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3012,
            'name'                  => "jackpot 师否开启",
            'sign'                  => "lottery_jackpot_open",
            'value'                 => 0,
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3013,
            'name'                  => "jackpot 网关地址",
            'sign'                  => "lottery_jackpot_gateway",
            'value'                 => "http://35.221.221.10:10010/jackpot",
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);


        DB::table('sys_configures')->insert([
            'id'                    => 3014,
            'name'                  => "jackpot 类型",
            'sign'                  => "lottery_jackpot_type",
            'value'                 => 2001,
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 3015,
            'name'                  => "jackpot 控水最低投注",
            'sign'                  => "lottery_jackpot_min_bet",
            'value'                 => 1000,
            'pid'                   => 3,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        /** ============================= 玩家 ============================= */
        DB::table('sys_configures')->insert([
            'id'                    => 5001,
            'name'                  => "开户最低奖金组",
            'sign'                  => "player_register_min_prize_group",
            'value'                 => "1800",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5002,
            'name'                  => "开户最高奖金组",
            'sign'                  => "player_register_max_prize_group",
            'value'                 => "1980",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5003,
            'name'                  => "注册默认上级",
            'sign'                  => "player_default_register_parent_username",
            'value'                 => "10000",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5004,
            'name'                  => "注册默认类型",
            'sign'                  => "player_default_type",
            'value'                 => "3",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
            'description'           => '代理:2,会员:3'
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5005,
            'name'                  => "每分钟注册不能超过N个",
            'sign'                  => "player_max_register_one_ip_minute",
            'value'                 => "10",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5006,
            'name'                  => "注册默认奖金组",
            'sign'                  => "player_open_register_default_group",
            'value'                 => "1800",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5007,
            'name'                  => "日工资最低百分比",
            'sign'                  => "player_salary_rate_min",
            'value'                 => "0",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5008,
            'name'                  => "日工资最高百分比",
            'sign'                  => "player_salary_rate_max",
            'value'                 => "5",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5009,
            'name'                  => "分红最低百分比",
            'sign'                  => "player_bonus_rate_min",
            'value'                 => "0",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5010,
            'name'                  => "分红最高百分比",
            'sign'                  => "player_bonus_rate_max",
            'value'                 => "50",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5011,
            'name'                  => "转账下级最小额度",
            'sign'                  => "player_transfer_child_min",
            'value'                 => "1",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5012,
            'name'                  => "转账下级最大额度",
            'sign'                  => "player_transfer_child_max",
            'value'                 => "10000",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 5013,
            'name'                  => "商户后台转帐最大额度",
            'sign'                  => "player_transfer_max_super",
            'value'                 => "20000",
            'pid'                   => 5,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

		DB::table('sys_configures')->insert([
			'id'                    => 5014,
			'name'                  => "是否展示VIP等级图片",
			'sign'                  => "player_vip_img_dispaly",
			'value'                 => "1",
			'pid'                   => 5,
			'status'                => 1,
			'partner_edit'          => 1,
			'partner_show'          => 1,
		]);

        /** ============================ 系统 ============================ */
        DB::table('sys_configures')->insert([
            'id'                    => 6001,
            'name'                  => "资金修正倍数",
            'sign'                  => "system_finance_fixed_times",
            'value'                 => 10000,
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 1,
            'partner_show'          => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6002,
            'name'                  => "最大可投奖金组",
            'sign'                  => "system_max_bet_group",
            'value'                 => 1960,
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6003,
            'name'                  => "是否开启加密",
            'sign'                  => "system_open_encryption",
            'value'                 => 0,
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6005,
            'name'                  => "图片CDN地址",
            'sign'                  => "system_pic_cdn_url",
            'value'                 => 0,
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

         if (isProductEnv()) {
            $system_bucket        = 'yx-prod';
            $system_pic_base_url  = "https://img.youxiabw.com";
        } else {
            $system_bucket        =  'yx-ptest';
            $system_pic_base_url  =  "https://img.play322.com";
        }

         DB::table('sys_configures')->insert([
            'id'                    => 6004,
            'name'                  => "图片基础地址",
            'sign'                  => "system_pic_base_url",
            'value'                 => $system_pic_base_url,
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6006,
            'name'                  => "上传云端的桶值",
            'sign'                  => "system_bucket",
            'value'                 => $system_bucket,
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6007,
            'name'                  => "帐变记录保存天数",
            'sign'                  => "system_fund_change_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6008,
            'name'                  => "玩家IP日志保留天数",
            'sign'                  => "system_player_ip_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6009,
            'name'                  => "玩家访问日志保留天数",
            'sign'                  => "system_player_visit_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6010,
            'name'                  => "商户访问日志保存天数",
            'sign'                  => "system_partner_visit_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6011,
            'name'                  => "商户行为日志保存天数",
            'sign'                  => "system_partner_behavior_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

         DB::table('sys_configures')->insert([
            'id'                    => 6012,
            'name'                  => "返点记录保存天数",
            'sign'                  => "system_commissions_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6013,
            'name'                  => "投注记录保存天数",
            'sign'                  => "system_project_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6014,
            'name'                  => "追号记录保存天数",
            'sign'                  => "system_traces_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6015,
            'name'                  => "奖期列表保存天数",
            'sign'                  => "system_issues_log_data_keep_day",
            'value'                 => "3",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);
        if (isProductEnv()) {
            $system_admin_api_domain='https://admin-api.youxiabw.com';
        } else {
            $system_admin_api_domain='https://admin-api.play322.com';
        }

        DB::table('sys_configures')->insert([
            'id'                    => 6016,
            'name'                  => "包网后台接口域名",
            'sign'                  => "system_admin_api_domain",
            'value'                 => $system_admin_api_domain,
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 6017,
            'name'                  => "测试用户是否记入统计",
            'sign'                  => "system_tester_stat",
            'value'                 => "0",
            'partner_edit'          => 1,
            'partner_show'          => 1,
            'pid'                   => 6,
            'status'                => 1,
        ]);

        DB::table('sys_configures')->insert([
            'id'                    => 9000,
            'name'                  => "聊天通道WK",
            'sign'                  => "system_wk",
            'value'                 => "talk.lottery-test.svc.cluster.local:1238",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);
        DB::table('sys_configures')->insert([
            'id'                    => 9001,
            'name'                  => "聊天通道TWS",
            'sign'                  => "system_tws",
            'value'                 => "wss://talk.play322.com/socket",
            'pid'                   => 6,
            'status'                => 1,
            'partner_edit'          => 0,
            'partner_show'          => 0,
        ]);
    }
}
