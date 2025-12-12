<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Thermometer extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'thermometers';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'kode_thermometer', 'area', 'standar', 
        'waktu_tera', 'hasil_tera', 'tindakan_koreksi', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];
    protected $casts = [
        'kode_thermometer' => 'array',
        'area' => 'array',
        'standar' => 'array',
        'waktu_tera' => 'array',
        'hasil_tera' => 'array',
        'tindakan_koreksi' => 'array', 
    ];

}
