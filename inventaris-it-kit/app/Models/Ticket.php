<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    public const STATUSES = ['baru', 'diproses', 'selesai', 'ditutup'];

    public const PRIORITAS = ['rendah', 'sedang', 'tinggi'];

    /** Alur status yang diperbolehkan (state machine sederhana). */
    public const TRANSITIONS = [
        'baru' => ['diproses'],
        'diproses' => ['selesai'],
        'selesai' => ['ditutup', 'diproses'],
        'ditutup' => [],
    ];

    protected $fillable = [
        'nomor_tiket',
        'asset_id',
        'pelapor_id',
        'teknisi_id',
        'judul',
        'deskripsi',
        'prioritas',
        'status',
        'selesai_pada',
    ];

    protected $casts = [
        'selesai_pada' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class)->orderBy('created_at')->orderBy('id');
    }

    /** Staff hanya boleh melihat tiket miliknya sendiri. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->role === 'staff') {
            return $query->where('pelapor_id', $user->id);
        }

        return $query;
    }

    /**
     * Buat nomor tiket berurutan per bulan, contoh: TKT-202611-0001.
     * Harus dipanggil di dalam DB::transaction() agar lockForUpdate efektif.
     */
    public static function generateNumber(): string
    {
        $prefix = 'TKT-' . now()->format('Ym') . '-';

        $last = static::where('nomor_tiket', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('nomor_tiket')
            ->value('nomor_tiket');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'baru' => 'primary',
            'diproses' => 'warning',
            'selesai' => 'success',
            default => 'secondary',
        };
    }

    public function prioritasBadge(): string
    {
        return match ($this->prioritas) {
            'tinggi' => 'danger',
            'sedang' => 'info',
            default => 'secondary',
        };
    }
}
