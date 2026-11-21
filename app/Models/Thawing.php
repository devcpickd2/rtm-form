<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;

class Thawing extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'thawings';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'kondisi_ruangan',
        'jenis_produk',
        'kode_produksi',
        'jumlah',
        'kondisi_produk',
        'keterangan_kondisi',
        'suhu_ruangan',
        'mulai_thawing',
        'selesai_thawing',
        'kondisi_produk_setelah',
        'keterangan_kondisi_setelah',
        'jumlah_setelah',
        'suhu_produk',
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
     * Snapshot dibuat satu kali saat data awal disimpan.
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

        // Tabel memiliki id auto increment dan uuid sebagai PK Eloquent.
        $salinan->setAttribute('id', null);
        $salinan->setAttribute('uuid', null);

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;

        $salinan->save();
    }
}
