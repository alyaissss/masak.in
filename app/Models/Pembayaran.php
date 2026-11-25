<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'learner_id',
        'kelas_id',
        'jumlah',
        'metode',
        'no_referensi',
        'bukti',
        'status',
        'tanggal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_BERHASIL = 'berhasil';

    public const STATUS_GAGAL = 'gagal';

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_BERHASIL => 'Berhasil',
            self::STATUS_GAGAL => 'Gagal',
            default => 'Menunggu Verifikasi',
        };
    }

    public function jumlahRp(): string
    {
        return 'Rp '.number_format((float) $this->jumlah, 0, ',', '.');
    }

    public function metodeLabel(): string
    {
        return match ($this->metode) {
            'bca' => 'Transfer Bank BCA',
            'mandiri' => 'Transfer Bank Mandiri',
            'qris' => 'QRIS',
            default => ucfirst($this->metode),
        };
    }

    public function nomorNota(): string
    {
        return 'MASAK-TRX-'.str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);
    }
}
