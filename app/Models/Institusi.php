<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Institusi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'institusis';
    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'jenis_produk',
        'kode_produksi',
        'waktu_proses_mulai',
        'waktu_proses_selesai',
        'lokasi',
        'suhu_sebelum',
        'suhu_sesudah',
        'sensori',
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
        'is_audit' => 'boolean',
        'waktu_proses_mulai' => 'datetime:H:i',
        'waktu_proses_selesai' => 'datetime:H:i',
        'tgl_update_produksi' => 'datetime',
        'tgl_update_spv' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('operasional', function (Builder $builder) {
            $builder->where(function ($query) {
                $query->where('is_audit', false)
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
        return $this->belongsTo(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operasional');
    }

    public function salinanAudit()
    {
        return $this->hasMany(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operasional');
    }

    public function buatSalinanAudit(): self
    {
        $salinan = $this->replicate();

        $salinan->id = null;
        $salinan->uuid = null;
        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;

        $salinan->save();

        return $salinan;
    }
}
