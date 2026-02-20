<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tahapan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'tahapans';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'nama_produk', 'kode_produksi', 'filling_mulai', 'filling_selesai', 'waktu_iqf', 'waktu_sealer', 'waktu_xray', 'waktu_sticker', 'waktu_shrink', 'waktu_packing', 'waktu_cs', 'suhu_filling', 'suhu_masuk_iqf', 'suhu_keluar_iqf', 'suhu_sealer', 'suhu_xray', 'suhu_sticker', 'suhu_shrink', 'downtime', 'suhu_cs', 'keterangan',
        'catatan','username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];
    
    protected $casts = [
        'suhu_filling' => 'array',
        'filling_mulai' => 'datetime:H:i',
        'filling_selesai' => 'datetime:H:i',
        'waktu_iqf' => 'datetime:H:i',
        'waktu_sealer' => 'datetime:H:i',
        'waktu_xray' => 'datetime:H:i',
        'waktu_sticker' => 'datetime:H:i',
        'waktu_shrink' => 'datetime:H:i',
        'waktu_packing' => 'datetime:H:i',
        'waktu_cs' => 'datetime:H:i'
    ];
    protected $dates = ['deleted_at'];
}
