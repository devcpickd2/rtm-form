<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Kontaminasi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kontaminasis';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'pukul',
        'shift',
        'jenis_kontaminasi',
        'bukti',
        'nama_produk',
        'kode_produksi',
        'tahapan',
        'tindakan_koreksi',
        'catatan',
        'username',
        'username_updated',
        'nama_produksi',
        'status_produksi',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'tgl_update_produksi',
        'tgl_update_spv',
        'is_audit',
        'source_uuid',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Query biasa hanya mengambil data operasional.
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
     * Scope khusus untuk mengambil data Audit.
     */
    public function scopeAuditData(Builder $query): Builder
    {
        return $query->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Relasi dari salinan Audit ke data operasional.
     */
    public function sumberAudit()
    {
        return $this->belongsTo(self::class, 'source_uuid', 'uuid')
            ->withTrashed();
    }

    /**
     * Relasi dari data operasional ke salinan Audit.
     */
    public function salinanAudit()
    {
        return $this->hasOne(self::class, 'source_uuid', 'uuid')
            ->withoutGlobalScope('operasional')
            ->where('is_audit', true);
    }

    /**
     * Membuat satu salinan Audit dari data operasional.
     */
    public function buatSalinanAudit(): ?self
    {
        if ($this->is_audit) {
            return null;
        }

        $existing = self::withoutGlobalScope('operasional')
            ->where('source_uuid', $this->uuid)
            ->where('is_audit', true)
            ->first();

        if ($existing) {
            return $existing;
        }

        $salinan = $this->replicate();

        // Tabel memiliki id auto-increment terpisah dari UUID.
        // Jangan menyalin nilai id milik data operasional.
        $salinan->setAttribute('id', null);

        $salinan->is_audit = true;
        $salinan->source_uuid = $this->uuid;
        $salinan->deleted_at = null;
        $salinan->save();

        // Salin file bukti agar foto Audit tetap tersedia
        // walaupun foto pada data operasional diganti atau dihapus.
        if (
            $this->bukti &&
            Storage::disk('public')->exists($this->bukti)
        ) {
            $extension = pathinfo(
                $this->bukti,
                PATHINFO_EXTENSION
            ) ?: 'jpg';

            $auditPath = 'uploads/kontaminasi/audit_'
                . $salinan->uuid . '.' . $extension;

            Storage::disk('public')->copy(
                $this->bukti,
                $auditPath
            );

            $salinan->bukti = $auditPath;
            $salinan->save();
        }

        return $salinan;
    }
}
