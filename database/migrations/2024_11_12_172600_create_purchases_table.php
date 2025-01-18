<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel purchase_headers
        Schema::create('purchase_headers', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique(); // Nomor transaksi
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Vendor terkait
            $table->date('purchase_date'); // Tanggal transaksi
            $table->decimal('total_amount', 10, 2)->default(0); // Total nilai transaksi
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID pengguna yang melakukan transaksi
            $table->timestamps();
        });

        // Tabel purchase_details
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_header_id')->constrained('purchase_headers')->onDelete('cascade'); // Relasi ke purchase_headers
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Relasi ke items
            $table->integer('quantity'); // Jumlah item yang dibeli
            $table->decimal('price', 10, 2); // Harga per item
            $table->decimal('total_price', 10, 2); // Total harga untuk item (quantity * price)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
        Schema::dropIfExists('purchase_headers');
    }
};
