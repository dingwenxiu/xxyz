<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 商户管理相关
class CreateTemplateTable extends Migration
{

    public function up()
    {
        // 模块
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->nullable();
            $table->string('sign', 256)->unique()->nullable();
            $table->string('partner_sign', 64)->nullable();
            $table->string('partner_name', 64)->nullable();
            $table->string('module_sign', 256)->nullable()->comment('模型标识');
            $table->string('module_name', 256)->nullable()->comment('模型名字');
            $table->tinyInteger('status')->default(1)->comment('0 禁用 1 启用');

            $table->timestamps();
            $table->index('partner_sign');
            $table->index('name');
            $table->index('sign');
            $table->index('module_sign');
            $table->index('status');
        });

        // 模块
        Schema::create('template_colors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->nullable();
            $table->string('sign', 64)->nullable();
            $table->string('value', 64)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 禁用 1 启用');

            $table->timestamps();
            $table->index('name');
            $table->index('sign');
            $table->index('value');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('templates');
        Schema::dropIfExists('template_colors');    
    }
}
