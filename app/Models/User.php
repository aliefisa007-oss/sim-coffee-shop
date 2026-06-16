<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStok::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKasir($query)
    {
        return $query->where('role', 'kasir');
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }
}