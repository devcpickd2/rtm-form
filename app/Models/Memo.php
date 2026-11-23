<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Memo extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'memos';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'retur_dari',
        'retur_ke',
        'subject',
        'detail_retur',
        'username',
        'username_updated',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'tgl_update_spv',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'date' => 'date',
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
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

        $salinan->id = null;
        $salinan->uuid = null;
        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;

        $salinan->save();

        return $salinan;
    }
}
