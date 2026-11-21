<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Sanitasi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sanitasis';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'pukul',
        'shift',
        'std_footbasin',
        'std_handbasin',
        'aktual_footbasin',
        'aktual_handbasin',
        'keterangan',
        'tindakan_koreksi',
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

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_audit' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Data operasional tidak boleh bercampur dengan data Audit.
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
            Sanitasi::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional');
    }

    public function salinanAudit()
    {
        return $this->hasOne(
            Sanitasi::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional');
    }

    public function buatSalinanAudit(): void
    {
        $sudahAda = self::auditData()
            ->where('source_uuid', $this->uuid)
            ->exists();

        if ($sudahAda) {
            return;
        }

        $salinan = $this->replicate();

        $salinan->id = null;
        $salinan->uuid = null;
        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;

        // Salin foto Footbasin ke folder khusus Audit.
        if (
            $this->aktual_footbasin &&
            Storage::disk('public')->exists($this->aktual_footbasin)
        ) {

            $extension = pathinfo($this->aktual_footbasin, PATHINFO_EXTENSION);

            $auditFootbasin = 'uploads/audit/footbasin/'
                . $this->uuid . '.' . $extension;

            Storage::disk('public')->copy(
                $this->aktual_footbasin,
                $auditFootbasin
            );

            $salinan->aktual_footbasin = $auditFootbasin;
        }

        // Salin foto Handbasin ke folder khusus Audit.
        if (
            $this->aktual_handbasin &&
            Storage::disk('public')->exists($this->aktual_handbasin)
        ) {

            $extension = pathinfo($this->aktual_handbasin, PATHINFO_EXTENSION);

            $auditHandbasin = 'uploads/audit/handbasin/'
                . $this->uuid . '.' . $extension;

            Storage::disk('public')->copy(
                $this->aktual_handbasin,
                $auditHandbasin
            );

            $salinan->aktual_handbasin = $auditHandbasin;
        }

        $salinan->save();
    }
}
