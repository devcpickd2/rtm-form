<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendukung extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'pendukungs';

    protected $fillable = ['nama_karyawan', 'area', 'uuid'];

    protected $dates = ['deleted_at'];
}
