<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Nama menu makanan
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Relasi ke tabel categories
            $table->foreignId('type_id')->constrained('types')->onDelete('cascade'); // Relasi ke tabel types
            $table->boolean('active')->default(true); // Flag aktif
            $table->decimal('price', 10, 2); // Harga menu makanan
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID pengguna yang melakukan transaksi
            $table->timestamps(); // Timestamps untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
