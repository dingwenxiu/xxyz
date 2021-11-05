<?php

use Illuminate\Database\Seeder;

class TemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('templates')->insert([
            'id'        => 1,
            'name'      => "游侠默认模板",
            'sign'      => "youxia",
            'status'    => 1,
            'module_sign'    => 'hot,is_hot,popular,hotGameCard,hotGameLive,hotGameEGame,templateColor',
            'partner_sign'    => '',
        ]);
        
        DB::table('templates')->insert([
            'id'        => 2,
            'name'      => "快乐彩模板",
            'sign'      => "klc",
            'status'    => 1,
            'module_sign'    => 'hot,is_hot,popular,recommend_open_lottery,hotGameLive,hotGameEGame,hotGameCard,hotGameSport,templateColor',
            'partner_sign'    => '',
        ]);




        // ========================== 模板颜色 ======

        DB::table('template_colors')->insert([
            'id'                    => 1,
            'name'                  => "主色",
            'sign'                  => "main_color",
            'value'                 => "#800fa9",
            'status'                => 1,
        ]);

        DB::table('template_colors')->insert([
            'id'                    => 2,
            'name'                  => "副色",
            'sign'                  => "secondary_color",
            'value'                 => "#ffd0b8",
            'status'                => 1,
        ]);

        DB::table('template_colors')->insert([
            'id'                    => 3,
            'name'                  => "边框拉杆色",
            'sign'                  => "frame_color",
            'value'                 => "#ffd0b8",
            'status'                => 1,
        ]);

        DB::table('template_colors')->insert([
            'id'                    => 4,
            'name'                  => "按钮色",
            'sign'                  => "button_color",
            'value'                 => "#ffd0b8",
            'status'                => 1,
        ]);

    }
}

