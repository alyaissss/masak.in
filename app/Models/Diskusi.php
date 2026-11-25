<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diskusi extends Model
{
    protected $table = 'diskusi';

    protected $fillable = ['kelas_id', 'user_id', 'parent_id', 'pesan'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Diskusi::class, 'parent_id');
    }

    public function balasan(): HasMany
    {
        return $this->hasMany(Diskusi::class, 'parent_id')->orderBy('created_at')->orderBy('id');
    }
}
