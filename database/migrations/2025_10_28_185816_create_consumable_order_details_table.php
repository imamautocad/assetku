<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel detail order consumable
     */
    public function up(): void
    {
        Schema::create('consumable_order_details', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key auto increment
            $table->unsignedBigInteger('consumable_order_id'); // Foreign ke header
            $table->string('no_req', 25)->nullable(); // Nomor request (header)
            $table->unsignedInteger('consumable_id')->nullable(); // Barang/Consumable
            $table->unsignedInteger('category_id')->nullable(); // Kategori
            $table->integer('qty')->nullable(); // Jumlah diminta
            $table->unsignedInteger('user_id')->nullable(); // Pembuat
            $table->timestamps();

            // Foreign key ke header (consumable_orders)
            $table->foreign('consumable_order_id')
                ->references('id')
                ->on('consumable_orders')
                ->onDelete('cascade');

            // Foreign key opsional (jika tabelnya ada)
            $table->foreign('consumable_id')->references('id')->on('consumables')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Rollback migrasi
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_order_details');
    }
};
