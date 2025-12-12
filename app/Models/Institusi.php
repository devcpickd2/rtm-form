<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institusi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'institusis';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'jenis_produk', 'kode_produksi', 
        'waktu_proses_mulai', 'waktu_proses_selesai', 'lokasi', 'suhu_sebelum', 'suhu_sesudah', 'sensori', 'keterangan', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ]; 

    protected $dates = ['deleted_at'];
}
