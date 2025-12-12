<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Iqf extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iqfs';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date','shift','no_iqf','pukul','nama_produk','kode_produksi','std_suhu',
        'suhu_pusat','average','problem','tindakan_koreksi','catatan',
        'username','nama_produksi','status_produksi','status_spv','username_updated'
    ];

    protected $casts = [
        'suhu_pusat' => 'array',
        'average' => 'float'
    ];

    protected $dates = ['deleted_at'];
}
