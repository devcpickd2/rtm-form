<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yoshinoya extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'yoshinoyas';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'saus', 'kode_produksi', 'suhu_pengukuran', 
        'brix', 'salt', 'visco', 'brookfield_sebelum', 'brookfield_frozen', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
