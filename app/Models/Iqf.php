<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Iqf extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iqfs';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'no_iqf',
        'pukul',
        'nama_produk',
        'kode_produksi',
        'std_suhu',
        'suhu_pusat',
        'average',
        'problem',
        'tindakan_koreksi',
        'catatan',
        'username',
        'nama_produksi',
        'status_produksi',
        'status_spv',
        'username_updated',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'suhu_pusat' => 'array',
        'average' => 'float',
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        // Query operasional tidak menampilkan snapshot Audit.
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
        return $this->belongsTo(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operasional');
    }

    public function auditCopies()
    {
        return $this->hasMany(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operasional');
    }

    /**
     * Membuat snapshot Audit satu kali dari data operasional.
     */
    public function buatSalinanAudit(): ?self
    {
        if ($this->is_audit) {
            return null;
        }

        $sudahAda = self::auditData()
            ->where('source_uuid', $this->uuid)
            ->exists();

        if ($sudahAda) {
            return null;
        }

        $audit = $this->replicate([
            'id',
            'uuid',
            'created_at',
            'updated_at',
        ]);

        $audit->id = null;
        $audit->uuid = null;
        $audit->is_audit = true;
        $audit->source_uuid = $this->uuid;
        $audit->deleted_at = null;

        $audit->save();

        return $audit;
    }
}
