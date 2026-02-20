<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemusnahan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pemusnahans';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'nama_produk', 'kode_produksi', 
        'expired_date', 'analisis', 'keterangan',
        'username', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
