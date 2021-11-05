<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 商户管理相关
class CreateBackupTable extends Migration
{

    public function up()
    {
        // 商户访问历史记录
        Schema::create('partner_admin_access_logs_backup', function (Blueprint $table) {
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

            $table->index(['partner_sign', 'ip'],'paalb_ps_i');
            $table->index(['partner_sign', 'partner_admin_id'],'paalb_ps_pai');
            $table->index(["partner_sign", "partner_admin_username"], "paalb_ps_pau");
        });

        // 商户历史行为日志
        Schema::create('partner_admin_behavior_backup', function (Blueprint $table) {
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
            $table->index(['partner_sign', 'partner_admin_id'],'pabb_ps_pai');
            $table->index(["partner_sign", 'partner_admin_username'], "pabb_ps_pau_at");
        });

        // 用户历史访问日志
         Schema::create('user_player_log_backup', function (Blueprint $table) {
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
        });

        // 用户历史ip日志
        Schema::create('user_ip_log_backup', function (Blueprint $table) {
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

            $table->index(["partner_sign", "ip"],'uilb_ps_i');
            $table->index(['user_id', 'ip'],'uilb_ui_i');
        });

         // 奖期
        Schema::create('lottery_issues_backup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);
            $table->string('issue', 64);
            $table->integer('issue_rule_id');

            // 时间
            $table->integer('begin_time');
            $table->integer('end_time');
            $table->integer('official_open_time');

            // 允许录入时间
            $table->integer('allow_encode_time')->default(0);

            // 官方号码
            $table->string('official_code', 64)->nullable();

            // 状态
            $table->unsignedTinyInteger('status_process')->default(0);
            $table->unsignedTinyInteger('status_commission')->default(0);
            $table->unsignedTinyInteger('status_trace')->default(0);
            $table->unsignedTinyInteger('status_prize')->default(0);

            // 时间
            $table->integer('time_encode')->default(0);
            $table->integer('time_open')->default(0);
            $table->integer('time_send')->default(0);
            $table->integer('time_commission')->default(0);
            $table->integer('time_trace')->default(0);
            $table->integer('time_cancel')->default(0);

            // 结束时间
            $table->integer('time_end_open')->default(0);
            $table->integer('time_end_send')->default(0);
            $table->integer('time_end_commission')->default(0);
            $table->integer('time_end_trace')->default(0);
            $table->integer('time_end_cancel')->default(0);

            $table->integer('time_stat')->default(0);

            // 统计
            $table->integer('total_project')->default(0);
            $table->integer('total_win_project')->default(0);

            // 录号人
            $table->integer('encode_id')->nullable();
            $table->string('encode_username', 64)->nullable();

            // day
            $table->integer('day')->default(0);

            $table->timestamps();

            $table->index(["issue"]);
            $table->index(['lottery_sign', "begin_time", "end_time"], "lib_ls_bt_et");
            $table->index(['lottery_sign', "issue", 'status_process'],'lib_ls_i_sp');
            $table->index(['lottery_sign', "issue", 'status_commission'],'lib_ls_i_sc');
            $table->index(['lottery_sign', 'status_process'], "lib_ls_sp");
            $table->index(['status_process', 'status_commission', 'begin_time'], "lib_sp_sc_bt");
            $table->index(['lottery_sign', 'status_commission'], "lib_ls_ssc");
            $table->index(['lottery_sign', 'status_trace'], "lib_ls_st");
        });

