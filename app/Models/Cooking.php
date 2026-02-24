<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cooking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'cookings';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'nama_produk', 'sub_produk', 'jenis_produk', 'kode_produksi', 'waktu_mulai', 'waktu_selesai', 'nama_mesin', 'pemasakan', 'catatan',
        'username', 'nama_produksi', 'status_produksi', 'nama_spv', 'status_spv', 'catatan_spv', 'username_updated', 'tgl_update_produksi', 'tgl_update_spv'
    ];

    protected $casts = [
        'cooking' => 'array',
    ];

    protected $dates = ['deleted_at'];
}
