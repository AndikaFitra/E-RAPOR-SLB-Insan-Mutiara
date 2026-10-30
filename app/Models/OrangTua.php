<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrangTua extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orang_tua';

    protected $fillable = [
        'siswa_id',
        'nama',
        'hubungan',
        'no_whatsapp',
        'is_penerima_utama',
    ];

    protected function casts(): array
    {
        return [
            'is_penerima_utama' => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function distribusi()
    {
        return $this->hasMany(DistribusiRapor::class);
    }
}
