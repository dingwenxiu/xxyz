<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 商户管理相关
class CreatePartnerTable extends Migration
{

    public function up()
    {

        // 模块
        Schema::create('partner_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 256)->nullable();
            $table->string('m_name', 256)->nullable();
            $table->string('sign', 64)->nullable();
            $table->string('route', 256)->nullable();
            $table->string('param', 256)->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 禁用 1 启用');
            $table->tinyInteger('style')->default(1)->comment('1 彩票 2 娱乐城');
            $table->string('template_sign', 256)->nullable()->comment('模型');
            $table->string('partner_sign', 128)->comment('代理');
            $table->tinyInteger('num_max')->comment('显示最大个数');

            $table->timestamps();
        });

        // 导航
        Schema::create('partner_navigations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign',  64)->default('')->comment('商户sign');
            $table->string('name',  256)->default('')->comment('商户name');
            $table->string('url',  256)->default('')->comment('导航链接');
            $table->tinyInteger('style')->default(1)->comment('1 本地当行 2 娱乐城导航 需要获取平台id');
            $table->string('casino_plat_id',  256)->nullable()->comment('平台id');
            $table->integer('order')->nullable()->comment('排序');
            $table->tinyInteger('status')->default(0)->comment('0 禁用 1 启用');
            $table->tinyInteger('home')->default(1);

