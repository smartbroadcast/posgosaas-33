<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('reference_no', 100);
            $table->integer('vendor_id')->default('0');
            $table->integer('customer_id')->default('0');
            $table->text('return_note')->nullable();
            $table->text('staff_note')->nullable();
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
        Schema::dropIfExists('products_returns');
    }
}
