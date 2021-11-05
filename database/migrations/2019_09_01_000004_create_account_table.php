<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTable extends Migration
{
    /**
     *　帐变类型
     * @return void
     */
    public function up() {

        // 帐变类型
        Schema::create('account_change_type', function(Blueprint $table) {
            $table->increments('id');
            $table->string('sign',32);
            $table->string('name', 32);

            // 1 +, 2 -
            $table->tinyInteger('type')->default(1);

            $table->tinyInteger('amount')->default(1);
            $table->tinyInteger('user_id')->default(1);
            $table->tinyInteger('project_id')->default(1);
            $table->tinyInteger('lottery_sign')->default(1);
            $table->tinyInteger('lottery_name')->default(1);
            $table->tinyInteger('method_sign')->default(1);
            $table->tinyInteger('method_name')->default(1);
            $table->tinyInteger('mode')->default(1);
            $table->tinyInteger('issue')->default(1);

            $table->tinyInteger('casino_platform_sign')->default(1);

            $table->tinyInteger('from_id')->default(1);
            $table->tinyInteger('from_admin_id')->default(1);
            $table->tinyInteger('to_id')->default(1);
            $table->tinyInteger('frozen_type')->default(1);
            $table->tinyInteger('activity_sign')->default(1);

            $table->integer('admin_id')->default(0);
            $table->timestamps();
        });

        // 帐变
        Schema::create('account_change_report', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('partner_sign', 32);

            $table->integer('user_id')->index();
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->string('rid',512)->index();
            $table->string('username',32);

            $table->integer('from_id')->default(0);
            $table->integer('from_admin_id')->default(0);
            $table->integer('to_id')->default(0);

            $table->string('type_sign',32);
            $table->string('type_name',32)->nullable();

            $table->string('lottery_sign',32)->nullable();
            $table->string('lottery_name',64)->nullable();
            $table->string('method_sign',32)->nullable();
            $table->string('method_name',64)->nullable();
            $table->integer('project_id')->default(0);
            
            // 元,角,分 模式
            $table->integer('mode')->default(1);
            $table->string('issue',64)->nullable();

            $table->string('casino_platform_sign',32)->nullable();

            $table->unsignedBigInteger('day_m')->index();

            // 活动
            $table->string('activity_sign', 64)->nullable()->index();

            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedBigInteger('before_balance')->default(0);
            $table->unsignedBigInteger('balance')->default(0);
            $table->unsignedBigInteger('before_frozen_balance')->default(0);
            $table->unsignedBigInteger('frozen_balance')->default(0);

            $table->tinyInteger('frozen_type')->default(0);
            $table->tinyInteger('is_tester')->default(0);

            $table->integer('process_time')->default(0);
            $table->integer('stat_time')->default(0);

            $table->string('desc', 256);
            $table->timestamps();

            $table->index(array("partner_sign", "username", "process_time"), "acr_ps_ui_pt");
            $table->index(array("partner_sign", "username", "type_sign",), "acr_ps_ts");
            $table->index(array("partner_sign", "type_sign", "process_time"), "acr_ps_ts_pt");
            $table->index(array("partner_sign", "lottery_sign", 'method_sign'), "acr_ps_ls_ms");
            $table->index(array("partner_sign", "issue", "project_id"), "acr_ps_i_pi");
            $table->index(array("partner_sign", "project_id"), "acr_ps_pi");
            $table->index(array("lottery_sign", "issue", "type_sign"), "acr_ls_i_ts");
            $table->index(array("type_sign", "stat_time", "day_m"), "acr_ts_d_st");
            $table->index(array("type_sign", "stat_time"), "acr_ts_st");
            $table->index(array("partner_sign", "top_id"), "acr_ps_ti");
            $table->index('mode');
        });

        // 礼金 帐变记录
        Schema::create('account_gift_change_report', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('partner_sign', 32);

            $table->integer('user_id')->index();
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->string('rid',512)->index();
            $table->string('username',32);
            $table->tinyInteger('is_tester')->default(0);

            $table->integer('flow_type')->default(1);       // 流动类型 1 出 2 进
            $table->integer('related_type')->default(1);    // 关联类型 1 活动 2 人工赠送 等等
            $table->integer('related_id')->default(0);      // 如果有记录 填写记录ID

            $table->unsignedBigInteger('day_m')->index();

            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedBigInteger('before_amount')->default(0);
            $table->unsignedBigInteger('current_amount')->default(0);

            $table->integer('process_time')->default(0);

            $table->string('desc', 256);
            $table->timestamps();

            $table->index(array("partner_sign", "username", "process_time"), "acr_ps_ui_pt");
            $table->index(array("partner_sign", "username", "related_id",), "acr_ps_ts");
        });

        // 积分 帐变记录
        Schema::create('account_score_change_report', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('partner_sign', 32);

            $table->integer('user_id')->index();
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->string('rid',512)->index();
            $table->string('username',32);
            $table->tinyInteger('is_tester')->default(0);

            // 关联类型
            $table->integer('flow_type')->default(1);       // 流动类型 1 出 2 进
            $table->integer('related_type')->default(1);    // 关联类型 1 活动 2 人工赠送 等等
            $table->integer('related_id')->default(0);      // 如果有记录 填写记录ID

            $table->unsignedBigInteger('day_m')->index();

            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedBigInteger('before_amount')->default(0);
            $table->unsignedBigInteger('current_amount')->default(0);

            $table->integer('process_time')->default(0);

            $table->string('desc', 256);
            $table->timestamps();

            $table->index(array("partner_sign", "username", "process_time"), "acr_ps_ui_pt");
            $table->index(array("partner_sign", "username", "related_id",), "acr_ps_ts");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_change_type');
        Schema::dropIfExists('account_change_report');
        Schema::dropIfExists('account_gift_change_report');
        Schema::dropIfExists('account_score_change_report');
    }
}
