<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTree extends Model
{
    protected $fillable = [
        'company_parent_id',
        'company_id',
        'company_parent',
        'levels',
        'status'
    ];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_parent_id');
    }
}
