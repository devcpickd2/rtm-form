<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaris extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'inventaris';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date','shift','area','detail','kondisi_awal','kondisi_akhir','keterangan', 'username', 'username_updated'
    ];

    protected $casts = [
        'detail' => 'array'
    ];

    protected $dates = ['deleted_at'];
}
