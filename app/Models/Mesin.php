<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Mesin extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'mesins';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'verif_mesin',
        'tindakan_perbaikan',
        'keterangan',
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
        'verif_mesin' => 'array',
        'is_audit' => 'boolean',
        'date' => 'date',
        'tgl_update_produksi' => 'datetime',
        'tgl_update_spv' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        static::addGlobalScope('operasional', function (Builder $query) {
            $query->where(function ($q) {
                $q->where('is_audit', false)
                    ->orWhereNull('is_audit');
            });
        });
    }

    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    public function source()
    {
        return $this->belongsTo(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional');
    }

    public function auditCopies()
    {
        return $this->hasMany(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional');
    }

    public function buatSalinanAudit()
    {
        if ($this->is_audit) {
            return null;
        }

        $sudahAda = self::withoutGlobalScope('operasional')
            ->where('source_uuid', $this->uuid)
            ->where('is_audit', true)
            ->exists();

        if ($sudahAda) {
            return null;
        }

        $salinan = $this->replicate([
            'id',
            'uuid',
        ]);

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;

        $salinan->save();

        return $salinan;
    }
}
