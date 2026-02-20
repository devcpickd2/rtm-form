<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retain extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'retains';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'sample_type', 'sample_storage', 'description', 'production_code', 'best_before', 
        'quantity', 'remarks', 'note',
        'username', 'nama_warehouse', 'status_warehouse', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_warehouse', 'tgl_update_spv'
    ];

    protected $casts = [
        'sample_storage' => 'array',
    ];

    protected $dates = ['deleted_at'];
}
