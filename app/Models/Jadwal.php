<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = ['kelas_id', 'menu_resep_id', 'tanggal', 'waktu', 'link_sesi'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu' => 'datetime:H:i',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function menuResep(): BelongsTo
    {
        return $this->belongsTo(MenuResep::class, 'menu_resep_id');
    }

    public function tanggalLengkap(): string
    {
        return $this->tanggal->translatedFormat('l, d F Y');
    }

    public function sudahLewat(): bool
    {
        return $this->tanggal->endOfDay()->isPast();
    }

    public function waktuSelesai(): ?CarbonInterface
    {
        return $this->tanggal->copy()->setTimeFrom($this->waktu);
    }
}
