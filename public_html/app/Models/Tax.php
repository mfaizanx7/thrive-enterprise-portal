<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name', 'rate','account_id', 'owned_by', 'created_by'
    ];

    public function chartAccount()
    {
        return $this->hasOne('App\Models\ChartOfAccount', 'id', 'account_id');
    }
    
}
