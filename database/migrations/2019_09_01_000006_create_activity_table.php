<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 活动相关
class CreateActivityTable extends Migration
{

    public function up()
    {
        // -------------- start peng---------------------
        // 活动规则大概
        Schema::create('activity_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 64)->default('')->comment('活动类型');
            $table->string('name', 64)->default('')->comment('活动名称');
            $table->tinyInteger('status')->default(1)->comment('开启关闭活动');
            $table->text('prize')->default('')->comment('活动礼品类型 1 礼金  2 积分 3 钱');
            $table->text('obtain_type')->default('')->comment('领取方式 1 及时领取 2 第二天赠送 3 客服领取');
            $table->text('home')->default('')->comment('首页静态图展示 1 展示 2不展示');
            $table->text('participants')->default('')->comment('参与人员 1 直属 2 代理 3 所有会员');
            $table->text('give_type')->default('')->comment('1 =》 固定金额, 2 比例');
            $table->text('params')->default('')->comment('活动所需要的参数');
            $table->text('open_partner')->default('')->comment('开启活动的商户');
            $table->string('img_list', 64)->default('')->comment('活动图片');
            $table->string('img_info', 64)->default('')->comment('活动图片');
            $table->dateTime('start_time')->nullable()->comment('活动开始时间');
            $table->dateTime('end_time')->nullable()->comment('活动结束时间');

            $table->timestamps();
        });

        // 活动规则详情
        Schema::create('partner_activity_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64)->default('')->comment('合作者标记');
            $table->string('type', 64)->default('')->comment('活动类型');
            $table->string('name', 64)->default('')->comment('活动名称');

            $table->text('params')->default('')->comment('活动所需要的参数');
            $table->text('pc_desc')->default('')->comment('');
            $table->text('h5_desc')->default('')->comment('');
            $table->string('img_banner', 256)->default('')->comment('活动图片');
            $table->string('img_list', 256)->default('')->comment('活动图片');
            $table->string('img_info', 256)->default('')->comment('活动图片');
            $table->tinyInteger('home')->default(1)->comment('1 显示 2不显示');
            $table->tinyInteger('status')->default(1)->comment('1 开启 2禁用');
            $table->tinyInteger('login_show')->default(1)->comment('1 需要登录显示 2 不需要登录显示');
            $table->dateTime('start_time')->nullable()->comment('活动开始时间');
            $table->dateTime('end_time')->nullable()->comment('活动结束时间');

            $table->timestamps();
            $table->index('partner_sign');
        });

        // 活动礼品
        Schema::create('partner_activity_prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64)->default('')->comment('合作者标记');
            $table->string('type', 64)->default('')->comment('奖品类型');
            $table->string('name', 64)->default('')->comment('奖品名称');
            $table->string('img', 64)->default('')->comment('奖品图片');
            $table->tinyInteger('status')->default(1)->comment('1 开启 2 关闭');
            $table->timestamps();
            $table->index('type');
            $table->index('name');
            $table->index('status');
            $table->index('partner_sign');
        });

        Schema::create('activity_prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 64)->default('')->comment('奖品类型');
            $table->string('name', 64)->default('')->comment('奖品名称');
            $table->string('img', 64)->default('')->comment('奖品图片');
            $table->tinyInteger('status')->default(1)->comment('1 开启 2 关闭');
            $table->timestamps();
            $table->index('type');
            $table->index('name');
            $table->index('status');
        });

        // 活动记录
        Schema::create('partner_activity_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64)->default('')->comment('合作者标记');
            $table->string('type', 64)->default('')->comment('活动类型');
            $table->string('type_child', 64)->default('')->comment('活动子类型');
            $table->string('type_name', 64)->default('')->comment('活动类型');
            $table->integer('active_id')->default(0)->comment('活动id');
            $table->tinyInteger('prize')->nullable()->comment('活动礼品类型 1 礼金  2 积分 3 钱  10 每日签到无奖励');
            $table->float('prize_val', 8, 2)->nullable()->comment('礼品金额');
            $table->integer('user_id')->comment('会员id');

            $table->integer('possible')->nullable()->comment('条件');
            $table->integer('possible_val')->nullable()->comment('条件值');
            $table->string('lottery_sign', 64)->default('')->comment('彩票id');
            $table->string('order_id', 64)->default('')->comment('参与活动的订单id');

            $table->tinyInteger('obtain_type')->nullable()->comment('领取的方式 1 及时领取 2 第二天赠送 3 客服领取 4 每日签到领取无奖励');
            $table->tinyInteger('check')->nullable()->comment('1 => 需要审核, 2 => 不需要审核');
            $table->string('username', 64)->default('')->comment('会员名称');
            $table->integer('top_id')->default(0)->comment('顶级id');
            $table->integer('parent_id')->default(0)->comment('顶级id');
            $table->string('rid', 512)->default('')->comment('所有上级id');
            $table->string('reason', 512)->default('')->comment('拒绝的理由');
            $table->tinyInteger('status')->nullable()->comment('领取类型 1 已领去/审核同意  2 未领取, 3 审核-拒绝, 4 次日发放失败, 5 客服拒绝发放, 6 锁住, 7 管理员-拒绝');
            $table->integer('admin_id')->default(0)->comment('最后一次操作的管理员id');
            $table->string('admin_name', 64)->default('')->comment('管理账号');
            $table->char('client_ip', 15)->default('');          // 充值IP

            $table->timestamps();

            $table->index('partner_sign');
            $table->index('type');
            $table->index('prize');
            $table->index('user_id');
            $table->index('admin_id');
            $table->index('admin_name');
            $table->index('username');
            $table->index('top_id');
            $table->index('parent_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('client_ip');
            $table->index('rid');
        });
    }

    public function down()
    {
        // ---- start --- peng ---
        Schema::dropIfExists('partner_activity_logs');
        Schema::dropIfExists('partner_activity_prizes');
        Schema::dropIfExists('activity_prizes');
        Schema::dropIfExists('activity_rules');
        Schema::dropIfExists('partner_activity_rules');
        // ---- edn  --- peng ---
    }
}
