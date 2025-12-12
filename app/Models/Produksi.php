<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Produksi extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'produksis';

    protected $fillable = ['nama_karyawan', 'area', 'uuid'];
}
