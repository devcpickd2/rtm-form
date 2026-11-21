<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Kebersihan_ruang extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kebersihan_ruangs';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'cr_rm',
        'cs_1',
        'cs_2',
        'seasoning',
        'prep_room',
        'cooking',
        'filling',
        'rice_boiling',
        'noodle',
        'topping',
        'packing',
        'iqf',
        'cs_fg',
        'ds',
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
        'cr_rm'        => 'array',
        'cs_1'         => 'array',
        'cs_2'         => 'array',
        'seasoning'    => 'array',
        'prep_room'    => 'array',
        'cooking'      => 'array',
        'filling'      => 'array',
        'rice_boiling' => 'array',
        'noodle'       => 'array',
        'topping'      => 'array',
        'packing'      => 'array',
        'iqf'          => 'array',
        'cs_fg'        => 'array',
        'ds'           => 'array',
        'is_audit'     => 'boolean',
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
        $sudahAda = self::auditData()
            ->where('source_uuid', $this->uuid)
            ->exists();

        if ($sudahAda) {
            return;
        }

        $salinan = $this->replicate();

        // Tabel memiliki id auto increment selain UUID.
        $salinan->id = null;
        $salinan->uuid = null;

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;

        $salinan->save();
    }
}
