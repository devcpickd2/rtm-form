<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retur extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'returs';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'no_mobil', 'nama_supir', 'nama_produk', 'kode_produksi', 'expired_date', 
        'jumlah', 'bocor', 'isi_kurang', 'lainnya', 'keterangan', 'catatan',
        'username', 'nama_warehouse', 'status_warehouse', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_warehouse', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
