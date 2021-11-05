<?php

use Illuminate\Database\Seeder;

class SysTelegramChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sys_telegram_channel')->insert([
            'partner_sign'          => "system",
            'channel_sign'          => "send_exception",
            'channel_group_name'    => "总控_HTTP异常",
            'channel_id'            => "-251108458",
            'status'                => 1,
        ]);

        DB::table('sys_telegram_channel')->insert([
            'partner_sign'          => "system",
            'channel_sign'          => "send_job_exception",
            'channel_group_name'    => "总控_队列异常",
            'channel_id'            => "-280126829",
            'status'                => 1,
        ]);

        DB::table('sys_telegram_channel')->insert([
            'partner_sign'          => "system",
            'channel_sign'          => "send_not_open_issue",
            'channel_group_name'    => "总控_奖期异常",
            'channel_id'            => "-280126829",
            'status'                => 1,
        ]);

        DB::table('sys_telegram_channel')->insert([
            'partner_sign'          => "system",
            'channel_sign'          => "send_code",
            'channel_group_name'    => "总控_总控验证码",
            'channel_id'            => "-1001438324849",
            'status'                => 1,
        ]);

        DB::table('sys_telegram_channel')->insert([
            'partner_sign'          => "system",
            'channel_sign'          => "report_push",
            'channel_group_name'    => "总控_报表接收",
            'channel_id'            => "-1001438324849",
            'status'                => 1,
        ]);

        DB::table('sys_telegram_channel')->insert([
            'partner_sign'          => "system",
            'channel_sign'          => "send_challenge",
            'channel_group_name'    => "总控_单挑_限额",
            'channel_id'            => "-267085135	",
            'status'                => 1,
        ]);

        DB::table('sys_telegram_channel')->insert([
            'partner_sign'          => "system",
            'channel_sign'          => "send_finance",
            'channel_group_name'    => "总控_财务接收",
            'channel_id'            => "-1001438324849",
            'status'                => 1,
        ]);


		DB::table('sys_telegram_channel')->insert([
			'partner_sign'          => "system",
			'channel_sign'          => "send_admin_behavior",
			'channel_group_name'    => "总控_管理员行为",
			'channel_id'            => "-372728334",
			'status'                => 1,
		]);
    }
}
