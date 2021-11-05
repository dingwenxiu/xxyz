<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysTable extends Migration
{
    /**
     *　网站配置
     * @return void
     */
    public function up() {
        /**
         * 系统配置
         */
        Schema::create('sys_configures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid')->comment('父类id, 顶级为0');
            $table->string('sign', 64)->comment('sign 标识');
            $table->string('name', 64)->comment('标题');
            $table->string('description',   128)->nullable()->comment('描述');
            $table->string('value',         128)->comment('配置选项value');


            $table->integer('add_admin_id')->default(0)->comment('添加人, 系统添加为0');
            $table->integer('last_update_admin_id')->default(0)->comment('上次更改人id');

            $table->tinyInteger('partner_edit')->default(0)->comment('0 禁用 1 启用');
            $table->tinyInteger('partner_show')->default(0)->comment('0 禁用 1 启用');

            $table->tinyInteger('status')->default(0)->comment('0 禁用 1 启用');
            $table->index('sign');
            $table->timestamps();
        });

        // 城市列表
        Schema::create('sys_city', function (Blueprint $table) {
            $table->increments('id');

            $table->string('region_id',        16);
            $table->string('region_parent_id', 16);
            $table->string('region_name',      64);
            $table->tinyInteger('region_level')->nullable()->comment('1.省 2.市(市辖区)3.县(区、市)4.镇(街道)');
            $table->nullableTimestamps();;
        });

        // 银行
        Schema::create('sys_bank', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 32)->comment('标题');
            $table->string('code',16)->comment('标识');
            $table->string('icon',32)->comment('图标');
            $table->tinyInteger('status')->default(1)->comment('0 禁用 1 启用');
            $table->timestamps();
        });

        // 统计
        Schema::create('sys_stat', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('report_start_m')->default(0);
            $table->unsignedBigInteger('report_end_m')->default(0);

            $table->unsignedBigInteger('stack_start_m')->default(0);
            $table->unsignedBigInteger('stack_end_m')->default(0);

            $table->integer('type')->default(1);

            $table->integer('total_report_count')->default(0);
            $table->integer('total_stack_count')->default(0);
            $table->integer('total_report_fail_count')->default(0);
            $table->integer('total_stack_fail_count')->default(0);
            $table->integer('project_count')->default(0);
            $table->integer('commission_count')->default(0);

            $table->integer('player_count')->default(0);
            $table->integer('recharge_count')->default(0);
            $table->integer('withdraw_count')->default(0);

            $table->integer('start_time')->default(0);
            $table->integer('end_time')->default(0);

            $table->integer('status')->default(1);

            $table->timestamps();
            $table->index(['report_start_m', 'report_end_m']);
        });

        // 推送渠道
        Schema::create('sys_telegram_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32)->default('');
            $table->string('channel_sign', 32)->default('');
            $table->string('channel_group_name', 64)->default('');

            $table->string('channel_id', 128)->default(0);
            $table->integer('status')->default(1);
            $table->unique(['channel_sign','channel_group_name']);
            $table->timestamps();
        });

        // 失败job
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_configures');
        Schema::dropIfExists('sys_city');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('sys_bank');
        Schema::dropIfExists('sys_stat');
        Schema::dropIfExists('sys_telegram_channel');
    }
}
