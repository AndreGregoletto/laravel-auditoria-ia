<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceSheet extends Model
{
    protected $fillable = [
        'code',
        'name',
        'company_tree_id',
        'company_id',
        'status',
        'parent_code',
        'sort_order',
        'side',
        'section',
        'config_name',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at',
        'updated_at',
    ];
}
