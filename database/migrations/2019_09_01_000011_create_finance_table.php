<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 财务相关
class CreateFinanceTable extends Migration
{

    public function up()
    {
        // 提现
        Schema::create('user_withdraw', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('top_id');
            $table->integer('parent_id');
            $table->integer('user_id');
            $table->string('username', 64);
            $table->string('nickname', 64);

            $table->string('order_id', 64)->default('');     // 订单号 和财务那边唯一
            $table->string('pay_order_id', 64)->default(''); // 外部订单号
            $table->integer('card_id');                                  // 提现卡id 关联到 user_bank_cards的id
            $table->string('bank_sign', 32)->default(0);     // 银行的sign
            $table->unsignedBigInteger('amount')->default(0);      // 体现金额
            $table->unsignedBigInteger('real_amount')->default(0); // 实际提现金额

            $table->integer('request_time')->default(0);           // 请求时间/订单发起时间
            $table->integer('check_time')->default(0);             // 审核时间/接手处理时间
            $table->integer('process_time')->default(0);           // 处理成功时间（成功/失败）

            $table->tinyInteger('from_device')->default(0);        // 来源默认 web

            $table->string('client_ip',20)->default('');     // 客户端IP
            $table->string('description',255)->default('');  // 描述
            // 回调错误返回
            $table->text('desc');

            $table->unsignedBigInteger('day_m')->default(0);       // 处理日期
            $table->tinyInteger('status')->default(0);             // 0 待审核 1 领取　2 审核完成 -2 审核失败;

            $table->integer('claim_admin_id')->default(0);         // 风控认领人ID
            $table->integer('claim_time')->default(0);             // 风控认领时间
            $table->integer('check_admin_id')->default(0);         // 风控审核管理员ID
            $table->integer('wind_process_time')->default(0);      // 风控处理时间

            $table->integer('finance_admin_id')->default(0);       // 财务认领人ID
            $table->integer('finance_time')->default(0);           // 财务认领时间
            $table->integer('finance_check_admin_id')->default(0); // 财务审核管理员ID
            $table->integer('finance_process_time')->default(0);   // 财务处理时间

            $table->integer('hand_check_admin_id') ->default(0);   // 手动处理认领人ID
            $table->integer('hand_time') ->default(0);             // 手动处理认领时间
            $table->integer('hand_admin_id') ->default(0);         // 手动处理管理员ID
            $table->integer('hand_process_time') ->default(0);     // 手动处理处理时间

            $table->timestamps();
            $table->index('order_id');
            $table->index('request_time');
            $table->index('day_m');
            $table->index(['partner_sign', 'order_id']);
            $table->index(['partner_sign', 'user_id', 'request_time']);
            $table->index(['partner_sign', 'user_id', 'process_time']);
        });

