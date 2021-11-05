<?php

use Illuminate\Database\Seeder;

// 模块
class AdminModulesTableSeeder extends Seeder
{

    public function run()
    {
        /** ======================= 菜单管理 ＠seed ========================== */

        // 系统管理
        DB::table('admin_modules')->insert(
            [
                'name'    => "推荐彩票",
                'm_name'  => "hot",
                'sign'    => "hot",
                'route'   => 'lottery/lotteryList',
                'param'   => '{"page_size":200}',
                'num_max' => 16,
                'style'   => 1,
                'status'  => 1,
            ]
        );
        DB::table('admin_modules')->insert(
            [
                'name'    => "热门彩票",
                'm_name'  => "is_hot",
                'sign'    => "is_hot",
                'route'   => 'lottery/lotteryList',
                'param'   => '{"page_size":200}',
                'num_max' => 8,
                'style'   => 1,
                'status'  => 1,
            ]
        );
        DB::table('admin_modules')->insert(
            [
                'name'    => "热门玩法",
                'm_name'  => "popular",
                'sign'    => "popular",
                'route'   => 'lottery/methodList',
                'num_max' => 4,
                'param'   => '{"method_group":"QW","logic_sign":"ssc","page_size":200}',
                'style'   => 1,
                'status'  => 1,
            ]
        );
        DB::table('admin_modules')->insert(
            [
                'name'    => "推荐开奖",
                'm_name'  => "recommend_open_lottery",
                'sign'    => "recommend_open_lottery",
                'route'   => 'lottery/lotteryList',
                'param'   => '',
                'num_max' => 4,
                'style'   => 1,
                'status'  => 1,
            ]
        );
        DB::table('admin_modules')->insert(
            [
                'name'    => "推荐视讯",
                'm_name'  => "hotGame",
                'sign'    => "hotGameLive",
                'route'   => 'casino/getGameList',
                'param'   => '{"category": "live","cn_name":"大厅"}',
                'num_max' => 6,
                'style'   => 2,
                'status'  => 1,
            ]
        );
        DB::table('admin_modules')->insert(
            [
                'name'    => "推荐电游",
                'm_name'  => "hotGame",
                'sign'    => "hotGameEGame",
                'route'   => 'casino/getGameList',
                'param'   => '{"category": "e-game"}',
                'num_max' => 6,
                'style'   => 2,
                'status'  => 1,
            ]
        );
        DB::table('admin_modules')->insert(
            [
                'name'    => "推荐棋牌",
                'm_name'  => "hotGame",
                'sign'    => "hotGameCard",
                'route'   => 'casino/getGameList',
                'param'   => '{"category": "card"}',
                'num_max' => 6,
                'style'   => 2,
                'status'  => 1,
            ]
        );
        DB::table('admin_modules')->insert(
            [
                'name'    => "推荐体育",
                'm_name'  => "hotGame",
                'sign'    => "hotGameSport",
                'route'   => 'casino/getGameList',
                'param'   => '{"category": "sport"}',
                'num_max' => 6,
                'style'   => 2,
                'status'  => 1,
            ]
        );

        DB::table('admin_modules')->insert(
            [
                'name'    => "主题颜色",
                'm_name'  => "templateColor",
                'sign'    => "templateColor",
                'route'   => 'getTemplateColor',
                'param'   => '',
                'num_max' => 4,
                'style'   => 1,
                'status'  => 1,
            ]
        );
    }
}
