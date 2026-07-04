<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAssetDetail extends Model
{
    use HasFactory;
      protected $fillable = [
        'name',
        'company_id',
        'asset_id',
        'quantity'
    ];
      public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
}
