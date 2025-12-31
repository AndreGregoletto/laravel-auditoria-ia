<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileStep extends Model
{
    protected $fillable = [
        'name',
        'name_conf',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at',
        'updated_at',
    ];
}
