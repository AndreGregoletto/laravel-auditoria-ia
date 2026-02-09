<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PivotBalanceSheetReference extends Model
{
    protected $fillable = [
        'balance_sheet_id',
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

    public function balanceSheet(): HasOne
    {
        return $this->hasOne(BalanceSheet::class, 'id', 'balance_sheet_id');
    }

    public function companyTree(): HasOne
    {
        return $this->hasOne(CompanyTree::class, 'id', 'company_tree_id');
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function userCreate(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'create_user_id');
    }

    public function userAlter(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'alter_user_id');
    }
}
