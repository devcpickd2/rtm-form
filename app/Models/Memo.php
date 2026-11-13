<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memo extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'memos';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'shift', 'retur_dari', 'retur_ke', 'subject', 'detail_retur','username', 'username_updated','nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}
