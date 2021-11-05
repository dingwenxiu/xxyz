<?php

use Illuminate\Database\Seeder;

class SystemBankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sys_bank')->insert([
            'id'        => 1,
            'title'      => "中国工商银行",
            'code'      => "icbc",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 2,
            'title'      => "中国农业银行",
            'code'      => "abc",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 3,
            'title'      => "中国招商银行",
            'code'      => "cmb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 4,
            'title'      => "中国银行",
            'code'      => "boc",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 5,
            'title'      => "中国建设银行",
            'code'      => "ccb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 6,
            'title'      => "中国民生银行",
            'code'      => "cmbc",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 7,
            'title'      => "中信银行",
            'code'      => "ecitic",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 8,
            'title'      => "中国交通银行",
            'code'      => "comm",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 9,
            'title'      => "中国兴业银行",
            'code'      => "cib",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 10,
            'title'      => "中国光大银行",
            'code'      => "ceb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 11,
            'title'      => "深圳发展银行",
            'code'      => "szfz",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 12,
            'title'      => "中国邮政储蓄银行",
            'code'      => "psbc",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 13,
            'title'      => "北京银行",
            'code'      => "bccb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 14,
            'title'      => "平安银行",
            'code'      => "payh",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 15,
            'title'      => "上海浦东发展银行",
            'code'      => "spdb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 16,
            'title'      => "深圳发展银行",
            'code'      => "gdb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 17,
            'title'      => "广东发展银行",
            'code'      => "cgb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 18,
            'title'      => "华夏银行",
            'code'      => "hxb",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 19,
            'title'      => "中国平安银行",
            'code'      => "pingan",
            'status'    => 1,
            'icon'    => '',
        ]);

        DB::table('sys_bank')->insert([
            'id'        => 20,
            'title'      => "上海银行",
            'code'      => "bos",
            'status'    => 1,
            'icon'    => '',
        ]);
    }
}