        // 帐变历史记录
        Schema::create('account_change_report_backup', function(Blueprint $table)
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

            //元,角,分 模式
            $table->integer('modes')->default(0);
            $table->integer('project_id')->default(0);
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

            $table->index(array("partner_sign", "username", "process_time"), "acrb_ps_ui_pt");
            $table->index(array("partner_sign", "username", "type_sign",), "acrb_ps_ts");
            $table->index(array("partner_sign", "type_sign", "process_time"), "acrb_ps_ts_pt");
            $table->index(array("partner_sign", "lottery_sign", 'method_sign'), "acrb_ps_ls_ms");
            $table->index(array("partner_sign", "issue", "project_id"), "acrb_ps_i_pi");
            $table->index(array("partner_sign", "project_id"), "acrb_ps_pi");
            $table->index(array("type_sign", "stat_time", "day_m"), "acrb_ts_d_st");
            $table->index('modes');
        });

        // 返点
        Schema::create('lottery_commissions_backup', function(Blueprint $table) {
            $table->increments('id');

            $table->string('partner_sign', 32);
            $table->integer('top_id');
            $table->integer('user_id');
            $table->string('username', 64);

            // 奖金组
            $table->integer('project_id');
            $table->string('from_type', 32);

            $table->integer('from_user_id');
            $table->string('from_username', 64);

            $table->integer('account_change_id')->default(0);

            $table->integer('self_prize_group')->default(0);
            $table->integer('child_prize_group')->default(0);
            $table->integer('bet_prize_group')->default(0);

            $table->unsignedInteger('amount')->default(0);

            $table->string('lottery_sign',  32);
            $table->string('lottery_name',  32);
            $table->string('method_sign',  32);
            $table->string('method_name',  32);
            $table->string('issue',  32);

            $table->tinyInteger('slot')->default(0);

            $table->tinyInteger('status')->default(0);

            $table->integer('add_time')->default(0);
            $table->integer('process_time')->default(0);

            $table->integer('day')->default(0);

            $table->index(["user_id", 'issue'],'lcb_ui_i');
            $table->index(["partner_sign", 'day', 'user_id'], 'lcb_ps_d_ui');
            $table->index('account_change_id');
            $table->index(["user_id", 'process_time'],'lcb_ui_pt');
            $table->index(["project_id"]);
            $table->index(["lottery_sign", 'issue', 'slot', 'status'], "lcb_ls_i_s_s");
            $table->index(["partner_sign", "lottery_sign", 'issue'], "lcb_ps_ls_i");
        });

         //历史投注列表
        Schema::create('lottery_projects_backup', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('partner_sign', 32);

            $table->integer('user_id');
            $table->string('username', 64)->default('');
            $table->integer('top_id')->index();
            $table->integer('parent_id');
            $table->tinyInteger('is_tester')->default(0);

            // 彩种
            $table->string('series_id', 32);
            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);
            $table->string('method_sign', 32);
            $table->string('method_name', 32);

            // 奖金组
            $table->integer('user_prize_group');
            $table->integer('bet_prize_group');

            // 单挑
            $table->tinyInteger('is_challenge')->default(0);
            $table->unsignedInteger('challenge_prize')->default(0);

            // 注单格式
            $table->integer('trace_main_id')->default(0);           // 追号主ID
            $table->integer('trace_detail_id')->default(0);         // 追号列ID

            $table->tinyInteger('mode');                                    // 模式
            $table->integer('count');                                       // 注数
            $table->unsignedInteger('times');                               // 倍数
            $table->integer('price');                                       // 单价
            $table->unsignedBigInteger('total_cost')->default(0);     // 总花费

            $table->string('issue', 32);
            $table->mediumText('bet_number');
            $table->mediumText('bet_number_view');
            $table->string('open_number', 64)->default('');

            // 是否赢 1 已中奖 2 未中奖
            $table->tinyInteger('is_win')->default(0);
            $table->integer('win_count')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);

            $table->mediumText('commission');
            $table->string('prize_set', 128);

            // 客户信息
            $table->char('ip', 15);
            $table->char('proxy_ip', 15);
            $table->tinyInteger('bet_from')->default(1);

            // 0 初始化 1 已经完成 2 撤单
            $table->tinyInteger('status_process')->default(0);
            $table->tinyInteger('status_commission')->default(0);

            $table->unsignedBigInteger('day_m')->default(0)->index();
            $table->tinyInteger('slot')->default(0);

            // 时间
            $table->integer('time_bought')->default(0);
            $table->integer('time_open')->default(0);
            $table->integer('time_send')->default(0);
            $table->integer('time_commission')->default(0);
            $table->integer('time_real_bet')->default(0);
            $table->integer('time_cancel')->default(0);

            // 统计状态
            $table->integer('time_stat')->default(0);

            $table->index(['user_id', "time_bought"], "ls_ui_tb");
            $table->index(['user_id', 'issue'], "ls_ui_i");
            $table->index(['user_id', 'lottery_sign', "time_bought"], "ls_ui_ls_tb");
            $table->index(["partner_sign", 'lottery_sign', "time_bought"], "ls_ps_ls_tb");
            $table->index(["partner_sign", 'lottery_sign', "issue", "status_commission"], "ls_ps_ls_i_sc");
            $table->index(["partner_sign", 'lottery_sign', "issue", "status_process"], "ls_ps_ls_i_sp");

            $table->index(["partner_sign", 'issue', 'status_process'], "ls_ps_ui_sp");
            $table->index(["lottery_sign", 'issue', 'slot'], "lp_ls_i_s");
            $table->index(["lottery_sign", 'issue', 'is_win'], "lp_ls_i_iw");
            $table->index(["partner_sign", "time_bought"], "lp_ps_tb");
        });

        // 追号历史记录
        Schema::create('lottery_traces_backup', function(Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('user_id');
            $table->integer('top_id')->index();
            $table->integer('parent_id')->index();
            $table->string('username', 64)->default('');
            $table->tinyInteger('is_tester')->default(0);

            // 游戏信息
            $table->string('series_id',     32);
            $table->string('lottery_sign',  32);
            $table->string('lottery_name',  32);
            $table->string('method_sign',   32);
            $table->string('method_name',   32);

            // 投注信息
            $table->longText('bet_number');                                             // 投注号码
            $table->mediumText('bet_number_view');
            $table->unsignedBigInteger('price');                                        // 单价
            $table->tinyInteger('mode')->default(1);
            $table->integer('count')->default(1);                                 // 注数
            $table->tinyInteger('win_stop')->default(0);
            $table->unsignedBigInteger('trace_total_cost');                             // 总话费

            // 返点
            $table->mediumText('commission');
            $table->string('prize_set', 128);

            // 单挑
            $table->tinyInteger('is_challenge')->default(0);
            $table->unsignedInteger('challenge_prize')->default(0);

            // 用户将进组
            $table->integer('user_prize_group');
            $table->integer('bet_prize_group');

            // 奖期状态
            $table->integer('total_issues');
            $table->integer('finished_issues')->default(0);
            $table->integer('canceled_issues')->default(0);

            // 资金状态
            $table->unsignedBigInteger('finished_amount')->default(0);
            $table->unsignedBigInteger('canceled_amount')->default(0);

            $table->unsignedBigInteger('total_bonus')->default(0);

            // 开始奖期
            $table->string('start_issue',32);
            $table->string('now_issue',32);
            $table->string('end_issue',32);
            $table->string('stop_issue',32);

            // 时间点
            $table->integer('time_bought');
            $table->integer('time_stop');

            // 客户信息
            $table->char('ip', 15);
            $table->char('proxy_ip', 15);
            $table->tinyInteger('bet_from')->default(1);        // bet_from 1 web, 2 iphone, 3 安卓

            // 0 初始化, 1 处理中, 2 已完成
            $table->tinyInteger('status')->default(0);

            $table->date('day');
            $table->timestamps();

            $table->index(["partner_sign", 'user_id', "time_bought"], 'ltb_ps_ui_tb');
            $table->index(["partner_sign", 'lottery_sign', "status"], 'ltb_ps_ls_s');
        });

        // 追号历史详情
        Schema::create('lottery_trace_detail_backup', function(Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            // 1 地一个　2 最后一个
            $table->tinyInteger('sort_type')->default(0);

            $table->integer('user_id');
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->string('username', 64);
            $table->tinyInteger('is_tester')->default(0);

            // 游戏信息
            $table->string('series_id',     32);
            $table->string('lottery_sign',  32);
            $table->string('lottery_name',  32);
            $table->string('method_sign',   32);
            $table->string('method_name',   32);
            $table->string('issue',         32);

            $table->integer('trace_id');

            // 投注信息
            $table->mediumText('bet_number');                                             // 投注号码
            $table->mediumText('bet_number_view');

            $table->integer('times')->default(1);                                       // 倍数
            $table->unsignedBigInteger('price');                                              // 单价
            $table->integer('count');                                                         // 注数
            $table->unsignedBigInteger('total_cost');                                         // 总价
            $table->tinyInteger('mode')->default(1);                                    // 模式

            // 返点
            $table->mediumText('commission');
            $table->string('prize_set', 128);

            // 单挑
            $table->tinyInteger('is_challenge')->default(0);
            $table->unsignedInteger('challenge_prize')->default(0);

            // 用户将进组
            $table->integer('user_prize_group');
            $table->integer('bet_prize_group');

            // 客户信息
            $table->char('ip', 15);
            $table->char('proxy_ip', 15);
            $table->tinyInteger('bet_from')->default(1);        // bet_from 1 web, 2 iphone, 3 安卓

            $table->integer('time_add')->default(0);
            $table->integer('time_bet')->default(0);
            $table->integer('time_cancel')->default(0);

            // 　控制变量
            $table->tinyInteger('is_win')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);

            // 0 初始化, 1 待处理, 2 已投注 -1 人工撤单 -2 追停撤单
            $table->tinyInteger('status')->default(0);

            $table->tinyInteger('slot')->default(0);

            $table->timestamps();

            $table->index('user_id');
            $table->index('trace_id');
            $table->index(["partner_sign", 'lottery_sign', 'issue', 'status'], 'ltdb_ps_ls_i_s');
            $table->index(['lottery_sign', 'issue', 'slot'], 'ltdb_ls_i_s');
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_admin_access_logs_backup');
        Schema::dropIfExists('partner_admin_behavior_backup');
        Schema::dropIfExists('user_player_log_backup');
        Schema::dropIfExists('user_ip_log_backup');
        Schema::dropIfExists('account_change_report_backup');
        Schema::dropIfExists('lottery_commissions_backup');
        Schema::dropIfExists('lottery_projects_backup');
        Schema::dropIfExists('lottery_traces_backup');
        Schema::dropIfExists('lottery_trace_detail_backup');
        Schema::dropIfExists('lottery_issues_backup');
    }
}
