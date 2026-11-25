<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Class diagram: pembayaran (learner_id FK, kelas_id FK, jumlah, status, metode, tanggal_bayar)
        // + no_referensi & bukti (konfirmasi manual tanpa payment gateway) + parent_id threading di diskusi.
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->string('metode', 30)->default('transfer_bank');
            $table->string('no_referensi', 60)->nullable();
            $table->string('bukti')->nullable(); // path di storage (maks 2 MB)
            $table->string('status', 20)->default('menunggu')->index(); // menunggu | berhasil | gagal
            $table->dateTime('tanggal_bayar')->nullable();
            $table->timestamps();
            $table->unique(['learner_id', 'kelas_id']); // satu pendaftaran per kelas per learner
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
