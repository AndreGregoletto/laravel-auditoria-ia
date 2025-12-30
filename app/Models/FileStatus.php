<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileStatus extends Model
{
    protected $fillable = [
        'name',
        'name_conf',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
