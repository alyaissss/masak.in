<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CHEF = 'chef';
    public const ROLE_LEARNER = 'learner';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'telepon',
        'spesialisasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed', // NFR-06: password wajib hashing
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isChef(): bool
    {
        return $this->role === self::ROLE_CHEF;
    }

    public function isLearner(): bool
    {
        return $this->role === self::ROLE_LEARNER;
    }

    public function kelasDiajar(): HasMany
    {
        return $this->hasMany(Kelas::class, 'chef_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'learner_id');
    }

    public function diskusi(): HasMany
    {
        return $this->hasMany(Diskusi::class, 'user_id');
    }

    public function inisial(): string
    {
        $bagian = preg_split('/\s+/', trim((string) $this->nama));
        $inisial = strtoupper(mb_substr($bagian[0] ?? 'M', 0, 1));
        if (count($bagian) > 1) {
            $inisial .= strtoupper(mb_substr(end($bagian), 0, 1));
        }

        return $inisial;
    }
}
