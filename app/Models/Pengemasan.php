<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Pengemasan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pengemasans';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'pukul',
        'nama_produk',
        'kode_produksi',

        'tray_checking',
        'box_checking',
        'date_packing',
        'shift_packing',
        'pukul_packing',
        'tray_packing',
        'box_packing',
        'keterangan_checking',
        'keterangan_packing',
        'catatan',

        'username',
        'nama_produksi',
        'status_produksi',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'username_updated',
        'tgl_update_produksi',
        'tgl_update_spv',

        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'tray_checking' => 'array',
        'box_checking'  => 'array',
        'tray_packing'  => 'array',
        'box_packing'   => 'array',
        'is_audit'      => 'boolean',
        'date'          => 'date',
        'date_packing'  => 'date',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        // Query operasional tidak boleh mengambil snapshot Audit.
        static::addGlobalScope('pengemasan_operasional', function (Builder $query) {
            $query->where(function ($q) {
                $q->where('is_audit', false)
                  ->orWhereNull('is_audit');
            });
        });
    }

    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('pengemasan_operasional')
            ->where('is_audit', true);
    }

    public function source()
    {
        return $this->belongsTo(
            Pengemasan::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('pengemasan_operasional');
    }

    public function auditCopies()
    {
        return $this->hasMany(
            Pengemasan::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('pengemasan_operasional')
         ->where('is_audit', true);
    }

    /**
     * Membuat snapshot Audit dari data operasional.
     * Snapshot hanya dibuat satu kali.
     */
    public function buatSalinanAudit(): self
    {
        if ($this->is_audit) {
            return $this;
        }

        $sudahAda = self::auditData()
            ->where('source_uuid', $this->uuid)
            ->exists();

        if ($sudahAda) {
            return $this;
        }

        $audit = $this->replicate();

        // Biarkan HasUuids membuat UUID baru.
        $audit->id = null;
        $audit->uuid = null;

        $audit->is_audit = true;
        $audit->source_uuid = $this->uuid;

        // Salinan Audit tidak ikut terhapus ketika data operasional
        // masuk Recycle Bin.
        $audit->deleted_at = null;

        $audit->save();

        return $audit;
    }
}
