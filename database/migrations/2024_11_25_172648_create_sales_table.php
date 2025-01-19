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
        // Tabel sales header
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // ID transaksi
            $table->string('transaction_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Referensi ke pelanggan
            $table->foreignId('type_id')->constrained()->onDelete('cascade');
            $table->decimal('total_price', 15, 2); // Total transaksi
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('payment_amount', 15, 2)->default(0); // Jumlah pembayaran
            $table->decimal('change_amount', 15, 2)->default(0);  // Jumlah kembalian
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID pengguna yang melakukan transaksi
            $table->timestamps(); // Timestamps untuk created_at dan updated_at
        });

        // Tabel sales detail
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id(); // ID detail transaksi
            $table->foreignId('sales_id')->constrained('sales')->onDelete('cascade'); // Referensi ke header transaksi
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Relasi ke items
            $table->integer('quantity'); // Jumlah item
            $table->decimal('price', 15, 2); // Harga per item
            $table->decimal('subtotal', 15, 2); // Subtotal (quantity * price)
            $table->decimal('discount', 10, 2)->default(0);
            $table->timestamps(); // Timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details'); // Hapus tabel detail terlebih dahulu
        Schema::dropIfExists('sales'); // Hapus tabel header
    }
};
