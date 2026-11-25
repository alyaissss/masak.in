<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Class diagram: diskusi (kelas_id FK, user_id FK, pesan) + parent_id (threading balasan chef).
        Schema::create('diskusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('diskusi')->nullOnDelete();
            $table->text('pesan');
            $table->timestamps();
            $table->index(['kelas_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskusi');
    }
};
