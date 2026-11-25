<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'chef_id',
        'nama_kelas',
        'deskripsi',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'harga',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'harga' => 'decimal:2',
        ];
    }

    // State machine (Dokumen Tugas 5): Draft → Tersedia ⇄ Selesai, dan terhapus.
    public const STATUS_DRAFT = 'draft';

    public const STATUS_TERSEDIA = 'tersedia';

    public const STATUS_SELESAI = 'selesai';

    public const TRANSISI_VALID = [
        self::STATUS_DRAFT => [self::STATUS_TERSEDIA],          // Simpan / publikasikan kelas
        self::STATUS_TERSEDIA => [self::STATUS_SELESAI],        // Tandai selesai
        self::STATUS_SELESAI => [self::STATUS_TERSEDIA],        // Buka kembali
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function menuResep(): HasMany
    {
        return $this->hasMany(MenuResep::class, 'kelas_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kelas_id')->orderBy('tanggal')->orderBy('waktu');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'kelas_id');
    }

    public function diskusi(): HasMany
    {
        return $this->hasMany(Diskusi::class, 'kelas_id');
    }

    public function rating(): HasMany
    {
        return $this->hasMany(Rating::class, 'kelas_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_TERSEDIA => 'Tersedia',
            self::STATUS_SELESAI => 'Selesai',
            default => 'Draft',
        };
    }

    public function bolehTransisi(string $tujuan): bool
    {
        return in_array($tujuan, self::TRANSISI_VALID[$this->status] ?? [], true);
    }

    public function pesertaBerhasil(): int
    {
        return $this->pembayaran()->where('status', Pembayaran::STATUS_BERHASIL)->count();
    }

    public function hargaRp(): string
    {
        return 'Rp '.number_format((float) $this->harga, 0, ',', '.');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto) {
            return null;
        }

        return filter_var($this->foto, FILTER_VALIDATE_URL)
            ? $this->foto
            : asset('storage/'.ltrim($this->foto, '/'));
    }

    public function menuUtama(): string
    {
        return (string) ($this->menuResep()->orderBy('id')->value('nama_menu') ?? 'Menu segera diumumkan');
    }

    public function menuRingkasan(): string
    {
        if ($this->relationLoaded('menuResep')) {
            $menus = $this->menuResep;

            return $menus->count() > 1
                ? $menus->count().' menu'
                : (string) ($menus->first()?->nama_menu ?? 'Menu segera diumumkan');
        }

        $jumlahMenu = $this->menu_resep_count ?? $this->menuResep()->count();

        return $jumlahMenu > 1 ? $jumlahMenu.' menu' : $this->menuUtama();
    }
}
