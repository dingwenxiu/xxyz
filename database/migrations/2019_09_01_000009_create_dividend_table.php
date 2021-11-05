<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


// 平台
class CreateDividendTable extends Migration
{
    /**
     *　分红记录
     * @return void
     */
    public function up() {

        Schema::create('report_user_dividend', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('top_id')->default(0);
            $table->integer('user_id');
            $table->tinyInteger('is_tester')->default(0);        // 是否測試用戶
            
            $table->integer('parent_id');
            $table->integer('user_level');

            $table->string('parent_username',64);
            $table->string('username',64);

            $table->integer('from_user_id')->default(0);
            $table->string('from_username',64)->default('');

            $table->integer('month');                       // 月份
            $table->integer('sort');                        // 每期标志

            $table->integer('send_day');                    // 发放日期
            $table->integer('from_day');                    // 开始时间
            $table->integer('end_day');                     // 结束时间

            $table->unsignedBigInteger('total_bets')->default(0);
            $table->unsignedBigInteger('total_bonus')->default(0);
            $table->unsignedBigInteger('total_cancel')->default(0);
            $table->unsignedBigInteger('total_he_return')->default(0);
            $table->unsignedBigInteger('total_commission_from_bet')->default(0);
            $table->unsignedBigInteger('total_commission_from_child')->default(0);
            $table->unsignedBigInteger('total_gift')->default(0);
            $table->unsignedBigInteger('total_salary')->default(0);
            $table->unsignedBigInteger('total_dividend')->default(0);

            $table->bigInteger('profit')->default(0);                       // 亏损
            $table->decimal('rate', 5,2)->default(0);                            // 百分比

            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedBigInteger('real_amount')->default(0);

            $table->integer('init_time')->default(0);
            $table->integer('send_time')->default(0);

            $table->tinyInteger('status')->default(0);

            $table->timestamps();

            $table->index(["partner_sign", "from_user_id", "month"]);
            $table->index(["partner_sign", "user_id", "month"]);
            $table->unique(["partner_sign", "user_id", "month", "sort"]);
            $table->index(["partner_sign", "month", "sort"]);
            $table->index(["partner_sign", "user_id", "init_time"]);
            $table->index(["partner_sign", "from_user_id", "init_time"]);
        });

        // 分红配置
        Schema::create('user_dividend_config', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('top_id');
            $table->integer('parent_id');
            $table->integer('user_id');
            $table->string('username',64);

            $table->text('contract_before')->nullable();                // 上次约定内容
            $table->text('contract')->nullable();

            $table->tinyInteger('verify')->default(0);
            $table->tinyInteger('status')->default(0);

            $table->integer('verify_time')->default(0);

            $table->timestamps();

            $table->index(array("partner_sign", "user_id"));
            $table->index(array("partner_sign", "parent_id"));
        });

    }

    public function down() {
        Schema::dropIfExists('report_user_dividend');
        Schema::dropIfExists('user_dividend_config');
    }
}
