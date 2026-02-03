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
        'prefix',
        'status',
    ];
}
