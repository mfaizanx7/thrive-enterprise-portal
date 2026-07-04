<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringInvoiceLineItem extends Model
{
    protected $fillable = [
        'template_id',
        'description',
        'quantity',
        'unit_price',
        'tax',
        'tax_amount',
        'discount',
        'total',
        'sort_order',
    ];

    public function template()
    {
        return $this->belongsTo(RecurringInvoiceTemplate::class, 'template_id');
    }
}