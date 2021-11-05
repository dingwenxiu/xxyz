<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatTable extends Migration
{
    /**
     *　统计
     * @return void
     */
    public function up() {

        // 商户每日统计
        Schema::create('report_stat_partner_day', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64);
            $table->string('partner_name', 64);

            // 是否第一天注册　统计使用
            $table->unsignedInteger('first_register')->default(0);
            $table->unsignedInteger('have_bet')->default(0);

            $table->unsignedBigInteger('recharge_amount')->default(0);
            $table->unsignedInteger('recharge_count')->default(0);
            $table->unsignedInteger('first_recharge_count')->default(0);
            $table->unsignedInteger('repeat_recharge_count')->default(0);
            $table->unsignedBigInteger('withdraw_amount')->default(0);
            $table->unsignedBigInteger('withdraw_count')->default(0);

            $table->unsignedBigInteger('bets')->default(0);
            $table->unsignedBigInteger('cancel')->default(0);
            $table->unsignedBigInteger('he_return')->default(0);
            $table->unsignedBigInteger('commission_from_bet')->default(0);
            $table->unsignedBigInteger('commission_from_child')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);
            $table->unsignedBigInteger('score')->default(0);

            // 转账 个人
            $table->unsignedBigInteger('transfer_to_child')->default(0);
            $table->unsignedBigInteger('transfer_from_parent')->default(0);

            $table->unsignedBigInteger('salary')->default(0);                          // 个人日工资
            $table->unsignedBigInteger('dividend')->default(0);                        // 个人分红
            $table->unsignedBigInteger('gift')->default(0);                            // 活动礼金

            $table->unsignedBigInteger('system_transfer_add')->default(0);             // 系统理赔增加
            $table->unsignedBigInteger('system_transfer_reduce')->default(0);          // 系统理赔减少

            $table->bigInteger('profit')->default(0);                                  // 盈亏

            $table->integer('day');                                                           // 更新日期

            $table->index(array("partner_sign", 'day'), "rspd_ps_d");
        });

        // 用户每日统计
        Schema::create('report_stat_user_day', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64);

            $table->integer('user_id');

            $table->integer('top_id');
            $table->integer('parent_id');
            $table->tinyInteger('is_tester');

            $table->string('username',32);

            // 是否第一天注册　统计使用
            $table->integer('first_register')->default(0);
            $table->integer('have_bet')->default(0);

            $table->unsignedBigInteger('recharge_amount')->default(0);
            $table->unsignedInteger('recharge_count')->default(0);
            $table->unsignedInteger('first_recharge_count')->default(0);
            $table->unsignedInteger('repeat_recharge_count')->default(0);
            $table->unsignedBigInteger('withdraw_amount')->default(0);
            $table->integer('withdraw_count')->default(0);
            $table->unsignedBigInteger('bets')->default(0);
            $table->unsignedBigInteger('cancel')->default(0);
            $table->unsignedBigInteger('he_return')->default(0);
            $table->unsignedBigInteger('commission_from_bet')->default(0);
            $table->unsignedBigInteger('commission_from_child')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);
            $table->unsignedBigInteger('score')->default(0);

            // 转账 个人
            $table->unsignedBigInteger('transfer_to_child')->default(0);
            $table->unsignedBigInteger('transfer_from_parent')->default(0);

            $table->unsignedBigInteger('salary')->default(0);                      // 个人日工资
            $table->unsignedBigInteger('dividend')->default(0);                    // 个人分红
            $table->unsignedBigInteger('gift')->default(0);                        // 活动礼金

            $table->unsignedBigInteger('system_transfer_add')->default(0);         // 系统理赔增加
            $table->unsignedBigInteger('system_transfer_reduce')->default(0);      // 系统理赔减少

            $table->integer('team_first_register')->default(0);
            $table->integer('team_have_bet')->default(0);

            $table->unsignedBigInteger('team_recharge_amount')->default(0);
            $table->unsignedInteger('team_first_recharge_count')->default(0);
            $table->unsignedInteger('team_recharge_count')->default(0);
            $table->unsignedInteger('team_repeat_recharge_count')->default(0);

            $table->unsignedBigInteger('team_withdraw_amount')->default(0);
            $table->integer('team_withdraw_count')->default(0);

            $table->unsignedBigInteger('team_bets')->default(0);
            $table->unsignedBigInteger('team_cancel')->default(0);
            $table->unsignedBigInteger('team_he_return')->default(0);
            $table->unsignedBigInteger('team_commission_from_bet')->default(0);
            $table->unsignedBigInteger('team_commission_from_child')->default(0);
            $table->unsignedBigInteger('team_bonus')->default(0);
            $table->unsignedBigInteger('team_score')->default(0);

            $table->unsignedBigInteger('team_gift')->default(0);                    // 活动礼金
            $table->unsignedBigInteger('team_salary')->default(0);                  // 日工资

            $table->unsignedBigInteger('team_system_transfer_add')->default(0);              // 理赔增加
            $table->unsignedBigInteger('team_system_transfer_reduce')->default(0);           // 理赔减少


            $table->integer('day'); //更新日期
            $table->integer('balance')->default(0);

            $table->timestamps();

            $table->index(array("partner_sign", 'day'), "rsud_ps_d");
            $table->unique(array("partner_sign", 'user_id',  'day'), "rsud_ps_ui_d");
            $table->index(array("partner_sign", 'username',  'day'), "rsud_ps_u_d");
            $table->index(array("partner_sign", 'parent_id', 'day'), "rsud_ps_pi_d");
            $table->index(array("partner_sign", 'top_id', 'day'), "rsud_ps_ti_d");
        });

        // 用户统计
        Schema::create('report_stat_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 64);

            $table->integer('user_id');

            $table->integer('top_id');
            $table->integer('parent_id');
            $table->tinyInteger('is_tester');

            $table->string('username',32);

            // 是否第一天注册　统计使用
            $table->integer('first_register')->default(0);
            $table->integer('have_bet')->default(0);

            $table->unsignedBigInteger('recharge_amount')->default(0);
            $table->unsignedInteger('recharge_count')->default(0);
            $table->unsignedInteger('first_recharge_count')->default(0);
            $table->unsignedInteger('repeat_recharge_count')->default(0);

            $table->unsignedBigInteger('withdraw_amount')->default(0);
            $table->integer('withdraw_count')->default(0);
            $table->unsignedBigInteger('bets')->default(0);
            $table->unsignedBigInteger('cancel')->default(0);
            $table->unsignedBigInteger('he_return')->default(0);
            $table->unsignedBigInteger('commission_from_bet')->default(0);
            $table->unsignedBigInteger('commission_from_child')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);
            $table->unsignedBigInteger('score')->default(0);

            // 转账 个人
            $table->unsignedBigInteger('transfer_to_child')->default(0);
            $table->unsignedBigInteger('transfer_from_parent')->default(0);

            $table->unsignedBigInteger('salary')->default(0);                      // 个人日工资
            $table->unsignedBigInteger('dividend')->default(0);                    // 个人分红
            $table->unsignedBigInteger('gift')->default(0);                        // 活动礼金

            $table->unsignedBigInteger('system_transfer_add')->default(0);         // 系统理赔增加
            $table->unsignedBigInteger('system_transfer_reduce')->default(0);      // 系统理赔减少

            $table->integer('team_first_register')->default(0);
            $table->integer('team_have_bet')->default(0);

            $table->unsignedBigInteger('team_recharge_amount')->default(0);
            $table->unsignedInteger('team_first_recharge_count')->default(0);
            $table->unsignedInteger('team_recharge_count')->default(0);
            $table->unsignedInteger('team_repeat_recharge_count')->default(0);

            $table->unsignedBigInteger('team_withdraw_amount')->default(0);
            $table->integer('team_withdraw_count')->default(0);
            $table->unsignedBigInteger('team_bets')->default(0);
            $table->unsignedBigInteger('team_cancel')->default(0);
            $table->unsignedBigInteger('team_he_return')->default(0);
            $table->unsignedBigInteger('team_commission_from_bet')->default(0);
            $table->unsignedBigInteger('team_commission_from_child')->default(0);
            $table->unsignedBigInteger('team_bonus')->default(0);
            $table->unsignedBigInteger('team_score')->default(0);

            $table->unsignedBigInteger('team_gift')->default(0);                            // 活动礼金
            $table->unsignedBigInteger('team_salary')->default(0);                          // 日工资

            $table->unsignedBigInteger('team_system_transfer_add')->default(0);             // 理赔增加
            $table->unsignedBigInteger('team_system_transfer_reduce')->default(0);          // 理赔减少


            $table->timestamps();

            $table->unique(array("partner_sign", 'user_id'), "rsu_ps_ui");
            $table->index(array("partner_sign", 'username'), "rsu_ps_u");
            $table->index(array("partner_sign", 'parent_id'), "rsu_ps_pi");
            $table->index(array("partner_sign", 'top_id'), "rsu_ps_ti");
        });

        // 每日销售
        Schema::create('report_stat_lottery_day', function (Blueprint $table) {

            $table->increments('id');

            $table->string('partner_sign',      64);
            $table->string('lottery_sign',      32);
            $table->string('lottery_name',      64);

            // 彩种
            $table->unsignedBigInteger('bets')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);
            $table->unsignedBigInteger('cancel')->default(0);
            $table->unsignedBigInteger('limit_reduce')->default(0);
            $table->unsignedBigInteger('challenge_reduce')->default(0);
            $table->unsignedBigInteger('he_return')->default(0);

            $table->unsignedBigInteger('commission_from_child')->default(0);
            $table->unsignedBigInteger('commission_from_bet')->default(0);

            $table->integer('day');         // 更新日期
            $table->timestamps();

            $table->index(array('partner_sign', 'lottery_sign', 'day'), "rsld_ps_ls_d");
        });

        // 统计时序
        Schema::create('report_stat_stack', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('user_id');
            $table->tinyInteger('is_tester')->default(0);        // 是否測試用戶
            $table->string('username', 64);
            $table->string('partner_sign', 64);
            $table->integer('top_id');
            $table->integer('parent_id');
            $table->string('rid', 512);

            // recharge
            // withdraw
            // register
            // first_bet
            $table->string('type_sign', 32);
            $table->unsignedBigInteger('amount');                      // 数量

            $table->tinyInteger('is_first')->default(0);         // 首次
            $table->tinyInteger('is_has')->default(0);           // 当期之前　有过
            $table->tinyInteger('is_day_first')->default(0);     // 当日首次
            $table->tinyInteger('is_before_has')->default(0);    // 当日有個


            $table->integer('day');
            $table->unsignedBigInteger('day_m');                       // 更新日期

            $table->integer('stat_time')->default(0);            // 统计时间

            $table->timestamps();

            $table->index(array('day_m', 'stat_time'), "rss_dm_st");
            $table->index(array('user_id', 'day', 'type_sign'), "rss_ui_d_ts");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_stat_partner_day');
        Schema::dropIfExists('report_stat_user_day');
        Schema::dropIfExists('report_stat_user');
        Schema::dropIfExists('report_stat_lottery_day');
        Schema::dropIfExists('report_stat_stack');
    }
}
