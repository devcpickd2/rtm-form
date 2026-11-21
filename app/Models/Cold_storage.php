<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Cold_storage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'cold_storages';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'pukul',
        'suhu_cs',
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

    protected $casts = [
        'suhu_cs' => 'array',
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        // Query standar hanya mengambil data operasional.
        static::addGlobalScope('operasional', function (Builder $query) {
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
        return $query->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Relasi salinan Audit ke data operasional.
     */
    public function sumberAudit()
    {
        return $this->belongsTo(self::class, 'source_uuid', 'uuid')
            ->withTrashed();
    }

    /**
     * Relasi data operasional ke salinan Audit.
     */
    public function salinanAudit()
    {
        return $this->hasOne(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Membuat snapshot Audit.
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

        // id merupakan auto increment terpisah dari UUID.
        $salinan->setAttribute('id', null);

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;

        $salinan->save();

        return $salinan;
    }
}
