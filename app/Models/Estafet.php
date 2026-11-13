<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Estafet extends Model
{
    use HasFactory, HasUuid, SoftDeletes;
    
    protected $fillable = ['username', 'area', 'nama_alat', 'jumlah', 'uuid'];
}
