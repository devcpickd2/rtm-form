<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Metal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'metals';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'nama_produk',
        'kode_produksi',
        'no_program',
        'pemeriksaan',
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
        'pemeriksaan' => 'array',
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        // Data operasional tidak menampilkan snapshot Audit.
        static::addGlobalScope('metal_operasional', function (Builder $query) {
            $query->where(function ($q) {
                $q->where('is_audit', false)
                    ->orWhereNull('is_audit');
            });
        });
    }

    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('metal_operasional')
            ->where('is_audit', true);
    }

    public function source()
    {
        return $this->belongsTo(
            Metal::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('metal_operasional');
    }

    public function auditCopies()
    {
        return $this->hasMany(
            Metal::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('metal_operasional')
         ->where('is_audit', true);
    }

    /**
     * Membuat snapshot Audit satu kali.
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

        $audit->id = null;
        $audit->uuid = null;
        $audit->is_audit = true;
        $audit->source_uuid = $this->uuid;
        $audit->deleted_at = null;

        $audit->save();

        return $audit;
    }
}
