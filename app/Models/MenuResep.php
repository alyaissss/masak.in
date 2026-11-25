<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MenuResep extends Model
{
    protected $table = 'menu_resep';

    protected $fillable = ['kelas_id', 'nama_menu', 'deskripsi', 'resep', 'foto'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jadwal(): HasOne
    {
        return $this->hasOne(Jadwal::class, 'menu_resep_id');
    }
}
