<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Steamer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'steamers';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'nama_produk', 'kode_produksi', 'steaming', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
