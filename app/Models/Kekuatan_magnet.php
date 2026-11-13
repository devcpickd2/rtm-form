<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kekuatan_magnet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kekuatan_magnets';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date','shift','nama_alat','gauss','kondisi','parameter','keterangan', 'nama_produksi', 'status_produksi', 'tgl_update_produksi', 'nama_spv', 'status_spv', 'tgl_update_spv', 'username', 'username_updated'
    ];

    protected $dates = ['deleted_at'];
}
