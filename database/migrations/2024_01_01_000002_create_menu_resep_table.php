<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Class diagram: MenuResep (kelas_id FK, nama_menu, deskripsi, resep, foto)
        Schema::create('menu_resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('nama_menu');
            $table->text('deskripsi')->nullable();
            $table->text('resep')->nullable(); // bahan + langkah
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_resep');
    }
};
