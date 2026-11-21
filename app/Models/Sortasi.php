<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;

class Sortasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sortasis';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'nama_bahan',
        'kode_produksi',
        'jumlah_bahan',
        'jumlah_sesuai',
        'jumlah_tidak_sesuai',
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

    protected $casts = [
        'is_audit' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Query normal hanya menampilkan data operasional.
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
        return $this->belongsTo(self::class, 'source_uuid', 'uuid');
    }

    public function salinanAudit()
    {
        return $this->hasOne(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operasional');
    }

    /**
     * Membuat snapshot Audit satu kali dari data operasional.
     */
    public function buatSalinanAudit(): void
    {
        if ($this->is_audit) {
            return;
        }

        $sudahAda = self::withoutGlobalScope('operasional')
            ->where('is_audit', true)
            ->where('source_uuid', $this->uuid)
            ->exists();

        if ($sudahAda) {
            return;
        }

        $salinan = $this->replicate();

        // Tabel mempunyai id auto increment dan uuid sebagai PK Eloquent.
        $salinan->setAttribute('id', null);
        $salinan->setAttribute('uuid', null);

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;

        $salinan->save();
    }
}
