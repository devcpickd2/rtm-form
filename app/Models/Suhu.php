<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suhu extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'suhus';

    // kasih tahu laravel kalau primary key = 'uuid'
    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'pukul', 'shift',
        'chillroom', 'cs_1', 'cs_2', 'anteroom_rm',
        'seasoning_suhu', 'seasoning_rh',
        'rice', 'noodle', 'prep_room', 'cooking',
        'filling', 'topping', 'packing',
        'ds_suhu', 'ds_rh',
        'cs_fg', 'anteroom_fg',
        'keterangan', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 
        'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
