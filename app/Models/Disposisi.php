<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Disposisi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'disposisis';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'nama_produk',
        'kode_produksi',
        'jumlah',
        'ketidaksesuaian',
        'tindakan',
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
        'date' => 'date',
        'jumlah' => 'float',
        'is_audit' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Data operasional tidak menampilkan salinan Audit.
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
     * Query khusus data Audit.
     */
    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Data operasional sumber dari salinan Audit.
     */
    public function source()
    {
        return $this->belongsTo(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional');
    }

    /**
     * Salinan Audit dari data operasional.
     */
    public function auditCopies()
    {
        return $this->hasMany(
            self::class,
            'source_uuid',
            'uuid'
        )->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Membuat satu salinan Audit.
     */
    public function buatSalinanAudit(): self
    {
        // Jangan membuat salinan lebih dari satu kali.
        $sudahAda = self::auditCopies()->exists();

        if ($sudahAda) {
            return self::auditCopies()->first();
        }

        $audit = $this->replicate([
            'id',
            'uuid',
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
