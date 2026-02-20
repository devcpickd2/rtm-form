<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cold_storage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'cold_storages';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'pukul', 'suhu_cs',
        'catatan','username', 'nama_warehouse', 'status_warehouse', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_warehouse', 'tgl_update_spv'
    ];
    
    protected $casts = [
        'suhu_cs' => 'array'
    ];

    protected $dates = ['deleted_at'];
}
