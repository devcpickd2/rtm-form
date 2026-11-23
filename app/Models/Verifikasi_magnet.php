<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Verifikasi_magnet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'verifikasi_magnets';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'pukul',
        'shift',
        'nama_produk',
        'kode_produksi',
        'bukti',
        'jumlah_temuan',
        'keterangan',

        'username',
        'username_updated',

        'nama_produksi',
        'status_produksi',
        'tgl_update_produksi',

        'nama_spv',
        'status_spv',
        'catatan_spv',
        'tgl_update_spv',

        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'is_audit' => 'boolean',
        'date' => 'date',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Query standar hanya mengambil data operasional.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('operasional', function (Builder $query) {
            $query->where(function ($q) {
                $q->where('is_audit', false)
                    ->orWhereNull('is_audit');
            });
        });
    }

    /**
     * Mengambil data Audit.
     */
    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Relasi data Audit ke data sumber.
     */
    public function sumberAudit()
    {
        return $this->belongsTo(
            self::class,
            'source_uuid',
            'uuid'
        )->withTrashed();
    }

    /**
     * Relasi data operasional ke salinan Audit.
     */
    public function salinanAudit()
    {
        return $this->hasOne(
            self::class,
            'source_uuid',
            'uuid'
        )
            ->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Membuat snapshot Audit satu kali saat data pertama disimpan.
     */
    public function buatSalinanAudit(): ?self
    {
        if ($this->is_audit) {
            return null;
        }

        $existing = self::withoutGlobalScope('operasional')
            ->where('source_uuid', $this->uuid)
            ->where('is_audit', true)
            ->first();

        if ($existing) {
            return $existing;
        }

        $salinan = $this->replicate();

        // Kolom id adalah auto increment, sedangkan uuid adalah primary key.
        $salinan->setAttribute('id', null);

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;

        $salinan->save();

        return $salinan;
    }
}
