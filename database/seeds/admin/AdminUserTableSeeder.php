<?php

use Illuminate\Database\Seeder;

class AdminUserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('admin_users')->insert([
            'username'          => "tom888",
            'email'             => "tom888@gmail.com",
            'status'            => 1,
            'group_id'          => 1,
            'register_ip'       => "127.0.0.1",
            'created_at'        => date('Y-m-d H:i:s'),
            'password'          => bcrypt('1234qwer'),
            'fund_password'     => bcrypt('qwer1234'),
        ]);


        /** ======== 管理组 ======= */
        DB::table('admin_groups')->insert([
            'id'                => 1,
            'pid'               => 0,
            'rid'               => "1",
            'member_count'      => 1,
            'name'              => "超级管理员",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        DB::table('admin_groups')->insert([
            'id'                => 2,
            'pid'               => 1,
            'rid'               => "1|2",
            'name'              => "运营经理",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        DB::table('admin_groups')->insert([
            'id'                => 3,
            'pid'               => 2,
            'rid'               => "1|2|3",
            'name'              => "运营主管",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        DB::table('admin_groups')->insert([
            'id'                => 4,
            'pid'               => 3,
            'rid'               => "1|2|3|4",
            'name'              => "运营专员",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        // 市场
        DB::table('admin_groups')->insert([
            'id'                => 5,
            'pid'               => 1,
            'rid'               => "1|5",
            'name'              => "市场经理",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        DB::table('admin_groups')->insert([
            'id'                => 6,
            'pid'               => 5,
            'rid'               => "1|5|6",
            'name'              => "市场主管",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        DB::table('admin_groups')->insert([
            'id'                => 7,
            'pid'               => 6,
            'rid'               => "1|5|6|7",
            'name'              => "业务员",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        // 开发
        DB::table('admin_groups')->insert([
            'id'                => 8,
            'pid'               => 1,
            'rid'               => "1|8",
            'name'              => "开发经理",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        DB::table('admin_groups')->insert([
            'id'                => 9,
            'pid'               => 8,
            'rid'               => "1|8|9",
            'name'              => "开发主管",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);

        DB::table('admin_groups')->insert([
            'id'                => 10,
            'pid'               => 9,
            'rid'               => "1|8|9|10",
            'name'              => "程序员",
            'acl'               => "*",
            'created_at'        => date("Y-m-d H:i:s"),
        ]);
    }
}
