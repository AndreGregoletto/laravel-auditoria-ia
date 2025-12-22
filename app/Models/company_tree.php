<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class company_tree extends Model
{
    protected $fillable = [
        'company_parent_id',
        'company_id',
        'company_parent',
        'levels',
        'status'
    ];
}
