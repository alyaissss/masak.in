<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Class diagram: kelas (chef_id FK, nama_kelas, deskripsi, status, tanggal_mulai, tanggal_selesai)
        // + harga (dipakai pembayaran->jumlah) + foto (cover kartu katalog).
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_kelas');
            $table->text('deskripsi')->nullable();
            $table->string('status', 20)->default('draft')->index(); // draft | tersedia | selesai
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('harga', 12, 2)->default(0);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
