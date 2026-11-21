<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Thumbling extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'thumblings';
    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'username_updated',
        'date',
        'shift',
        'nama_produk',
        'kode_produksi',
        'identifikasi_daging',
        'asal_daging',
        'kode_daging',
        'berat_daging',
        'suhu_daging',
        'rata_daging',
        'kondisi_daging',
        'premix',
        'kode_premix',
        'berat_premix',
        'bahan_lain',
        'air',
        'suhu_air',
        'suhu_marinade',
        'lama_pengadukan',
        'marinade_brix_salinity',
        'drum_on',
        'drum_off',
        'drum_speed',
        'vacuum_time',
        'total_time',
        'waktu_mulai',
        'waktu_selesai',
        'suhu_daging_thumbling',
        'rata_daging_thumbling',
        'kondisi_daging_akhir',
        'catatan_akhir',
        'catatan',
        'nama_produksi',
        'status_produksi',
        'tgl_update_produksi',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'tgl_update_spv',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'kode_daging' => 'array',
        'berat_daging' => 'array',
        'suhu_daging' => 'array',
        'rata_daging' => 'array',
        'premix' => 'array',
        'kode_premix' => 'array',
        'berat_premix' => 'array',
        'bahan_lain' => 'array',
        'suhu_daging_thumbling' => 'array',
        'rata_daging_thumbling' => 'float',
        'is_audit' => 'boolean',
        'date' => 'date',
        'tgl_update_produksi' => 'datetime',
        'tgl_update_spv' => 'datetime',
    ];

    protected $attributes = [
        'kode_daging' => '[]',
        'berat_daging' => '[]',
        'suhu_daging' => '[]',
        'rata_daging' => '[]',
        'premix' => '[]',
        'kode_premix' => '[]',
        'berat_premix' => '[]',
        'bahan_lain' => '[]',
        'suhu_daging_thumbling' => '[]',
    ];

    protected $dates = ['deleted_at'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

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
