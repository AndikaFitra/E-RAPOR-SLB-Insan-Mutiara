<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    use HasFactory;

    protected $table = 'rapor';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'wali_kelas_id',
        'status',
        'catatan_revisi',
        'catatan_wali_kelas',
        'tgl_rapor',
        'tgl_pengajuan',
        'tgl_persetujuan',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'tgl_rapor'       => 'date',
            'tgl_pengajuan'   => 'datetime',
            'tgl_persetujuan' => 'datetime',
        ];
    }

    // ── Status Constants ─────────────────────────────────────
    public const STATUS_DRAFT = 'draft';
    public const STATUS_DIAJUKAN = 'diajukan';
    public const STATUS_DIKEMBALIKAN = 'dikembalikan';
    public const STATUS_DISETUJUI = 'disetujui';

    // ── Relasi ──────────────────────────────────────────────
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function distribusi()
    {
        return $this->hasMany(DistribusiRapor::class);
    }

    // ── Status Helpers ───────────────────────────────────────
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isDiajukan(): bool
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function isDikembalikan(): bool
    {
        return $this->status === self::STATUS_DIKEMBALIKAN;
    }

    public function isDisetujui(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function isLocked(): bool
    {
        return in_array($this->status, [self::STATUS_DIAJUKAN, self::STATUS_DISETUJUI]);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_DRAFT => ['label' => 'Draft', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            self::STATUS_DIAJUKAN => ['label' => 'Diajukan', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            self::STATUS_DIKEMBALIKAN => ['label' => 'Dikembalikan', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
            self::STATUS_DISETUJUI => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-gray-100 text-gray-800 border-gray-200'],
        };
    }
}
