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
            // $table->id('product_id');
            $table->string('product_id', 20);
            $table->primary('product_id');
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->text('description')->nullable();
            $table->double('product_price')->default(0);
            $table->tinyInteger('is_sale')->default(1)->comment("0 : Dừng bán hoặc dừng sản xuất  , 1: Có hàng bán");
            $table->timestamps();
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
