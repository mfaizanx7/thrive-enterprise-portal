<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringInvoiceLog extends Model
{
    protected $fillable = [
        'template_id',
        'invoice_id',
        'generated_at',
        'sent_at',
        'status',
        'error_message',
    ];

    public function template()
    {
        return $this->belongsTo(RecurringInvoiceTemplate::class, 'template_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}