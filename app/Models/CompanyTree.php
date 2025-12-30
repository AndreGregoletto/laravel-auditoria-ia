<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTree extends Model
{
    protected $fillable = [
        'company_tree_id',
        'company_id',
        'company_parent_id',
        'holding',
        'levels',
        'status'
    ];

    protected $casts = [
        'holding' => 'boolean',
        'status'  => 'boolean',
    ];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function companyTree()
    {
        return $this->hasOne(Company::class, 'id', 'company_tree_id');
    }

    function companyParent()
    {
        return $this->hasOne(Company::class, 'id', 'company_parent_id');
    }

    public function parentTree()
    {
        return $this->belongsTo(self::class, 'company_tree_id');
    }

//    public function children()
//    {
//        return $this->hasMany(self::class, 'company_tree_id');
//    }

    public function parentCompany()
    {
        return $this->belongsTo(Company::class, 'company_parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'company_parent_id', 'company_id')
            ->whereColumn('company_tree_id', 'company_tree_id');
    }
}
