<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'commercial_name',
        'cnpj',
        'publicity_trade',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'publicity_trade' => 'boolean',
    ];
}
