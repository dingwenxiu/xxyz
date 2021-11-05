<?php

use Illuminate\Database\Seeder;

class ActivityPrizesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_prizes')->insert(
            [
                'type' => 'checkin',
                'name' => '礼金',
                'img'  => '',
                'status' => 1,
            ]
        );
        DB::table('activity_prizes')->insert(
            [
                'type' => 'checkin',
                'name' => '积分',
                'img'  => '',
                'status' => 1,
            ]
        );
        DB::table('activity_prizes')->insert(
            [
                'type' => 'checkin',
                'name' => '钱',
                'img'  => '',
                'status' => 1,
            ]
        );
    }
}
