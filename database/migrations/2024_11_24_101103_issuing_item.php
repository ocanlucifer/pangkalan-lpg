<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Membuat tabel issuing_transactions
        Schema::create('issuings', function (Blueprint $table) {
            $table->id(); // ID transaksi
            $table->string('transaction_number')->unique(); // Nomor transaksi
            $table->date('transaction_date'); // Tanggal transaksi
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID pengguna yang melakukan transaksi
            $table->text('remarks')->nullable(); // Keterangan tambahan (jika ada)
            $table->timestamps(); // Timestamp: created_at, updated_at
        });

        // Membuat tabel issuing_items
        Schema::create('issuing_detail', function (Blueprint $table) {
            $table->id(); // ID untuk item
            $table->foreignId('issuing_id')->constrained('issuings')->onDelete('cascade'); // Hubungan ke tabel transaksi
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // ID item yang dikeluarkan
            $table->integer('quantity'); // Jumlah barang yang dikeluarkan
            $table->timestamps(); // Timestamp: created_at, updated_at
        });
    }

    public function down()
    {
        // Menghapus tabel issuing_detail dan issuings
        Schema::dropIfExists('issuing_detail');
        Schema::dropIfExists('issuings');
    }
};
