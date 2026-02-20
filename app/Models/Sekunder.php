<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sekunder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sekunders';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift',
        'nama_produk', 'kode_produksi', 'shift', 'best_before', 'isi_per_zak', 'jumlah_produk', 'petugas','catatan',
        'username', 'nama_checker', 'status_checker', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_checker', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
