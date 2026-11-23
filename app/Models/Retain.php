<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Retain extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'retains';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'plant',
        'sample_type',
        'sample_storage',
        'description',
        'production_code',
        'best_before',
        'quantity',
        'remarks',
        'note',
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
        'sample_storage' => 'array',
        'is_audit' => 'boolean',
        'date' => 'date',
        'best_before' => 'date',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        // Data operasional tidak boleh bercampur dengan snapshot Audit.
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

    /**
     * Membuat satu snapshot Audit dari data operasional.
     */
    public function buatSalinanAudit(): self
    {
        $salinan = $this->replicate();

        // Tabel memiliki kolom id auto increment dan uuid.
        $salinan->id = null;
        $salinan->uuid = null;

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;

        $salinan->save();

        return $salinan;
    }
}
