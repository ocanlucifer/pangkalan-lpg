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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique(); //nomor identitas
            $table->string('name');
            $table->string('address');
            $table->string('contact');
            $table->boolean('active')->default(true);
            $table->text('ktp_image')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID pengguna yang melakukan transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
