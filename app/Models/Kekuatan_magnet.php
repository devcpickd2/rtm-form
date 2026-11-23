<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Kekuatan_magnet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kekuatan_magnets';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'nama_alat',
        'gauss',
        'kondisi',
        'parameter',
        'keterangan',
        'nama_produksi',
        'status_produksi',
        'tgl_update_produksi',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'tgl_update_spv',
        'username',
        'username_updated',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'date' => 'date',
        'is_audit' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Global Scope:
     * Data utama hanya menampilkan data QC (is_audit = 0).
     */
    protected static function booted(): void
    {
        static::addGlobalScope('operational', function (Builder $query) {
            $query->where(function ($q) {
                $q->where('is_audit', false)
                    ->orWhereNull('is_audit');
            });
        });
    }

    /**
     * Scope untuk mengambil data Audit.
     */
    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('operational')
            ->where('is_audit', true);
    }

    /**
     * Relasi ke data QC sumber.
     */
    public function source()
    {
        return $this->belongsTo(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operational');
    }

    /**
     * Relasi ke salinan Audit.
     */
    public function auditCopies()
    {
        return $this->hasMany(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operational')
            ->where('is_audit', true);
    }

    /**
     * Membuat salinan data untuk Audit.
     */
    public function buatSalinanAudit(): self
    {
        $salinan = $this->replicate();

        $salinan->id = null;
        $salinan->uuid = null;
        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;

        $salinan->save();

        return $salinan;
    }
}
