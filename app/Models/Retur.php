<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Retur extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'returs';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'no_mobil',
        'nama_supir',
        'nama_produk',
        'kode_produksi',
        'expired_date',
        'jumlah',
        'bocor',
        'isi_kurang',
        'lainnya',
        'keterangan',
        'catatan',
        'username',
        'nama_warehouse',
        'status_warehouse',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'username_updated',
        'tgl_update_warehouse',
        'tgl_update_spv',
        'is_audit',
        'source_uuid',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_audit' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Snapshot Audit tidak ditampilkan pada halaman operasional.
        static::addGlobalScope('operational', function (Builder $query) {
            $query->where(function ($q) {
                $q->where('is_audit', false)
                    ->orWhereNull('is_audit');
            });
        });
    }

    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('operational')
            ->where('is_audit', true);
    }

    public function source()
    {
        return $this->belongsTo(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operational');
    }

    public function auditCopies()
    {
        return $this->hasMany(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operational')
            ->where('is_audit', true);
    }

    public function buatSalinanAudit(): self
    {
        $salinan = $this->replicate();

        // Tabel memiliki id auto increment dan uuid.
        $salinan->id = null;
        $salinan->uuid = null;
        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;

        $salinan->save();

        return $salinan;
    }
}
