<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiRapor extends Model
{
    use HasFactory;

    protected $table = 'distribusi_rapor';

    protected $fillable = [
        'rapor_id',
        'orang_tua_id',
        'tgl_kirim',
        'status_kirim',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_kirim' => 'datetime',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────
    public function rapor()
    {
        return $this->belongsTo(Rapor::class);
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class);
    }
}
