<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotIncomeStatementReference extends Model
{
    protected $fillable = [
        'income_statement_id',
        'value',
        'company_tree_id',
        'company_id',
        'status',
        'create_user_id',
        'alter_user_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at',
        'updated_at',
    ];
}
