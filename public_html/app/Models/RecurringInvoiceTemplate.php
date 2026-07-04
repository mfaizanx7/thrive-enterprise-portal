<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringInvoiceTemplate extends Model
{
    protected $fillable = [
        'name',
        'customer_id',
        'contract_id',
        'space_id',
        'cycle',
        'start_date',
        'end_date',
        'next_invoice_date',
        'invoice_day',
        'auto_send',
        'status',
        'notes',
        'owned_by',
        'created_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(RecurringInvoiceLineItem::class, 'template_id');
    }

    public function logs()
    {
        return $this->hasMany(RecurringInvoiceLog::class, 'template_id');
    }

    public function generatedInvoices()
    {
        return $this->hasMany(Invoice::class, 'recurring_template_id');
    }
}