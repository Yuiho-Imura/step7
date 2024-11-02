<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->string('product_name')->nullable();
            $table->integer('price')->nullable();
            $table->integer('stock');
            $table->text('comment')->nullable();
            $table->string('img_path')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            // 外部キー制約を追加
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
