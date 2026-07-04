<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'capacity',
        'price',
        'pricing_type',
        'per_seat_pricing',
        'base_price',
        'custom',
        'type_id',
        'meeting',
        'window',
        'description',
        'owned_by',
        'created_by',
    ];

    public function type()
    {
        return $this->hasOne('App\Models\SpaceType', 'id', 'type_id');
    }
}
