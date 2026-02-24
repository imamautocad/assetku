<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumableOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('consumable_order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('consumable_id');
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('consumable_orders')->onDelete('cascade');
            $table->foreign('consumable_id')->references('id')->on('consumables')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consumable_order_details');
    }
}
