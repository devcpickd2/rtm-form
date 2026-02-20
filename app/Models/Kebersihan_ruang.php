<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kebersihan_ruang extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kebersihan_ruangs';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift',
        'cr_rm', 'cs_1', 'cs_2', 'seasoning', 'prep_room', 'cooking', 'filling', 
        'rice_boiling', 'noodle', 'topping', 'packing', 'iqf', 'cs_fg', 'ds',
        'catatan', 'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 
        'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];

    // 🔑 Tambahkan ini
    protected $casts = [
        'cr_rm'        => 'array',
        'cs_1'         => 'array',
        'cs_2'         => 'array',
        'seasoning'    => 'array',
        'prep_room'    => 'array',
        'cooking'      => 'array',
        'filling'      => 'array',
        'rice_boiling' => 'array',
        'noodle'       => 'array',
        'topping'      => 'array',
        'packing'      => 'array',
        'iqf'          => 'array',
        'cs_fg'        => 'array',
        'ds'           => 'array',
    ];

    protected $dates = ['deleted_at'];
}
