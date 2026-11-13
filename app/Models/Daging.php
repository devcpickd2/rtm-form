<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Daging extends Model
{
    use HasFactory, HasUuid, SoftDeletes;
    
    protected $fillable = ['username', 'nama_daging', 'uuid'];
}
