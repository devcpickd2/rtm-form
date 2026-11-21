<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Cooking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'cookings';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'shift',
        'nama_produk',
        'sub_produk',
        'jenis_produk',
        'kode_produksi',
        'waktu_mulai',
        'waktu_selesai',
        'nama_mesin',
        'pemasakan',
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

    protected $dates = [
        'deleted_at',
    ];

    protected static function booted(): void
    {
        // Data operasional tidak boleh menampilkan snapshot Audit.
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

    public function auditSnapshots()
    {
        return $this->hasMany(self::class, 'source_uuid', 'uuid')
                    ->withoutGlobalScope('operational');
    }

    /**
     * Membuat satu salinan Audit dari data operasional.
     * Dipanggil hanya ketika data pertama kali disimpan.
     */
    public function buatSalinanAudit(): self
    {
        $snapshot = $this->replicate();

        // Tabel memiliki kolom id auto increment dan uuid sebagai PK Eloquent.
        $snapshot->id = null;
        $snapshot->uuid = null;

        $snapshot->is_audit = true;
        $snapshot->source_uuid = $this->uuid;
        $snapshot->deleted_at = null;

        $snapshot->save();

        return $snapshot;
    }
}
