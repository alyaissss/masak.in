<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Rating extends Model
{
    protected $table = 'rating';

    protected $fillable = ['kelas_id', 'learner_id', 'bintang'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    /**
     * Rata-rata bintang per chef (dari semua kelas yang diampunya).
     *
     * @return Collection<int, object{chef_id:int, rata:float, jumlah:int}> keyed by chef_id
     */
    public static function ringkasanChef(): Collection
    {
        return self::query()
            ->join('kelas', 'kelas.id', '=', 'rating.kelas_id')
            ->selectRaw('kelas.chef_id as chef_id, AVG(rating.bintang) as rata, COUNT(*) as jumlah')
            ->groupBy('kelas.chef_id')
            ->get()
            ->map(fn ($r) => (object) [
                'chef_id' => (int) $r->chef_id,
                'rata' => round((float) $r->rata, 1),
                'jumlah' => (int) $r->jumlah,
            ])
            ->keyBy('chef_id');
    }
}
