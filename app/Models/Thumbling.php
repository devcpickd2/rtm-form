<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Thumbling extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'thumblings'; 
    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'username',
        'username_updated',
        'date',
        'shift',
        'nama_produk',
        'kode_produksi',
        'identifikasi_daging',
        'asal_daging',
        'kode_daging',
        'berat_daging',
        'suhu_daging',
        'rata_daging',
        'kondisi_daging',
        'premix',
        'kode_premix',
        'berat_premix',
        'bahan_lain',
        'air',
        'suhu_air',
        'suhu_marinade',
        'lama_pengadukan',
        'marinade_brix_salinity',
        'drum_on',
        'drum_off',
        'drum_speed',
        'vacuum_time',
        'total_time',
        'waktu_mulai',
        'waktu_selesai',
        'suhu_daging_thumbling',
        'rata_daging_thumbling',
        'kondisi_daging_akhir',
        'catatan_akhir',
        'catatan',
        'nama_produksi',
        'status_produksi',
        'tgl_update_produksi',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'tgl_update_spv'
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $casts = [
        'kode_daging'    => 'array',
        'berat_daging'   => 'array',
        'suhu_daging'    => 'array',
        'rata_daging'    => 'array',
        'premix' => 'array',
        'kode_premix' => 'array',
        'premix' => 'array',
        'berat_premix'    => 'array',
        'bahan_lain'     => 'array',
        'suhu_daging_thumbling' => 'array',
    ];

}
