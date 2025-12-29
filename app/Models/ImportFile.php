<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImportFile extends Model
{

    protected $fillable = [
        'user_id',
        'company_id',
        'reference_month',
        'reference_year',
        'company_id',
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

    public function type_file(): HasOne
    {
        return $this->hasOne(TypeFile::class, 'id', 'file_service');
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