            $table->timestamps();
        });


        // 首页
        Schema::create('partner_homes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign',  64)->default('')->comment('商户sign');
            $table->integer('module_id')->comment('模块id');
            $table->string('other_id',  256)->comment('关联游戏id');
            $table->string('value',  256)->default('')->comment('关联游戏id');
            $table->integer('order')->default(1)->comment('排序');
            $table->tinyInteger('status')->default(1);
            $table->string('template_sign', 64)->nullable()->comment('模型');

            $table->timestamps();
            $table->index('template_sign');
            $table->index('partner_sign');
            $table->index('module_id');
            $table->index('other_id');
            $table->index('order');
            $table->index('status');

        });


        // 商户列表
        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name',  64);
            $table->string('sign',     32)->unique();
            $table->string('theme', 32)->default('default');
            $table->string('remark',  128);
            $table->string('template_sign', 64)->default('')->comment('模板标识');

            // 模式 1 包网 2 API
            $table->tinyInteger('mode')->default(1);

            // logo 图片
            $table->string('logo_image_pc_1',     128)->default('');
            $table->string('logo_image_pc_2',     128)->default('');
            $table->string('logo_image_h5_1',     128)->default('');
            $table->string('logo_image_h5_2',     128)->default('');
            $table->string('logo_image_partner',  128)->default(''); // 总后台商户LOGO

            //logo_icon
            $table->string('logo_icon',  128)->default('');

            // 商户接入key
            $table->string('api_id',  128)->nullable();
            $table->string('api_key',  128)->nullable();

            //　变更人
            $table->integer('add_admin_id')->default(0);
            $table->integer('update_admin_id')->default(0);


            //測試域名
            $table->string('test_domain_name',  128)->default('');

            // 1 正常 0　维护
            $table->tinyInteger('status')->default(1);
            // 1 开启 0 关闭
            $table->tinyInteger('rate_open')->default(0);
            $table->timestamps();

            $table->index(['template_sign']);
        });

        // 商户配置
        Schema::create('partner_setting', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign',     64)->unique();

            // 二维码 配置
            $table->string('qr_code_1', 256)->nullable();
            $table->string('qr_code_2', 256)->nullable();
            $table->string('qr_code_3', 256)->nullable();

            // 客服地址
            $table->string('cs_url', 256)->nullable();

            // 日工资 配置
            $table->mediumText('salary_config')->nullable();

            // 分红 配置
            $table->mediumText('dividend_config')->nullable();

            // 首页显示开奖 配置
            $table->mediumText('lottery_open_list')->nullable();

            // 热门彩 配置
            $table->mediumText('popular_lottery')->nullable();

            // 热门棋牌 配置
            $table->mediumText('popular_chess')->nullable();

            // 热门电邮 配置
            $table->mediumText('popular_e_game')->nullable();

            // 热门捕鱼 配置
            $table->mediumText('popular_fish')->nullable();

            // 热门真人 配置
            $table->mediumText('popular_casino')->nullable();

            //　变更人
            $table->integer('add_partner_admin_id')->default(0);
            $table->integer('update_partner_admin_id')->default(0);

            $table->timestamps();

            $table->index('partner_sign');
        });

        // 绑定域名
        Schema::create('partner_domain', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign',  64);
            $table->string('name',          64);
            $table->string('domain',        128);

            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('env_type')->default(1);

            $table->string('remark', 128)->default('');

            $table->integer('add_admin_id')->default(0);
            $table->integer('update_admin_id')->default(0);

            $table->integer('add_partner_admin_id')->default(0);
            $table->integer('update_partner_admin_id')->default(0);

            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->index(['partner_sign','type','env_type']);
            $table->index('partner_sign');
        });

        // 绑定彩种
        Schema::create('partner_lottery', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign',      64);

            //是否高频彩
            $table->tinyInteger('is_fast')->default(1);
            //是否是体育彩票
            $table->tinyInteger('is_sport')->default(0);

            // 排序
            $table->integer('sort')->default(1);

            // 图片路径
            $table->string('icon_path',  256)->default('default.jpg');

            // 广告图片
            $table->string('ad_img', 256)->default('');

            $table->decimal('rate', 4,2)->default(0);

            // 是否热门
            $table->tinyInteger('is_hot')->default(0);

            // 开奖公告
            $table->tinyInteger('is_lottery')->default(0);

            $table->string('series_id', 32);

            $table->string('lottery_sign',      64);
            $table->string('lottery_name',      64);

            $table->integer('max_trace_number')->default(50);

            // 娱乐城
            $table->tinyInteger('open_casino')->default(0);

            // 合法奖金组
            $table->integer('min_prize_group');
            $table->integer('max_prize_group');

            // 总倍数
            $table->integer('min_times');
            $table->integer('max_times');

            //是否包网后台关闭
            $table->integer('is_admin_stop')->default(0);

            // 奖金组差值
            $table->integer('diff_prize_group')->default(0);

            // 单注奖金
            $table->unsignedBigInteger('max_prize_per_code')->default(0);

            // 单期最大奖金
            $table->unsignedBigInteger('max_prize_per_issue')->default(0);

            $table->string('valid_modes',      64);                         // 开放模式
            $table->string('valid_price',      64);                         // 开放单价

            $table->integer('add_admin_id')->default(0);
            $table->integer('update_admin_id')->default(0);

            $table->integer('add_partner_admin_id')->default(0);
            $table->integer('update_partner_admin_id')->default(0);

            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('rate_open')->default(0);
            $table->timestamps();

            $table->unique(['partner_sign', 'lottery_sign']);
            $table->index(['is_hot']);
            $table->index(['is_lottery']);
        });

        // 绑定玩法
        Schema::create('partner_methods', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('config_id');

            $table->string('partner_sign',          64);
            $table->string('lottery_sign',          64);
            $table->string('lottery_name',          64);
            $table->string('method_sign',           64);
            $table->string('method_name',           64);

            // 流行
            $table->tinyInteger('is_popular')->default(0);
            $table->integer('sort')->default(1);

            // 单注奖金
            $table->unsignedBigInteger('max_prize_per_code')->default(0);

            // 单期最大奖金
            $table->unsignedBigInteger('max_prize_per_issue')->default(0);

            //是否包网管理员关闭
            $table->integer('is_admin_stop')->default(0);

            // 单挑
            $table->tinyInteger('challenge_type')->default(0);
            $table->integer('challenge_min_count')->default(0);
            $table->string('challenge_config', 128)->nullable();
            $table->unsignedBigInteger('challenge_bonus')->default(0);

            $table->integer('add_partner_admin_id')->default(0);
            $table->integer('update_partner_admin_id')->default(0);

            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['partner_sign', 'lottery_sign', "method_sign"]);
            $table->unique(['partner_sign', 'lottery_sign', 'config_id']);
            $table->index(['partner_sign', 'is_popular']);
        });

        // 后台转账记录
        Schema::create('partner_admin_transfer_records', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('partner_sign', 64);
            $table->string('rid', 512);
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->integer('user_id');
            $table->tinyInteger('is_tester');
            $table->string('username', 64);                                // 用户名


            $table->string('mode')->default('add');                         // 模型
            $table->tinyInteger('type')->default(1);                        // 类型
            $table->string('type_name', 64)->default('');            // 类型


            $table->unsignedInteger('amount');                                        // 金额

            $table->string('process_admin_id', 32)->default(0);         // 管理员名称
            $table->string('process_admin_name', 32)->default(0);       // 管理员名称

            $table->unsignedBigInteger('day_m');                                         // 时间线
            $table->integer('process_time')->default(0);                        // 处理时间
            $table->integer('stat_time')->default(0);                           // 统计时间

            $table->tinyInteger('status')->default(1);                          // 状态

            $table->timestamps();
            $table->index(['partner_sign', 'type']);
            $table->index(["day_m", 'stat_time']);
        });

        // 商户独有配置
        Schema::create('partner_configures', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64)->comment('商户标识');

            $table->integer('pid')->comment('父类id, 顶级为0');
            $table->string('sign', 64)->comment('sign 标识');
            $table->string('name', 64)->comment('标题');

            // 是否可以编辑
            $table->tinyInteger('can_edit')->default(0)->comment('0 不可编辑 1 可编辑');
            $table->tinyInteger('can_show')->default(0)->comment('0 不显示 1 显示');

            $table->string('description',   128)->nullable()->comment('描述');
            $table->string('value',         128)->comment('配置选项value');

            $table->integer('add_partner_admin_id')->default(0)->comment('添加人, 系统添加为0');
            $table->integer('update_partner_admin_id')->default(0)->comment('上次更改人id');

            $table->tinyInteger('status')->default(0)->comment('0 禁用 1 启用');

            $table->timestamps();

            $table->index('sign');
            $table->index(['partner_sign', 'sign']);
        });

        // 推送渠道
        Schema::create('partner_telegram_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign',        32);
            $table->string('send_code_sign', 32)->default('');
            $table->string('send_code_name', 32)->default('');
            $table->string('report_push_sign', 32)->default('');
            $table->string('report_push_name', 32)->default('');
            $table->string('recharge_cash_push_sign', 32)->default('');
            $table->string('recharge_cash_push_name', 32)->default('');
            $table->string('background_audit_sign', 32)->default('');
            $table->string('background_audit_name', 32)->default('');
            
            $table->integer('status')->default(1);

            $table->timestamps();
        });

        // 商户段 = 管理用户
        Schema::create('partner_admin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64)->comment('商户标识');

            // 分组
            $table->integer('group_id')->default(0);

            $table->string('username',  64);
            $table->string('email',     64);
            $table->string('password', 64);
            $table->string('fund_password', 64);

            $table->string('remember_token', 64)->default('');

            // 管理员头像
			$table->string('avatar',      254);

            // 数据
            $table->char('register_ip',     15);
            $table->char('last_login_ip',   15)->default('');

            $table->integer('last_login_time')->default(0);

            $table->integer('add_admin_id')->default(0);
            $table->integer('add_partner_admin_id')->default(0);
            $table->integer('update_admin_id')->default(0);
            $table->integer('update_partner_admin_id')->default(0);

            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->index(['partner_sign']);
        });

        // 商户 = 可用菜单配置
        Schema::create('partner_menu_config', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pid');
            $table->string('rid',64)->default("");

            // 菜单
            $table->string('cn_name',             64);
            $table->string('en_name',           64);
            $table->string('route',             64);

            $table->string('api_path',     64)->default("");

            // 整形
            $table->integer('sort')->default(0);
            $table->string('css_class',     64)->default("");

            // 1 菜单 2 链接
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('level')->default(1);
            $table->tinyInteger('status')->default(1);

            $table->integer('add_admin_id')->default(0);
            $table->integer('update_admin_id')->default(0);

            $table->timestamps();
        });

        // 商户 = 管理菜单
        Schema::create('partner_menus', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64)->comment('商户标识');
            $table->integer('menu_id');

            $table->integer('sort')->default(0);

            $table->tinyInteger('status')->default(1);

            $table->integer('add_admin_id')->default(0);
            $table->integer('update_admin_id')->default(0);

            // 商户自己修改
            $table->integer('add_partner_admin_id')->default(0);
            $table->integer('update_partner_admin_id')->default(0);

            $table->timestamps();

            $table->index(['partner_sign']);
        });

        // 商户 = 管理组
        Schema::create('partner_admin_groups', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64)->comment('商户标识');

            $table->string('name', 32);
            $table->text('acl');

            $table->string('remark',64);

            $table->integer('level')->default(0);

            $table->integer('add_admin_id')->default(0);

            // 商户自己修改
            $table->integer('add_partner_admin_id')->default(0);
            $table->integer('update_partner_admin_id')->default(0);

            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['partner_sign']);
        });

        // 商户 管理员 访问日志
        Schema::create('partner_admin_access_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64)->comment('商户标识');

            $table->integer('partner_admin_id')->default(0);
            $table->string('partner_admin_username', 64)->default("");
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

            $table->index(['partner_sign', 'ip']);
            $table->index(['partner_sign', 'partner_admin_id']);
            $table->index(["partner_sign", "partner_admin_username"], "paal_ps_pau");
        });

        // 商户 管理员　行为
        Schema::create('partner_admin_behavior', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64)->comment('商户标识');
            $table->integer('partner_admin_id');
            $table->string('partner_admin_username', 64);
            $table->char('ip', 15)->default('');
            $table->char('proxy_ip', 15);

            $table->string('route',             64);
            $table->string('country',           64)->default('');
            $table->string('city',              64)->default('');
            $table->mediumText('params');

            // 域名和动作
            $table->string('domain',            64)->default('');
            $table->string('action',            32);
            $table->mediumText('context')->nullable();

            $table->string('device', 64)->default('');
            $table->string('platform', 64)->default('');
            $table->string('browser', 64)->default('');
            $table->string('agent', 256)->default('');
            $table->integer('day');

            $table->timestamps();
            $table->index(['partner_sign', 'partner_admin_id']);
            $table->index(["partner_sign", 'partner_admin_username'], "pab_ps_pau_at");
        });

        // 商户　管理员　动作审核
        Schema::create('partner_admin_action_review', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 64)->comment('商户标识');

            $table->integer('player_id');
            $table->string('player_username',     64);

            // 处理类型
            $table->string('type', 32);
            // 类型详情
            $table->tinyInteger('type_detail');

            // 配置 / 描述
            $table->string('process_config', 128);
            $table->string('process_desc', 128);

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

            // 审阅管理员
			$table->string('handle_admin_one', 64)->default('');
			$table->string('handle_admin_two', 64)->default('');
			$table->string('handle_admin_three', 64)->default('');


			// 审核失败原因
            $table->string('review_fail_reason', 64)->default('');

            // 当前状态
            $table->tinyInteger('status')->default(0);

            $table->timestamps();

			$table->index(["partner_sign", "handle_admin_one"],'ps_hao');
			$table->index(["partner_sign", "handle_admin_two"],'ps_hat');
			$table->index(["partner_sign", "handle_admin_three"],'ps_hath');
            $table->index(["partner_sign", "player_id"],'ps_pi');
            $table->index(["partner_sign", "player_username"],'ps_pu');
        });

        // 玩家 层级 配置
        Schema::create('partner_player_level_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('level');
            $table->string('name', 32);
            $table->string('show_name', 32);
            $table->string('icon', 64);

            $table->timestamps();

            $table->index([ "partner_sign", "level"], 'ulc_ps_l');

            $table->index("partner_sign");
        });

        // 玩家 VIP 配置
        Schema::create('partner_player_vip_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('vip_level')->default(1);

            $table->string('name',      32);
            $table->string('show_name', 32);
            $table->string('icon',      254);

            // 需求
            $table->integer('recharge_count')->default(0);
            $table->integer('recharge_total')->default(0);
            $table->integer('recharge_max_amount')->default(0);
            $table->integer('withdraw_count')->default(0);
            $table->integer('withdraw_total')->default(0);
            $table->integer('child_count')->default(0);

            $table->timestamps();

            $table->index([ "partner_sign", "vip_level"], 'uvc_ps_vl');
        });

        // 玩家 头像 配置
        Schema::create('partner_player_avatar_img', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);
            $table->string('avatar',      254);

            $table->timestamps();
        });

        /**
         * 系统公告
         */
        Schema::create('partner_notice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->tinyInteger('device_type')->comment('设备类型')->default(1);
            $table->tinyInteger('type')->comment('类型')->default(1);

            $table->string('title', 128)->comment('标题');
			$table->text('type_desc')->default('')->comment('副标题');
            $table->text('content')->comment('内容详情');

            $table->string('notice_image', 256)->nullable()->comment('公告图片');

            $table->integer('start_time')->default(0)->comment('开始时间');
            $table->integer('end_time')->default(0)->comment('结束时间');

            $table->tinyInteger('top_score')->default(0)->comment('置顶权重');
            $table->tinyInteger('status')->default(1)->comment('0 禁用 1 启用');

			$table->text('no_popup')->default('')->comment('玩家id');

            $table->integer('add_partner_admin_id')->default(0)->comment('添加管理员id');
            $table->integer('update_partner_admin_id')->default(0)->comment('修改管理员id');

            $table->timestamps();
        });

         /**
         * 站内信
         */
        Schema::create('partner_message', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->string('title', 32)->comment('标题');
            $table->text('content')->comment('内容详情');

            $table->tinyInteger('user_type')->comment('玩家类型');
            $table->text('user_config')->comment('玩家账号和已读未读状态存储');

            $table->timestamps();

            $table->index([ "user_type"], 'pm_ut_ui');
            $table->index("partner_sign");
            $table->index("user_type");
        });


        /**
         * 帮助菜单
         */
        Schema::create('help_center_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);
            $table->string('name', 128)->comment('分类名');

            $table->integer('add_partner_admin_id')->default(0)->comment('添加管理员id');
            $table->integer('update_partner_admin_id')->default(0)->comment('修改管理员id');

            $table->timestamps();
        });


        /**
         * 帮助中心
         */
        Schema::create('help_center', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pid');

            $table->string('title', 128)->comment('标题');
            $table->text('content')->comment('内容详情');

            $table->string('help_image', 256)->nullable()->comment('文章图片');

            $table->tinyInteger('status')->default(0)->comment('0 禁用 1 启用');

            $table->integer('add_partner_admin_id')->default(0)->comment('添加管理员id');
            $table->integer('update_partner_admin_id')->default(0)->comment('修改管理员id');

            $table->timestamps();
        });

         /**
         * 审核流程
         */
        Schema::create('partner_review_flows', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 32);

            $table->string('type', 64)->comment('审核类型 1＝理赔，2＝扣减，3＝取消禁止提现，4＝取消禁止转帐，5＝取消禁止资金操作');

            $table->string('users', 256)->default(0)->comment('用户');
            $table->string('user_group', 256)->default(0)->comment('用户');
            $table->tinyInteger('type_detail')->default(0)->comment('0');

            $table->timestamps();

            $table->index(["partner_sign","type"]);
            $table->index('type_detail');
            $table->index('users');
            $table->index('type');
            $table->index('partner_sign');
        });


        /**
         * 广告位
         */
        Schema::create('partner_advertisings', function (Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 32);

            $table->string('type', 64)->comment('类型');
            $table->string('type_name', 64)->comment('类型');
            $table->string('module_sign', 64)->comment('model类型');
            $table->string('module_name', 64)->comment('model类型');
            $table->string('sign', 64)->comment('广告位');
            $table->string('sign_name', 64)->comment('游戏ID');
            $table->integer('pid')->comment('上级ID');
            $table->string('game_id', 64)->comment('游戏ID');

            $table->string('title', 256)->default('')->comment('标题');
            $table->string('img', 256)->default('')->comment('图片地址');
            $table->string('url', 256)->default('')->comment('0');

            $table->timestamps();

            $table->index('type');
            $table->index('module_sign');
            $table->index('game_id');
            $table->index('pid');
            $table->index('title');
            $table->index('partner_sign');
        });
    }

    public function down()
    {
        Schema::dropIfExists('partners');
        Schema::dropIfExists('partner_domain');
        Schema::dropIfExists('partner_advertisings');
        Schema::dropIfExists('partner_lottery');
        Schema::dropIfExists('partner_methods');
        Schema::dropIfExists('partner_modules');

        Schema::dropIfExists('partner_menus');
        Schema::dropIfExists('partner_configures');


        Schema::dropIfExists('partner_admin_users');
        Schema::dropIfExists('partner_admin_groups');
        Schema::dropIfExists('partner_admin_group_users');
        Schema::dropIfExists('partner_admin_access_logs');

        Schema::dropIfExists('partner_admin_behavior');
        Schema::dropIfExists('partner_admin_action_review');

        Schema::dropIfExists('partner_player_level_config');
        Schema::dropIfExists('partner_player_vip_config');

        Schema::dropIfExists('partner_player_avatar_img');

        Schema::dropIfExists('partner_notice');
        Schema::dropIfExists('partner_message');
        Schema::dropIfExists('partner_setting');
        Schema::dropIfExists('partner_menu_config');

        Schema::dropIfExists('help_center_menu');
        Schema::dropIfExists('help_center');
        Schema::dropIfExists('partner_navigations');
        Schema::dropIfExists('partner_homes');
        Schema::dropIfExists('partner_telegram_channel');
        Schema::dropIfExists('partner_admin_transfer_records');
        Schema::dropIfExists('partner_review_flows');
        
    }
}
