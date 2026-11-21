<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Inventaris extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'inventaris';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'area',
        'detail',
        'kondisi_awal',
        'kondisi_akhir',
        'keterangan',
        'username',
        'username_updated',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'detail' => 'array',
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        // Query operasional hanya menampilkan data bukan Audit.
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

    public function sumberAudit()
    {
        return $this->belongsTo(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional');
    }

    public function salinanAudit()
    {
        return $this->hasOne(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional');
    }

    public function buatSalinanAudit(): void
    {
        // Cegah pembuatan snapshot ganda.
        $sudahAda = self::auditData()
            ->where('source_uuid', $this->uuid)
            ->exists();

        if ($sudahAda) {
            return;
        }

        $salinan = $this->replicate();

        // Tabel memiliki id auto increment dan uuid.
        $salinan->id = null;
        $salinan->uuid = null;

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;

        $salinan->save();
    }
}
