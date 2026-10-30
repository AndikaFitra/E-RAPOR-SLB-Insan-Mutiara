<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_log';

    protected $fillable = [
        'user_id',
        'subjek',
        'model_type',
        'model_id',
        'status',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // ── Helper Pencatatan ────────────────────────────────────
    public static function log(string $subjek, ?Model $model = null, ?string $status = null, ?User $user = null): self
    {
        return static::create([
            'user_id'    => $user?->id ?? auth()->id(),
            'subjek'     => $subjek,
            'model_type' => $model ? class_basename($model) : null,
            'model_id'   => $model?->getKey(),
            'status'     => $status,
        ]);
    }
}
