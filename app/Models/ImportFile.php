<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportFile extends Model
{

    protected $fillable = [
        'user_id',
        'file_name',
        'file_extension',
        'file_service',
        'file_size',
        'file_step_id',
        'file_status_id',
        'error_log'
    ];

    protected $casts = [
        'created_at',
        'updated_at'
    ];
}
