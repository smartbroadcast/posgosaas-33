<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returned_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('return_id')->default('0');
            $table->integer('product_id')->default('0');
            $table->decimal('price', 15, 2)->default('0.00');
            $table->integer('quantity')->default('0');
            $table->integer('tax_id')->default('0');
            $table->float('tax')->default('0.00');
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
        Schema::dropIfExists('returned_items');
    }
}
