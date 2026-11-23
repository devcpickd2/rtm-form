<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Gramasi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'gramasis';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'nama_produk',
        'kode_produksi',
        'tindakan_koreksi',
        'gramasi_topping',
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
        'gramasi_topping' => 'array',
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Data operasional tidak boleh menampilkan snapshot Audit.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('operasional', function (Builder $query) {
            $query->where(function ($q) {
                $q->where('is_audit', false)
                  ->orWhereNull('is_audit');
            });
        });
    }

    /**
     * Scope untuk mengambil data Audit saja.
     */
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
     * Membuat snapshot Audit satu kali untuk setiap data operasional.
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
