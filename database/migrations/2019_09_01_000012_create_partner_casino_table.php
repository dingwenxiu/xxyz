<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 商户管理相关
class CreatePartnerCasinoTable extends Migration
{

    public function up()
    {
        // 娱乐城游戏记录
        Schema::create('partner_casino_player_bet', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('top_id')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('rid',512)->index();

            $table->string('partner_sign',      64)->nullable();
            $table->string('username',      256)->nullable();

            $table->integer('site_id')->nullable();
            $table->string('site_username',     256)->nullable();
            $table->string('account_username',     256)->nullable();

            $table->string('game_code',     256)->nullable();

            $table->string('main_game_plat_code',     256)->nullable();
            $table->integer('method_id')->nullable();


            $table->string('platform_order_id',     64)->nullable();

            $table->decimal('bet_amount',     13, 3)->nullable();

            $table->decimal('company_payout_amount',     13, 3)->nullable();
            $table->decimal('company_win_amount',     13, 3)->nullable();
            $table->decimal('company_win_neat_amount',     13, 3)->nullable();

            $table->string('plat_type',     64)->nullable();
            $table->string('lobby_type',     64)->nullable();
            $table->string('bet_detail',     512)->nullable();
            $table->string('result',     512)->nullable();
            $table->string('c_name',     64)->nullable();

            $table->tinyInteger('bet_flow_available')->nullable();
            $table->tinyInteger('status')->nullable();

            $table->dateTime('bet_time')->nullable();
            $table->dateTime('pull_at')->nullable();
            $table->text('api_data')->nullable();

            $table->timestamps();

            $table->index("top_id");
            $table->index("parent_id");
            $table->index("user_id");
            $table->index("partner_sign");
            $table->index("account_username");
            $table->index("bet_time");
            $table->index("status");
            $table->index("main_game_plat_code");
            $table->index("plat_type");
            $table->index("platform_order_id");
            $table->index("company_payout_amount");
            $table->index("company_win_amount");
            $table->index("c_name");
            $table->index("bet_amount");
        });


        // 绑定娱乐城平台
        Schema::create('partner_casino_platforms', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign',      64);
            $table->string('main_game_plat_code',     64);
            $table->string('main_game_plat_name',     64);
            $table->decimal('rate',     5, 4);
            $table->string('image',     256)->default('/klc/logo/logo_image_pc_1.png');

            $table->tinyInteger('status')->default(1);

            $table->integer('add_admin_id')->default(999999);
            $table->integer('update_admin_id')->default(0);

            $table->integer('update_partner_admin_id')->default(0);
            $table->timestamps();

            $table->index("partner_sign");
        });

        // 绑定娱乐城平台
        Schema::create('partner_casino_methods', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign',      32);
            $table->string('main_game_plat_code',      64);
            $table->string('cn_name',      128);
            $table->string('en_name',      128)->nullable();
            $table->string('pc_game_code',     64)->nullable();
            $table->string('pc_game_deputy_code',     64)->nullable();
            $table->string('mobile_game_code',     64)->nullable();
            $table->string('mobile_game_deputy_code',     64)->nullable();
            $table->string('record_match_code',     128)->nullable();
            $table->string('record_match_deputy_code',     128)->nullable();
            $table->string('img',     256)->nullable();
            // 广告图片
            $table->string('ad_img', 256)->default('');
            $table->string('type',     32)->nullable();
            $table->string('category',     32)->nullable();
            $table->integer('line_num')->default(0);
            $table->integer('able_demo')->default(0);
            $table->integer('able_recommend')->default(0);
            $table->decimal('bonus_pool', 13,3)->default(0.000);
            $table->tinyInteger('home')->default(0);
            $table->integer('add_admin_id')->default(999999);
            $table->tinyInteger('status')->default(1);
            $table->timestamp('deleted_at')->nullable();


            $table->timestamps();

            $table->index("partner_sign");
        });

        // 绑定娱乐城平台--游戏分类
        Schema::create('partner_casino_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',64);
            $table->string('code',64);
            $table->tinyInteger('home')->default(0);

            $table->integer('add_admin_id')->default(999999);
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });

        // 绑定娱乐城平台---游戏接口记录
        Schema::create('partner_casino_api_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign',32);
            $table->integer('user_id');
            $table->string('username',64);

            // 接口
            $table->string('api',64);

            // 平台
            $table->string('platform_sign',32);
            $table->string('call_url',256);

            // 来源
            $table->tinyInteger('from')->default(1);
            $table->char('ip',15);

            $table->text('params');
            $table->text('return_content');

            $table->timestamps();

            $table->index([ "partner_sign", "user_id", "api"], 'cal_ps_u_t');
            $table->index([ "partner_sign", "platform_sign", "api"], 'cal_ps_ps_t');
        });

        // 每日数据更新---游戏记录
        Schema::create('report_stat_casino_days', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign',64);
            $table->string('main_game_plat_code',64);


            // 来源
            $table->integer('day')->default(0);
            $table->integer('month')->default(0);

            $table->decimal('bet_amount', 13, 3)->default(0.000);
            $table->decimal('company_win_amount', 13, 3)->default(0.000);
            $table->decimal('company_payout_amount', 13, 3)->default(0.000);
            $table->decimal('casino_transfer_out', 13, 3)->default(0.000);
            $table->decimal('casino_transfer_in', 13, 3)->default(0.000);


            $table->timestamps();

            $table->index('partner_sign');
            $table->index('main_game_plat_code');
            $table->index('month');
            $table->index('day');
        });

    }

    public function down()
    {
        Schema::dropIfExists('report_stat_casino_days');
        Schema::dropIfExists('partner_casino_platforms');
        Schema::dropIfExists('partner_casino_methods');
        Schema::dropIfExists('partner_casino_categories');
        Schema::dropIfExists('partner_casino_api_logs');
        Schema::dropIfExists('partner_casino_player_bet');
    }
}
