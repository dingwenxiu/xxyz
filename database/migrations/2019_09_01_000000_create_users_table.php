<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 用户相关的
class CreateUsersTable extends Migration
{

    public function up()
    {
        // 玩家列表
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('top_id');
            $table->integer('parent_id');
            $table->string('rid', 512);

            $table->string('username', 64);
            $table->string('nickname', 64);

            $table->string('phone',11)->nullable();

            $table->string('password', 64);
            $table->string('fund_password', 64)->nullable();

            $table->integer('prize_group');

            // 图标
            $table->string('user_icon', 64);

            //  1 直属  2 代理 3 会员
            $table->tinyInteger('type')->default(1)->comment("用户类型你:1 直属  2 代理 3 会员");

            // 下级总数
            $table->integer('subordinate_count')->default(0)->comment("下级总数");

            // 用户等级
            $table->integer('user_level')->default(0)->comment("用户等级");

            // vip 级别
            $table->integer('vip_level')->default(1)->comment("vip等级");

            // 财务级别
            $table->integer('finance_level')->default(1)->comment("财务级别");

            // 链接id
            $table->integer('link_id')->default(0)->comment("链接id");

            $table->tinyInteger('allowed_transfer')->default(0);

            $table->tinyInteger('is_tester')->default(1);

            // 日工资比列
            $table->decimal('salary_percentage', 5,2)->default(0);

            // 代理分红比列
            $table->decimal('bonus_percentage',5,2)->default(0);

            // 冻结类型 1, 禁止登录, 2, 禁止投注 3, 禁止提现 4, 禁止转账 5, 禁止资金
            $table->tinyInteger('frozen_type')->default(1)->comment("冻结类型:1, 禁止登录, 2, 禁止投注 3, 禁止提现, 4, 禁止转账, 5, 禁止资金");

            // 申请解冻
            $table->tinyInteger('unfrozen')->default(0)->comment("解除冻结:1, 解除禁止登录, 2, 解除禁止投注 3, 解除禁止提现, 4, 解除禁止转账, 5, 解除禁止资金");

            //备注
            $table->string('mark', 64)->default('');

            $table->char('register_ip', 15);
            $table->char('last_login_ip', 15)->default('');

            $table->integer('register_device')->default(1);
            $table->tinyInteger('register_type')->default(1);
            $table->integer('register_time');
            $table->integer('last_login_time')->default(0);
            $table->integer('last_online_time')->default(0);

            $table->integer('direct_child_count')->default(0);
            $table->integer('child_count')->default(0);
            $table->integer('login_times')->default(0);

            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            // 索引
            $table->index("username");
            $table->index("parent_id");
            $table->index("rid");
            $table->index("top_id");
            $table->index([ "partner_sign", "vip_level"], 'u_ps_vl');
            $table->index([ "register_time"], "u_ps_rt");
            $table->index([ "last_login_time"], "u_ps_llt");
        });

        // 玩家额外信息
        Schema::create('user_extend_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('user_id');

            $table->string('address', 128)->default('');
            $table->string('email', 64)->default('');
            $table->string('mobile', 64)->default('');
            $table->string('real_name', 64)->default('');
            $table->string('zip_code', 64)->default('');

            $table->timestamps();

            $table->index([ "partner_sign", "user_id"] );
            $table->index([ "user_id" ]);
        });

        // 用户账户
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('user_id')->unique();
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->string('rid', 512);

            // 单位分
            $table->unsignedBigInteger('balance')->default(0);
            $table->unsignedBigInteger('frozen')->default(0);
            $table->unsignedBigInteger('gift')->default(0);
            $table->unsignedBigInteger('score')->default(0);

            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'balance']);
            $table->index('top_id');
            $table->index('parent_id');
            $table->index("rid");
            $table->index("partner_sign");
        });

        // 用户银行卡
        Schema::create('user_bank_cards', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 32);

            $table->integer('user_id');
            $table->integer('parent_id');
            $table->integer('top_id');
            $table->string('username', 64);

            $table->string('bank_sign', 32);
            $table->string('bank_name', 64);

            $table->string('owner_name', 128);
            $table->string('card_number', 64);
            $table->string('province_id', 64);
            $table->string('city_id', 64);
            $table->string('branch', 64);

            $table->tinyInteger('status')->default(0); // 1 软删除
            $table->integer('admin_id')->default(0);

            $table->timestamps();

            $table->index('user_id');
            $table->index('username');
            $table->index('owner_name');
            $table->index('card_number');
            $table->index("partner_sign");
        });

        // 用户转账记录
        Schema::create('user_transfer_records', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64);

            $table->integer('from_parent_id');
            $table->integer('from_user_id');
            $table->string('from_username', 64);

            $table->integer('to_parent_id');
            $table->integer('to_user_id');
            $table->string('to_username', 64);

            $table->unsignedInteger('amount');
            $table->integer('add_time');
            $table->integer('day');
            $table->timestamps();

            $table->index("partner_sign");
            $table->index(["from_user_id", "add_time"], "utr_f_a");
            $table->index(["to_user_id", "add_time"], "utr_t_a");
            $table->index(["from_user_id", "to_user_id", "add_time"], "utr_f_t_a");
        });

        // 用户邀请链接
        Schema::create('user_invite_link', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64);

            $table->integer('user_id');
            $table->string('username', 64);                                    // 用户名

            $table->tinyInteger('type')->default(1);                            // 类型
            $table->integer('prize_group')->default(0);                         // 奖金组
            $table->string('code', 32)->default('');                    // 邀请码
            $table->string('qq', 64)->default('');
            $table->string('wechat', 64)->default('');                  // 微信
            $table->string('remark', 512)->default('');                 // 说明

            $table->integer('total_register')->default(0);                      // 总注册
            $table->string('channel', 32)->default(0);                  // 渠道
            $table->char('ip', 15)->default('');                        // 添加IP

            $table->tinyInteger('status')->default(1);                          // 状态

            $table->integer('expired_at');                                             // 过期时间

            $table->timestamps();
        });

        // 用户邀请 注册日志
        Schema::create('user_invite_link_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64);

            $table->integer('user_id');
            $table->integer('parent_id');

            $table->string('username', 64);
            $table->string('parent_name', 64);                              // 用户名

            // 设备
            $table->string('device_type', 32)->default('');
            $table->string('platform_type', 32)->default('');
            $table->string('platform_version', 64)->default('');
            $table->string('browser_type', 32)->default('');
            $table->string('browser_version', 64)->default('');
            $table->string('agent', 256)->default('');

            $table->integer('link_id')->default(0);                         // 邀请id
            $table->char('ip', 15)->default('');                    // IP

            $table->timestamps();
            $table->index("partner_sign", "ip");
            $table->index("user_id");
        });

        // 所有ip日志
        Schema::create('user_ip_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64);

            $table->integer('user_id');
            $table->integer('top_id');
            $table->integer('parent_id');

            $table->string('username', 64);
            $table->string('nickname', 64);

            $table->string('country', 64)->default('');
            $table->string('city', 64)->default('');
            $table->char('ip', 15);

            $table->integer('login_count')->default(0);

            $table->timestamps();

            $table->index("partner_sign", "ip");
            $table->index(['user_id', 'ip']);
        });


        // 所有玩家日志
        Schema::create('user_player_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64);

            $table->integer('user_id');
            $table->string('username', 64);

            $table->char('ip',          15);
            $table->char('proxy_ip',            15);

            $table->string('route',             64);
            $table->string('country',           64)->default('');
            $table->string('city',              64)->default('');            
            $table->text('params');

             // 域名和动作
            $table->string('domain',            64)->default('');
            $table->string('action',            32);

            $table->string('device', 64)->default('');
            $table->string('platform', 64)->default('');
            $table->string('browser', 64)->default('');
            $table->string('agent', 256)->default('');

            $table->integer('day');
            
            $table->timestamps();

            $table->index('partner_sign');
            $table->index('username');
        });

        // 玩家列表
        Schema::create('users_wechat', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);
            $table->integer('sender_id');
            $table->integer('receiver_id');
            $table->integer('receiver_iscustomer');
            $table->integer('sender_iscustomer');
            $table->string('content');
            $table->timestamps();

            $table->index("partner_sign");
            $table->index("sender_id");
            $table->index("receiver_id");
            $table->index("receiver_iscustomer");
            $table->index("sender_iscustomer");
            $table->index([ "partner_sign","sender_id","receiver_id"], "uw_ps_si_ri");
        });

        // ID 自增
        DB::update("ALTER TABLE `users` AUTO_INCREMENT = 10000;");
    }

    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_level_config');
        Schema::dropIfExists('user_vip_config');
        Schema::dropIfExists('user_accounts');
        Schema::dropIfExists('user_bank_cards');
        Schema::dropIfExists('user_transfer_records');
        Schema::dropIfExists('user_invite_link');
        Schema::dropIfExists('user_invite_link_log');
        Schema::dropIfExists('user_ip_log');
        Schema::dropIfExists('user_extend_info');
        Schema::dropIfExists('user_player_log');
        Schema::dropIfExists('users_wechat');
    }
}
