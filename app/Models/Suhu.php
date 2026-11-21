<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Suhu extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAudit;

    protected $table = 'suhus';

    // kasih tahu laravel kalau primary key = 'uuid'
    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date',
        'pukul',
        'shift',
        'is_audit',
        'source_uuid',
        'chillroom',
        'cs_1',
        'cs_2',
        'anteroom_rm',
        'seasoning_suhu',
        'seasoning_rh',
        'rice',
        'noodle',
        'prep_room',
        'cooking',
        'filling',
        'topping',
        'packing',
        'ds_suhu',
        'ds_rh',
        'cs_fg',
        'anteroom_fg',
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
        'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];


    /**
     * Membuat salinan data ke tabel yang sama sebagai Data Audit.
     */

    public function copyToAudit()
    {
        return \Illuminate\Support\Facades\DB::transaction(function () {

            if ((bool) $this->is_audit) {
                throw new \RuntimeException(
                    'Data Audit tidak dapat disalin kembali.'
                );
            }

            // Cek apakah salinan Audit sudah ada.
            $existing = self::where('source_uuid', $this->uuid)
                ->where('is_audit', true)
                ->first();

            if ($existing) {
                return $existing;
            }

            // Ambil seluruh atribut kecuali primary key dan timestamps.
            $attributes = $this->getAttributes();

            unset(
                $attributes['id'],
                $attributes['uuid'],
                $attributes['created_at'],
                $attributes['updated_at'],
                $attributes['deleted_at']
            );

            // Buat record Audit baru.
            $copy = new self();

            $copy->fill($attributes);

            // Pastikan primary key dibuat otomatis oleh database.
            $copy->setAttribute('id', null);

            $copy->is_audit = true;
            $copy->source_uuid = $this->uuid;

            $copy->deleted_at = null;

            $copy->save();

            return $copy;
        });
    }
}
