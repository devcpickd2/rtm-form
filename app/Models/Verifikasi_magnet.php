<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Verifikasi_magnet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'verifikasi_magnets';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'pukul', 'shift', 'nama_produk', 'kode_produksi', 'bukti', 'jumlah_temuan', 'keterangan', 'username', 'username_updated', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_produksi', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
