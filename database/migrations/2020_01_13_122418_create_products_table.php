<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
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
            $table->string('name',100);
            $table->string('slug',100);
            $table->string('sku')->nullable();
            $table->decimal('purchase_price', 15, 2)->default('0.00');
            $table->decimal('sale_price', 15, 2)->default('0.00');
            $table->text('description')->nullable();
            $table->integer('quantity')->default('0');
            $table->integer('tax_id')->default('0');
            $table->integer('unit_id')->default('0');
            $table->integer('category_id')->default('0');
            $table->integer('brand_id')->default('0');
            $table->string('image')->nullable();
            $table->tinyInteger('product_type')->default(0);
            $table->integer('created_by')->default('0');
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
}
