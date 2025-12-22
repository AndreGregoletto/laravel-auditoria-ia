<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class company extends Model
{
    protected $fillable = [
        'name',
        'commercial_name',
        'cnpj',
        'publicity_trade',
        'status',
    ];
}
