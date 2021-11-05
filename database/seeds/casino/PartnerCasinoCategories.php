<?php

use Illuminate\Database\Seeder;

class PartnerCasinoCategories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 娱乐城游戏 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
        // 娱乐城游戏类型
        DB::table('partner_casino_categories')->insert(
            [
                'id'           => 1,
                'name'         => '真人视讯',
                'code'         => 'live',
                'home'         => 1,
                'add_admin_id' => 999999,
                'status'       => 1,
            ]
        );
        DB::table('partner_casino_categories')->insert(
            [
                'id'           => 2,
                'name'         => '电子游戏',
                'code'         => 'e-game',
                'home'         => 1,
                'add_admin_id' => 999999,
                'status'       => 1,
            ]
        );
        DB::table('partner_casino_categories')->insert(
            [
                'id'           => 3,
                'name'         => '电子竞技',
                'code'         => 'e-sports',
                'home'         => 1,
                'add_admin_id' => 999999,
                'status'       => 1,
            ]
        );
        DB::table('partner_casino_categories')->insert(
            [
                'id'           => 4,
                'name'         => '体育',
                'code'         => 'sport',
                'home'         => 1,
                'add_admin_id' => 999999,
                'status'       => 1,
            ]
        );
        DB::table('partner_casino_categories')->insert(
            [
                'id'           => 5,
                'name'         => '彩票',
                'code'         => 'lottery',
                'home'         => 1,
                'add_admin_id' => 999999,
                'status'       => 1,
            ]
        );
        DB::table('partner_casino_categories')->insert(
            [
                'id'           => 6,
                'name'         => '棋牌',
                'code'         => 'card',
                'home'         => 1,
                'add_admin_id' => 999999,
                'status'       => 1,
            ]
        );
        DB::table('partner_casino_categories')->insert(
            [
                'id'           => 7,
                'name'         => '金融',
                'code'         => 'finance',
                'home'         => 1,
                'add_admin_id' => 999999,
                'status'       => 1,
            ]
        );
    }
}
