<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Xray extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'xrays';
    protected $guarded = []; 

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'nama_produk', 'kode_produksi', 'no_program', 'pemeriksaan', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];
    
    protected $casts = [
        'pemeriksaan' => 'array',  // otomatis JSON <-> array
    ];
}
