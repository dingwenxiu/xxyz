<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCustomerServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_services', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('title', 25)->nullable()->default(null)->comment('该项通道名称')	;
            $table->string('desc', 256)->nullable()->default(null)->comment('描述')	;
            $table->string('partner_sign', 256)->nullable()->default(null)->comment('商户标识')	;
            $table->integer('create_partner')->nullable()->default(-1)->comment('创建人ID')	;
            $table->tinyInteger('sort')->nullable()->default(0)->comment('排序')	;
            $table->tinyInteger('status')->default(0)->comment('是否启用 1启用 2停用')	;
            $table->tinyInteger('service_flag')->default(2)->comment('创建渠道 1系统默认 2自建')	;
            $table->timestamps();
            //$table->primary('id');
            $table->index('partner_sign','partner_sign');
            $table->index('title','title');
            $table->index('sort','sort');
            $table->index('status','status');
        });
        \DB::statement("ALTER TABLE `customer_services` comment '客服表'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_services');
    }
}
