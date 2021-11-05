<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 后台管理相关
class CreateAdminTable extends Migration
{
    public function up()
    {
        // 管理用户
        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('username',  64);
            $table->string('email',     64);
            $table->integer('group_id');
            $table->string('password', 64);
            $table->string('fund_password', 64);

            $table->string('theme', 32)->default('default');

            $table->string('remember_token', 64)->default('');

            // 数据
            $table->char('register_ip',     15);
            $table->char('last_login_ip',   15)->default('');

            $table->integer('last_login_time')->default(0);
            $table->string('last_login_domain', 64)->default(0);

            $table->integer('login_times')->default(0);

            // 操作人
            $table->integer('add_admin_id')->default(0);
            $table->integer('update_admin_id')->default(0);

            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });

        // 后台管理菜单
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid');
            $table->string('rid',64)->default("");

            // 菜单
            $table->string('title',         64);
            $table->string('route',         64);

            $table->string('api_path',     64)->default("");

            // 整形
            $table->integer('sort')->default(0);
            $table->string('css_class',     64)->default("");

            // 1 菜单 2 链接
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('level')->default(1);
            $table->tinyInteger('status')->default(1);

            $table->integer('admin_id')->default(0);
            $table->timestamps();
        });

        // 管理组
        Schema::create('admin_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid');
            $table->tinyInteger('status');
            $table->string('rid',64);
            $table->string('name', 32);
            $table->integer('member_count')->default(0);
            $table->text('acl');
            $table->timestamps();
        });

        // 管理组 - 用户
        Schema::create('admin_group_users', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('group_id');
            $table->integer('user_id');

            $table->timestamps();
        });

        // 后台角色
        Schema::create('admin_access_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('admin_id')->default(0);
            $table->string('admin_username',    64)->default("");

            $table->char('ip',                  15);
            $table->char('proxy_ip',            15);

            $table->string('route',             64);
            $table->string('country',           64)->default('');
            $table->string('city',              64)->default('');

            $table->mediumText('params');

            // 域名和动作
            $table->string('domain',            64)->default('');
            $table->string('action',            32);

            // 信息
            $table->string('device',    32)->default('');
            $table->string('platform',  32)->default('');
            $table->string('browser',   32)->default('');
            $table->string('agent',     256)->default('');

            $table->integer('day');
            $table->timestamps();
        });

        // 用户行为
        Schema::create('admin_behavior', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('admin_id')->default(0);
            $table->string('admin_username',    64)->default("");

            $table->char('ip',                  15);
            $table->char('proxy_ip',            15);

            $table->string('route',             64);
            $table->string('country',           64)->default('');
            $table->string('city',              64)->default('');

            $table->mediumText('params');

            // 域名和动作
            $table->string('domain',            64)->default('');

            $table->string('action',            32);
            $table->mediumText('context')->nullable();

            // 信息
            $table->string('device',    32)->default('');
            $table->string('platform',  32)->default('');
            $table->string('browser',   32)->default('');
            $table->string('agent',     256)->default('');

            $table->integer('day');
            $table->timestamps();
        });

        // 管理审核动作
        Schema::create('admin_action_review', function (Blueprint $table) {
            $table->increments('id');

			$table->integer('config_id')->default(0);
			$table->integer('config_pid')->default(0);
			$table->string('config_name',64)->default('');
			$table->string('config_sign',64)->default('');
			$table->string('config_value',128)->default('');
			$table->string('config_description',128)->nullable();
			$table->tinyInteger('config_partner_show')->default(0);
			$table->tinyInteger('config_partner_edit')->default(0);
			$table->tinyInteger('config_is_edit_pid')->default(0);
			$table->string('config_partner_sign',64)->default('');


			$table->integer('partner_admin_id');
            $table->string('partner_admin_name',     64);

            // 处理类型
            $table->string('type', 32);
            $table->string('request_desc', 128);

            // 配置 / 描述
            $table->string('value', 128);
            $table->string('process_desc', 128);
            // 审核内容集结
            $table->string('process_config', 256);

            // ip
            $table->char('request_ip',      15);
            $table->char('review_ip',       15)->default('');

            // 时间
            $table->integer('request_time')->default(0);
            $table->integer('review_time')->default(0);


            // 添加人员
            $table->integer('request_admin_id')->default(0);
            $table->string('request_admin_name', 64)->default('');

            // 审核人员
            $table->integer('review_admin_id')->default(0);
            $table->string('review_admin_name', 64)->default('');

            // 审核失败原因
            $table->string('review_fail_reason', 64)->default('');

            // 当前状态
            $table->tinyInteger('status')->default(0);

            $table->timestamps();
        });

        // 后台转账记录
        Schema::create('admin_transfer_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->integer('user_id');
            $table->string('username', 64);                                // 用户名
            $table->string('nickname', 64)->default('');


            $table->string('mode')->default('add');                         // 模型
            $table->tinyInteger('type')->default(1);                        // 类型

            $table->unsignedInteger('amount');                                     // 金额
            $table->integer('admin_id')->default(0);                        // 管理员ID

            $table->string('admin_name', 32)->default(0);           // 管理员名称
            $table->string('reason', 128)->default('');             // 原因

            $table->string('process_admin_name', 32)->default(0);   // 管理员名称
            $table->string('process_reason', 128)->default('');     // 原因

            $table->integer('add_time');                                           // 添加时间
            $table->integer('process_time')->default(0);                    // 处理时间
            $table->integer('stat_time')->default(0);                       // 统计时间

            $table->tinyInteger('status')->default(1);                      // 状态

            $table->timestamps();
            $table->index(array("user_id", "mode"));
        });

        // 首页模块配置
        Schema::create('admin_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 256)->nullable();
            $table->string('m_name', 256)->nullable();
            $table->string('route', 256)->nullable();
            $table->string('param', 256)->nullable();
            $table->string('sign', 64)->nullable();
            $table->string('template_sign', 256)->nullable()->comment('模型');
            $table->tinyInteger('num_max')->comment('显示最大个数');
            $table->tinyInteger('status')->default(0)->comment('0 禁用 1 启用');
            $table->tinyInteger('style')->default(1)->comment('1 彩票 2 娱乐城');
            $table->timestamps();
        });


         /**
         * 审核流程
         */

        Schema::create('admin_review_flow', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('type')->comment('审核类型 1＝修改商户密码，2＝修改商户配置');


            $table->json('admins')->comment('用户');

            $table->timestamps();

            $table->index("type");
        });
    }


    public function down()
    {
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('admin_menus');
        Schema::dropIfExists('admin_groups');
        Schema::dropIfExists('admin_modules');
        Schema::dropIfExists('admin_group_users');
        Schema::dropIfExists('admin_access_logs');
        Schema::dropIfExists('admin_behavior');
        Schema::dropIfExists('admin_action_review');
        Schema::dropIfExists('admin_transfer_records');
        Schema::dropIfExists('admin_review_flow');
    }
}