        // 提现日志
        Schema::create('user_withdraw_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->char('ip', 15)->default('');
            $table->string('order_id', 64)->default('');

            $table->unsignedInteger("amount")->default(0);

            $table->integer('user_id')->default(0);
            $table->string('username', 32)->default('');
            $table->string('nickname', 64)->default('');

            $table->text("request_params");
            $table->mediumText("request_back");
            $table->text("content");

            $table->string("request_reason", 128)->default('');
            $table->tinyInteger("request_status")->default(0);

            $table->string("back_reason", 128)->default('');
            $table->tinyInteger("back_status")->default(0);

            $table->timestamps();
            $table->index('order_id');
            $table->index(['partner_sign', 'order_id']);
        });

        // 充值
        Schema::create('user_recharge', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->integer('top_id');
            $table->integer('parent_id');
            $table->integer('user_id');
            $table->string('username', 64);
            $table->string('nickname', 64)->default('');

            // 订单属性
            $table->string('order_id', 64)->default('');         // 订单号
            $table->string('pay_order_id', 64)->default(0);      // 外部订单号-通过支付产生
            $table->string('channel', 64)->default('');          // 渠道名称
            $table->string('bank_sign', 32)->default('');        // 银行表示会
            $table->unsignedBigInteger('amount')->default(0);          // 充值金额
            $table->unsignedBigInteger('real_amount')->default(0);     // 真实到账
            $table->string('sign', 32)->default('');             // 附言

            $table->string('desc', 256)->default('');            // 充值描述
            $table->char('client_ip', 15)->default('');          // 充值IP

            $table->tinyInteger('from_device')->default(0);             // 来源默认 0:web,1:mobile

            // 时间
            $table->integer('request_time')->default(0);                // 请求时间
            $table->integer('send_time')->default(0);                   // 转发时间
            $table->integer('callback_time')->default(0);               // 回调时间

            $table->unsignedBigInteger('day_m')->default(0);            // 处理 日期
            $table->tinyInteger('status')->default(0);                  // 处理状态  0:未处理(默认); -1 请求失败; 2 回调成功 -2 回调失败
            $table->string('fail_reason', 256)->default('');      // 失败原因

            $table->integer('partner_admin_id')->default(0);            // 管理员ID 手工处理者 的ID

            $table->timestamps();
            $table->index('order_id');
            $table->index('channel');
            $table->index('username');
            $table->index('partner_sign');
            $table->index(['partner_sign', 'order_id']);
            $table->index(['partner_sign', 'user_id', 'request_time']);
            $table->index(['partner_sign', 'user_id', 'callback_time']);
        });

        // 充值日志
        Schema::create('user_recharge_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign', 32);

            $table->char('ip', 15)->default('');
            $table->string('order_id', 64)->default('');

            $table->unsignedInteger("amount")->default(0);

            $table->integer('user_id')->default(0);
            $table->string('username', 32)->default('');
            $table->string('nickname', 64)->default('');

            $table->text("request_params");
            $table->mediumText("request_back");
            $table->text("content");

            $table->string("request_reason", 128)->default('');
            $table->integer("request_time")->default(0);
            $table->tinyInteger("request_status")->default(0);

            $table->char("back_ip", 15)->default('');
            $table->string("back_reason", 128)->default('');
            $table->integer("back_time")->default(0);
            $table->tinyInteger("back_status")->default(0);

            $table->timestamps();

            $table->index('order_id');
            $table->index(['partner_sign', 'order_id']);
            $table->index(['partner_sign', 'user_id']);
        });

        // 支付渠道
        Schema::create('finance_platform_channel', function (Blueprint $table) {
            $table->increments('id');

            $table->string('platform_sign', 64)->default(0)->comment('支付平台的标识');
            $table->string('platform_child_sign', 64)->default(0)->comment('支付子平台的标识');
            $table->string('type_sign', 64)->default(0)->comment('支付类型的id');

            $table->string('channel_name',256)->default('')->comment('支付方式名称');
            $table->string('channel_sign',256)->default('')->comment('支付方式标记');

            $table->string('banks_code',256)->default('')->comment('网银支付时系统银行码与支付厂商银行码对照表');

            $table->tinyInteger('request_mode')->default(0)->comment('支付的请求方式 0 jump 1 json');
            $table->tinyInteger('direction')->default(1)->comment('金流的方向 1 入款 0 出款');
            $table->tinyInteger('status')->default(1)->comment('状态 1 上架 0 下架');

            $table->timestamps();
        });

        // 支付方式类型表
        Schema::create('finance_channel_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type_name',256)->default('')->comment('支付方式种类名称');
            $table->string('type_sign',256)->default('')->comment('支付方式种类标记');
            $table->tinyInteger('is_bank')->default(0)->comment('是否是银行 0 不是 1 是');
            $table->string('icon',256)->default('')->comment('支付方式图标');
            $table->timestamps();
        });

        // 第三方厂商表
        Schema::create('finance_platform', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_name',256)->default('')->comment('支付方式厂商名称');
            $table->string('platform_sign',256)->default('')->comment('支付方式厂商标记');
            $table->tinyInteger('is_pull')->default(0)->comment('是否拉取 0 不是 1 是');
            $table->string('platform_url',256)->default('')->comment('支付方式厂商url');
            $table->string('whitelist_ips',256)->default('')->comment('ip白名单');
            $table->timestamps();
        });

        // 支付账户
        Schema::create('finance_platform_account', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign',64)->default('')->comment('商户标识');
            $table->string('platform_sign', 32)->comment('平台标识');

            $table->string('merchant_code',256)->default('')->comment('商户号');
            $table->text('merchant_secret')->nullable()->comment('商户秘钥');
            $table->text('public_key')->nullable()->comment('第三方公钥');
            $table->text('private_key')->nullable()->comment('第三方私钥');

            $table->string('app_id',256)->nullable()->comment('第三方终端号');

            $table->tinyInteger('status')->default(1)->comment('状态 1 启用 0 停用');
            $table->timestamps();
        });

        // 支付账户-开放渠道
        Schema::create('finance_platform_account_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_sign',64)->default('')->comment('合作者标记');

            $table->integer('account_id')->default(0)->comment('帐户');

            $table->string('platform_sign', 32)->comment('平台');
            $table->string('platform_child_sign', 32)->comment('BB子平台');
            $table->string('channel_sign', 32)->comment('渠道的标识');
            $table->string('type_sign', 32)->comment('渠道类型s');

            $table->string('platform_channel_id', 32)->comment('渠道的id');

            $table->string('front_name',256)->default('')->comment('前台名称');
            $table->string('front_remark',256)->default('')->comment('前台备注');
            $table->string('back_name',256)->default('')->comment('后台名称');
            $table->string('back_remark',256)->default('')->comment('后台备注');

            $table->string('do_fixed_price',256)->default('')->comment('单价');

            // 手续费
            $table->tinyInteger('fee_type')->default(1)->comment('手续费类型'); // 1：定额，2：比例
            $table->tinyInteger('fee_from')->default(1)->comment('手续费来源'); // 1:平台，2：玩家

            $table->integer('fee_amount')->default(0)->comment('手续费');
            $table->integer('fee_return')->default(0)->comment('返利');

            $table->unsignedBigInteger('max')->default(100)->comment('最大金额');
            $table->unsignedBigInteger('min')->default(1)->comment('最小金额');

            $table->tinyInteger('device')->default(0)->comment('设备 0 全部 1 电脑端 2 手机端');

            $table->index('channel_sign');
            $table->index('platform_child_sign');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('level')->default(1)->comment('等级');
            $table->tinyInteger('status')->default(1)->comment('状态 1 启用 0 停用');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('user_withdraw');
        Schema::dropIfExists('user_withdraw_log');
        Schema::dropIfExists('user_recharge');
        Schema::dropIfExists('user_recharge_log');
        Schema::dropIfExists('finance_platform_channel');
        Schema::dropIfExists('finance_channel_type');
        Schema::dropIfExists('finance_platform');
        Schema::dropIfExists('finance_platform_account');
        Schema::dropIfExists('finance_platform_account_channel');
    }
}
