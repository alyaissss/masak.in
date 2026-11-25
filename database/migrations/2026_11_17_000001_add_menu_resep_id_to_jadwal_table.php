<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreignId('menu_resep_id')
                ->nullable()
                ->after('kelas_id')
                ->constrained('menu_resep')
                ->cascadeOnDelete()
                ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropUnique(['menu_resep_id']);
            $table->dropConstrainedForeignId('menu_resep_id');
        });
    }
};
