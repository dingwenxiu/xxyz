<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 游戏相关的
class CreateLotteryTable extends Migration
{
    public function up()
    {
        // 游戏
        Schema::create('lotteries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cn_name', 32);
            $table->string('en_name', 32);
            $table->string('lottery_icon', 64)->default('default.jpg');

            //广告图片
            $table->string('ad_img', 256)->default('');

            $table->string('partner_sign', 32)->default('system')->comment('商戶識別');

            $table->string('series_id', 32);
            // 逻辑标识
            $table->string('logic_sign', 32)->comment('與series_id 一致 後期可能用不到');

            $table->tinyInteger('is_fast')->default(1);
            $table->tinyInteger('is_sport')->default(0)->comment('是否是体育彩票');
            $table->tinyInteger('auto_open')->default(0);

            $table->integer('max_trace_number')->default(50);
            $table->integer('day_issue');

            // issue
            $table->string('issue_format', 32);
            $table->integer('issue_part')->default(1);

            // day 每日开始 increase 递增 random 随机
            $table->string('issue_type', 32)->default('day');

            // 允许的号码
            $table->string('valid_code', 256);

            // 号码长度
            $table->integer('code_length')->comment('根據positions有幾個號碼作變動');

            // 娱乐城
            $table->tinyInteger('open_casino')->default(0);

            // 号码位置
            $table->string('positions', 256);

            // 开奖配置
            $table->string('open_time', 256);

            // 合法奖金组
            $table->integer('min_prize_group');
            $table->integer('max_prize_group');
            $table->integer('diff_prize_group')->default(0);

            // 总倍数
            $table->integer('min_times');
            $table->integer('max_times');

            // 单注奖金
            $table->unsignedBigInteger('max_prize_per_code')->default(0);

            // 单期最大奖金
            $table->unsignedBigInteger('max_prize_per_issue')->default(0);

            // 允许模式
            $table->string('valid_modes', 64);

            // 允许价格
            $table->string('valid_price', 32);

            // 彩种描述
            $table->string('issue_desc', 256);

            // 状态
            $table->tinyInteger('status')->default(0);

            $table->timestamps();
        });

        // 玩法
        Schema::create('lottery_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('series_id', 32);
            $table->string('logic_sign', 32);
            $table->string('lottery_name', 32);
            $table->string('lottery_sign', 32);
            $table->string('method_sign', 32);

            // 玩法类型 官方还是娱乐城
            $table->string('method_type', '32')->default('official');
            $table->string('method_name', 32);
            $table->string('method_group', 32);
            $table->string('method_row', 32)->nullable();

            $table->integer('group_sort')->default(0);
            $table->integer('row_sort')->default(0);
            $table->integer('method_sort')->default(0);

            // 单注奖金
            $table->unsignedBigInteger('max_prize_per_code')->default(0);

            // 单期最大奖金
            $table->unsignedBigInteger('max_prize_per_issue')->default(0);

            // 1 一等奖 2 兼中兼得
            $table->tinyInteger('win_mode')->default(1);

            // 单挑
            $table->tinyInteger('challenge_type')->default(0);
            $table->integer('challenge_min_count')->default(0);
            $table->string('challenge_config', 128)->nullable();
            $table->unsignedBigInteger('challenge_bonus')->default(0);

            // 是否显示
            $table->tinyInteger('show')->default(1);

            // 状态
            $table->tinyInteger('status')->default(0);

            $table->timestamps();
        });

        // 奖期
        Schema::create('lottery_issues', function (Blueprint $table) {
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
            $table->tinyInteger('status_process')->default(0);
            $table->tinyInteger('status_commission')->default(0);
            $table->tinyInteger('status_trace')->default(0);
            $table->tinyInteger('status_prize')->default(0);

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
            $table->index(['lottery_sign', "begin_time", "end_time"], "li_ls_bt_et");
            $table->index(['lottery_sign', "issue", 'status_process']);
            $table->index(['lottery_sign', "issue", 'status_commission']);
            $table->index(['lottery_sign', 'status_process'], "li_ls_sp");
            $table->index(['status_process', 'status_commission', 'begin_time'], "li_sp_sc_bt");
            $table->index(['lottery_sign', 'status_commission'], "li_ls_ssc");
            $table->index(['lottery_sign', 'status_trace'], "li_ls_st");
        });

        // 奖期 = 撤单记录
        Schema::create('lottery_issue_cancel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);

            $table->integer('issue_id');
            $table->string('issue', 64);

            $table->integer('total_project')->default(0);
            $table->integer('total_success')->default(0);
            $table->integer('total_fail')->default(0);

            $table->integer('start_time')->default(0);
            $table->integer('end_time')->default(0);

            $table->tinyInteger('status')->default(0);

            $table->string('msg', 256)->default('');

            $table->index("issue_id");
        });

        // 奖期 = 中奖记录
        Schema::create('lottery_issue_bonus', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->string('username', 64);
            $table->string('partner_sign', 64);

            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);

            $table->string('method_sign', 64);
            $table->string('method_name', 64);

            $table->integer('issue_id');
            $table->string('issue', 64);

            $table->integer('project_id')->default(0);

            $table->tinyInteger('is_challenge')->default(0);
            $table->unsignedBigInteger('challenge_prize')->default(0);
            $table->unsignedBigInteger('challenge_count')->default(0);
            $table->unsignedBigInteger('reduce_challenge_amount')->default(0);

            $table->string('type', 32);
            $table->unsignedBigInteger('amount')->default(0);


            $table->tinyInteger('has_limit')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->unsignedBigInteger('reduce_amount')->default(0);


            $table->integer('process_time')->default(0);
            $table->integer('process_challenge_time')->default(0);

            $table->tinyInteger('status_limit')->default(0);
            $table->tinyInteger('status_challenge')->default(0);


            $table->timestamps();

            $table->index("issue_id", "user_id");
            $table->index("issue_id", "type");
        });

        // 奖期 = 投注
        Schema::create('lottery_issue_bet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64);

            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);

            $table->string('issue', 64);

            $table->unsignedBigInteger('total_bet')->default(0);
            $table->unsignedBigInteger('total_cancel')->default(0);
            $table->unsignedBigInteger('total_bet_commission')->default(0);
            $table->unsignedBigInteger('total_child_commission')->default(0);
            $table->unsignedBigInteger('total_bonus')->default(0);
            $table->unsignedBigInteger('total_challenge_bonus')->default(0);
            $table->unsignedBigInteger('total_real_bonus')->default(0);

            $table->decimal('rate', 5, 2)->default(0);

            $table->integer('day')->default(0);

            $table->tinyInteger('status')->default(0);

            $table->index(["partner_sign", "lottery_sign", "issue"], "lib_pli");
            $table->index(["partner_sign", "lottery_sign", "day"], "lib_pld");

        });

        // jackpot plan
        Schema::create('lottery_jackpot_plan', function (Blueprint $table) {

            $table->increments('id');
            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);
            $table->string('issue', 64);

            $table->mediumText('detail');
            $table->unsignedInteger('day_m');

            $table->unique(['lottery_sign', 'issue']);
            $table->index('day_m');

        });

        // 规则
        Schema::create('lottery_issue_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lottery_sign');
            $table->string('lottery_name');

            $table->time('begin_time');
            $table->time('end_time');

            $table->integer('issue_seconds');
            $table->time('first_time');

            // 调整时间
            $table->smallInteger('adjust_time');
            $table->smallInteger('encode_time');
            $table->smallInteger('issue_count');

            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });

        // 注单
        Schema::create('lottery_projects', function (Blueprint $table) {
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

        // 返点
        Schema::create('lottery_commissions', function (Blueprint $table) {
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

            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);
            $table->string('method_sign', 32);
            $table->string('method_name', 32);
            $table->string('issue', 32);

            $table->tinyInteger('slot')->default(0);

            $table->tinyInteger('status')->default(0);

            $table->integer('add_time')->default(0);
            $table->integer('process_time')->default(0);

            $table->integer('day')->default(0);

            $table->index(["user_id", 'issue']);
            $table->index(["partner_sign", 'day', 'user_id'], 'lc_ps_d_ui');
            $table->index('account_change_id');
            $table->index(["user_id", 'process_time']);
            $table->index(["project_id"]);
            $table->index(["lottery_sign", 'issue', 'slot', 'status'], "lp_ls_i_s_s");
            $table->index(["partner_sign", "lottery_sign", 'issue'], "lp_ps_ls_i");
        });

        // 追号=详情
        Schema::create('lottery_trace_detail', function (Blueprint $table) {
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
            $table->string('series_id', 32);
            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);
            $table->string('method_sign', 32);
            $table->string('method_name', 32);
            $table->string('issue', 32);

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
            $table->index(["partner_sign", 'lottery_sign', 'issue', 'status'], 'ltd_ps_ls_i_s');
            $table->index(['lottery_sign', 'issue', 'slot'], 'ltd_ls_i_s');
        });

        // 追号 = 主
        Schema::create('lottery_traces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('user_id');
            $table->integer('top_id')->index();
            $table->integer('parent_id')->index();
            $table->string('username', 64)->default('');
            $table->tinyInteger('is_tester')->default(0);

            // 游戏信息
            $table->string('series_id', 32);
            $table->string('lottery_sign', 32);
            $table->string('lottery_name', 32);
            $table->string('method_sign', 32);
            $table->string('method_name', 32);

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
            $table->string('start_issue', 32);
            $table->string('now_issue', 32);
            $table->string('end_issue', 32);
            $table->string('stop_issue', 32);

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

            $table->index(["partner_sign", 'user_id', "time_bought"], 'ltl_ps_ui_tb');
            $table->index(["partner_sign", 'lottery_sign', "status"], 'ltl_ps_ls_s');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('lotteries');
        Schema::dropIfExists('lottery_methods');
        Schema::dropIfExists('lottery_issues');
        Schema::dropIfExists('lottery_issue_rules');
        Schema::dropIfExists('lottery_projects');
        Schema::dropIfExists('lottery_traces');
        Schema::dropIfExists('lottery_trace_detail');
        Schema::dropIfExists('lottery_commissions');
        Schema::dropIfExists('lottery_issue_bonus');
        Schema::dropIfExists('lottery_issue_cancel');
        Schema::dropIfExists('lottery_issue_bet');
        Schema::dropIfExists('lottery_jackpot_plan');
        
    }
}
