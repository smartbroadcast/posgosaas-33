<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAmountFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'products', function (Blueprint $table){
            $table->float('purchase_price', 25, 2)->default('0.00')->nullable()->change();
            $table->float('sale_price', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'purchased_items', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'selled_items', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'returned_items', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'quotation_items', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'expenses', function (Blueprint $table){
            $table->float('amount', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'branch_target_lists', function (Blueprint $table){
            $table->float('target_amount', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'plans', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->nullable()->change();
        });

        Schema::table( 'orders', function (Blueprint $table){
            $table->float('price', 25, 2)->default('0.00')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
