<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_aktif',
        'guru_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_aktif'  => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    // ── Helpers ──────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'admin'          => 'admin.dashboard',
            'kepala_sekolah' => 'kepala.dashboard',
            'wali_kelas'     => 'wali.dashboard',
            default          => 'login',
        };
    }
}
