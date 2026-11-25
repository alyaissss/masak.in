<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rating bintang 1–5 dari learner untuk kelas yang sudah selesai diikuti.
        Schema::create('rating', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('bintang');
            $table->timestamps();
            $table->unique(['kelas_id', 'learner_id']); // satu rating per learner per kelas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating');
    }
};
