<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengemasan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pengemasans';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'pukul', 'nama_produk', 'kode_produksi', 'tray_checking', 'box_checking', 'date_packing', 'shift_packing', 'pukul_packing','tray_packing', 'box_packing', 'keterangan_checking', 'keterangan_packing', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];

    protected $casts = [
        'tray_checking' => 'array',  // otomatis JSON <-> array
        'box_checking' => 'array',
        'tray_packing' => 'array',
        'box_packing' => 'array',
    ];

    protected $dates = ['deleted_at'];
}
