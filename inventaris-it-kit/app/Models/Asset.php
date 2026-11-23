<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    public const STATUSES = ['aktif', 'maintenance', 'rusak', 'nonaktif'];

    protected $fillable = [
        'kode_aset',
        'nama',
        'category_id',
        'location_id',
        'merk',
        'serial_number',
        'ip_address',
        'tanggal_beli',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_beli' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /** Cari berdasarkan kode, nama, serial number, atau IP. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('kode_aset', 'like', "%{$term}%")
                ->orWhere('nama', 'like', "%{$term}%")
                ->orWhere('serial_number', 'like', "%{$term}%")
                ->orWhere('ip_address', 'like', "%{$term}%");
        });
    }

    /** Nama warna badge Bootstrap untuk status aset. */
    public function badgeClass(): string
    {
        return match ($this->status) {
            'aktif' => 'success',
            'maintenance' => 'warning',
            'rusak' => 'danger',
            default => 'secondary',
        };
    }
}
