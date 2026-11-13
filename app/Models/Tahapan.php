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
        'date', 'shift', 'nama_produk', 'kode_produksi', 'filling_mulai', 'filling_selesai', 'waktu_iqf', 'waktu_sealer', 'waktu_xray', 'waktu_sticker', 'waktu_shrink', 'waktu_packing', 'waktu_cs', 'suhu_filling', 'suhu_masuk_iqf', 'suhu_keluar_iqf', 'suhu_sealer', 'suhu_xray', 'suhu_sticker', 'suhu_shrink', 'downtime', 'suhu_cs', 'tanggal_filling','tanggal_iqf','tanggal_sealer','tanggal_xray','tanggal_sticker','tanggal_shrink','tanggal_packing','tanggal_cs', 'keterangan', 'catatan','username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
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
        'waktu_cs' => 'datetime:H:i',
        'tanggal_filling' => 'date',
        'tanggal_iqf' => 'date',
        'tanggal_sealer' => 'date',
        'tanggal_xray' => 'date',
        'tanggal_sticker' => 'date',
        'tanggal_shrink' => 'date',
        'tanggal_packing' => 'date',
        'tanggal_cs' => 'date',
    ];
    protected $dates = ['deleted_at'];
}
