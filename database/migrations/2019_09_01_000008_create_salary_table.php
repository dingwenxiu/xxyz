<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 平台
class CreateSalaryTable extends Migration
{
    /**
     *　平台配置
     * @return void
     */
    public function up() {
        // 日工资 配置
        Schema::create('user_salary_config', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('top_id');
            $table->integer('parent_id');
            $table->integer('user_id');
            $table->string('parent_username', 64);
            $table->string('username', 64);

            // 1 固定 2 阶梯
            $table->tinyInteger('type')->default(1);

            $table->tinyInteger('user_type')->default(1);
            $table->text('contract_before');
            $table->text('contract');
            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->index("user_id");
            $table->index("partner_sign");
            $table->index(['parent_id', 'user_id']);
        });

        // 日工资 记录
        Schema::create("report_user_salary", function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('top_id')->default(0);
            $table->integer('parent_id')->default(0);
            $table->integer('user_id');
            $table->tinyInteger('is_tester')->default(0);        // 是否測試用戶

            $table->integer('user_level');

            $table->string('parent_username', 64)->default('');
            $table->string('username', 64);

            $table->unsignedBigInteger('self_bets')->default(0);
            $table->unsignedBigInteger('self_cancel')->default(0);
            $table->unsignedBigInteger('self_he_return')->default(0);
            $table->unsignedBigInteger('self_real_bet')->default(0);

            $table->unsignedBigInteger('self_commission_from_bet')->default(0);
            $table->unsignedBigInteger('self_commission_from_child')->default(0);

            $table->unsignedBigInteger('team_bets')->default(0);
            $table->unsignedBigInteger('team_cancel')->default(0);
            $table->unsignedBigInteger('team_he_return')->default(0);
            $table->unsignedBigInteger('team_real_bet')->default(0);

            $table->unsignedBigInteger('team_commission_from_bet')->default(0);
            $table->unsignedBigInteger('team_commission_from_child')->default(0);

            // 金额
            $table->unsignedBigInteger('total_salary')->default(0);
            $table->unsignedBigInteger('child_salary')->default(0);
            $table->unsignedBigInteger('self_salary')->default(0);

            // 实际发送金额
            $table->unsignedBigInteger('real_salary')->default(0);

            // 单位
            $table->decimal('rate', 5, 2)->default(0);

            $table->integer('day');

            $table->integer('init_time')->default(0);
            $table->integer('count_time')->default(0);
            $table->integer('send_time')->default(0);
            $table->integer('resend_time')->default(0);

            $table->tinyInteger('status')->default(0);

            $table->timestamps();

            $table->index(["day", "username"]);
            $table->index(["day", "parent_username"]);
            $table->index(['partner_sign', "day"]);
            $table->unique(["user_id", "day"]);
        });

    }

    public function down() {
        Schema::dropIfExists('user_salary_config');
        Schema::dropIfExists('report_user_salary');
    }
}
